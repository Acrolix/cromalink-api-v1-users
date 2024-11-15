<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $users = UserProfile::whereHas('user', function ($query) {
                $query->where('active', true);
            })->paginate(20);

            if (!$users) return response()->json(['message' => 'No se encontraron usuarios'], 404);

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el Servidor'], 500);
        }
    }

    /**
     * Display the specified resource.
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $user = UserProfile::where('user_id', $id)->whereHas('user', function ($query) {
                $query->where('active', true);
            })->first();
            if (!$user) return response()->json(['message' => 'No se encontró el usuario'], 404);
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el Servidor'], 500);
        }
    }

    function me(): JsonResponse
    {
        try {
            $user = UserProfile::find(request()->user()->id);
            if (!$user) return response()->json(['message' => 'No se encontró el usuario'], 404);
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el Servidor'], 500);
        }
    }

    /**
     * Update the user profile
     *
     * @param ProfileUpdateRequest $request
     * @return JsonResponse
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = UserProfile::find($request->user()->id);
            if (!$user) return response()->json(['message' => 'No se encontró el usuario'], 404);

            if ($request->hasFile('avatar')) {
                $image = ImageHelper::saveAvatar($request->file('avatar'));
                $user->avatar = $image;
            }

            $request->first_name && $user->first_name = $request->first_name;
            $request->last_name && $user->last_name = $request->last_name;
            $request->biography && $user->biography = $request->biography;

            $user->save();
            DB::commit();
            return response()->json(['message' => 'Perfil actualizado correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error en el servidor'], 500);
        }
    }

    /**
     * Disable the user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function disable(Request $request): JsonResponse
    {
        try {
            $user = UserProfile::find($request->user()->id);
            if (!$user) return response()->json(['message' => 'No se encontró el usuario'], 404);
            if (!$user->isActive()) return response()->json(['message' => 'El usuario no está activo'], 403);

            $user->active = false;
            $user->save();

            return response()->json(['message' => 'Perfil desactivado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al desactivar el perfil del usuario'], 500);
        }
    }
}
