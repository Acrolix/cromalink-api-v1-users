<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

Route::middleware('oauth')->get('/hello', function (Request $request) {
    return response()->json([
        'message' => 'Hello World! -> API Users v1',
        'user' => $request->user()
    ]);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/refresh_token', [AuthController::class, 'refreshToken']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('oauth');
    Route::get('/verify', [AuthController::class, 'verify'])->middleware('oauth');
});

Route::group(['prefix' => 'users', 'middleware' => 'oauth'], function () {
    Route::get('/', [UserProfileController::class, 'index']);
    Route::get('/me', [UserProfileController::class, 'me']);
    Route::get('/{id}', [UserProfileController::class, 'show']);
    Route::put('/', [UserProfileController::class, 'update']);
    Route::delete('/disable', [UserProfileController::class, 'disable']);
    
});

Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{id}', [CountryController::class, 'show']);

Route::group(['prefix' => 'media'], function () {
    Route::get('avatars/{filename}', function ($filename) {
        $path = storage_path("app/public/avatars/$filename");

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    });
});

