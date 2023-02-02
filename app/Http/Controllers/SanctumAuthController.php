<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class SanctumAuthController extends Controller
{
    public function registro(Request $request)
    {
        $request->validate([

        ]);
        // reglas de cada atributo
        $rules = array(
            'cedula' => 'required|numeric|unique:users',
            'nombres' => 'required',
            'apellidos' => 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed');

        // mensaje de errores
        $messajes = array(

            // validacion de requerido
            "cedula.required" => 'por favor ingrese numero de cedula',
            "nombres.required" => 'por favor ingrese nombres',
            "apellidos.required" => 'por favor ingrese apellidos',
            "email.required" => 'por favor ingrese email',
            "password.required" => 'por favor ingrese password',

            "email.unique" => 'no se puede usar este email',
            "email.email" => 'email ingresado no es valido',
            "password.confirmed" => 'por favor confirme su password',
            "cedula.unique" => 'no se puede usar este numero de cedula',

        );

        // captura si hay un error en los datos pasados por request
        $validator = FacadesValidator::make($request->all(), $rules, $messajes);

        // validacion si el dato es fallido muestra un 500 de lo contrario no pasa nada y se ejecuta la siguiente linea
        if ($validator->fails()) {
            $messajes = $validator->messages();
            return response()->json(['mensaje' => $messajes], 422);
        }

        $user = new User();
        $user->cedula = $request->cedula;
        $user->nombres = $request->nombres;
        $user->apellidos = $request->apellidos;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        $token = $user->createToken("auth_token")->plainTextToken;
        return response()->json(['mensaje' => 'usuario registrado con exito', 'acess_token' => $token, 'usuario' => $user], 200);
    }

    public function login(Request $request)
    {
        $request->validate([

        ]);
        // reglas de cada atributo
        $rules = array(
            'email' => 'required|email',
            'password' => 'required');

        // mensaje de errores
        $messajes = array(

            // validacion de requerido
            "email.required" => 'por favor ingrese email',
            "password.required" => 'por favor ingrese password',
            "email.email" => 'email ingresado no es valido',
        );

        // captura si hay un error en los datos pasados por request
        $validator = FacadesValidator::make($request->all(), $rules, $messajes);

        // validacion si el dato es fallido muestra un 500 de lo contrario no pasa nada y se ejecuta la siguiente linea
        if ($validator->fails()) {
            $messajes = $validator->messages();
            return response()->json(['messages' => $messajes], 422);
        }

        $user = User::where("email", "=", $request->email)->first();
        if (isset($user)) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken("auth_token")->plainTextToken;
                return response()->json(['mensaje' => 'se inicio sesion con exito', "acess_token" => $token, "usuario" => $user], 200);
            } else {
                return response()->json(['mensaje' => 'password incorrecto'], 422);
            }
        } else {
            return response()->json(['mensaje' => 'usuario no existe'], 422);
        }
    }

    public function perfil()
    {
        return Auth::user();
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json(["mensaje" => "Se cerro la sesion con exito"], 200);
    }

}
