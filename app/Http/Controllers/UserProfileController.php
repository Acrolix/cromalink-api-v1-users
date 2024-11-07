<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserProfile;
use Illuminate\Http\Request;


class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = UserProfile::whereHas('user', function ($query) {
            $query->where('active', true);
        })->paginate(10);
        if (!$users) return response()->json(['message' => 'No se encontraron usuarios'], 404);
        return response()->json($users);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = UserProfile::where('user_id', $id)->whereHas('user', function ($query) {
            $query->where('active', true);
        })->first();
        if (!$user) return response()->json(['message' => 'No se encontró el usuario'], 404);
        return response()->json($user);
    }

    function me()
    {
        $user = UserProfile::find(request()->user()->id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProfileUpdateRequest $request, $id)
    {
        try {
            $user = UserProfile::find($id);
            if (!$user) return response()->json(['message' => 'No se encontró el usuario'], 404);

            if ($request->hasFile('avatar')) {
                $image = ImageHelper::resize($request->file('avatar'));
                $user->avatar = base64_encode($image->toJpeg(30));
            }

            $request->first_name && $user->first_name = $request->first_name;
            $request->last_name && $user->last_name = $request->last_name;
            $request->biography && $user->biography = $request->biography;

            $user->save();
            return response()->json(['message' => 'Perfil actualizado correctamente'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
            return response()->json(['message' => 'Error al actualizar el perfil del usuario'], 500);
        }
    }

    public function avatar($id)
    {
        $user = UserProfile::find($id);
        if (!$user) return response()->json(['message' => 'No se encontró el usuario'], 404);
        $image = "data:image/jpeg;base64," . $user->avatar;
        return response($image)->withHeaders([
            'Content-disposition' => 'attachment; filename=' . $user->first_name . '.jpg',
            'Access-Control-Expose-Headers' => 'Content-Disposition',
            'Content-Type' => 'image/jpeg',
          ])->send();

    }

    public function disable($id)
    {
        try {
            $user = UserProfile::find($id);
            if (!$user) return response()->json(['message' => 'No se encontró el usuario'], 404);
            if (!$user->isActive()) return response()->json(['message' => 'El usuario no está activo'], 403);

            $user->user->active = false;
            $user->user->save();

            return response()->json(['message' => 'Perfil desactivado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al desactivar el perfil del usuario'], 500);
        }
    }
}
