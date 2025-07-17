<?php   


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DahiraController;
use App\Http\Controllers\API\MembreController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;

// Auth 
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Déconnexion réussie.']);
});

// Routes protégées - Dahiras (admin uniquement)

Route::middleware('auth:sanctum')->get('/admin/dashboard', [DashboardController::class, 'adminDashboard']);


// Routes protégées - Membres (membre + président)
Route::middleware(['auth:sanctum', 'role:membre,président'])->group(function () {
    Route::apiResource('membres', MembreController::class);

});

Route::middleware(['auth:sanctum', 'role:admin,membre'])->get('/membres', [MembreController::class, 'index']);


Route::middleware(['auth:sanctum', 'role:admin,trésorier'])->group(function () {
    Route::apiResource('cotisations', CotisationController::class);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('cotisations', \App\Http\Controllers\API\CotisationController::class);
});

Route::post('/paiement/simule', [PaymentController::class, 'simulatePayment']);




// Optionnel : route pour voir l'utilisateur connecté
Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
