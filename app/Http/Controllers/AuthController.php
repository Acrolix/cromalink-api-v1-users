<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials))
            return response()->json(['error' => 'Credenciales inválidas'], 401);

        if (!UserProfile::where('user_id', Auth::user()->id)->exists())
            return response()->json(['error' => 'Perfil Inválido, acceso denegado'], 403);

        $response = $this->oAuthToken($credentials);

        if ($response->failed())
            return response()->json(['error' => 'Error de autenticación'], 500);

        Auth::user()->save_last_login();
        return response()->json($response->json());
    }


    public function logout(Request $request)
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

        return response()->json($response->json(), 200);
    }

    protected function oAuthToken($credentials)
    {
        return Http::asForm()->post(env('OAUTH_URL') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
            'username' => $credentials['email'],
            'password' => $credentials['password'],
            'scope' => '',
        ]);
    }

    protected function oAuthRefreshToken($refreshToken)
    {
        return Http::asForm()->post(env('OAUTH_URL') . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
            'scope' => '',
        ]);
    }

    protected function revokeAccessAndRefreshTokens($tokenId) {
        $tokenRepository = app('Laravel\Passport\TokenRepository');
        $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');
        $tokenRepository->revokeAccessToken($tokenId);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);
    }
}
