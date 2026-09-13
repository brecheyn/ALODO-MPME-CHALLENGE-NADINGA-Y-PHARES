<?php

namespace App\Http\Controllers\Api\beta;

use App\Http\Controllers\Controller;
use App\Models\Diagnostic;
use Illuminate\Http\JsonResponse;

class StatController extends Controller
{
    /**
     * GET /api/beta/stats/public
     * La preuve sociale de la page d'accueil : "Déjà utilisé par X MPME".
     * On met le résultat en cache 5 minutes pour ne pas marteler la BDD
     * à chaque visite de la page d'accueil (LOAD conteneur léger).
     */
    public function publicStats(): JsonResponse
    {
        $count = cache()->remember(
            'stats.diagnostics_completed',   // clé de cache
            now()->addMinutes(5),            // durée de vie
            fn () => Diagnostic::where('status', 'completed')->count(),
        );

        return response()->json([
            'data' => [
                'diagnostics_completed' => $count,
            ],
        ]);
    }
}
