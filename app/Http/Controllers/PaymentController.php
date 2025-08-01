<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Paiement;
use App\Models\PaiementPaydunya;
use App\Services\PaydunyaService;

class PaymentController extends Controller
{
    protected $paydunyaService;

    public function __construct(PaydunyaService $paydunyaService)
    {
        $this->paydunyaService = $paydunyaService;
    }

    public function simulatePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'phone' => 'required|string',
        ]);

        $reference = uniqid('sim_');
        $isSuccess = rand(1, 100) <= 80;
        $statut = $isSuccess ? 'reussi' : 'echoue';

        $paiement = Paiement::create([
            'telephone' => $request->phone,
            'montant' => $request->amount,
            'statut' => $statut,
            'reference_transaction' => $reference,
        ]);

        return response()->json([
            'status' => $statut,
            'reference' => $reference,
            'message' => $isSuccess ? 'Paiement réussi.' : 'Paiement échoué.',
            'paiement_id' => $paiement->id,
        ]);
    }

    /**
     * Obtenir la liste des opérateurs supportés par PayDunya
     */
    public function obtenirOperateurs()
    {
        try {
            $operateurs = $this->paydunyaService->getSupportedOperators();
            
            return response()->json([
                'success' => true,
                'operateurs' => $operateurs,
                'message' => 'Liste des opérateurs récupérée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des opérateurs : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Traiter un paiement de cotisation via PayDunya
     */
    public function traiterPaiementPaydunya(Request $request)
    {
        $validated = $request->validate([
            'membre_id' => 'required|integer',
            'montant' => 'required|numeric|min:100',
            'telephone' => 'required|string',
            'type_cotisation' => 'required|string',
            'description' => 'nullable|string',
        ]);

        try {
            // Générer une référence unique
            $reference = 'DAHIRA_' . $validated['membre_id'] . '_' . time();

            // Préparer les données de paiement
            $paymentData = [
                'membre_id' => $validated['membre_id'],
                'reference' => $reference,
                'telephone' => $validated['telephone'],
                'montant' => $validated['montant'],
                'type_cotisation' => $validated['type_cotisation'],
                'description' => $validated['description'] ?? "Cotisation {$validated['type_cotisation']}"
            ];

            // Initier le paiement PayDunya
            $result = $this->paydunyaService->initiatePayment($paymentData);

            if ($result['success']) {
                // Sauvegarder le paiement dans la base de données
                $paiementPaydunya = PaiementPaydunya::create([
                    'membre_id' => $validated['membre_id'],
                    'reference' => $reference,
                    'invoice_token' => $result['invoice_token'],
                    'invoice_url' => $result['invoice_url'],
                    'montant' => $validated['montant'],
                    'telephone' => $validated['telephone'],
                    'operateur' => $result['operateur'],
                    'mode_paiement' => $result['mode_paiement'],
                    'type_cotisation' => $validated['type_cotisation'],
                    'description' => $validated['description'] ?? "Cotisation {$validated['type_cotisation']}",
                    'statut' => 'en_cours',
                    'statut_paydunya' => 'pending',
                    'donnees_paydunya' => $result,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Paiement initié avec succès',
                    'paiement_id' => $paiementPaydunya->id,
                    'operateur' => $result['operateur'],
                    'mode_paiement' => $result['mode_paiement'],
                    'invoice_token' => $result['invoice_token'],
                    'invoice_url' => $result['invoice_url'],
                    'reference' => $reference,
                    'montant' => $validated['montant'],
                    'telephone' => $validated['telephone'],
                ]);
            } else {
                // 🔄 FALLBACK AUTOMATIQUE : Si PayDunya est bloqué par Cloudflare, utiliser la simulation
                if (isset($result['error_code']) && $result['error_code'] === 'CLOUDFLARE_BLOCK') {
                    \Log::info('🔄 PayDunya indisponible (Cloudflare), fallback automatique vers simulation', [
                        'membre_id' => $validated['membre_id'],
                        'montant' => $validated['montant'],
                        'telephone' => $validated['telephone']
                    ]);

                    // Utiliser le système de simulation interne
                    $isSuccess = rand(1, 100) <= 80; // 80% de succès
                    $statutSimulation = $isSuccess ? 'reussi' : 'echoue';
                    $referenceSimulation = uniqid('sim_');

                    // Créer directement dans la table paiements (système de simulation)
                    $paiementSimule = Paiement::create([
                        'telephone' => $validated['telephone'],
                        'montant' => $validated['montant'],
                        'statut' => $statutSimulation,
                        'reference_transaction' => $referenceSimulation,
                        'method_paiement' => 'simulation_fallback',
                        'type_cotisation' => $validated['type_cotisation'],
                        'description' => "SIMULATION - " . ($validated['description'] ?? "Cotisation {$validated['type_cotisation']}"),
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => $isSuccess ? 'Paiement réussi (mode simulation)' : 'Paiement échoué (mode simulation)',
                        'mode' => 'simulation_fallback',
                        'paiement_id' => $paiementSimule->id,
                        'reference' => $referenceSimulation,
                        'statut' => $statutSimulation,
                        'operateur' => 'simulation',
                        'montant' => $validated['montant'],
                        'telephone' => $validated['telephone'],
                        'fallback_reason' => 'PayDunya temporairement indisponible'
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Erreur lors de l\'initiation du paiement',
                    'error_code' => $result['error_code'] ?? null
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier le statut d'un paiement PayDunya
     */
    public function verifierStatutPaydunya($invoiceToken)
    {
        try {
            $result = $this->paydunyaService->checkPaymentStatus($invoiceToken);

            // Mettre à jour le paiement dans la base de données
            $paiementPaydunya = PaiementPaydunya::where('invoice_token', $invoiceToken)->first();
            
            if ($paiementPaydunya && $result['success']) {
                $statutPaydunya = $result['paydunya_status'] ?? 'unknown';
                $statutInterne = $result['status'] ?? 'en_cours';
                
                $paiementPaydunya->update([
                    'statut_paydunya' => $statutPaydunya,
                    'statut' => $statutInterne,
                    'date_paiement' => $statutInterne === 'reussi' ? now() : null,
                    'donnees_paydunya' => array_merge($paiementPaydunya->donnees_paydunya ?? [], $result),
                ]);
            }

            return response()->json([
                'success' => $result['success'],
                'statut_paydunya' => $result['paydunya_status'] ?? 'inconnu',
                'statut_interne' => $result['status'] ?? 'inconnu',
                'message' => $result['message'] ?? 'Statut récupéré',
                'invoice_token' => $invoiceToken,
                'paiement_id' => $paiementPaydunya->id ?? null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir l'historique des paiements PayDunya pour un membre
     */
    public function obtenirHistoriquePaydunya($membreId)
    {
        try {
            $paiements = PaiementPaydunya::where('membre_id', $membreId)
                ->with('membre')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($paiement) {
                    return [
                        'id' => $paiement->id,
                        'reference' => $paiement->reference,
                        'invoice_token' => $paiement->invoice_token,
                        'montant' => $paiement->montant,
                        'telephone' => $paiement->telephone,
                        'operateur' => $paiement->operateur,
                        'mode_paiement' => $paiement->mode_paiement,
                        'type_cotisation' => $paiement->type_cotisation,
                        'description' => $paiement->description,
                        'statut' => $paiement->statut,
                        'statut_paydunya' => $paiement->statut_paydunya,
                        'date_creation' => $paiement->created_at->format('Y-m-d H:i:s'),
                        'date_paiement' => $paiement->date_paiement?->format('Y-m-d H:i:s'),
                        'membre_nom' => $paiement->membre->nom ?? 'Inconnu',
                        'membre_prenom' => $paiement->membre->prenom ?? '',
                    ];
                });

            return response()->json([
                'success' => true,
                'membre_id' => $membreId,
                'paiements' => $paiements,
                'total' => $paiements->count(),
                'message' => 'Historique récupéré avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'historique : ' . $e->getMessage()
            ], 500);
        }
    }

    public function genererRecu($id)
    {
        $paiement = Paiement::findOrFail($id);

        $pdf = Pdf::loadView('recu', [
            'paiement' => $paiement,
            'dahira' => 'Dahira Sokhna',
        ]);

        return $pdf->download('recu_'.$paiement->reference.'.pdf');
    }
}
