<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paiement;
use App\Models\Cotisation;
use App\Models\Membre;
use App\Models\User;
use App\Models\Dahira;
use App\Services\PdfService;
use App\Services\OrangeMoneyService;
use App\Services\PaydunyaService;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\RecuPaiementMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentController extends Controller
{
    protected $orangeMoneyService;
    protected $paydunyaService;

    public function __construct(OrangeMoneyService $orangeMoneyService, PaydunyaService $paydunyaService)
    {
        $this->orangeMoneyService = $orangeMoneyService;
        $this->paydunyaService = $paydunyaService;
    }

    /**
     * Initier un paiement de cotisation avec Orange Money API
     */
    public function initierPaiementCotisation(Request $request)
    {
        try {
            \Log::info('💰 Initiation paiement cotisation RÉEL - Données reçues: ' . json_encode($request->all()));
            
            $validated = $request->validate([
                'membre_id' => 'required|exists:membres,id',
                'montant' => 'required|numeric|min:500', // Minimum 500 FCFA
                'telephone' => 'required|string|regex:/^(77|78|70|76|33)[0-9]{7}/', // Format sénégalais
                'operateur' => 'required|in:orange,free,wave', // Opérateurs supportés
                'type_cotisation' => 'required|in:mensuelle,annuelle,evenement,zakat',
                'description' => 'nullable|string|max:255',
            ]);

            $membre = Membre::findOrFail($validated['membre_id']);
            $reference = $this->genererReference($validated['operateur']);
            
            // Créer l'enregistrement du paiement
            $paiement = Paiement::create([
                'membre_id' => $membre->id,
                'dahira_id' => $membre->dahira_id,
                'montant' => $validated['montant'],
                'telephone' => $validated['telephone'],
                'operateur' => $validated['operateur'],
                'type_cotisation' => $validated['type_cotisation'],
                'description' => $validated['description'],
                'reference' => $reference,
                'statut' => 'en_attente',
                'method_paiement' => 'mobile_money',
            ]);
            
            // NOUVEAU : Utiliser PayDunya en priorité (tous opérateurs)
            $resultPaiement = $this->traiterPaiementPaydunya($paiement, $validated);
            
            // Mettre à jour le statut
            $paiement->update([
                'statut' => $resultPaiement['status'],
                'transaction_id' => $resultPaiement['transaction_id'] ?? null,
                'payment_token' => $resultPaiement['payment_token'] ?? null,
                'date_paiement' => $resultPaiement['status'] === 'reussi' ? now() : null,
            ]);
            
            // Si paiement réussi, créer la cotisation
            if ($resultPaiement['status'] === 'reussi') {
                $cotisation = Cotisation::create([
                    'membre_id' => $membre->id,
                    'dahira_id' => $membre->dahira_id,
                    'montant' => $validated['montant'],
                    'type' => $validated['type_cotisation'],
                    'date_paiement' => now(),
                    'paiement_id' => $paiement->id,
                    'statut' => 'paye',
                ]);
                
                \Log::info('✅ Cotisation créée avec ID: ' . $cotisation->id);
            }
            
            return response()->json([
                'success' => $resultPaiement['status'] === 'reussi',
                'status' => $resultPaiement['status'],
                'reference' => $reference,
                'message' => $resultPaiement['message'],
                'paiement_id' => $paiement->id,
                'montant' => $validated['montant'],
                'operateur' => $validated['operateur'],
                'membre' => [
                    'nom' => $membre->nom,
                    'prenom' => $membre->prenom,
                    'email' => $membre->email,
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ Erreur validation paiement: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('❌ Erreur paiement cotisation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement du paiement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 📱 Obtenir la liste des opérateurs de paiement supportés
     */
    public function obtenirOperateurs()
    {
        try {
            $operateurs = $this->paydunyaService->getSupportedOperators();
            
            return response()->json([
                'success' => true,
                'data' => $operateurs,
                'message' => 'Opérateurs récupérés avec succès'
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Erreur obtenirOperateurs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des opérateurs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Vérifier le statut d'un paiement
     */
    public function verifierStatutPaiement($reference)
    {
        try {
            $paiement = Paiement::where('reference', $reference)->first();
            
            if (!$paiement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paiement non trouvé'
                ], 404);
            }
            
            // Si en attente, vérifier avec l'opérateur (simulation)
            if ($paiement->statut === 'en_attente') {
                $nouveauStatut = $this->verifierAvecOperateur($paiement);
                if ($nouveauStatut !== $paiement->statut) {
                    $paiement->update(['statut' => $nouveauStatut]);
                    
                    // Si devient réussi, créer cotisation
                    if ($nouveauStatut === 'reussi' && !$paiement->cotisation) {
                        $membre = $paiement->membre;
                        Cotisation::create([
                            'membre_id' => $membre->id,
                            'dahira_id' => $membre->dahira_id,
                            'montant' => $paiement->montant,
                            'type' => $paiement->type_cotisation,
                            'date_paiement' => now(),
                            'paiement_id' => $paiement->id,
                            'statut' => 'paye',
                        ]);
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'statut' => $paiement->statut,
                'reference' => $paiement->reference,
                'montant' => $paiement->montant,
                'date_creation' => $paiement->created_at,
                'date_paiement' => $paiement->date_paiement,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('❌ Erreur vérification statut: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification'
            ], 500);
        }
    }
    
    /**
     * Obtenir l'historique des paiements d'un membre (Paiements classiques + PayDunya)
     */
    public function historiquePaiements($membreId)
    {
        try {
            $membre = Membre::findOrFail($membreId);
            
            // Récupérer les paiements classiques
            $paiementsClassiques = Paiement::where('membre_id', $membreId)
                ->with(['cotisation'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($paiement) {
                    return [
                        'id' => $paiement->id,
                        'reference' => $paiement->reference,
                        'montant' => $paiement->montant,
                        'statut' => $paiement->statut,
                        'operateur' => $paiement->operateur,
                        'type_cotisation' => $paiement->type_cotisation,
                        'description' => $paiement->description,
                        'date_creation' => $paiement->created_at->format('d/m/Y H:i'),
                        'date_paiement' => $paiement->date_paiement ? $paiement->date_paiement->format('d/m/Y H:i') : null,
                        'cotisation_id' => $paiement->cotisation ? $paiement->cotisation->id : null,
                        'type_paiement' => 'classique',
                    ];
                });

            // Récupérer les paiements PayDunya
            $paiementsPaydunya = \App\Models\PaiementPaydunya::where('membre_id', $membreId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($paiement) {
                    return [
                        'id' => $paiement->id,
                        'reference' => $paiement->reference,
                        'montant' => $paiement->montant,
                        'statut' => $paiement->statut,
                        'operateur' => $paiement->operateur,
                        'type_cotisation' => $paiement->type_cotisation,
                        'description' => $paiement->description,
                        'date_creation' => $paiement->created_at->format('d/m/Y H:i'),
                        'date_paiement' => $paiement->date_paiement ? $paiement->date_paiement->format('d/m/Y H:i') : null,
                        'cotisation_id' => null,
                        'type_paiement' => 'paydunya',
                    ];
                });

            // Fusionner et trier par date
            $tousPaiements = $paiementsClassiques->concat($paiementsPaydunya)
                ->sortByDesc('date_creation')
                ->values();
            
            return response()->json([
                'success' => true,
                'membre' => [
                    'nom' => $membre->nom,
                    'prenom' => $membre->prenom,
                ],
                'paiements' => $tousPaiements,
                'total_paiements' => $tousPaiements->where('statut', 'reussi')->count(),
                'montant_total' => $tousPaiements->where('statut', 'reussi')->sum('montant'),
            ]);
            
        } catch (\Exception $e) {
            \Log::error('❌ Erreur historique paiements: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'historique'
            ], 500);
        }
    }
    
    /**
     * Générer un reçu PDF
     */
    public function genererRecu($paiementId)
    {
        try {
            $paiement = Paiement::with(['membre', 'dahira', 'cotisation'])->findOrFail($paiementId);
            
            if ($paiement->statut !== 'reussi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de générer un reçu pour un paiement non réussi'
                ], 400);
            }
            
            $pdfService = new PdfService();
            $filename = $pdfService->genererRecuPaiement($paiement);
            
            $filePath = storage_path('app/public/recu/' . $filename);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la génération du reçu'
                ], 500);
            }
            
            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('❌ Erreur génération reçu: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du reçu'
            ], 500);
        }
    }
    
    /**
     * Statistiques des paiements (pour trésorier/admin)
     */
    public function statistiquesPaiements(Request $request)
    {
        try {
            $dahiraId = $request->query('dahira_id');
            $periode = $request->query('periode', '30'); // 30 jours par défaut
            
            $query = Paiement::query();
            
            if ($dahiraId) {
                $query->where('dahira_id', $dahiraId);
            }
            
            $query->where('created_at', '>=', now()->subDays($periode));
            
            $stats = [
                'total_paiements' => $query->count(),
                'paiements_reussis' => $query->where('statut', 'reussi')->count(),
                'paiements_en_attente' => $query->where('statut', 'en_attente')->count(),
                'paiements_echecs' => $query->where('statut', 'echoue')->count(),
                'montant_total' => $query->where('statut', 'reussi')->sum('montant'),
                'montant_moyen' => $query->where('statut', 'reussi')->avg('montant'),
            ];
            
            // Répartition par opérateur
            $parOperateur = $query->where('statut', 'reussi')
                ->selectRaw('operateur, COUNT(*) as nombre, SUM(montant) as total')
                ->groupBy('operateur')
                ->get();
            
            // Évolution par jour
            $evolution = $query->where('statut', 'reussi')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as nombre, SUM(montant) as total')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(7)
                ->get();
            
            return response()->json([
                'success' => true,
                'periode' => $periode . ' jours',
                'stats' => $stats,
                'par_operateur' => $parOperateur,
                'evolution_7_jours' => $evolution,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('❌ Erreur statistiques paiements: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques'
            ], 500);
        }
    }
    
    // Méthodes privées pour simulation
    
    private function genererReference($operateur)
    {
        $prefix = [
            'orange' => 'OM',
            'free' => 'FM',
            'wave' => 'WV'
        ][$operateur] ?? 'PM';
        
        return $prefix . date('YmdHis') . rand(1000, 9999);
    }
    
    private function traiterPaiementMobile($paiement)
    {
        // Simulation du traitement avec l'API de l'opérateur
        \Log::info('📱 Simulation paiement mobile pour: ' . $paiement->operateur);
        
        // 85% de réussite en simulation
        $reussi = rand(1, 100) <= 85;
        
        if ($reussi) {
            return [
                'status' => 'reussi',
                'transaction_id' => 'TXN' . rand(100000, 999999),
                'message' => 'Paiement effectué avec succès'
            ];
        } else {
            $erreurs = [
                'Solde insuffisant',
                'Code PIN incorrect',
                'Limite de transaction dépassée',
                'Service temporairement indisponible'
            ];
            
            return [
                'status' => 'echoue',
                'transaction_id' => null,
                'message' => $erreurs[array_rand($erreurs)]
            ];
        }
    }

    /**
     * 🚀 NOUVELLE MÉTHODE : Traiter paiement avec PayDunya (TOUS OPÉRATEURS)
     */
    private function traiterPaiementPaydunya($paiement, $validated)
    {
        try {
            \Log::info('💰 PayDunya: Traitement paiement unifié pour: ' . $validated['operateur']);

            // Valider le numéro de téléphone
            if (!$this->paydunyaService->validatePhoneNumber($validated['telephone'])) {
                throw new \Exception('Numéro de téléphone invalide pour les paiements mobile money');
            }

            // Détecter automatiquement l'opérateur
            $operateurDetecte = $this->paydunyaService->detectOperator($validated['telephone']);
            \Log::info("🤖 Opérateur détecté automatiquement: $operateurDetecte pour {$validated['telephone']}");

            // Formater le numéro
            $phoneFormatted = $this->paydunyaService->formatPhoneNumber($validated['telephone']);

            // Préparer les données pour PayDunya
            $paymentData = [
                'membre_id' => $validated['membre_id'],
                'reference' => $paiement->reference,
                'telephone' => $phoneFormatted,
                'montant' => $validated['montant'],
                'type_cotisation' => $validated['type_cotisation'],
                'description' => $validated['description'] ?? "Cotisation dahira - {$validated['type_cotisation']}"
            ];

            \Log::info('🔄 PayDunya: Envoi données paiement', $paymentData);

            // Appeler PayDunya (gère automatiquement Orange, Free, Wave, Cartes)
            $result = $this->paydunyaService->initiatePayment($paymentData);

            if ($result['success']) {
                \Log::info("✅ PayDunya: Paiement initié avec succès via {$result['operateur']}");
                
                return [
                    'status' => 'en_cours', // PayDunya nécessite confirmation utilisateur
                    'transaction_id' => $paiement->reference,
                    'invoice_token' => $result['invoice_token'],
                    'invoice_url' => $result['invoice_url'],
                    'operateur_detecte' => $result['operateur'],
                    'mode_paiement' => $result['mode_paiement'],
                    'message' => $result['message']
                ];
            } else {
                \Log::error('❌ PayDunya: Échec paiement', $result);
                
                // Si c'est un blocage Cloudflare, utiliser le fallback automatiquement
                if (isset($result['fallback_needed']) && $result['fallback_needed']) {
                    \Log::info('🔄 Basculement automatique vers simulation à cause de Cloudflare');
                    return $this->traiterPaiementMobile($paiement);
                }
                
                return [
                    'status' => 'echoue',
                    'transaction_id' => null,
                    'message' => $result['message'] ?? 'Erreur PayDunya'
                ];
            }
        } catch (\Exception $e) {
            \Log::error('❌ Erreur PayDunya: ' . $e->getMessage());
            
            // Fallback vers l'ancienne simulation si PayDunya échoue
            \Log::info('🔄 Fallback vers simulation pour: ' . $validated['operateur']);
            return $this->traiterPaiementMobile($paiement);
        }
    }

    /**
     * NOUVELLE MÉTHODE : Traiter paiement avec vraies APIs
     */
    private function traiterPaiementReel($paiement, $validated)
    {
        try {
            \Log::info('🚀 Traitement paiement RÉEL avec opérateur: ' . $validated['operateur']);

            switch ($validated['operateur']) {
                case 'orange':
                    return $this->traiterPaiementOrange($paiement, $validated);
                    
                case 'free':
                    // TODO: Implémenter Free Money API
                    \Log::info('🔶 Free Money: API pas encore implémentée, fallback simulation');
                    return $this->traiterPaiementMobile($paiement);
                    
                case 'wave':
                    // TODO: Implémenter Wave API
                    \Log::info('🌊 Wave: API pas encore implémentée, fallback simulation');
                    return $this->traiterPaiementMobile($paiement);
                    
                default:
                    throw new \Exception('Opérateur non supporté: ' . $validated['operateur']);
            }
        } catch (\Exception $e) {
            \Log::error('❌ Erreur traitement paiement réel: ' . $e->getMessage());
            
            // En cas d'erreur, fallback vers simulation
            \Log::info('🔄 Fallback vers simulation pour: ' . $validated['operateur']);
            return $this->traiterPaiementMobile($paiement);
        }
    }

    /**
     * Traiter paiement Orange Money avec vraie API
     */
    private function traiterPaiementOrange($paiement, $validated)
    {
        try {
            // Valider que c'est bien un numéro Orange
            if (!$this->orangeMoneyService->validateOrangeNumber($validated['telephone'])) {
                throw new \Exception('Numéro Orange Money invalide');
            }

            // Formater le numéro
            $phoneFormatted = $this->orangeMoneyService->formatPhoneNumber($validated['telephone']);

            // Préparer les données pour Orange Money API
            $paymentData = [
                'reference' => $paiement->reference,
                'transaction_id' => 'TXN_' . $paiement->id . '_' . time(),
                'telephone' => $phoneFormatted,
                'montant' => (int) $validated['montant'], // Orange attend un entier
                'description' => $validated['description'] ?? 'Cotisation dahira - ' . $validated['type_cotisation']
            ];

            \Log::info('🍊 Orange Money: Envoi données API', $paymentData);

            // Appeler l'API Orange Money
            $result = $this->orangeMoneyService->initiatePayment($paymentData);

            if ($result['success']) {
                \Log::info('✅ Orange Money: Paiement initié avec succès');
                
                return [
                    'status' => 'en_cours', // Orange Money nécessite confirmation utilisateur
                    'transaction_id' => $paymentData['transaction_id'],
                    'payment_token' => $result['payment_token'],
                    'payment_url' => $result['payment_url'] ?? null,
                    'message' => 'Paiement initié. Confirmez sur votre téléphone.'
                ];
            } else {
                \Log::error('❌ Orange Money: Échec initiation paiement', $result);
                
                return [
                    'status' => 'echoue',
                    'transaction_id' => null,
                    'message' => $result['message'] ?? 'Erreur Orange Money'
                ];
            }
        } catch (\Exception $e) {
            \Log::error('❌ Erreur Orange Money: ' . $e->getMessage());
            
            return [
                'status' => 'echoue',
                'transaction_id' => null,
                'message' => 'Erreur technique Orange Money: ' . $e->getMessage()
            ];
        }
    }
    
    private function verifierAvecOperateur($paiement)
    {
        // Simulation de vérification du statut
        if ($paiement->created_at->diffInMinutes(now()) > 5) {
            // Après 5 minutes, finaliser le statut
            return rand(1, 100) <= 80 ? 'reussi' : 'echoue';
        }
        
        return $paiement->statut;
    }
}
