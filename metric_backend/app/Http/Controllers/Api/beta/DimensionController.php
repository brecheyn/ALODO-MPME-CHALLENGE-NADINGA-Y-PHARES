<?php

namespace App\Http\Controllers\Api\beta;

use App\Http\Controllers\Controller;
use App\Models\Dimension;
use Illuminate\Http\JsonResponse;

class DimensionController extends Controller
{
    public function index(): JsonResponse
    {
        $dimensions = Dimension::query()
            ->orderBy('display_order')
            ->get();

        return response()->json([
            'data' => $dimensions,
        ]);
    }
}
