<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\JwtService;

class AuthController extends Controller
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Iniciar Sesión (POST /api/v1/auth/login)
     * Autentica un usuario y genera token JWT.
     * Argumento: JSON { "email": "admin@nexuserp.com", "password": "123" }
     * Responsable: Daniels J. Altamirano
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        $token = $this->jwtService->generateToken($user);

        return response()->json([
            'success'     => true,
            'message'     => 'Autenticación exitosa.',
            'token'       => $token,
            'token_type'  => 'Bearer',
            'user'        => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'branch_id' => $user->branch_id,
                'roles'     => $user->getRoleNames(),
            ],
        ], 200);
    }
}
