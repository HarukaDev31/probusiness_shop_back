<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValidateApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        
        if (!$token) {
            return response()->json([
                'error' => 'Token de autorización requerido'
            ], 401);
        }

        // Remover 'Bearer ' si está presente
        $token = str_replace('Bearer ', '', $token);

        // Validar el token en la base de datos
        $user = DB::table('users')
            ->where('api_token', $token)
            ->where('api_token', '!=', null)
            ->first();

        if (!$user) {
            Log::warning('Intento de acceso con token inválido', [
                'token' => $token,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'error' => 'Token de autorización inválido'
            ], 401);
        }

        // Agregar el usuario a la request para uso posterior
        $request->merge(['authenticated_user' => $user]);

        return $next($request);
    }
} 