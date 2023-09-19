<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Cookie;

//use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request){
        //this is the way without HEADER Accept: application/json
        /*$validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }*/


        $this->validate($request,[
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        $user->save();
        return response()->json([
            "status" =>1,
            "msg" => "Registro exitoso"
        ]);
    }
    public function login(Request $request){
        //this is the way without HEADER Accept: application/json
        /*$validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }*/

        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user = User::where("email","=",$request->email)->first();
        if(isset($user->id)){
            if(Hash::check($request->password, $user->password)){
                //delete unused tokens
                $user->tokens()->delete();

                $token = $user->createToken("auth_token", expiresAt:now()->addMinutes(10))->plainTextToken;

                // Create Cookie
                $cookie = Cookie::create('Laravel')
                ->withValue($token)
                ->withExpires(strtotime("+1 months"))
                ->withSecure(false) //https
                ->withHttpOnly(true)
                ->withDomain("localhost")
                ->withSameSite("none");
                $response = [
                    "status" =>1,
                    "message" => "Usuario logueado exitosamente!",
                    "access_token" => $token
                ];   
                // Return user, token and set refresh cookie
                return response($response, 201)->cookie($cookie);

                // return response()->json([
                //     "status" =>1,
                //     "message" => "Usuario logueado exitosamente!",
                //     "access_token" => $token
                // ]); 
            }else{
                return response()->json([
                    "status" =>0,
                    "message" => "Password incorrecto"
                ],404);
            }
        }else{
            return response()->json([
                "status" =>0,
                "message" => "Usuario no registrado"
            ], 404);
        }

    }
    public function userProfile(){
        auth()->user()->incrementExpireTime(now()->addMinutes(5));
        return response()->json([
            "status" =>1,
            "msg" => "Acerca del perfil de usuario",
            "data" => auth()->user()
        ]);
    }
    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json([
            "status" =>1,
            "message" => "Cierre de Sesión",
        ]);
    }
    public function islogged(){
        return ["status" =>1, "message" => "Sesión iniciada"];
    }
}
