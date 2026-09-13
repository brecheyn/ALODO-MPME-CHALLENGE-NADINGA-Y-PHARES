<?php

namespace App\Http\Controllers\Api\beta;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Diagnostic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * POST /api/beta/diagnostics/{token}/answers
     * Enregistre (ou met à jour) la réponse à une question.
     * Correspond au bouton "Suivant" de l'interface.
     */
    public function store(Request $request, string $token): JsonResponse
    {
        // 1. Validation des entrées : les règles sont vérifiées AVANT tout traitement.
        //    En cas d'échec, Laravel renvoie automatiquement un 422 avec les erreurs.
        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
            'option_id'   => ['required', 'integer', 'exists:question_options,id'],
        ]);

        // 2. Le diagnostic doit exister (via son token anonyme)
        $diagnostic = Diagnostic::where('token', $token)->first();
        if (! $diagnostic) {
            return response()->json(['message' => 'Diagnostic introuvable.'], 404);
        }

        // 3. Sécurité métier : l'option choisie doit appartenir à la question répondue
        //    (sinon un client malveillant pourrait "tricher" en combinant n'importe quoi)
        $option = QuestionOption::where('id', $validated['option_id'])
            ->where('question_id', $validated['question_id'])
            ->first();
        if (! $option) {
            return response()->json([
                'message' => 'Cette option n\'appartient pas à cette question.',
            ], 422);
        }

        // 4. Enregistrement idempotent : si l'utilisateur change d'avis et
        //    répond à nouveau à la même question, on MET À JOUR au lieu de dupliquer.
        Answer::updateOrCreate(
            [
                'diagnostic_id' => $diagnostic->id,
                'question_id'   => $validated['question_id'],
            ],
            ['option_id' => $validated['option_id']]
        );

        // 5. On renvoie la progression (le "Question 3/8" du frontend)
        $answeredCount = $diagnostic->answers()->count();
        $totalQuestions = Question::count();

        return response()->json([
            'data' => [
                'answered'  => $answeredCount,
                'total'     => $totalQuestions,
                'completed' => $answeredCount >= $totalQuestions,
            ],
        ], 201);
    }
}
