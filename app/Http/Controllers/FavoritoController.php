<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use Illuminate\Http\Request;

class FavoritoController extends Controller
{public function index()
    {
    return Favorito::all();
}

    public function store(Request $request)
    {
        // Verificar si el usuario ya tiene un producto con el mismo código
        $existingProduct = Favorito::where('user_id', $request->user_id)->where('ref_api', $request->ref_api)->first();
        if ($existingProduct) {
            // Si el usuario ya tiene un producto con el mismo código, devuelve un error
            return response()->json(['mensaje' => 'Personaje ya se agregado a su lista'], 409);
        } else {
            $favorito = new Favorito();
            $favorito->ref_api = $request->ref_api;
            $favorito->user_id = $request->user_id;
            $favorito->save();
            return response()->json(['mensaje' => 'Personaje agregado'], 200);
        }

    }
/*

$favorite = Favorito::create($request->all());
return response()->json($favorite, 201); */

    public function show(Favorito $favorite)
    {
        return $favorite;
    }

    public function update(Request $request, Favorito $favorite)
    {
        $favorite->update($request->all());
        return response()->json($favorite, 200);
    }

    public function delete($userId, $id)
    {
        $favorite = Favorito::where('user_id', $userId)
            ->where('ref_api', $id)
            ->first();
        if ($favorite) {
            $favorite->delete();
            return response()->json("Eliminado con exito", 200);
        }
        return response()->json("No encontrado", 402);
    }

    public function listaFavoritosUser($id)
    {
        $favoritos = Favorito::where('user_id', $id)->get();
        return response()->json($favoritos);
    }

    public function favoritoUser($user_id, $id)
    {
        $favorite = Favorito::where('user_id', $user_id)
            ->where('ref_api', $id)
            ->first();

        if ($favorite) {
            return response()->json(true, 200);
        }
        return response()->json(false, 200);
    }

}
