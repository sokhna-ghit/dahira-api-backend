<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Vérifie que l'utilisateur connecté a bien un des rôles permis.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles  Liste des rôles autorisés (ex: 'admin', 'membre')
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            // Pas connecté : interdit
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        // Charger la relation role si elle n'est pas déjà chargée
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        // Déterminer le rôle de l'utilisateur
        $userRole = null;
        if ($user->role && is_object($user->role) && isset($user->role->name)) {
            $userRole = $user->role->name;
        } elseif (isset($user->role) && is_string($user->role)) {
            // Fallback pour l'ancien système
            $userRole = $user->role;
        }

        \Log::info('🔐 CheckRole - Utilisateur: ' . $user->email . ', Rôle: ' . ($userRole ?? 'NULL') . ', Rôles autorisés: ' . implode(',', $roles));

        if (!$userRole || !in_array($userRole, $roles)) {
            \Log::warning('❌ Accès refusé - Rôle: ' . ($userRole ?? 'NULL') . ' non autorisé pour: ' . implode(',', $roles));
            abort(403, 'Vous n\'avez pas le droit d\'accéder à cette ressource.');
        }

        \Log::info('✅ Accès autorisé pour rôle: ' . $userRole);
        // Autorisé, continue la requête
        return $next($request);
    }
}
