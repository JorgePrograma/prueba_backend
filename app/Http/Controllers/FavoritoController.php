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

    public function delete(Favorito $favorite)
    {
        $favorite->delete();
        return response()->json(null, 204);
    }

    public function listaFavoritosUser($id)
    {
        $favoritos = Favorito::where('user_id', $id)->get();
        return response()->json($favoritos);
    }
}
