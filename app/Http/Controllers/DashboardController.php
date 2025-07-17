<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Membre;


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

}
