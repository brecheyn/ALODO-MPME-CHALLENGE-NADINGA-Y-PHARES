<?php

namespace App\Http\Controllers\Api\beta;

use App\Http\Controllers\Controller;
use App\Models\Diagnostic;
use App\Models\Level;
use App\Models\Result;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiagnosticController extends Controller
{
    /**
     * POST /api/beta/diagnostics
     * Crée une session de diagnostic anonyme. Renvoie un token unique
     * que le frontend conservera pour toute la durée du questionnaire.
     */
    public function store(): JsonResponse
    {
        $diagnostic = Diagnostic::create([
            'token'  => bin2hex(random_bytes(32)), // 64 caractères hexadécimaux, imprévisible
            'status' => 'in_progress',
        ]);

        return response()->json([
            'data' => [
                'token'  => $diagnostic->token,
                'status' => $diagnostic->status,
            ],
        ], 201); // 201 = Created
    }

    /**
     * POST /api/beta/diagnostics/{token}/complete
     * LE MOTEUR DE SCORING :
     * 1. récupère toutes les réponses du diagnostic (+ leurs options et dimensions)
     * 2. calcule le score global (somme des points / max possible × 100)
     * 3. calcule le score par dimension (en %)
     * 4. détermine le niveau de maturité, le point fort et la priorité
     * 5. enregistre le résultat (idempotent : un 2e appel renvoie le même résultat)
     */
    public function complete(string $token): JsonResponse
    {
        $diagnostic = Diagnostic::where('token', $token)->first();

        if (! $diagnostic) {
            return response()->json(['message' => 'Diagnostic introuvable.'], 404);
        }

        // Idempotence : si le résultat existe déjà, on le renvoie tel quel
        if ($diagnostic->result) {
            return response()->json(['data' => $this->formatResult($diagnostic->result)]);
        }

        // 1. On charge les réponses avec leurs options et les dimensions (eager loading)
        $answers = $diagnostic->answers()
            ->with('option', 'question.dimension')
            ->get();

        if ($answers->isEmpty()) {
            return response()->json(['message' => 'Aucune réponse enregistrée pour ce diagnostic.'], 422);
        }

        // 2. Agrégation par dimension
        $perDimension = [];
        $totalEarned  = 0;
        $totalMax     = 0;

        foreach ($answers as $answer) {
            $dimension = $answer->question->dimension;

            if (! isset($perDimension[$dimension->code])) {
                $perDimension[$dimension->code] = [
                    'label'  => $dimension->label,
                    'earned' => 0,
                    'max'    => 0,
                ];
            }
            $perDimension[$dimension->code]['earned'] += $answer->option->score;
            $perDimension[$dimension->code]['max']    += 3; // max de points par question
            $totalEarned += $answer->option->score;
            $totalMax    += 3;
        }

        // 3. Scores en pourcentage (arrondis)
        $details = [];
        $percentByCode = [];
        foreach ($perDimension as $code => $values) {
            $percent = (int) round(($values['earned'] / $values['max']) * 100);
            $details[$code] = ['label' => $values['label'], 'score' => $percent];
            $percentByCode[$code] = $percent;
        }

        $globalScore = (int) round(($totalEarned / $totalMax) * 100);

        // 4. Niveau de maturité = fourchette contenant le score global
        $level = Level::where('min_score', '<=', $globalScore)
            ->where('max_score', '>=', $globalScore)
            ->firstOrFail();

        // Point fort = dimension au meilleur score, Priorité = la plus faible
        $strengthCode = array_search(max($percentByCode), $percentByCode);
        $priorityCode = array_search(min($percentByCode), $percentByCode);

        // 5. Enregistrement + mise à jour du diagnostic
        $result = Result::create([
            'diagnostic_id' => $diagnostic->id,
            'global_score'  => $globalScore,
            'level_id'      => $level->id,
            'strength_text' => $details[$strengthCode]['label'],
            'priority_text' => $details[$priorityCode]['label'],
            'details'       => $details,
        ]);

        $diagnostic->update([
            'status'  => 'completed',
            'level_id' => $level->id,
        ]);

        return response()->json(['data' => $this->formatResult($result)], 201);
    }

    /**
     * Formatage commun de la réponse (single source of truth du format).
     */
    private function formatResult(Result $result): array
    {
        return [
            'global_score' => $result->global_score,
            'level'        => [
                'number'        => $result->level->level_number,
                'label'         => $result->level->label,
                'encouragement' => $result->level->encouragement,
            ],
            'strength' => ['label' => $result->strength_text], // le point fort "À préserver"
            'priority' => ['label' => $result->priority_text], // la priorité "À structurer"
            'details'  => $result->details,                     // scores par dimension
        ];
    }
}
