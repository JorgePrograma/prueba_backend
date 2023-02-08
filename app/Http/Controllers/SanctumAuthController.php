<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Validation\Rule;

class SanctumAuthController extends Controller
{

    public function registro(Request $request)
    {
        // reglas de cada atributo
        $rules = array(
            'name' => 'required|max:250',
            'email' => 'required|unique:users|max:150|email',
            'password' => 'required|confirmed|max:250|min:4',
            'address' => 'max:50',
            'birthdate' => 'date',
            'city' => 'max:50',
        );

        // mensaje de errores
        $messajes = array(
            "name.required" => 'Campo name es requerido',
            "name.max" => 'Campo name solo permite 250 caracteres',
            "password.required" => 'Campo password es requerido',
            "password.min" => 'Campo password debe terner mas de 4 caracteres',
            "password.max" => 'Campo password solo permite 250 caracteres',
            "password.confirmed" => 'Campo password debe ser confirmado',
            "email.required" => 'Campo email es requerido',
            "email.max" => 'Campo email solo permite 150 caracteres',
            "email.email" => 'Campo email no es valido',
            "email.unique" => 'Campo email no es permitido',
            "address.max" => 'Campo address solo permite 50 caracteres',
            "city.max" => 'Campo city solo permite 50 caracteres',
            "birthdate.date" => 'Campo birthdate no tiene un formato date',
        );

        // captura si hay un error en los datos pasados por request
        $validator = FacadesValidator::make($request->all(), $rules, $messajes);

        // validacion si el dato es fallido muestra un 500 de lo contrario no pasa nada y se ejecuta la siguiente linea
        if ($validator->fails()) {
            $messajes = $validator->messages();
            return response()->json(['mensaje' => $messajes], 500);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->address = $request->address;
        $user->birthdate = $request->birthdate;
        $user->city = $request->city;
        $user->save();
        $token = $user->createToken("auth_token")->plainTextToken;
        return response()->json(['mensaje' => 'usuario registrado con exito', 'acess_token' => $token, 'usuario' => $user], 200);
    }

    public function login(Request $request)
    {
        // reglas de cada atributo
        $rules = array(
            'email' => 'required|email',
            'password' => 'required',
        );

        // mensaje de errores
        $messajes = array(

            // validacion de requerido
            "email.required" => 'Campo email es requerido',
            "password.required" => 'Campo password es requerido',
            "email.email" => 'Campo email no es valido',
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

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json(["mensaje" => "Se cerro la sesion con exito"], 200);
    }

    public function update(Request $request, $id)
    {

        $user = User::find($id);

        // reglas de cada atributo
        $rules = array(
            'name' => 'required|max:250',
            'email' => [
                'required', 'max:150', 'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'sometimes|max:250|min:4|nullable',
            'address' => 'max:50',
            'birthdate' => 'date|nullable',
            'city' => 'max:50',
        );

        $messajes = array(
            "name.required" => 'Campo name es requerido',
            "name.max" => 'Campo name solo permite 250 caracteres',
            "email.required" => 'Campo email es requerido',
            "email.max" => 'Campo email solo permite 150 caracteres',
            "email.email" => 'Campo email no es valido',
            "email.unique" => 'Campo email no es permitido',
            "password.max" => 'Campo password solo permite 250 caracteres',
            "password.min" => 'Campo password debe terner mas de 4 caracteres',
            "address.max" => 'Campo address solo permite 50 caracteres',
            "city.max" => 'Campo city solo permite 50 caracteres',
            "birthdate.date" => 'Campo birthdate no tiene un formato date',
        );

        // captura si hay un error en los datos pasados por request
        $validator = FacadesValidator::make($request->all(), $rules, $messajes);

        // validacion si el dato es fallido muestra un 500 de lo contrario no pasa nada y se ejecuta la siguiente linea
        if ($validator->fails()) {
            $messajes = $validator->messages();
            return response()->json(['mensaje' => $messajes], 500);
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->address = $request->address;
        $user->birthdate = $request->birthdate;
        $user->city = $request->city;
        $user->save();
            return response()->json(['mensaje' => 'usuario actualizado con exito', 'usuario' => $user], 200);
    }

    public function validatePassword($password)
    {
        return Hash::check($password, Auth::user()->password);
    }

    public function delete(Request $request, $id)
    {
        // reglas de cada atributo
        $rules = array(
            'password' => 'required|confirmed',
        );

        // mensaje de errores
        $messajes = array(
            "password.required" => 'Campo password es requerido',
            "password.confirmed" => 'Campo password debe ser confirmado',
        );

        // captura si hay un error en los datos pasados por request
        $validator = FacadesValidator::make($request->all(), $rules, $messajes);

        // validacion si el dato es fallido muestra un 500 de lo contrario no pasa nada y se ejecuta la siguiente linea
        if ($validator->fails()) {
            $messajes = $validator->messages();
            return response()->json(['mensaje' => $messajes], 500);
        }

        if (!$this->validatePassword($request->password)) {
            return response()->json(['mensaje' => 'Contraseña inválida'], 500);
        }
        // buscar el usuario y lo elimina de la base de datos
        $user = User::find($id);
        $user->delete();
        return response()->json(['mensaje' => 'cuenta eliminada con exito'], 200);
    }

}
