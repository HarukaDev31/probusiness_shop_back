<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class RedirectIfAuthenticatedApi
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'Ya autenticado.'], 403);
            }
        } catch (JWTException $e) {
            // No hay token o es inválido, dejar pasar
            return $next($request);
        } catch (\Exception $e) {
            // Cualquier otro error, dejar pasar
            return $next($request);
        }
        return $next($request);
    }
}
