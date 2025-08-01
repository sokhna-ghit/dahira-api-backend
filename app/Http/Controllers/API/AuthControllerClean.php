<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            \Log::info('🔐 Connexion réussie pour: ' . $user->email . ' (ID: ' . $user->id . ')');
            
            // Charger la relation role si elle existe
            if (method_exists($user, 'role')) {
                $user->load('role');
                \Log::info('🎭 Relation role chargée');
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            // Déterminer le rôle de façon robuste
            $userRole = null;
            if ($user->role && is_object($user->role) && isset($user->role->name)) {
                $userRole = $user->role->name;
                \Log::info('✅ Rôle trouvé via relation: ' . $userRole);
            } elseif (isset($user->role) && is_string($user->role)) {
                $userRole = $user->role;
                \Log::info('✅ Rôle trouvé via colonne string: ' . $userRole);
            } elseif (isset($user->role_id)) {
                // Si vous avez une colonne role_id, récupérer le nom du rôle
                \Log::info('🔍 Recherche du rôle via role_id: ' . $user->role_id);
                $role = \App\Models\Role::find($user->role_id);
                $userRole = $role ? $role->name : 'membre';
                \Log::info('✅ Rôle trouvé via role_id: ' . $userRole);
            } else {
                $userRole = 'membre'; // Valeur par défaut
                \Log::warning('⚠️ Aucun rôle trouvé, utilisation du défaut: ' . $userRole);
            }

            \Log::info('📤 Réponse envoyée avec rôle: ' . $userRole);

            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $userRole,
                ],
                'success' => true,
                'message' => 'Connexion réussie'
            ], 200);
        }

        return response()->json([
            'message' => 'Identifiants invalides',
            'success' => false
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie',
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,président,membre,trésorier',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 201);
    }
}
