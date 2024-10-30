<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware('oauth')->get('/hello', function (Request $request) {
    return response()->json([
        'message' => 'Hello World! -> API Users v1',
        'user' => $request->user()
    ]);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('oauth')->post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh_token', [AuthController::class, 'refreshToken']);
});
