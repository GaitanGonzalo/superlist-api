<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RequestLogin;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(RequestLogin $request){
        $credentials = $request->only('email', 'password');
        if(!Auth::attempt($credentials)){
            return response()->json([
                'message'=>'Invalid credentials',
            ], 400);
        }
        $user = User::where(['email'=>$credentials['email'], 'deleted'=>0])->first();
        if(!$user || !Hash::check($credentials['password'], $user->password)){
            return response()->json([
                'success'=>false,
                'message'=>'Invalid credentials'
            ], 400);
        }
        $token = $this->createNewToken($user);
        $response = [
            'message'=>'access granted',
            'auth_token'=>$token,
            'user'=>$user
        ];
        return response()->json($response)
        ->cookie('AUTH_TOKEN', $token, 120, '/', null, true, true, false, 'None');
    }
    public function refresh(Request $request){

        try {
            // Obtener el token actual del usuario
            $token = JWTAuth::getToken();
            
            // Verificar que el token es válido
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            // Renovar el token
            $newToken = JWTAuth::refresh($token);
    
            // Devolver el nuevo token al usuario
            return response()->json(['message'=>'Refresh ok - access granted'])
            ->cookie('AUTH_TOKEN', $token, 120, '/', null, true, true, false, 'None');
    
        } catch (JWTException $e) {
            // Si hay algún error en la renovación del token, devolver un error
            return response()->json(['error' => 'Could not refresh token'], 500);
        }
    }

    protected function createNewToken($user){
        return JWTAuth::fromUser($user);
    }

    public function logout()
{
    $cookie = Cookie::forget('AUTH_TOKEN', '/', null);
    JWTAuth::unsetToken();
    return response()->json(['message' => 'logout ok'])
        ->withCookie($cookie);
}

}
