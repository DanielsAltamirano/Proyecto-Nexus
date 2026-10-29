<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    /**
     * Registrar Cliente (POST /api/v1/clientes)
     * Agrega un nuevo cliente al módulo CRM.
     * Argumento: JSON { "nombre": "Juan", "empresa": "Devs" }
     * Responsable: Daniels J. Altamirano
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            'empresa'   => 'nullable|string|max:255',
            'telefono'  => 'nullable|string|max:50',
            'email'     => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
        ]);

        $client = Client::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cliente agregado exitosamente al módulo CRM.',
            'data'    => $client,
        ], 201);
    }

    /**
     * Actualizar Cliente (PUT /api/v1/clientes/{id})
     * Modifica los datos de contacto del cliente.
     * Argumento: JSON { "telefono": "4431234567" }
     * Responsable: Juan M. Martinez
     */
    public function update(Request $request, $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => "Cliente con ID {$id} no encontrado.",
            ], 404);
        }

        $validated = $request->validate([
            'telefono'  => 'nullable|string|max:50',
            'nombre'    => 'nullable|string|max:255',
            'empresa'   => 'nullable|string|max:255',
            'email'     => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
        ]);

        $client->update(array_filter($validated, fn($val) => !is_null($val)));

        return response()->json([
            'success' => true,
            'message' => 'Datos de contacto del cliente actualizados correctamente.',
            'data'    => $client,
        ], 200);
    }
}
