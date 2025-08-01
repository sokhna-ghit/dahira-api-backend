<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Membre;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class ValidationController extends Controller
{
    /**
     * Demande d'inscription d'un nouveau membre
     */
    public function demandeInscription(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'telephone' => 'required|string',
            'adresse' => 'required|string',
            'genre' => 'required|in:masculin,féminin',
            'date_naissance' => 'required|date',
            'dahira_id' => 'required|exists:dahiras,id',
            'profession' => 'nullable|string',
        ]);

        try {
            // Créer l'utilisateur en attente
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => \App\Models\Role::where('name', 'membre')->first()->id,
                'dahira_id' => $validated['dahira_id'],
                'status' => 'pending',
                'is_approved' => false,
            ]);

            // Créer le profil membre associé
            $membre = Membre::create([
                'nom' => explode(' ', $validated['name'])[0],
                'prenom' => implode(' ', array_slice(explode(' ', $validated['name']), 1)),
                'email' => $validated['email'],
                'telephone' => $validated['telephone'],
                'adresse' => $validated['adresse'],
                'genre' => $validated['genre'],
                'date_naissance' => $validated['date_naissance'],
                'dahira_id' => $validated['dahira_id'],
                'profession' => $validated['profession'],
                'statut' => 'en_attente_validation',
                'user_id' => $user->id,
            ]);

            // Envoyer notification aux administrateurs
            $this->notifierAdministrateurs($user, $membre);

            // Envoyer email de confirmation au membre
            event(new Registered($user));

            return response()->json([
                'success' => true,
                'message' => 'Demande d\'inscription envoyée avec succès. Vous recevrez un email de confirmation une fois votre compte validé par un administrateur.',
                'user_id' => $user->id,
                'membre_id' => $membre->id,
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Erreur demande inscription: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la demande d\'inscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lister les demandes en attente (Président/Admin)
     */
    public function demandesEnAttente()
    {
        $user = Auth::user();
        
        // Vérifier les permissions
        if (!in_array($user->role->name, ['admin', 'président', 'super_admin'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $query = User::with(['role', 'dahira', 'membre'])
            ->where('status', 'pending')
            ->where('is_approved', false);

        // Filtrer selon le rôle de l'utilisateur connecté
        switch ($user->role->name) {
            case 'super_admin':
                // Super admin voit toutes les demandes
                break;
                
            case 'admin':
                // Admin voit les présidents, trésoriers, secrétaires et membres
                $query->whereHas('role', function($roleQuery) {
                    $roleQuery->whereIn('name', ['président', 'membre', 'trésorier', 'secrétaire_général']);
                });
                break;
                
            case 'président':
                // Président voit seulement les membres de sa dahira
                $query->where('dahira_id', $user->dahira_id)
                      ->whereHas('role', function($roleQuery) {
                          $roleQuery->where('name', 'membre');
                      });
                break;
        }

        $demandes = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'demandes' => $demandes->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->name ?? 'inconnu',
                    'dahira' => $user->dahira->nom ?? 'Non définie',
                    'telephone' => $user->membre->telephone ?? '',
                    'date_demande' => $user->created_at->format('d/m/Y H:i'),
                    'membre_info' => $user->membre ? [
                        'nom' => $user->membre->nom,
                        'prenom' => $user->membre->prenom,
                        'genre' => $user->membre->genre,
                        'profession' => $user->membre->profession,
                        'date_naissance' => $user->membre->date_naissance,
                    ] : null
                ];
            })
        ]);
    }

    /**
     * Valider/Rejeter une demande
     */
    public function traiterDemande(Request $request, $userId)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500'
        ]);

        $userAdmin = Auth::user();
        
        // Vérifier les permissions selon la hiérarchie
        $user = User::with(['membre', 'role'])->findOrFail($userId);
        
        // Règles de validation hiérarchique
        $canValidate = false;
        $roleAdmin = $userAdmin->role->name;
        $roleCandidat = $user->role->name;
        
        switch ($roleAdmin) {
            case 'super_admin':
                // Super admin peut valider tout le monde
                $canValidate = true;
                break;
                
            case 'admin':
                // Admin peut valider présidents et membres, mais pas d'autres admins
                $canValidate = in_array($roleCandidat, ['président', 'membre', 'trésorier', 'secrétaire_général']);
                break;
                
            case 'président':
                // Président peut valider uniquement les membres de sa dahira
                $canValidate = ($roleCandidat === 'membre') && ($user->dahira_id === $userAdmin->dahira_id);
                break;
                
            default:
                $canValidate = false;
        }
        
        if (!$canValidate) {
            return response()->json([
                'message' => "Vous n'avez pas l'autorisation de valider ce type de compte. Rôle candidat: $roleCandidat, Votre rôle: $roleAdmin"
            ], 403);
        }

        try {
            if ($validated['action'] === 'approve') {
                // Approuver le compte
                $user->update([
                    'status' => 'approved',
                    'is_approved' => true,
                    'approved_at' => now(),
                    'approved_by' => $userAdmin->id,
                    'approval_notes' => $validated['notes'],
                    'email_verified_at' => now(), // Valider l'email automatiquement
                ]);

                // Activer le membre
                if ($user->membre) {
                    $user->membre->update([
                        'statut' => 'actif',
                        'date_inscription' => now(),
                    ]);
                }

                $message = 'Compte approuvé avec succès';
                $this->envoyerEmailValidation($user, true, $validated['notes']);

            } else {
                // Rejeter le compte
                $user->update([
                    'status' => 'rejected',
                    'approved_by' => $userAdmin->id,
                    'approval_notes' => $validated['notes'],
                ]);

                if ($user->membre) {
                    $user->membre->update(['statut' => 'refuse']);
                }

                $message = 'Compte rejeté';
                $this->envoyerEmailValidation($user, false, $validated['notes']);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'user_status' => $user->status
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur traitement demande: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement de la demande'
            ], 500);
        }
    }

    /**
     * Renvoyer un email de vérification
     */
    public function renvoyerEmailVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié'], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Email de vérification envoyé'
        ]);
    }

    /**
     * Notifier les administrateurs d'une nouvelle demande
     */
    private function notifierAdministrateurs($user, $membre)
    {
        // Récupérer les admins et présidents de la dahira
        $administrateurs = User::whereHas('role', function($query) {
            $query->whereIn('name', ['admin', 'président', 'super_admin']);
        })
        ->where(function($query) use ($user) {
            $query->where('dahira_id', $user->dahira_id)
                  ->orWhereHas('role', function($q) {
                      $q->whereIn('name', ['admin', 'super_admin']);
                  });
        })
        ->get();

        // TODO: Envoyer notification email/push aux administrateurs
        foreach ($administrateurs as $admin) {
            \Log::info("Notification envoyée à {$admin->email} pour nouvelle demande de {$user->email}");
        }
    }

    /**
     * Envoyer email de validation/rejet
     */
    private function envoyerEmailValidation($user, $approved, $notes = null)
    {
        // TODO: Créer et envoyer l'email de validation
        \Log::info("Email " . ($approved ? 'validation' : 'rejet') . " envoyé à {$user->email}");
    }
}
