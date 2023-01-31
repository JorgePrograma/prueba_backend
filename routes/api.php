<?php

use App\Http\Controllers\SanctumAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("registro", [SanctumAuthController::class, 'registro']);
Route::post("login", [SanctumAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post("perfil", [SanctumAuthController::class, 'perfil']);
    Route::post("logout", [SanctumAuthController::class, 'logout']);
});
