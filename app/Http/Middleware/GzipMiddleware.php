<?php

namespace App\Http\Middleware;

use Closure;

class GzipMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        if (app()->environment('local')) { // Solo en local
            // No comprimir respuestas de error
            if ($response->getStatusCode() >= 400) {
                return $response;
            }
            
            $content = $response->getContent();
            $compressed = gzencode($content, 9);
            
            return response($compressed)
                ->header('Content-Encoding', 'gzip')
                ->header('Vary', 'Accept-Encoding')
                ->header('Content-Length', strlen($compressed));
        }
        
        return $response;
    }
}