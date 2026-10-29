<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function index()
    {
        $providers = Provider::orderBy('name', 'asc')->get();
        return view('providers.index', compact('providers'));
    }

    public function create()
    {
        return view('providers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:providers',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        Provider::create($request->all());

        return redirect()->route('providers.index')->with('success', 'Proveedor registrado con éxito.');
    }
    public function storeApi(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:providers',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $provider = Provider::create($request->all());

        return response()->json([
            'success' => true,
            'provider' => $provider,
            'message' => 'Proveedor registrado con éxito.'
        ]);
    }
    public function edit(Provider $provider)
    {
        return view('providers.edit', compact('provider'));
    }

    public function update(Request $request, Provider $provider)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:providers,name,' . $provider->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $provider->update($request->all());

        return redirect()->route('providers.index')->with('success', 'Datos del proveedor actualizados.');
    }

    public function destroy(Provider $provider)
    {
        try {
            $provider->delete();
            return redirect()->route('providers.index')->with('success', 'Proveedor eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('providers.index')->with('error', 'No se puede eliminar: este proveedor ya tiene medicamentos registrados en el sistema.');
        }
    }
}