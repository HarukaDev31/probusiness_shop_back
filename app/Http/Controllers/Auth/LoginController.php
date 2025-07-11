<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new \Exception('Credenciales incorrectas', 404);
        }

        $authUser = auth()->user();
        $user = User::find($authUser->id);
        $user->api_token = $token;
        $user->save();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user
        ], 200);
    }
}
