<?php

namespace App\Http\Controllers\Api\beta;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(): JsonResponse
    {
     $questions = Question::query()
            ->with('options')               // eager loading : options chargées dans la même requête (anti "N+1")
            ->orderBy('display_order')
            ->get();

        return response()->json([
            'data' => $questions,
            ]);
        }
    }
