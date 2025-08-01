<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Membre;
use App\Models\Dahira;
use App\Models\User;


class DashboardController extends Controller
{public function adminDashboard()
{
    $total_members = Membre::count();

    $active_members = Membre::where('active', true)->count();

    // Récupérer les 5 derniers membres créés (ordre décroissant par date de création)
    $recent_members = Membre::orderBy('created_at', 'desc')
        ->take(3)
        ->get(['nom', 'prenom', 'email', 'telephone',]);  // sélectionne les colonnes que tu veux exposer

    return response()->json([
        'message' => 'Bienvenue sur le dashboard admin',
        'total_members' => $total_members,
        'active_members' => $active_members,
        'recent_members' => $recent_members,
    ]);
}

/**
 * Statistiques globales pour Super Admin
 */
public function globalStats()
{
    try {
        \Log::info('📊 DashboardController->globalStats() appelée');
        
        $totalDahiras = Dahira::count();
        $totalUsers = User::count();
        $totalMembres = Membre::count();
        $activeDahiras = Dahira::where('statut', 'actif')->count();
        
        $stats = [
            'total_dahiras' => $totalDahiras,
            'total_users' => $totalUsers,
            'total_membres' => $totalMembres,
            'active_dahiras' => $activeDahiras,
            'recent_dahiras' => Dahira::orderBy('created_at', 'desc')->take(5)->get(),
            'recent_users' => User::with('role')->orderBy('created_at', 'desc')->take(5)->get(),
        ];
        
        \Log::info('✅ Statistiques calculées: ' . json_encode($stats));
        return response()->json($stats);
    } catch (\Exception $e) {
        \Log::error('❌ Erreur dans globalStats(): ' . $e->getMessage());
        return response()->json(['error' => 'Erreur lors de la récupération des statistiques'], 500);
    }
}

}
