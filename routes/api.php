<?php

use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\SanctumAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("registro", [SanctumAuthController::class, 'registro']);
Route::post("loguin", [SanctumAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put("update/{id}", [SanctumAuthController::class, 'update']);
    Route::post("delete/{id}", [SanctumAuthController::class, 'delete']);
    Route::post("logout", [SanctumAuthController::class, 'logout']);

    Route::get("user/favoritos/{id}", [FavoritoController::class, 'listaFavoritosUser']);
    Route::post("favorito", [FavoritoController::class, 'store']);
    Route::delete("favorito/{user_id}/{id}", [FavoritoController::class, 'delete']);
    Route::get("favorito/{user_id}/{ref_api}", [FavoritoController::class, 'favoritoUser']);

});
