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
}
