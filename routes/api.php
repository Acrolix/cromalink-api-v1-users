<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;


Route::middleware('auth:api')->get('/hello', function (Request $request) {
    return response()->json([
        'message' => 'Hello World! -> API Users v1',
    ]);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh_token', [AuthController::class, 'refreshToken']);
});
