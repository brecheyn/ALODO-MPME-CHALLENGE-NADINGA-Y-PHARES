<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\beta\QuestionController;
use App\Http\Controllers\Api\beta\DimensionController;
use App\Http\Controllers\Api\beta\DiagnosticController;
use App\Http\Controllers\Api\beta\AnswerController;
use App\Http\Controllers\Api\beta\ResultController;
use App\Http\Controllers\Api\beta\StatController;
use App\Http\Controllers\Api\beta\AdminController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ─────────────────────────────────────────────────────────────
// API ALODO MPME · Diagnostic — préfixe /api/beta
// Toutes les URLs ci-dessous deviennent /api/beta/...
// ─────────────────────────────────────────────────────────────
Route::prefix('beta')->group(function () {

    // Écran "Question 3/8" : le questionnaire complet (8 questions + options)
    Route::get('questions', [QuestionController::class, 'index']);

    // Les 8 dimensions (écran de résultat)
    Route::get('dimensions', [DimensionController::class, 'index']);

    // Bouton "Commencer le diagnostic" → crée la session, renvoie un token
    Route::post('diagnostics', [DiagnosticController::class, 'store']);

    // Bouton "Suivant" → enregistre la réponse (idempotent)
    Route::post('diagnostics/{token}/answers', [AnswerController::class, 'store']);

    // Bouton "Voir mon résultat" → déclenche le moteur de scoring
    Route::post('diagnostics/{token}/complete', [DiagnosticController::class, 'complete']);

    // Écran de résultat (score, niveau, force, priorité, recommandations)
    Route::get('results/{token}', [ResultController::class, 'show']);

    // Preuve sociale : "Déjà utilisé par X MPME"
    Route::get('stats/public', [StatController::class, 'publicStats']);

    // ─────────── BACK-OFFICE ADMIN (protégé par token Sanctum) ───────────
    Route::prefix('admin')->group(function () {
        Route::post('login', [AdminController::class, 'login']);  // public : obtenir un token
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [AdminController::class, 'me']);         // vérifier le token
            Route::post('logout', [AdminController::class, 'logout']); // révoquer le token
            Route::get('stats', [AdminController::class, 'stats']);   // statistiques globales
            // Les routes CRUD questions/options/dimensions + stats arriveront ici
        });
    });
});


