<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Branch;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Registrar Usuario (POST /api/v1/usuarios)
     * Añade un nuevo usuario al ERP.
     * Argumento: JSON { "nombre": "Ana", "rol": "admin" }
     * Responsable: Juan M. Martinez
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:255',
            'rol'       => 'nullable|string',
            'email'     => 'nullable|email|unique:users,email',
            'password'  => 'nullable|string|min:3',
            'telefono'  => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $nombre = $request->input('nombre');
        $rolName = $request->input('rol', 'admin');

        // Generar email único por defecto si no viene en el payload
        $slug = Str::slug($nombre);
        $email = $request->filled('email') 
            ? $request->input('email') 
            : $slug . '_' . time() . '@nexuserp.com';

        // Password por defecto si no viene
        $plainPassword = $request->filled('password') ? $request->input('password') : '123456';
        
        $branchId = $request->input('branch_id');
        if (!$branchId) {
            $defaultBranch = Branch::first();
            $branchId = $defaultBranch ? $defaultBranch->id : null;
        }

        $user = User::create([
            'name'      => $nombre,
            'email'     => $email,
            'password'  => Hash::make($plainPassword),
            'phone'     => $request->input('telefono'),
            'branch_id' => $branchId,
        ]);

        // Asignar rol si existe o crearlo
        if ($rolName) {
            Role::firstOrCreate(['name' => $rolName]);
            $user->assignRole($rolName);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente en el ERP.',
            'data'    => [
                'id'       => $user->id,
                'nombre'   => $user->name,
                'email'    => $user->email,
                'rol'      => $rolName,
                'branch_id'=> $user->branch_id,
            ],
        ], 201);
    }
}
