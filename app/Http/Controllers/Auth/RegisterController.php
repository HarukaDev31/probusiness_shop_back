<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        // La validación se ejecuta automáticamente en RegisterRequest
        // Si falla, se lanza una excepción con status 422
        $validatedData = $request->validated();
        //remove + from whatsapp
        $validatedData['whatsapp'] = str_replace('+', '', $validatedData['whatsapp']);
        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'whatsapp' => $validatedData['whatsapp'],
                'password' => Hash::make($validatedData['password']),
            ]);
    
            // Generar token JWT para el usuario registrado
            $token = JWTAuth::fromUser($user);
    
            $user->api_token = $token;
            $user->save();
    
            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'bearer'
            ], 201);
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
