<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provider;

class ProviderController extends Controller
{
    /**
     * Registrar Proveedor (POST /api/v1/proveedores)
     * Da de alta un proveedor en el sistema.
     * Argumento: JSON { "razon_social": "Tech SA", "rfc": "XAXX010101000" }
     * Responsable: Gabriel Cazares
     */
    public function store(Request $request)
    {
        $request->validate([
            'razon_social' => 'required|string|max:255',
            'rfc'          => 'required|string|max:20',
            'telefono'     => 'nullable|string|max:50',
            'direccion'    => 'nullable|string|max:500',
        ]);

        $razonSocial = $request->input('razon_social');
        $rfc = strtoupper(trim($request->input('rfc')));

        $provider = Provider::create([
            'name'         => $razonSocial,
            'razon_social' => $razonSocial,
            'rfc'          => $rfc,
            'phone'        => $request->input('telefono'),
            'address'      => $request->input('direccion'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proveedor registrado exitosamente en el sistema.',
            'data'    => [
                'id'           => $provider->id,
                'razon_social' => $provider->razon_social,
                'rfc'          => $provider->rfc,
                'telefono'     => $provider->phone,
                'direccion'    => $provider->address,
            ],
        ], 201);
    }

    /**
     * Listar Proveedores (GET /api/v1/proveedores)
     * Obtiene el catálogo de proveedores.
     * Argumento: page (int), limit (int)
     * Responsable: Cristian A. Perez
     */
    public function index(Request $request)
    {
        $limit = (int) $request->input('limit', 10);
        if ($limit <= 0) $limit = 10;

        $page = (int) $request->input('page', 1);

        $proveedores = Provider::orderBy('name', 'asc')->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data'    => $proveedores->items(),
            'meta'    => [
                'total_registros' => $proveedores->total(),
                'pagina_actual'   => $proveedores->currentPage(),
                'por_pagina'      => $proveedores->perPage(),
                'total_paginas'   => $proveedores->lastPage(),
            ],
        ], 200);
    }
}
