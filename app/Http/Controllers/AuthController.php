<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AuthHelper;

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

            $user = User::where('email', $credentials['email'])->first() or null;

            if (!AuthHelper::checkUserProfile($user))
                return response()->json(['message' => 'Usuario y/o contraseña inválida'], 401);

            $response = AuthHelper::oAuthToken($credentials);

            if (isset($response['error']))
                return response()->json(['message' => 'Usuario y/o contraseña inválida'], 401);

            $user->save_last_login();

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el Servidor'], 500);
        }
    }

    /**
     * Register a new user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $userData = [
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'active' => true,
                'email_verified_at' => now()
            ];

            $userProfileData = [
                'username' => $request->username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'birth_date' => $request->birth_date,
                'country_code' => $request->country_code,
            ];

            $user = User::create($userData);
            if (!$user) throw new \Exception("Error al registrar el usuario");

            if (!$user->profile()->create($userProfileData))
                throw new \Exception("Error al registrar el perfil del usuario");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Se produjo un error en el servidor'], 500);
        }

        return response()->json(['message' => 'Se Creo el usuario correctamente'], 201);
    }

    /**
     * Log out the user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $response = AuthHelper::revokeAccessAndRefreshTokens($request);
            if (!$response || isset($response["error"])) return response()->json(['message' => 'No se cerro la sesion correctamente'], 202);

            return response()->json(['message' => 'Se cerro la sesión correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el Servidor'], 500);
        }
    }


    /**
     * Refresh the token
     *
     * @param RefreshTokenRequest $request
     * @return JsonResponse
     */
    public function refreshToken(RefreshTokenRequest $request): JsonResponse
    {
        try {
            $response = AuthHelper::oAuthRefreshToken($request->refresh_token);
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el Servidor'], 500);
        }
    }

    /**
     * Verify the token
     *
     * @return JsonResponse
     */
    public function verify(): JsonResponse
    {
        return response()->json(['message' => 'OK']);
    }
}
