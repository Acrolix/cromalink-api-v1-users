<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * Login the user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');

            $user = User::where('email', $credentials['email'])->first();

            if (!$user->user_profile->exists() || !$user->active)
                return response()->json(['error' => 'Perfil Inválido, acceso denegado'], 403);

            $response = $this->oAuthToken($credentials);

            $user->save_last_login();

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el Servidor'], 500);
        }
    }

    /**
     * Log out the user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()
            ->tokens
            ->each(function ($token, $key) {
                $this->revokeAccessAndRefreshTokens($token->id);
            });
        return response()->json('Logged out successfully', 200);
    }


    /**
     * Refresh the token
     *
     * @param RefreshTokenRequest $request
     * @return JsonResponse
     */
    public function refreshToken(RefreshTokenRequest $request): JsonResponse
    {
        $response = $this->oAuthRefreshToken($request->refresh_token);
        return response()->json($response, 200);
    }

    protected function oAuthToken($credentials)
    {
        return Http::asForm()->post(config('app.services.auth_api.url') . '/token', [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
            'username' => $credentials['email'],
            'password' => $credentials['password'],
            'scope' => ''
        ])->json();
    }

    protected function oAuthRefreshToken($refreshToken)
    {
        return Http::asForm()->post(config('app.services.auth_api.url') . '/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
            'scope' => '',
        ])->json();
    }

    protected function revokeAccessAndRefreshTokens($tokenId)
    {
        $tokenRepository = app('Laravel\Passport\TokenRepository');
        $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');
        $tokenRepository->revokeAccessToken($tokenId);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);
    }
}
