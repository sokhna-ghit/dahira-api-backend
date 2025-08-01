<?php

namespace App\Http\Controllers;

use App\Models\Membre;
use App\Models\Dahira;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MembreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            Log::info('MembreController@index - User:', ['user_id' => $user->id, 'role' => $user->role->name ?? 'N/A']);

            $query = Membre::with(['dahira', 'user']);

            // Si l'utilisateur n'est pas Super Admin, filtrer par dahira
            if ($user->role->name !== 'super_admin') {
                if ($user->dahira_id) {
                    $query->where('dahira_id', $user->dahira_id);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non associé à un dahira'
                    ], 403);
                }
            }

            // Recherche
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('telephone', 'like', "%{$search}%");
                });
            }

            // Filtre par statut
            if ($request->has('statut') && !empty($request->statut)) {
                $query->where('statut', $request->statut);
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            $membres = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $membres->items(),
                'meta' => [
                    'current_page' => $membres->currentPage(),
                    'total' => $membres->total(),
                    'per_page' => $membres->perPage(),
                    'last_page' => $membres->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('MembreController@index - Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des membres'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            Log::info('MembreController@store - Data:', $request->all());

            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:membres,email',
                'telephone' => 'required|string|max:20',
                'adresse' => 'required|string',
                'genre' => 'required|in:masculin,féminin',
                'date_naissance' => 'required|date|before:today',
                'profession' => 'nullable|string|max:255',
                'statut' => 'required|in:actif,inactif,suspendu',
                'commentaires' => 'nullable|string',
                'dahira_id' => 'required|exists:dahiras,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérifier les permissions
            if ($user->role->name !== 'super_admin' && $user->role->name !== 'président') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé pour ajouter des membres'
                ], 403);
            }

            // Si pas Super Admin, forcer le dahira_id de l'utilisateur
            $data = $request->all();
            if ($user->role->name !== 'super_admin') {
                $data['dahira_id'] = $user->dahira_id;
            }

            $data['date_inscription'] = now()->toDateString();

            $membre = Membre::create($data);
            $membre->load(['dahira', 'user']);

            Log::info('MembreController@store - Success:', ['membre_id' => $membre->id]);

            return response()->json([
                'success' => true,
                'message' => 'Membre ajouté avec succès',
                'data' => $membre
            ], 201);

        } catch (\Exception $e) {
            Log::error('MembreController@store - Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du membre'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Membre $membre)
    {
        try {
            $user = $request->user();

            // Vérifier les permissions
            if ($user->role->name !== 'super_admin' && $user->dahira_id !== $membre->dahira_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé à ce membre'
                ], 403);
            }

            $membre->load(['dahira', 'user', 'cotisations']);

            return response()->json([
                'success' => true,
                'data' => $membre
            ]);

        } catch (\Exception $e) {
            Log::error('MembreController@show - Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du membre'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Membre $membre)
    {
        try {
            $user = $request->user();
            Log::info('MembreController@update - Data:', $request->all());

            // Vérifier les permissions
            if ($user->role->name !== 'super_admin' && $user->role->name !== 'président') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé pour modifier des membres'
                ], 403);
            }

            if ($user->role->name !== 'super_admin' && $user->dahira_id !== $membre->dahira_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé à ce membre'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:membres,email,' . $membre->id,
                'telephone' => 'required|string|max:20',
                'adresse' => 'required|string',
                'genre' => 'required|in:masculin,féminin',
                'date_naissance' => 'required|date|before:today',
                'profession' => 'nullable|string|max:255',
                'statut' => 'required|in:actif,inactif,suspendu',
                'commentaires' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $membre->update($request->all());
            $membre->load(['dahira', 'user']);

            Log::info('MembreController@update - Success:', ['membre_id' => $membre->id]);

            return response()->json([
                'success' => true,
                'message' => 'Membre modifié avec succès',
                'data' => $membre
            ]);

        } catch (\Exception $e) {
            Log::error('MembreController@update - Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du membre'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Membre $membre)
    {
        try {
            $user = $request->user();

            // Vérifier les permissions
            if ($user->role->name !== 'super_admin' && $user->role->name !== 'président') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé pour supprimer des membres'
                ], 403);
            }

            if ($user->role->name !== 'super_admin' && $user->dahira_id !== $membre->dahira_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé à ce membre'
                ], 403);
            }

            $nomComplet = $membre->nom_complet;
            $membre->delete();

            Log::info('MembreController@destroy - Success:', ['membre' => $nomComplet]);

            return response()->json([
                'success' => true,
                'message' => "Membre $nomComplet supprimé avec succès"
            ]);

        } catch (\Exception $e) {
            Log::error('MembreController@destroy - Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du membre'
            ], 500);
        }
    }

    /**
     * Get statistics for members
     */
    public function statistiques(Request $request)
    {
        try {
            $user = $request->user();

            $query = Membre::query();

            // Si l'utilisateur n'est pas Super Admin, filtrer par dahira
            if ($user->role->name !== 'super_admin') {
                if ($user->dahira_id) {
                    $query->where('dahira_id', $user->dahira_id);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non associé à un dahira'
                    ], 403);
                }
            }

            $stats = [
                'total' => $query->count(),
                'actifs' => $query->where('statut', 'actif')->count(),
                'inactifs' => $query->where('statut', 'inactif')->count(),
                'suspendus' => $query->where('statut', 'suspendu')->count(),
                'hommes' => $query->where('genre', 'masculin')->count(),
                'femmes' => $query->where('genre', 'féminin')->count(),
                'nouveaux_ce_mois' => $query->whereMonth('date_inscription', now()->month)
                                           ->whereYear('date_inscription', now()->year)
                                           ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('MembreController@statistiques - Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques'
            ], 500);
        }
    }

    /**
     * Search members
     */
    public function rechercher(Request $request)
    {
        try {
            $user = $request->user();
            $query = $request->get('q', '');

            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $membres = Membre::with(['dahira', 'user'])
                ->where(function ($q) use ($query) {
                    $q->where('nom', 'like', "%{$query}%")
                      ->orWhere('prenom', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('telephone', 'like', "%{$query}%");
                });

            // Si l'utilisateur n'est pas Super Admin, filtrer par dahira
            if ($user->role->name !== 'super_admin') {
                if ($user->dahira_id) {
                    $membres->where('dahira_id', $user->dahira_id);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non associé à un dahira'
                    ], 403);
                }
            }

            $resultat = $membres->limit(20)->get();

            return response()->json([
                'success' => true,
                'data' => $resultat
            ]);

        } catch (\Exception $e) {
            Log::error('MembreController@rechercher - Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche'
            ], 500);
        }
    }
}
