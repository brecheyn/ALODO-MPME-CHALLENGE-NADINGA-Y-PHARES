<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app'      => 'ALODO MPME · Diagnostic',
        'tagline'  => 'Structurez votre entreprise avant de chercher à la financer',
        'audience' => 'Dirigeants de MPME — Afrique de l\'Ouest',
        'status'   => 'operational',
        'version'  => 'beta',
        'offers'   => 'Diagnostic gratuit · sans inscription · 4 minutes · 8 dimensions',
        'endpoints' => [
            'questions'    => 'GET  /api/beta/questions',                       // écran Question X/8
            'diagnostics'  => 'POST /api/beta/diagnostics',                     // bouton "Commencer le diagnostic"
            'answers'      => 'POST /api/beta/diagnostics/{token}/answers',     // bouton "Suivant"
            'complete'     => 'POST /api/beta/diagnostics/{token}/complete',    // bouton "Voir mon résultat"
            'results'      => 'GET  /api/beta/results/{token}',                 // écran de résultat (score, niveau, force, priorité)
            'stats'        => 'GET  /api/beta/stats/public',                    // preuve sociale : "Déjà utilisé par X MPME"
        ],
    ]);
});
