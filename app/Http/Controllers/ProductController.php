<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Provider;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'provider', 'batches.branch'])->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('active_ingredient', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 25);
        $allowedPerPage = [25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 25;
        }

        $products = $query->paginate($perPage)->appends($request->query());

        $categories = Category::orderBy('name', 'asc')->get();
        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Traemos los datos para llenar los menús desplegables
        $categories = Category::orderBy('name', 'asc')->get();
        $providers = Provider::orderBy('name', 'asc')->get();
        
        $branches = \App\Models\Branch::orderBy('name', 'asc')->get();
        $bodega = \App\Models\Branch::where('name', 'Bodega')->first();

        return view('products.create', compact('categories', 'providers', 'branches', 'bodega'));
    }

    /**
     * Show the form for creating a general product (non-medicine).
     */
    public function createGeneral()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $providers = Provider::orderBy('name', 'asc')->get();
        
        $branches = \App\Models\Branch::orderBy('name', 'asc')->get();
        $bodega = \App\Models\Branch::where('name', 'Bodega')->first();

        return view('products.create_general', compact('categories', 'providers', 'branches', 'bodega'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:255|unique:products,name',
            'category_id' => 'required|exists:categories,id',
            'provider_id' => 'required|exists:providers,id',
            'active_ingredient' => 'nullable|string|max:255',
            'presentation' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ], [
            'sku.unique' => 'Este código de barras (SKU) ya está registrado en otro producto.',
            'name.unique' => 'Ya existe un producto registrado con este mismo nombre.',
            'category_id.required' => 'Debes seleccionar una categoría.',
            'provider_id.required' => 'Debes seleccionar un proveedor.',
        ]);

        $data = $request->all();
        $data['prescription_required'] = $request->has('prescription_required');
        $data['is_controlled'] = $request->has('is_controlled');

        $product = Product::create($data);


        return redirect()->route('products.index')->with('success', 'Medicamento registrado correctamente en el catálogo.');
    }
    /**
     * Almacena un medicamento vía AJAX y devuelve JSON (Usado en el modal de Lotes).
     */
    public function storeApi(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|unique:products,sku',
            'name' => 'required|string|max:255|unique:products,name',
            'type' => 'required|string|in:GEN,PAT,OTR',
            'category_id' => 'required|exists:categories,id',
            'provider_id' => 'required|exists:providers,id',
            'active_ingredient' => 'nullable|string|max:255',
            'presentation' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ], [
            'sku.unique' => 'Este código de barras (SKU) ya está registrado en otro producto.',
            'name.unique' => 'Ya existe un producto registrado con este mismo nombre.',
        ]);

        $data = $request->all();
        $data['prescription_required'] = $request->boolean('prescription_required');
        $data['is_controlled'] = $request->boolean('is_controlled');

        $product = \App\Models\Product::create($data);

        return response()->json([
            'success' => true,
            'product' => $product,
            'message' => 'Medicamento registrado correctamente.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $providers = Provider::orderBy('name', 'asc')->get();
        
        $branches = \App\Models\Branch::orderBy('name', 'asc')->get();
        $bodega = \App\Models\Branch::where('name', 'Bodega')->first();

        return view('products.edit', compact('product', 'categories', 'providers', 'branches', 'bodega'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'provider_id' => 'required|exists:providers,id',
            'type' => 'nullable|string|in:GEN,PAT,OTR',
            'active_ingredient' => 'nullable|string|max:255',
            'presentation' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ], [
            'sku.unique' => 'Este código de barras (SKU) ya está registrado en otro producto.',
            'name.unique' => 'Ya existe un producto registrado con este mismo nombre.',
            'category_id.required' => 'Debes seleccionar una categoría.',
            'provider_id.required' => 'Debes seleccionar un proveedor.',
        ]);

        $data = $request->all();

        // Si se cambia a producto general (OTR), limpiamos los campos exclusivos de medicamentos
        if (isset($data['type']) && $data['type'] === 'OTR') {
            $data['prescription_required'] = false;
            $data['is_controlled'] = false;
            $data['active_ingredient'] = null;
            $data['presentation'] = null;
        } else {
            // Si es medicamento (GEN o PAT), procesamos los checkboxes de requerimientos médicos
            $data['prescription_required'] = $request->has('prescription_required');
            $data['is_controlled'] = $request->has('is_controlled');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Medicamento actualizado correctamente.');
    }

    /**
     * Elimina el producto especificado de la base de datos (SoftDelete).
     * Bloqueado para 'Personal de Mostrador'; exclusivo para 'admin' / 'superadmin'.
     */
    public function destroy(\App\Models\Product $product)
    {
        $user = auth()->user();
        if (!$user || $user->hasRole('Personal de Mostrador') || !$user->hasRole(['admin', 'superadmin'])) {
            abort(403, 'Acceso denegado: El rol Personal de Mostrador no puede realizar eliminaciones lógicas. Solo un Administrador puede hacerlo.');
        }

        try {
            // Intentamos eliminar el medicamento
            $product->delete();
            
            return redirect()->route('products.index')
                             ->with('success', 'Medicamento eliminado del catálogo con éxito.');
                             
        } catch (\Illuminate\Database\QueryException $e) {
            // El código 23000 en MySQL corresponde a una violación de llave foránea (Integrity constraint violation)
            if ($e->getCode() == "23000") {
                return redirect()->route('products.index')
                                 ->with('error', 'No se puede eliminar: Este medicamento ya tiene lotes en bodega o historial de ventas. Para mantener la contabilidad, no es posible borrarlo.');
            }
            
            // Cualquier otro error de base de datos
            return redirect()->route('products.index')
                             ->with('error', 'Ocurrió un error inesperado al intentar eliminar el medicamento.');
        }
    }


}
