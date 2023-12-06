<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Autenticar el usuario
            $user = JWTAuth::parseToken()->authenticate();

            // Verificar si el usuario está eliminado
            if ($user && $user->deleted) {
                return response()->json(['error' => 'El usuario ha sido eliminado.'], 403);
            }
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->unauthorized(['error' => 'Token inválido'], 401);
            }
            if ($e instanceof TokenExpiredException) {
                return response()->unauthorized(['error' => 'El token expiró'], 401);
            }
            return response()->unauthorized(['error' => 'Error al verificar el token', 401]);
        }
        return $next($request);
    }
}
