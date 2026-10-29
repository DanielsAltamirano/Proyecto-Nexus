<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\JwtService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class JwtAuthMiddleware
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Token de autorización ausente o con formato incorrecto. Enviar Bearer <token>.',
            ], 401);
        }

        $token = substr($authHeader, 7);

        try {
            $payload = $this->jwtService->validateToken($token);
            $user = User::find($payload['sub']);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario asociado al token no encontrado.',
                ], 401);
            }

            Auth::setUser($user);
            $request->attributes->set('jwt_payload', $payload);

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido: ' . $e->getMessage(),
            ], 401);
        }
    }
}
