<?php

namespace App\Http\Controllers\Api\beta;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * POST /api/beta/admin/login
     * Vérifie email + mot de passe, renvoie un token Sanctum.
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::where('email', $data['email'])->first();

        // Même message dans les 2 cas : on ne révèle jamais si l'email existe
        if (! $admin || ! Hash::check($data['password'], $admin->password)) {
            return response()->json(['message' => 'Identifiants incorrects.'], 401);
        }

        $token = $admin->createToken('admin-session')->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'admin' => ['name' => $admin->name, 'email' => $admin->email],
            ],
        ]);
    }

    /**
     * GET /api/beta/admin/me — protégé par auth:sanctum
     * Confirme que le token est valide et renvoie le profil.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => ['admin' => $request->user()]]);
    }

    /**
     * POST /api/beta/admin/logout — protégé par auth:sanctum
     * Révoque le token utilisé (déconnexion de CETTE session uniquement).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté.']);
    }

    /**
     * GET /api/beta/admin/stats — protégé par auth:sanctum
     * Agrège tous les résultats :
     * - score moyen global + répartition par niveau
     * - par dimension : score moyen + % de MPME en difficulté (< 50)
     * - classement des difficultés les plus rencontrées
     */
    public function stats(): JsonResponse
    {
        $results = \App\Models\Result::with('level')->get();

        if ($results->isEmpty()) {
            return response()->json(['data' => [
                'global'           => ['diagnostics' => 0, 'avg_score' => 0, 'levels' => []],
                'dimensions'       => [],
                'top_difficulties' => [],
            ]]);
        }

        // Répartition par niveau de maturité
        $levels = [];
        foreach ($results as $r) {
            $key = (string) $r->level->level_number;
            $levels[$key] = ($levels[$key] ?? 0) + 1;
        }

        // Agrégation par dimension depuis le JSON "details" de chaque résultat
        $dims = [];
        foreach ($results as $r) {
            foreach ($r->details ?? [] as $code => $d) {
                if (! isset($dims[$code])) {
                    $dims[$code] = ['label' => $d['label'], 'total' => 0, 'count' => 0, 'weak' => 0];
                }
                $dims[$code]['total'] += $d['score'];
                $dims[$code]['count']++;
                if ($d['score'] < 50) $dims[$code]['weak']++;   // < 50% = en difficulté
            }
        }

        $dimensions = [];
        foreach ($dims as $code => $d) {
            $dimensions[] = [
                'code'      => $code,
                'label'     => $d['label'],
                'avg_score' => (int) round($d['total'] / $d['count']),
                'weak_pct'  => (int) round($d['weak'] / $d['count'] * 100),
            ];
        }

        // Classement : difficultés les plus rencontrées d'abord
        usort($dimensions, fn ($a, $b) => $b['weak_pct'] <=> $a['weak_pct']);

        return response()->json(['data' => [
            'global' => [
                'diagnostics' => $results->count(),
                'avg_score'   => (int) round($results->avg('global_score')),
                'levels'      => $levels,
            ],
            'dimensions'       => $dimensions,
            'top_difficulties' => array_map(
                fn ($d) => ['label' => $d['label'], 'weak_pct' => $d['weak_pct']],
                array_slice($dimensions, 0, 3)
            ),
        ]]);
    }
}
