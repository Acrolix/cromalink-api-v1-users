<?php

namespace App\helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthHelper
{
    public static function oAuthToken($credentials)
    {
        $response = Http::asForm()->post(config('app.services.auth_api.url') . '/token', [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
            'username' => $credentials['email'],
            'password' => $credentials['password'],
            'scope' => ''
        ]);
        if ($response->serverError())
            throw new \Exception('Error en el servidor');

        if (isset($response['error']) && $response['error'] == 'invalid_client')
            throw new \Exception('invalid_client');
        return $response->json();
    }

    public static function oAuthRefreshToken($refreshToken)
    {
        $response = Http::asForm()->post(config('app.services.auth_api.url') . '/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
            'scope' => '',
        ]);
        if ($response->serverError())
            throw new \Exception('Error en el servidor');
        return $response->json();
    }

    public static function revokeAccessAndRefreshTokens(Request $request)
    {
        $tokenId = preg_match('/^[^\.]+\.([^\.]+)\./', $request->bearerToken(), $matches) ? json_decode(base64_decode($matches[1]))->jti : null;

        $response = Http::asForm()->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $request->bearerToken(),
        ])->delete(config('app.services.auth_api.url') . "/tokens/$tokenId");
        
        if ($response->serverError())
            throw new \Exception($response->serverError());

        return $response->json();
    }

    public static function checkUserProfile($user)
    {
        if (!$user || !$user->profile?->exists() || !$user->active)
            return false;
        return true;
    }
}
