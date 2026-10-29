<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Batch;
use App\Models\Category;
use App\Models\Provider;
use App\Models\Branch;

class InventoryController extends Controller
{
    /**
     * Agregar Producto (POST /api/v1/inventario/productos)
     * Inserta un registro de producto al inventario.
     * Argumento: JSON { "nombre": "Monitor LCD", "precio": 3500 }
     * Responsable: Daniels J. Altamirano
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'precio'      => 'required|numeric|min:0',
            'sku'         => 'nullable|string|max:50|unique:products,sku',
            'category_id' => 'nullable|exists:categories,id',
            'provider_id' => 'nullable|exists:providers,id',
            'stock'       => 'nullable|integer|min:0',
            'descripcion' => 'nullable|string',
        ]);

        $nombre = $request->input('nombre');
        $precio = (float) $request->input('precio');
        $sku = $request->input('sku') ?: 'NX-' . strtoupper(Str::random(6));

        // Asignar categoría por defecto si no se especifica
        $categoryId = $request->input('category_id');
        if (!$categoryId) {
            $cat = Category::firstOrCreate(['name' => 'General'], ['description' => 'Categoría general del ERP']);
            $categoryId = $cat->id;
        }

        // Asignar proveedor por defecto si no se especifica
        $providerId = $request->input('provider_id');
        if (!$providerId) {
            $prov = Provider::first();
            $providerId = $prov ? $prov->id : 1;
        }

        $product = Product::create([
            'name'         => $nombre,
            'price'        => $precio,
            'sku'          => $sku,
            'description'  => $request->input('descripcion', 'Registro creado vía API'),
            'category_id'  => $categoryId,
            'provider_id'  => $providerId,
        ]);

        $initialStock = (int) $request->input('stock', 0);
        $branch = Branch::first();

        // Crear lote asociado para sincronizar con inventario operativo
        $batch = Batch::create([
            'product_id'      => $product->id,
            'batch_number'    => 'LOTE-' . strtoupper(Str::random(5)),
            'stock'           => $initialStock,
            'cost_price'      => round($precio * 0.70, 2),
            'selling_price'   => $precio,
            'expiration_date' => now()->addYears(2)->toDateString(),
            'branch_id'       => $branch ? $branch->id : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto insertado al catálogo de inventario con éxito.',
            'data'    => [
                'id'          => $product->id,
                'nombre'      => $product->name,
                'precio'      => (float) $product->price,
                'sku'         => $product->sku,
                'stock_total' => $initialStock,
                'categoria'   => optional($product->category)->name,
                'proveedor'   => optional($product->provider)->name,
            ],
        ], 201);
    }

    /**
     * Consultar Producto (GET /api/v1/inventario/productos/{id})
     * Retorna la información de un producto.
     * Argumento: id_producto (entero)
     * Responsable: Juan M. Martinez
     */
    public function show($id)
    {
        $product = Product::with(['category', 'provider', 'batches'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => "Producto con ID {$id} no encontrado en inventario.",
            ], 404);
        }

        $stockTotal = (int) $product->batches->sum('stock');
        $precio = (float) ($product->price > 0 ? $product->price : ($product->batches->first()?->selling_price ?? 0));

        return response()->json([
            'success' => true,
            'data'    => [
                'id_producto'   => $product->id,
                'nombre'        => $product->name,
                'sku'           => $product->sku,
                'precio'        => $precio,
                'stock_total'   => $stockTotal,
                'descripcion'   => $product->description,
                'categoria'     => optional($product->category)->name,
                'proveedor'     => optional($product->provider)->name,
                'lotes_activos' => $product->batches->count(),
                'creado_el'     => $product->created_at->format('Y-m-d H:i:s'),
            ],
        ], 200);
    }

    /**
     * Actualizar Stock (PATCH /api/v1/inventario/productos/{id}/stock)
     * Modifica las existencias de un producto.
     * Argumento: JSON { "cantidad_agregada": 50 }
     * Responsable: Gabriel Cazares
     */
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'cantidad_agregada' => 'required|integer',
        ]);

        $product = Product::with('batches')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => "Producto con ID {$id} no encontrado.",
            ], 404);
        }

        $cantidad = (int) $request->input('cantidad_agregada');

        // Buscar lote existente o crear uno nuevo
        $batch = $product->batches()->orderBy('id', 'desc')->first();

        if (!$batch) {
            $branch = Branch::first();
            $batch = Batch::create([
                'product_id'      => $product->id,
                'batch_number'    => 'LOTE-' . strtoupper(Str::random(5)),
                'stock'           => max(0, $cantidad),
                'cost_price'      => round(((float) $product->price) * 0.70, 2),
                'selling_price'   => (float) $product->price,
                'expiration_date' => now()->addYears(2)->toDateString(),
                'branch_id'       => $branch ? $branch->id : null,
            ]);
        } else {
            $batch->stock = max(0, $batch->stock + $cantidad);
            $batch->save();
        }

        $nuevoStockTotal = (int) $product->batches()->sum('stock');

        return response()->json([
            'success' => true,
            'message' => 'Existencias actualizadas correctamente.',
            'data'    => [
                'id_producto'       => $product->id,
                'nombre'            => $product->name,
                'cantidad_agregada' => $cantidad,
                'stock_total_nuevo' => $nuevoStockTotal,
            ],
        ], 200);
    }

    /**
     * Eliminar Producto (DELETE /api/v1/inventario/productos/{id})
     * Realiza borrado lógico de un producto.
     * Bloquea eliminaciones lógicas para 'Personal de Mostrador' (Cajero); solo 'admin' / 'superadmin'.
     * Argumento: id de producto
     * Responsable: Cristian A. Perez
     */
    public function destroy(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user && $request->filled('user_id')) {
            $user = \App\Models\User::find($request->input('user_id'));
        }

        $roleHeaderOrParam = strtolower(trim((string) ($request->input('role') ?? $request->header('X-User-Role', ''))));
        if (in_array($roleHeaderOrParam, ['personal de mostrador', 'cajero'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado: El rol Personal de Mostrador no tiene permisos para realizar eliminaciones lógicas (SoftDeletes). Solo un Administrador puede hacerlo.',
            ], 403);
        }

        if ($user && ($user->hasRole('Personal de Mostrador') || !$user->hasRole(['admin', 'superadmin']))) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado: El rol Personal de Mostrador no tiene permisos para realizar eliminaciones lógicas (SoftDeletes). Solo un Administrador puede hacerlo.',
            ], 403);
        }

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => "Producto con ID {$id} no encontrado o ya fue eliminado.",
            ], 404);
        }

        // Aplica SoftDeletes (borrado lógico: actualiza deleted_at)
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => "El producto '{$product->name}' fue dado de baja lógicamente con éxito.",
            'data'    => [
                'id_producto' => $product->id,
                'nombre'      => $product->name,
                'eliminado_en'=> now()->format('Y-m-d H:i:s'),
                'tipo_borrado'=> 'Lógico (Soft Delete)',
            ],
        ], 200);
    }
}
