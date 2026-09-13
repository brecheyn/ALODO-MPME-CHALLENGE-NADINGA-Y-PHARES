<?php

namespace App\Http\Controllers\Api\beta;

use App\Http\Controllers\Controller;
use App\Models\Diagnostic;
use App\Models\Recommendation;
use Illuminate\Http\JsonResponse;

class ResultController extends Controller
{
    /**
     * GET /api/beta/results/{token}
     * L'écran de résultat : score global, niveau, force, priorité,
     * scores par dimension + les recommandations actionnables pour
     * la dimension prioritaire au niveau atteint.
     */
    public function show(string $token): JsonResponse
    {
        $diagnostic = Diagnostic::where('token', $token)
            ->with('result.level')
            ->first();

        if (! $diagnostic || ! $diagnostic->result) {
            return response()->json([
                'message' => 'Aucun résultat pour ce token. Terminez d\'abord le diagnostic.',
            ], 404);
        }

        $result = $diagnostic->result;

        // Recommandations pour la dimension prioritaire AU niveau du diagnostic.
        // On retrouve la dimension prioritaire via son label dans details.
        $priorityLabel = $result->priority_text;
        $priorityCode = null;
        foreach ($result->details ?? [] as $code => $info) {
            if (($info['label'] ?? null) === $priorityLabel) {
                $priorityCode = $code;
                break;
            }
        }

        $recommendations = [];
        if ($priorityCode && $diagnostic->level_id) {
            $dimension = \App\Models\Dimension::where('code', $priorityCode)->first();
            if ($dimension) {
                $recommendations = Recommendation::where('dimension_id', $dimension->id)
                    ->where('level_id', $diagnostic->level_id)
                    ->get(['title', 'action_text']);
            }
        }

        return response()->json([
            'data' => [
                'global_score'    => $result->global_score,
                'level'           => [
                    'number'        => $result->level->level_number,
                    'name'          => $result->level->level_name,
                    'label'         => $result->level->label,
                    'encouragement' => $result->level->encouragement,
                ],
                'strength'        => ['label' => $result->strength_text], // "À préserver"
                'priority'        => ['label' => $result->priority_text], // "À structurer"
                'details'         => $result->details,                    // ex: {"tresorerie": {"label": "Trésorerie", "score": 48}}
                'recommendations' => $recommendations,
            ],
        ]);
    }
}
