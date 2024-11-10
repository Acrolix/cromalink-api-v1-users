<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\UserProfileController;

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
    Route::middleware('oauth')->post('/logout', [AuthController::class, 'logout']);
    Route::middleware('oauth')->get('/verify', [AuthController::class, 'verify']);
});

Route::group(['prefix' => 'users', 'middleware' => 'oauth'], function () {
    Route::get('/', [UserProfileController::class, 'index']);
    Route::get('/me', [UserProfileController::class, 'me']);
    Route::get('/{id}', [UserProfileController::class, 'show']);
    Route::put('/', [UserProfileController::class, 'update']);
    Route::put('/{id}/disable', [UserProfileController::class, 'disable']);
    Route::get('/{id}/avatar', [UserProfileController::class, 'avatar']);
    
});

Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{id}', [CountryController::class, 'show']);

