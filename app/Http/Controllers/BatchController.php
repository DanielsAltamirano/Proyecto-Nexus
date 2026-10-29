<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    /**
     * Muestra el listado del inventario de lotes.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Batch::select('batches.*')
            ->join('products', 'batches.product_id', '=', 'products.id')
            ->whereNull('products.deleted_at')
            ->with(['product', 'branch'])
            ->where('batches.stock', '>', 0)  // Ocultar lotes sin existencias
            ->orderBy('products.name', 'asc')
            ->orderBy('batches.expiration_date', 'asc');
        
        // Global Access can see all branches
        if (!$user->hasGlobalInventoryAccess()) {
            // Normal users only see their own branch
            $query->where('branch_id', $user->branch_id);
        }
        
        // Búsqueda (Search)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                })->orWhere('batch_number', 'like', "%{$search}%");
            });
        }

        // Filtro de sucursal
        if ($request->filled('branch_id') && $user->hasGlobalInventoryAccess()) {
            $query->where('branch_id', $request->branch_id);
        }

        $allBatches = $query->get();
        $groupedBatches = $allBatches->groupBy(function($batch) {
            return $batch->product_id . '_' . $batch->branch_id;
        });

        // Pagination
        $allowedPerPage = [25, 50, 100];
        $perPage = $request->input('per_page', 25);
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 25;
        }
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $batches = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupedBatches->forPage($page, $perPage),
            $groupedBatches->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );
        
        // Búsqueda global: Cargar todos los lotes externos para filtrar con Alpine JS
        $externalBatches = collect();
        if (!$user->hasGlobalInventoryAccess()) {
            $rawExternal = Batch::select('batches.*')
                ->join('products', 'batches.product_id', '=', 'products.id')
                ->whereNull('products.deleted_at')
                ->with(['product', 'branch'])
                ->where('branch_id', '!=', $user->branch_id)
                ->whereHas('branch', function($q) {
                    $q->where('is_warehouse', false); // No mostrar bodega al mostrador
                })
                ->where('stock', '>', 0)
                ->orderBy('products.name', 'asc')
                ->orderBy('batches.expiration_date', 'asc')
                ->get();

            $externalBatches = $rawExternal->groupBy(fn($b) => $b->product_id . '_' . $b->branch_id)->map(function($group) {
                $first = $group->first();
                $first->stock = $group->sum('stock');
                return $first;
            })->values();
        }

        // Si buscó y no hay local ni externo, buscamos el producto en sí para permitir pedirlo a compras
        $searchedProduct = null;
        if ($request->filled('search') && $batches->isEmpty() && $externalBatches->isEmpty()) {
            $searchTrimmed = trim($request->search);
            $searchedProduct = Product::where('name', 'like', "%{$searchTrimmed}%")
                                      ->orWhere('sku', 'like', "%{$searchTrimmed}%")
                                      ->first();
        }
        
        $user = auth()->user();
        
        if ($user->hasGlobalInventoryAccess()) {
            $branches = \App\Models\Branch::orderBy('name', 'asc')->get();
        } else {
            $branches = \App\Models\Branch::where('id', $user->branch_id)->get();
        }
        
        return view('batches.index', compact('batches', 'branches', 'externalBatches', 'searchedProduct'));
    }

    /**
     * Muestra el formulario para registrar el ingreso de un nuevo lote.
     */
    public function create()
    {
        if (auth()->user()->hasRole('Personal de Mostrador')) {
            abort(403, 'El personal de mostrador no tiene permisos para ingresar nuevas existencias.');
        }

        // Requerimos la lista de medicamentos activos
        $products = Product::orderBy('name', 'asc')->get();
        
        // Requerimos los catálogos para el Modal AJAX de "Nuevo Medicamento"
        $categories = \App\Models\Category::orderBy('name', 'asc')->get();
        $providers = \App\Models\Provider::orderBy('name', 'asc')->get();
        
        // Sucursales para asignar el lote
        $branches = \App\Models\Branch::orderBy('name', 'asc')->get();
        $bodega = \App\Models\Branch::where('name', 'Bodega')->first();
        
        return view('batches.create', compact('products', 'categories', 'providers', 'branches', 'bodega'));
    }

    /**
     * Procesa y guarda el nuevo lote en la base de datos.
     */
    public function store(Request $request)
    {
        if (auth()->user()->hasRole('Personal de Mostrador')) {
            abort(403, 'El personal de mostrador no tiene permisos para ingresar nuevas existencias.');
        }

        $rules = [
            'product_id'      => 'required|exists:products,id',
            'batch_number'    => 'nullable|string|max:100',
            'stock'           => 'required|integer|min:0',
            'cost_price'      => 'required|numeric|min:0',
            'selling_price'   => 'required|numeric|min:0',
            'expiration_date' => 'nullable|date',
            'rack'            => 'nullable|string|max:50',
            'shelf'           => 'nullable|string|max:50',
        ];

        if (auth()->user()->hasGlobalInventoryAccess()) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }

        $request->validate($rules, [
            // Prevención de error humano en la captura de caducidades
            'expiration_date.after' => 'La fecha de caducidad no puede ser una fecha pasada.',
        ]);

        $data = $request->all();
        
        // Si el usuario no tiene acceso global, forzar el branch_id a su sucursal actual
        if (!auth()->user()->hasGlobalInventoryAccess()) {
            $data['branch_id'] = auth()->user()->branch_id;
        }

        $batch = Batch::create($data);

        // Registro en Historial (Trazabilidad SAP)
        \App\Models\BatchHistory::create([
            'batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'before_state' => null,
            'after_state' => $batch->toArray(),
        ]);

        return redirect()->route('batches.index')
                         ->with('success', 'Lote registrado e ingresado al inventario con éxito.');
    }

    /**
     * Muestra el formulario para editar un lote existente (correcciones o reubicación).
     */
    public function edit(Batch $batch)
    {
        $products = Product::orderBy('name', 'asc')->get();
        $branches = \App\Models\Branch::orderBy('name', 'asc')->get();
        
        return view('batches.edit', compact('batch', 'products', 'branches'));
    }

    /**
     * Procesa la actualización de los datos del lote.
     */
    public function update(Request $request, Batch $batch)
    {
        $needsAuthorization = auth()->user()->hasRole(['Personal de Mostrador', 'compras']);

        $rules = [
            'product_id'      => 'required|exists:products,id',
            'rack'            => 'nullable|string|max:50',
            'shelf'           => 'nullable|string|max:50',
        ];

        if (!$needsAuthorization) {
            $rules['batch_number']    = 'nullable|string|max:100';
            $rules['stock']           = 'required|integer|min:0';
            $rules['cost_price']      = 'required|numeric|min:0';
            $rules['selling_price']   = 'required|numeric|min:0';
            $rules['expiration_date'] = 'nullable|date';
            $rules['branch_id']       = 'nullable|exists:branches,id';
        }

        $request->validate($rules);

        $data = $request->all();
        
        if ($needsAuthorization) {
            unset($data['stock'], $data['cost_price'], $data['selling_price'], $data['batch_number'], $data['expiration_date'], $data['branch_id']);
        } elseif (!auth()->user()->hasRole('superadmin')) {
            unset($data['branch_id']); // Evitar que alguien que no sea superadmin mueva lotes mágicamente editando
        }

        $beforeState = $batch->toArray();
        $batch->update($data);

        // Registro en Historial
        \App\Models\BatchHistory::create([
            'batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'action' => 'updated',
            'before_state' => $beforeState,
            'after_state' => $batch->refresh()->toArray(),
        ]);

        return redirect()->route('batches.index')
                         ->with('success', 'Lote actualizado correctamente.');
    }

    /**
     * Elimina el lote del sistema.
     */
    public function destroy(Request $request, Batch $batch)
    {
        abort_unless(auth()->user()->hasRole(['superadmin', 'admin']), 403, 'Acceso denegado. Solo administradores pueden eliminar lotes.');
        
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $beforeState = $batch->toArray();
        
        // Comprobar si hay ventas o traspasos asociados
        $hasSales = \Illuminate\Support\Facades\DB::table('sales')->where('batch_id', $batch->id)->exists();
        $hasTransfers = \Illuminate\Support\Facades\DB::table('transfer_items')->where('batch_id', $batch->id)->exists();

        if ($hasSales || $hasTransfers) {
            return redirect()->route('batches.index')
                             ->with('error', 'No se puede eliminar este lote porque ya tiene ventas o traspasos asociados en el sistema.');
        }

        \App\Models\BatchDeletion::create([
            'product_name' => $batch->product->name,
            'batch_number' => $batch->batch_number,
            'stock' => $batch->stock,
            'user_id' => auth()->id(),
            'reason' => $request->reason,
        ]);

        \Illuminate\Support\Facades\DB::table('batch_edit_requests')->where('batch_id', $batch->id)->delete();
        \Illuminate\Support\Facades\DB::table('batch_histories')->where('batch_id', $batch->id)->delete();
        \Illuminate\Support\Facades\DB::table('inventory_movements')->where('batch_id', $batch->id)->delete();

        $batch->delete();
        
        return redirect()->route('batches.index')
                         ->with('success', 'Lote eliminado de forma permanente.');
    }

    /**
     * Actualización inline de un lote desde la pantalla de edición de producto.
     * Solo actualiza stock, cost_price, selling_price, batch_number y expiration_date.
     * Registra un movimiento de auditoría si el stock cambia.
     */
    public function inlineUpdate(Request $request, Batch $batch)
    {
        $request->validate([
            'stock'           => 'required|integer|min:0',
            'cost_price'      => 'required|numeric|min:0',
            'selling_price'   => 'required|numeric|min:0',
            'batch_number'    => 'nullable|string|max:100',
            'expiration_date' => 'nullable|date',
        ]);

        $beforeState = $batch->toArray();
        $oldStock = $batch->stock;
        $newStock = (int) $request->stock;

        $batch->update([
            'stock'           => $newStock,
            'cost_price'      => $request->cost_price,
            'selling_price'   => $request->selling_price,
            'batch_number'    => $request->batch_number,
            'expiration_date' => $request->expiration_date,
        ]);

        // Registro en Historial
        \App\Models\BatchHistory::create([
            'batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'action' => 'updated',
            'before_state' => $beforeState,
            'after_state' => $batch->refresh()->toArray(),
        ]);

        // Auditoría si el stock cambió
        if ($oldStock !== $newStock) {
            $diff = $newStock - $oldStock;
            \App\Models\InventoryMovement::create([
                'branch_id'  => $batch->branch_id,
                'batch_id'   => $batch->id,
                'user_id'    => auth()->id(),
                'type'       => $diff > 0 ? 'adjustment' : 'adjustment',
                'quantity'   => abs($diff),
                'notes'      => ($diff > 0 ? 'Ajuste +' : 'Ajuste -') . abs($diff) . " uds. desde edición de producto (stock anterior: {$oldStock})",
            ]);
        }

        return redirect()->back()->with('success', 'Existencia actualizada correctamente.');
    }

    /**
     * Devuelve el historial de modificaciones del lote vía API.
     */
    public function historyApi(\Illuminate\Http\Request $request, Batch $batch)
    {
        // Solo para directivos y compras
        if (!auth()->user()->hasRole(['superadmin', 'admin', 'compras'])) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $monthFilter = $request->query('month'); // Formato esperado: YYYY-MM

        // 1. Obtener historial de ediciones
        $historyQuery = \App\Models\BatchHistory::where('batch_id', $batch->id)
            ->with('user:id,name');
        
        if ($monthFilter) {
            $historyQuery->where('created_at', 'like', $monthFilter . '-%');
        }
        
        $histories = $historyQuery->get()->map(function($h) {
            return [
                'type' => 'history',
                'id' => 'h_' . $h->id,
                'action' => $h->action,
                'before_state' => $h->before_state,
                'after_state' => $h->after_state,
                'user' => $h->user,
                'created_at' => $h->created_at,
            ];
        });

        // 2. Obtener historial de movimientos (traspasos y ajustes)
        $movementsQuery = \App\Models\InventoryMovement::where('batch_id', $batch->id)
            ->with(['user:id,name']);
            
        if ($monthFilter) {
            $movementsQuery->where('created_at', 'like', $monthFilter . '-%');
        }
        
        $movements = $movementsQuery->get()->map(function($m) {
            $transferUrl = null;
            
            // Extraer el ID del traspaso de las notas (ej: "... traspaso #7")
            if (preg_match('/#(\d+)/', $m->notes, $matches)) {
                $transferId = $matches[1];
                if (\App\Models\Transfer::where('id', $transferId)->exists()) {
                    $transferUrl = route('transfers.show', $transferId);
                }
            }

            return [
                'type' => 'movement',
                'id' => 'm_' . $m->id,
                'action' => $m->type, // transfer_in, transfer_out, adjustment, etc.
                'quantity' => $m->quantity,
                'notes' => $m->notes,
                'user' => $m->user,
                'transfer_url' => $transferUrl,
                'created_at' => $m->created_at,
            ];
        });

        // 3. Combinar y ordenar
        $combined = $histories->concat($movements)->sortByDesc('created_at')->values();

        return response()->json($combined);
    }

    /**
     * Muestra el expediente completo de una autorización de modificación (Tipo SAP)
     */
    public function showRequest(\App\Models\BatchEditRequest $batchEditRequest)
    {
        $batchEditRequest->load(['batch.product', 'batch.branch', 'user', 'resolver']);
        return view('batches.requests.show', compact('batchEditRequest'));
    }

    /**
     * Solicita autorización para modificar stock o precio de un lote
     */
    public function requestEdit(Request $request, Batch $batch)
    {
        $request->validate([
            'type' => 'nullable|string|in:edit,delete',
            'requested_stock' => 'nullable|integer|min:0',
            'requested_selling_price' => 'nullable|numeric|min:0',
            'requested_expiration_date' => 'nullable|date',
            'reason' => 'required|string|max:1000'
        ]);

        $type = $request->type ?? 'edit';

        if ($type === 'edit' && is_null($request->requested_stock) && is_null($request->requested_selling_price) && is_null($request->requested_expiration_date)) {
            return redirect()->back()->with('error', 'Debes especificar al menos un valor a cambiar (Stock, Precio o Fecha) para una edición.');
        }

        \App\Models\BatchEditRequest::create([
            'batch_id' => $batch->id,
            'type' => $type,
            'user_id' => auth()->id(),
            'old_stock' => $batch->stock,
            'old_selling_price' => $batch->selling_price,
            'old_expiration_date' => $batch->expiration_date,
            'requested_stock' => $request->requested_stock,
            'requested_selling_price' => $request->requested_selling_price,
            'requested_expiration_date' => $request->requested_expiration_date,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        $msg = $type === 'delete' ? 'Tu solicitud de eliminación ha sido enviada para su autorización.' : 'Tu solicitud de modificación ha sido enviada para su autorización.';

        return redirect()->route('batches.index')->with('success', $msg);
    }

    /**
     * Aprueba una solicitud de modificación
     */
    public function approveEdit(Request $request, \App\Models\BatchEditRequest $batchEditRequest)
    {
        abort_unless(auth()->user()->hasRole(['superadmin', 'admin']), 403, 'Acceso denegado. Solo administradores pueden aprobar solicitudes.');

        $request->validate([
            'resolution_notes' => 'nullable|string|max:1000'
        ]);

        $batch = $batchEditRequest->batch;
        
        if ($batchEditRequest->type === 'delete') {
            // Comprobar si hay ventas o traspasos asociados
            $hasSales = \Illuminate\Support\Facades\DB::table('sales')->where('batch_id', $batch->id)->exists();
            $hasTransfers = \Illuminate\Support\Facades\DB::table('transfer_items')->where('batch_id', $batch->id)->exists();

            if ($hasSales || $hasTransfers) {
                return redirect()->back()->with('error', 'El lote no pudo ser eliminado porque tiene ventas o traspasos vinculados.');
            }

            $note = $request->resolution_notes ? ' | Notas de aprobación: ' . $request->resolution_notes : '';
            \App\Models\BatchDeletion::create([
                'product_name' => $batch->product->name,
                'batch_number' => $batch->batch_number,
                'stock' => $batch->stock,
                'user_id' => $batchEditRequest->user_id, // El que lo solicitó
                'reason' => $batchEditRequest->reason . ' (Aprobado por ' . auth()->user()->name . ')' . $note,
            ]);

            // Eliminar registros dependientes para que MySQL permita borrar el lote
            \Illuminate\Support\Facades\DB::table('batch_edit_requests')->where('batch_id', $batch->id)->delete();
            \Illuminate\Support\Facades\DB::table('batch_histories')->where('batch_id', $batch->id)->delete();
            \Illuminate\Support\Facades\DB::table('inventory_movements')->where('batch_id', $batch->id)->delete();

            $batch->delete();

            return redirect()->route('batches.index')->with('success', 'Solicitud de eliminación aprobada. El lote ha sido borrado permanentemente y registrado en bajas.');
        }

        $beforeState = $batch->toArray();
        $oldStock = $batch->stock;
        
        $updates = [];
        if (!is_null($batchEditRequest->requested_stock)) {
            $updates['stock'] = $batchEditRequest->requested_stock;
        }
        if (!is_null($batchEditRequest->requested_selling_price)) {
            $updates['selling_price'] = $batchEditRequest->requested_selling_price;
        }
        if (!is_null($batchEditRequest->requested_expiration_date)) {
            $updates['expiration_date'] = $batchEditRequest->requested_expiration_date;
        }

        $batch->update($updates);

        // Registro en Historial
        \App\Models\BatchHistory::create([
            'batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'action' => 'updated_via_request',
            'before_state' => $beforeState,
            'after_state' => $batch->refresh()->toArray(),
        ]);

        // Auditoría si el stock cambió
        if (isset($updates['stock']) && $oldStock !== $updates['stock']) {
            $diff = $updates['stock'] - $oldStock;
            \App\Models\InventoryMovement::create([
                'branch_id'  => $batch->branch_id,
                'batch_id'   => $batch->id,
                'user_id'    => auth()->id(),
                'type'       => 'adjustment',
                'quantity'   => abs($diff),
                'notes'      => 'Ajuste autorizado por ' . auth()->user()->name . '. Motivo: ' . $batchEditRequest->reason,
            ]);
        }

        $batchEditRequest->update([
            'status' => 'approved',
            'resolved_by' => auth()->id(),
            'resolution_notes' => $request->resolution_notes
        ]);

        return redirect()->back()->with('success', 'Solicitud aprobada y lote actualizado correctamente.');
    }

    public function deletions()
    {
        $deletions = \App\Models\BatchDeletion::with('user')->latest()->get();
        return view('batches.deletions', compact('deletions'));
    }

    /**
     * Rechaza una solicitud de modificación
     */
    public function rejectEdit(Request $request, \App\Models\BatchEditRequest $batchEditRequest)
    {
        abort_unless(auth()->user()->hasRole(['superadmin', 'admin']), 403, 'Acceso denegado. Solo administradores pueden rechazar solicitudes.');

        $request->validate([
            'resolution_notes' => 'nullable|string|max:1000'
        ]);

        $batchEditRequest->update([
            'status' => 'rejected',
            'resolved_by' => auth()->id(),
            'resolution_notes' => $request->resolution_notes
        ]);

        return redirect()->back()->with('success', 'Solicitud rechazada.');
    }

    /**
     * Cancela una solicitud de modificación (para el autor o admin)
     */
    public function cancelRequest(Request $request, \App\Models\BatchEditRequest $batchEditRequest)
    {
        abort_unless(auth()->id() === $batchEditRequest->user_id || auth()->user()->hasRole(['superadmin', 'admin']), 403, 'Acceso denegado. No puedes cancelar esta solicitud.');

        $batchEditRequest->delete();

        return redirect()->route('batches.index')->with('success', 'Solicitud cancelada exitosamente.');
    }
}