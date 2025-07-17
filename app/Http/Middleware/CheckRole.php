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

        if (!in_array($request->user()->role, $roles)) {
    abort(403, 'Vous n’avez pas le droit d’accéder à cette ressource.');
}


        // Autorisé, continue la requête
        return $next($request);
    }
}
