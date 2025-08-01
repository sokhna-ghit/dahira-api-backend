<?php   


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DahiraController;
use App\Http\Controllers\MembreController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\API\PaymentController as APIPaymentController;
use App\Http\Controllers\DashboardController;

// Auth 
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes publiques
Route::get('/public/dahiras', [DahiraController::class, 'index']); // Endpoint public pour les dahiras

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Déconnexion réussie.']);
});

// Routes Admin - Dashboard et statistiques
Route::middleware(['auth:sanctum', 'role:admin,super_admin,président'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/users', [AdminController::class, 'getUsers']);
    Route::get('/admin/dahiras', [AdminController::class, 'getDahiras']);
});

// Routes Super Admin - Gestion des dahiras et utilisateurs  
Route::middleware(['auth:sanctum', 'role:super_admin,admin'])->group(function () {
    // Gestion des dahiras
    Route::get('/dahiras', [DahiraController::class, 'index']);
    Route::post('/dahiras', [DahiraController::class, 'store']);
    Route::get('/dahiras/{id}', [DahiraController::class, 'show']);
    Route::put('/dahiras/{id}', [DahiraController::class, 'update']);
    Route::delete('/dahiras/{id}', [DahiraController::class, 'destroy']);
    
    // Gestion des utilisateurs
    Route::get('/users', [AuthController::class, 'getAllUsers']);
    Route::post('/users', [AuthController::class, 'createUser']);
    Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);
    
    // Statistiques globales
    Route::get('/stats', [DashboardController::class, 'globalStats']);
    Route::get('/admin/stats', [DashboardController::class, 'globalStats']); // Alias pour compatibilité
});


// Routes protégées - Membres (président et super_admin)
Route::middleware(['auth:sanctum', 'role:super_admin,président'])->group(function () {
    Route::apiResource('membres', \App\Http\Controllers\MembreController::class);
    Route::get('/membres/statistiques', [\App\Http\Controllers\MembreController::class, 'statistiques']);
    Route::get('/membres/rechercher', [\App\Http\Controllers\MembreController::class, 'rechercher']);
});

// Routes de consultation pour tous les rôles connectés
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/membres', [\App\Http\Controllers\MembreController::class, 'index']);
    Route::get('/membres/{membre}', [\App\Http\Controllers\MembreController::class, 'show']);
});


Route::middleware(['auth:sanctum', 'role:admin,trésorier'])->group(function () {
    Route::apiResource('cotisations', CotisationController::class);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('cotisations', \App\Http\Controllers\API\CotisationController::class);
});

Route::post('/paiement/simule', [PaymentController::class, 'simulatePayment']);

// Routes Paiements - API intégrée
Route::middleware(['auth:sanctum'])->group(function () {
    // Obtenir les opérateurs de paiement supportés (PayDunya)
    Route::get('/paiements/operateurs', [APIPaymentController::class, 'obtenirOperateurs']);
    
    // Initier un paiement de cotisation
    Route::post('/paiements/cotisation', [APIPaymentController::class, 'initierPaiementCotisation']);
    
    // Vérifier le statut d'un paiement
    Route::get('/paiements/statut/{reference}', [APIPaymentController::class, 'verifierStatutPaiement']);
    
    // Historique des paiements d'un membre
    Route::get('/paiements/historique/{membreId}', [APIPaymentController::class, 'historiquePaiements']);
    
    // Générer un reçu PDF
    Route::get('/paiements/recu/{paiementId}', [APIPaymentController::class, 'genererRecu']);
});

// Routes Paiements - Administration (Trésorier/Admin)
Route::middleware(['auth:sanctum', 'role:admin,trésorier,président'])->group(function () {
    // Statistiques des paiements
    Route::get('/paiements/statistiques', [APIPaymentController::class, 'statistiquesPaiements']);
});

// Routes PayDunya - Système de paiement unifié
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/paydunya/operateurs', [PaymentController::class, 'obtenirOperateurs']);
    Route::post('/paydunya/paiement', [PaymentController::class, 'traiterPaiementPaydunya']);
    Route::get('/paydunya/statut/{invoiceToken}', [PaymentController::class, 'verifierStatutPaydunya']);
    Route::get('/paydunya/historique/{membreId}', [PaymentController::class, 'obtenirHistoriquePaydunya']);
});

// Optionnel : route pour voir l'utilisateur connecté
Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
