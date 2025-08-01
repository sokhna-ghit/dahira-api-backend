<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dahira;
use App\Models\Membre;
use App\Models\Cotisation;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Dashboard administrateur avec statistiques
     */
    public function dashboard()
    {
        try {
            \Log::info('📊 AdminController->dashboard() appelée');
            
            // Statistiques générales
            $totalUsers = User::count();
            $totalDahiras = Dahira::count();
            $totalMembres = Membre::count();
            $membresActifs = Membre::where('active', true)->count();
            $totalCotisations = Cotisation::sum('montant') ?? 0;
            
            // Utilisateurs récents (5 derniers)
            $recentUsers = User::with('role')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role ? $user->role->name : 'membre',
                        'created_at' => $user->created_at->format('d/m/Y'),
                    ];
                });
            
            // Membres récents (5 derniers)
            $recentMembres = Membre::with('dahira')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($membre) {
                    return [
                        'id' => $membre->id,
                        'nom' => $membre->nom,
                        'prenom' => $membre->prenom,
                        'email' => $membre->email,
                        'telephone' => $membre->telephone,
                        'dahira' => $membre->dahira ? $membre->dahira->nom : null,
                        'created_at' => $membre->created_at->format('d/m/Y'),
                    ];
                });
            
            // Statistiques par dahira
            $statsDahiras = Dahira::withCount(['membres'])
                ->get()
                ->map(function ($dahira) {
                    return [
                        'id' => $dahira->id,
                        'nom' => $dahira->nom,
                        'ville' => $dahira->ville,
                        'membres_count' => $dahira->membres_count,
                    ];
                });
            
            // Cotisations par mois (6 derniers mois)
            $cotisationsParMois = DB::table('cotisations')
                ->select(
                    DB::raw('YEAR(date_paiement) as annee'),
                    DB::raw('MONTH(date_paiement) as mois'),
                    DB::raw('SUM(montant) as total'),
                    DB::raw('COUNT(*) as nombre')
                )
                ->where('date_paiement', '>=', now()->subMonths(6))
                ->groupBy('annee', 'mois')
                ->orderBy('annee', 'desc')
                ->orderBy('mois', 'desc')
                ->get();
            
            $response = [
                'message' => 'Bienvenue Administrateur !',
                'user' => auth()->user()->only(['id', 'name', 'email']),
                'stats' => [
                    'total_users' => $totalUsers,
                    'total_dahiras' => $totalDahiras,
                    'total_membres' => $totalMembres,
                    'membres_actifs' => $membresActifs,
                    'membres_inactifs' => $totalMembres - $membresActifs,
                    'total_cotisations' => $totalCotisations,
                    'pending_approvals' => 0, // À implémenter selon vos besoins
                ],
                'recent_users' => $recentUsers,
                'recent_membres' => $recentMembres,
                'stats_dahiras' => $statsDahiras,
                'cotisations_par_mois' => $cotisationsParMois,
                'success' => true
            ];
            
            \Log::info('✅ Dashboard data compiled successfully');
            return response()->json($response);
            
        } catch (\Exception $e) {
            \Log::error('❌ Erreur dans AdminController->dashboard(): ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la récupération des statistiques',
                'message' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }
    
    /**
     * Obtenir tous les utilisateurs
     */
    public function getUsers()
    {
        try {
            $users = User::with('role', 'dahira')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role ? $user->role->name : 'membre',
                        'dahira' => $user->dahira ? $user->dahira->nom : null,
                        'created_at' => $user->created_at->format('d/m/Y H:i'),
                        'updated_at' => $user->updated_at->format('d/m/Y H:i'),
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $users,
                'total' => $users->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('❌ Erreur dans AdminController->getUsers(): ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la récupération des utilisateurs',
                'success' => false
            ], 500);
        }
    }
    
    /**
     * Obtenir toutes les dahiras avec statistiques
     */
    public function getDahiras()
    {
        try {
            $dahiras = Dahira::withCount(['membres'])
                ->with(['membres' => function($query) {
                    $query->where('active', true);
                }])
                ->get()
                ->map(function ($dahira) {
                    return [
                        'id' => $dahira->id,
                        'nom' => $dahira->nom,
                        'ville' => $dahira->ville,
                        'description' => $dahira->description ?? '',
                        'membres_count' => $dahira->membres_count,
                        'membres_actifs' => $dahira->membres->count(),
                        'created_at' => $dahira->created_at->format('d/m/Y'),
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $dahiras,
                'total' => $dahiras->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('❌ Erreur dans AdminController->getDahiras(): ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la récupération des dahiras',
                'success' => false
            ], 500);
        }
    }
}
