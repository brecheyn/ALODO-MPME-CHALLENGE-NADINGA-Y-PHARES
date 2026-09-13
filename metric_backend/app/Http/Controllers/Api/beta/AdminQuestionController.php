<?php

namespace App\Http\Controllers\Api\beta;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD back-office : questions + options.
 * Toutes les méthodes sont derrière auth:sanctum.
 */
class AdminQuestionController extends Controller
{
    // GET /admin/questions — tout le questionnaire éditable
    public function index(): JsonResponse
    {
        $questions = Question::with('options')->orderBy('display_order')->get();

        return response()->json(['data' => $questions]);
    }

    // POST /admin/questions — ajouter une question (le quiz public s'adapte seul)
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'dimension_id'   => ['required', 'integer', 'exists:dimensions,id'],
            'text'           => ['required', 'string', 'max:500'],
            'display_order'  => ['required', 'integer', 'min:1'],
        ]);

        $question = Question::create($data);

        return response()->json(['data' => $question->load('options')], 201);
    }

    // PUT /admin/questions/{id} — modifier texte / ordre
    public function update(Request $request, Question $question): JsonResponse
    {
        $data = $request->validate([
            'text'          => ['sometimes', 'string', 'max:500'],
            'display_order' => ['sometimes', 'integer', 'min:1'],
        ]);

        $question->update($data);

        return response()->json(['data' => $question->load('options')]);
    }

    // DELETE /admin/questions/{id} — refusé si des réponses existent (intégrité des historiques)
    public function destroy(Question $question): JsonResponse
    {
        $used = Answer::where('question_id', $question->id)->exists();

        if ($used) {
            return response()->json([
                'message' => 'Impossible : cette question a déjà des réponses enregistrées. Désactivez-la plutôt.',
            ], 409); // 409 = Conflict
        }

        $question->options()->delete();
        $question->delete();

        return response()->json(['message' => 'Question supprimée.']);
    }

    // PUT /admin/options/{id} — texte / score / ordre. Score verrouillé si l'option a servi.
    public function updateOption(Request $request, QuestionOption $option): JsonResponse
    {
        $data = $request->validate([
            'text'          => ['sometimes', 'string', 'max:500'],
            'score'         => ['sometimes', 'integer', 'min:0', 'max:3'],
            'display_order' => ['sometimes', 'integer', 'min:1'],
        ]);

        if (isset($data['score']) && $data['score'] != $option->score) {
            $used = Answer::where('option_id', $option->id)->exists();
            if ($used) {
                return response()->json([
                    'message' => 'Score verrouillé : cette option a déjà été choisie dans des diagnostics.',
                ], 409);
            }
        }

        $option->update($data);

        return response()->json(['data' => $option]);
    }

    // POST /admin/options — ajouter une option à une question
    public function storeOption(Request $request): JsonResponse
    {
        $data = $request->validate([
            'question_id'   => ['required', 'integer', 'exists:questions,id'],
            'text'          => ['required', 'string', 'max:500'],
            'score'         => ['required', 'integer', 'min:0', 'max:3'],
            'display_order' => ['required', 'integer', 'min:1'],
        ]);

        $option = QuestionOption::create($data);

        return response()->json(['data' => $option], 201);
    }
}
