<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $query = \App\Models\Transfer::with(['fromBranch', 'toBranch', 'creator', 'items.product', 'items.batch.product']);
        
        if (!$user->hasRole(['superadmin', 'admin', 'compras'])) {
            $query->where(function ($q) use ($user) {
                $q->where('from_branch_id', $user->branch_id)
                  ->orWhere('to_branch_id', $user->branch_id);
            });
        }
        
        $transfers = $query->orderBy('created_at', 'desc')->get();
        
        return view('transfers.index', compact('transfers'));
    }

    public function requestTransfer(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        
        // El destino de la solicitud es la propia sucursal del usuario
        // Si es admin/superadmin/compras, puede pedir para cualquier sucursal
        $toBranchId = $user->hasRole(['superadmin', 'admin', 'compras']) ? null : $user->branch_id;
        
        $branches = \App\Models\Branch::all();
        $products = \App\Models\Product::all();

        // Solo cuenta el stock de lotes vigentes (los caducados no se pueden enviar)
        $stocks = \App\Models\Batch::notExpired()
            ->selectRaw('branch_id, product_id, SUM(stock) as total_stock')
            ->groupBy('branch_id', 'product_id')
            ->get();

        $prefillProductId = $request->query('product_id');
        $prefillFromBranchId = $request->query('from_branch_id');

        return view('transfers.request', compact('branches', 'products', 'toBranchId', 'prefillProductId', 'prefillFromBranchId', 'stocks'));
    }

    public function storeRequest(Request $request)
    {
        // Un usuario sin rol admin/compras solo puede solicitar para su propia sucursal
        if (!auth()->user()->hasRole(['superadmin', 'admin', 'compras']) && auth()->user()->branch_id) {
            $request->merge(['to_branch_id' => auth()->user()->branch_id]);
        }

        $request->validate([
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        // Consolidamos productos repetidos para validar el total solicitado por producto
        $consolidated = [];
        foreach ($request->items as $item) {
            $consolidated[$item['product_id']] = ($consolidated[$item['product_id']] ?? 0) + (int) $item['quantity'];
        }

        // Validación de stock disponible (solo lotes vigentes) en la sucursal de origen
        $errors = [];
        foreach ($consolidated as $productId => $qty) {
            $product = \App\Models\Product::find($productId);
            if (!$product) continue;
            
            $availableStock = \App\Models\Batch::notExpired()
                ->where('branch_id', $request->from_branch_id)
                ->where('product_id', $productId)
                ->sum('stock');
                
            if ($qty > $availableStock) {
                $errors["items"] = "La sucursal de origen solo cuenta con {$availableStock} unidades vigentes (no caducadas) de {$product->name}; solicitaste {$qty}. Si necesitas más, debes solicitarlo a Compras.";
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        \DB::beginTransaction();
        try {
            $transfer = \App\Models\Transfer::create([
                'from_branch_id' => $request->from_branch_id,
                'to_branch_id' => $request->to_branch_id,
                'created_by' => auth()->id(),
                'status' => 'pending', // Solicitud pendiente
                'notes' => $request->notes,
            ]);

            \App\Models\TransferLog::create([
                'transfer_id' => $transfer->id,
                'user_id' => auth()->id(),
                'action' => 'Solicitud de Traspaso Creada',
            ]);

            foreach ($consolidated as $productId => $qty) {
                \App\Models\TransferItem::create([
                    'transfer_id' => $transfer->id,
                    'product_id' => $productId,
                    'batch_id' => null, // Aún no se asignan lotes
                    'quantity_requested' => $qty,
                    'quantity_sent' => 0, // Aún no se envía
                ]);
            }

            \DB::commit();
            return redirect()->route('transfers.index')->with('success', 'Solicitud de traspaso creada. En espera de aprobación.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Error al crear la solicitud: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $user = auth()->user();
        
        // Un empleado solo puede enviar desde su propia sucursal (o admin/superadmin/compras elige)
        $fromBranchId = $user->hasRole(['superadmin', 'admin', 'compras']) ? null : $user->branch_id;
        
        $branches = \App\Models\Branch::all();
        
        // Obtener los lotes disponibles (con stock y NO caducados) en la sucursal de origen
        $batchesQuery = \App\Models\Batch::notExpired()->with('product')->where('stock', '>', 0);
        if ($fromBranchId) {
            $batchesQuery->where('branch_id', $fromBranchId);
        }
        $batches = $batchesQuery->get();
        $products = \App\Models\Product::all();

        return view('transfers.create', compact('branches', 'batches', 'products', 'fromBranchId'));
    }

    public function store(Request $request)
    {
        // Un usuario sin rol admin/compras solo puede enviar desde su propia sucursal
        if (!auth()->user()->hasRole(['superadmin', 'admin', 'compras']) && auth()->user()->branch_id) {
            $request->merge(['from_branch_id' => auth()->user()->branch_id]);
        }

        $mode = $request->input('mode', 'product');

        if ($request->has('items_json') && !empty($request->items_json)) {
            $itemsArray = json_decode($request->items_json, true);
            $formattedItems = [];
            foreach($itemsArray as $item) {
                if ($mode === 'product') {
                    $formattedItems[] = [
                        'product_id' => $item['product']['id'] ?? null,
                        'quantity' => $item['quantity'] ?? 1
                    ];
                } else {
                    $formattedItems[] = [
                        'batch_id' => $item['batch']['id'] ?? null,
                        'quantity' => $item['quantity'] ?? 1
                    ];
                }
            }
            $request->merge(['items' => $formattedItems]);
        }

        if ($mode === 'product') {
            $request->validate([
                'from_branch_id' => 'required|exists:branches,id',
                'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string'
            ]);
        } else {
            $request->validate([
                'from_branch_id' => 'required|exists:branches,id',
                'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
                'items' => 'required|array|min:1',
                'items.*.batch_id' => 'required|exists:batches,id',
                'items.*.quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string'
            ]);
        }

        // Personal de mostrador no puede enviar a bodega (la UI ya lo oculta, aquí se valida en servidor)
        $destBranch = \App\Models\Branch::find($request->to_branch_id);
        if ($destBranch && $destBranch->is_warehouse && auth()->user()->hasRole('Personal de Mostrador')) {
            return back()->withInput()->with('error', 'No tienes permiso para enviar traspasos hacia la bodega.');
        }

        // Consolidamos ítems repetidos (mismo producto / mismo lote) para validar y registrar correctamente
        $consolidated = [];
        foreach ($request->items as $item) {
            $key = $mode === 'product' ? $item['product_id'] : $item['batch_id'];
            $consolidated[$key] = ($consolidated[$key] ?? 0) + (int) $item['quantity'];
        }

        // Validación de stock disponible en origen ANTES de crear el traspaso
        $errors = [];
        foreach ($consolidated as $id => $qty) {
            if ($mode === 'product') {
                $product = \App\Models\Product::find($id);
                $available = \App\Models\Batch::notExpired()
                    ->where('branch_id', $request->from_branch_id)
                    ->where('product_id', $id)
                    ->sum('stock');
                if ($qty > $available) {
                    $errors['items'] = "La sucursal de origen solo cuenta con {$available} unidades vigentes (no caducadas) de {$product->name} (solicitaste {$qty}).";
                }
            } else {
                $batch = \App\Models\Batch::find($id);
                if ($batch->branch_id != $request->from_branch_id) {
                    $errors['items'] = "El lote {$batch->batch_number} no pertenece a la sucursal de origen.";
                } elseif ($batch->expiration_date && \Carbon\Carbon::parse($batch->expiration_date)->endOfDay()->isPast()) {
                    $errors['items'] = "El lote {$batch->batch_number} está caducado y no se puede enviar.";
                } elseif ($qty > $batch->stock) {
                    $errors['items'] = "Stock insuficiente para el lote {$batch->batch_number} (disponible: {$batch->stock}, solicitado: {$qty}).";
                }
            }
        }
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        \DB::beginTransaction();
        try {
            if ($mode === 'product') {
                $transfer = \App\Models\Transfer::create([
                    'from_branch_id' => $request->from_branch_id,
                    'to_branch_id' => $request->to_branch_id,
                    'created_by' => auth()->id(),
                    'status' => 'en_proceso',
                    'notes' => $request->notes,
                ]);

                \App\Models\TransferLog::create([
                    'transfer_id' => $transfer->id,
                    'user_id' => auth()->id(),
                    'action' => 'Traspaso Creado (En Proceso)',
                ]);

                // Solo registramos qué se va a mandar, el descuento FIFO ocurre al pasar a en_transito
                foreach ($consolidated as $productId => $qty) {
                    \App\Models\TransferItem::create([
                        'transfer_id' => $transfer->id,
                        'batch_id' => null,
                        'product_id' => $productId,
                        'quantity_requested' => $qty,
                        'quantity_sent' => 0,
                    ]);
                }
                
                \DB::commit();
                return redirect()->route('transfers.index')->with('success', 'Traspaso iniciado. Siguiente paso: Empaquetar.');

            } else {
                // Modo Lote (Manual)
                $isAdmin = auth()->user()->hasRole(['superadmin', 'admin', 'compras']);
                $status = $isAdmin ? 'en_proceso' : 'pending_override'; // Si es admin lo inicia, si no, pide permiso
                
                $transfer = \App\Models\Transfer::create([
                    'from_branch_id' => $request->from_branch_id,
                    'to_branch_id' => $request->to_branch_id,
                    'created_by' => auth()->id(),
                    'status' => $status,
                    'notes' => $request->notes,
                ]);

                \App\Models\TransferLog::create([
                    'transfer_id' => $transfer->id,
                    'user_id' => auth()->id(),
                    'action' => $status === 'en_proceso' ? 'Traspaso Manual Creado (En Proceso)' : 'Petición de Traspaso Manual Creada',
                ]);

                foreach ($consolidated as $batchId => $qty) {
                    $batch = \App\Models\Batch::find($batchId);

                    // Registramos los items. En lote manual SÍ conocemos el lote desde el principio
                    \App\Models\TransferItem::create([
                        'transfer_id' => $transfer->id,
                        'batch_id' => $batch->id,
                        'product_id' => null,
                        'quantity_requested' => $qty,
                        'quantity_sent' => 0,
                    ]);
                }

                \DB::commit();
                if ($status === 'pending_override') {
                    return redirect()->route('transfers.index')->with('success', 'Petición de traspaso manual creada. Esperando autorización de un administrador.');
                }
                return redirect()->route('transfers.index')->with('success', 'Traspaso manual iniciado correctamente.');
            }

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Error al procesar el traspaso: ' . $e->getMessage());
        }
    }

    public function show(\App\Models\Transfer $transfer)
    {
        $transfer->load(['fromBranch', 'toBranch', 'creator', 'receiver', 'items.batch.product', 'items.product', 'logs.user']);
        return view('transfers.show', compact('transfer'));
    }

    public function print(\App\Models\Transfer $transfer)
    {
        $transfer->load(['fromBranch', 'toBranch', 'creator', 'receiver', 'items.batch.product', 'items.product']);
        return view('transfers.print', compact('transfer'));
    }

    public function update(Request $request, \App\Models\Transfer $transfer)
    {
        $user = auth()->user();
        
        // Verificar permisos según la acción
        // El administrador siempre puede. 
        // Si no es admin, depende del status.
        $isAdmin = $user->hasRole(['superadmin', 'admin', 'compras']);
        $isOrigin = $user->branch_id == $transfer->from_branch_id;
        $isDest = $user->branch_id == $transfer->to_branch_id;

        $request->validate([
            'action' => 'required|in:mark_empaquetado,mark_en_transito,receive,reject,cancel,approve_request,reject_request,approve_override',
            'notes' => 'nullable|string',
            'received_items' => 'nullable|array',
            'received_items.*.id' => 'exists:transfer_items,id',
            'received_items.*.quantity_received' => 'required_if:action,receive|integer|min:0',
            'approved_items' => 'nullable|array',
            'approved_items.*.id' => 'exists:transfer_items,id',
            'approved_items.*.quantity_approved' => 'required_if:action,approve_request|integer|min:0',
            'send_notes' => 'nullable|string',
        ]);

        \DB::beginTransaction();
        try {
            // Bloqueamos el traspaso para evitar doble clic / acciones simultáneas que descuenten dos veces
            $transfer = \App\Models\Transfer::lockForUpdate()->findOrFail($transfer->id);

            if ($request->action === 'approve_request') {
                if ($transfer->status !== 'pending') {
                    throw new \Exception("Esta solicitud ya no está pendiente.");
                }
                if (!$isAdmin && !$isOrigin) {
                    throw new \Exception("Solo la sucursal de origen o administradores pueden aprobar esta solicitud.");
                }

                // Lógica FIFO para cada item
                $sendNotes = $request->input('send_notes', '');
                $approvedItemsData = $request->input('approved_items', []);
                $totalApproved = 0;

                foreach ($transfer->items as $item) {
                    $qtyToFulfill = $item->quantity_requested;
                    if (isset($approvedItemsData[$item->id])) {
                        // Nunca se puede aprobar más de lo solicitado ni menos de 0
                        $qtyToFulfill = max(0, min((int)$approvedItemsData[$item->id]['quantity_approved'], (int)$item->quantity_requested));
                    }
                    $totalApproved += $qtyToFulfill;
                    
                    $originalRequested = $item->quantity_requested;
                    
                    // Solo lotes vigentes: nunca se envían productos caducados
                    $batches = \App\Models\Batch::notExpired()
                        ->where('branch_id', $transfer->from_branch_id)
                        ->where('product_id', $item->product_id)
                        ->where('stock', '>', 0)
                        ->orderByRaw('expiration_date IS NULL ASC, expiration_date ASC')
                        ->lockForUpdate()
                        ->get();
                        
                    // Eliminamos el item original genérico porque ahora crearemos los específicos
                    $item->delete();
                    
                    if ($qtyToFulfill == 0) {
                        \App\Models\TransferItem::create([
                            'transfer_id' => $transfer->id,
                            'batch_id' => null,
                            'product_id' => $item->product_id,
                            'quantity_requested' => $originalRequested,
                            'quantity_sent' => 0,
                        ]);
                        continue;
                    }
                    
                    $first = true;
                    foreach ($batches as $batch) {
                        if ($qtyToFulfill <= 0) break;
                        
                        $take = min($batch->stock, $qtyToFulfill);
                        $batch->decrement('stock', $take);
                        
                        \App\Models\TransferItem::create([
                            'transfer_id' => $transfer->id,
                            'batch_id' => $batch->id,
                            'product_id' => null,
                            'quantity_requested' => $first ? $originalRequested : 0,
                            'quantity_sent' => $take,
                        ]);
                        $first = false;
                        
                        \App\Models\InventoryMovement::create([
                            'branch_id' => $transfer->from_branch_id,
                            'batch_id' => $batch->id,
                            'user_id' => auth()->id(),
                            'type' => 'transfer_out',
                            'quantity' => $take,
                            'notes' => "Envío de solicitud aprobada (FIFO) #{$transfer->id}",
                        ]);
                        
                        $qtyToFulfill -= $take;
                    }
                    
                    if ($qtyToFulfill > 0) {
                        throw new \Exception("Stock insuficiente para cubrir la cantidad aprobada del producto. Faltan {$qtyToFulfill} unidades.");
                    }
                }
                
                if ($totalApproved <= 0) {
                    throw new \Exception("No puedes aprobar una solicitud con cantidad 0 en todos los productos. Usa 'Rechazar' en su lugar.");
                }

                $transfer->update([
                    'status' => 'en_proceso',
                    'notes' => $transfer->notes . "\n[Aprobado por Origen - " . now()->format('d/m/Y H:i') . "]" . ($sendNotes ? "\nNotas: " . $sendNotes : "")
                ]);

                \App\Models\TransferLog::create([
                    'transfer_id' => $transfer->id,
                    'user_id' => auth()->id(),
                    'action' => 'Solicitud Aprobada (En Proceso)',
                ]);
            }
            elseif ($request->action === 'mark_empaquetado') {
                if ($transfer->status !== 'en_proceso') throw new \Exception("Solo se puede empaquetar si está en proceso.");
                if (!$isAdmin && !$isOrigin) throw new \Exception("Solo la sucursal de origen puede empaquetar.");

                $transfer->update(['status' => 'empaquetado']);
                \App\Models\TransferLog::create([
                    'transfer_id' => $transfer->id,
                    'user_id' => auth()->id(),
                    'action' => 'Marcado como Empaquetado',
                ]);
            }
            elseif ($request->action === 'mark_en_transito') {
                if ($transfer->status !== 'empaquetado') throw new \Exception("Debe estar empaquetado para enviarlo.");
                if (!$isAdmin && !$isOrigin) throw new \Exception("Solo la sucursal de origen puede enviar.");

                // AQUÍ EJECUTAMOS EL DESCUENTO FIFO O MANUAL
                // Si la solicitud fue aprobada vía 'approve_request', el stock YA fue descontado
                // en esa etapa (los items ya tienen lote y quantity_sent). No se debe descontar de nuevo.
                $stockAlreadyReserved = $transfer->logs()->where('action', 'Solicitud Aprobada (En Proceso)')->exists();
                $newItems = [];
                foreach ($transfer->items as $item) {
                    // Item cuyo stock ya fue descontado al aprobar la solicitud
                    if ($item->batch_id && $item->quantity_sent > 0) {
                        // Si el lote reservado caducó antes del envío, no se permite enviarlo
                        $reservedBatch = $item->batch;
                        if ($reservedBatch && $reservedBatch->expiration_date && \Carbon\Carbon::parse($reservedBatch->expiration_date)->endOfDay()->isPast()) {
                            throw new \Exception("El lote {$reservedBatch->batch_number} caducó antes del envío y no puede enviarse. Cancela el traspaso para regresar el stock al inventario.");
                        }
                        continue;
                    }
                    // Item aprobado con cantidad 0 en una solicitud: no se envía nada
                    if ($stockAlreadyReserved) continue;

                    $qtyToFulfill = $item->quantity_requested;
                    if ($qtyToFulfill <= 0) continue;

                    if ($item->batch_id) {
                        // Fue un lote manual o ya asignado
                        $batch = \App\Models\Batch::lockForUpdate()->find($item->batch_id);
                        if (!$batch || $batch->stock < $qtyToFulfill) {
                            throw new \Exception("Stock insuficiente en el lote específico.");
                        }
                        if ($batch->expiration_date && \Carbon\Carbon::parse($batch->expiration_date)->endOfDay()->isPast()) {
                            throw new \Exception("El lote {$batch->batch_number} está caducado y no puede enviarse.");
                        }
                        $batch->decrement('stock', $qtyToFulfill);
                        
                        $item->update(['quantity_sent' => $qtyToFulfill]);
                        
                        \App\Models\InventoryMovement::create([
                            'branch_id' => $transfer->from_branch_id,
                            'batch_id' => $batch->id,
                            'user_id' => auth()->id(),
                            'type' => 'transfer_out',
                            'quantity' => $qtyToFulfill,
                            'notes' => "Traspaso enviado #{$transfer->id}",
                        ]);
                    } else {
                        // Fue FIFO (producto genérico)
                        $batches = \App\Models\Batch::notExpired()
                            ->where('branch_id', $transfer->from_branch_id)
                            ->where('product_id', $item->product_id)
                            ->where('stock', '>', 0)
                            ->orderByRaw('expiration_date IS NULL ASC, expiration_date ASC')
                            ->lockForUpdate()
                            ->get();
                            
                        $originalRequested = $item->quantity_requested;
                        $item->delete(); // Reemplazaremos este item genérico con items específicos de lote
                        
                        foreach ($batches as $batch) {
                            if ($qtyToFulfill <= 0) break;
                            
                            $take = min($batch->stock, $qtyToFulfill);
                            $batch->decrement('stock', $take);
                            
                            $newItems[] = \App\Models\TransferItem::create([
                                'transfer_id' => $transfer->id,
                                'batch_id' => $batch->id,
                                'product_id' => null,
                                'quantity_requested' => $originalRequested,
                                'quantity_sent' => $take,
                            ]);
                            $originalRequested = 0; // Solo lo marcamos en el primero
                            
                            \App\Models\InventoryMovement::create([
                                'branch_id' => $transfer->from_branch_id,
                                'batch_id' => $batch->id,
                                'user_id' => auth()->id(),
                                'type' => 'transfer_out',
                                'quantity' => $take,
                                'notes' => "Traspaso enviado (FIFO) #{$transfer->id}",
                            ]);
                            
                            $qtyToFulfill -= $take;
                        }
                        
                        if ($qtyToFulfill > 0) {
                            throw new \Exception("Stock insuficiente para cubrir la cantidad solicitada. Faltan {$qtyToFulfill} unidades.");
                        }
                    }
                }

                $transfer->update(['status' => 'en_transito', 'sent_at' => now()]);
                \App\Models\TransferLog::create([
                    'transfer_id' => $transfer->id,
                    'user_id' => auth()->id(),
                    'action' => 'Enviado (En Tránsito)',
                ]);
            }
            elseif ($request->action === 'approve_override') {
                if ($transfer->status !== 'pending_override') {
                    throw new \Exception("Esta petición no está pendiente de autorización.");
                }
                if (!$isAdmin) {
                    throw new \Exception("Solo los administradores pueden autorizar salidas de lotes específicos.");
                }

                // Al aprobar override manual, pasa a en_proceso
                $transfer->update([
                    'status' => 'en_proceso',
                    'notes' => $transfer->notes . "\n[Autorizado por Admin]"
                ]);

                \App\Models\TransferLog::create([
                    'transfer_id' => $transfer->id,
                    'user_id' => auth()->id(),
                    'action' => 'Petición Manual Aprobada (Pasa a En Proceso)',
                ]);
            }
            elseif ($request->action === 'reject_request') {
                if (!in_array($transfer->status, ['pending', 'pending_override'])) {
                    throw new \Exception("No hay peticiones pendientes que rechazar.");
                }
                if ($transfer->status === 'pending' && !$isAdmin && !$isOrigin) {
                    throw new \Exception("No tienes permisos.");
                }
                if ($transfer->status === 'pending_override' && !$isAdmin) {
                    throw new \Exception("No tienes permisos.");
                }

                // No hay que regresar stock porque nunca se descontó
                $transfer->update([
                    'status' => 'rejected',
                    'notes' => $transfer->notes . "\n[Rechazado antes del envío]"
                ]);

                \App\Models\TransferLog::create([
                    'transfer_id' => $transfer->id,
                    'user_id' => auth()->id(),
                    'action' => 'Petición Rechazada',
                ]);
            }
            elseif ($request->action === 'cancel') {
                // Solo el origen puede cancelar, si sigue en estado en_proceso o empaquetado (antes de enviar)
                if (!in_array($transfer->status, ['en_proceso', 'empaquetado', 'en_transito'])) {
                    throw new \Exception("El traspaso no puede ser cancelado en este estado.");
                }
                if (!$isAdmin && !$isOrigin) {
                    throw new \Exception("No tienes permisos.");
                }
                
                // Revertir el stock de todo lo que ya fue descontado (quantity_sent > 0).
                // Aplica en tránsito, y también en en_proceso/empaquetado cuando la solicitud
                // fue aprobada (el stock se descuenta al aprobar).
                foreach ($transfer->items as $item) {
                    if ($item->batch_id && $item->quantity_sent > 0) {
                        $item->batch->increment('stock', $item->quantity_sent);
                        
                        \App\Models\InventoryMovement::create([
                            'branch_id' => $transfer->from_branch_id,
                            'batch_id' => $item->batch_id,
                            'user_id' => auth()->id(),
                            'type' => 'adjustment', // Cancelación retorna el stock
                            'quantity' => $item->quantity_sent,
                            'notes' => "Cancelación de traspaso #{$transfer->id}",
                        ]);
                    }
                }
                
                $transfer->update([
                    'status' => 'cancelled',
                    'notes' => $transfer->notes . "\n[Cancelado] " . $request->notes
                ]);

                \App\Models\TransferLog::create([
                    'transfer_id' => $transfer->id,
                    'user_id' => auth()->id(),
                    'action' => 'Traspaso Cancelado',
                ]);
            } 
            elseif ($request->action === 'reject') {
                if ($transfer->status !== 'en_transito') {
                    throw new \Exception("El traspaso no está en estado de ser rechazado.");
                }
                if ($user->hasRole('compras') && !$user->hasRole(['superadmin', 'admin'])) {
                    throw new \Exception("El perfil de compras no puede confirmar ni rechazar la recepción de traspasos.");
                }
                if (!$user->hasRole(['superadmin', 'admin']) && !$isDest) {
                    throw new \Exception("No tienes permisos.");
                }

                // Revertir el stock al origen
                foreach ($transfer->items as $item) {
                    if (!$item->batch_id || $item->quantity_sent <= 0) continue;
                    $item->batch->increment('stock', $item->quantity_sent);
                    
                    \App\Models\InventoryMovement::create([
                        'branch_id' => $transfer->from_branch_id,
                        'batch_id' => $item->batch_id,
                        'user_id' => auth()->id(),
                        'type' => 'adjustment',
                        'quantity' => $item->quantity_sent,
                        'notes' => "Rechazo de traspaso #{$transfer->id}",
                    ]);
                }

                $transfer->update([
                    'status' => 'rejected',
                    'received_by' => auth()->id(),
                    'received_at' => now(),
                    'notes' => $transfer->notes . "\n[Rechazado en destino] " . $request->notes
                ]);

                \App\Models\TransferLog::create([
                    'transfer_id' => $transfer->id,
                    'user_id' => auth()->id(),
                    'action' => 'Rechazado en Destino',
                ]);
            }
            elseif ($request->action === 'receive') {
                if ($transfer->status !== 'en_transito') {
                    throw new \Exception("El traspaso no está en estado de ser recibido.");
                }
                if ($user->hasRole('compras') && !$user->hasRole(['superadmin', 'admin'])) {
                    throw new \Exception("El perfil de compras no puede confirmar ni rechazar la recepción de traspasos.");
                }
                if (!$user->hasRole(['superadmin', 'admin']) && !$isDest) {
                    throw new \Exception("No tienes permisos.");
                }

                $allComplete = true;
                
                // Procesar la recepción de cada item (solo items que pertenecen a este traspaso)
                $receivedMap = collect($request->input('received_items', []))->keyBy('id');
                foreach ($transfer->items as $transferItem) {
                    if (!$transferItem->batch_id || $transferItem->quantity_sent <= 0) continue;

                    if (!$receivedMap->has($transferItem->id)) {
                        throw new \Exception("Debes indicar la cantidad recibida de todos los productos.");
                    }
                    $receivedQty = (int) $receivedMap[$transferItem->id]['quantity_received'];
                    
                    if ($receivedQty > $transferItem->quantity_sent) {
                        throw new \Exception("No puedes recibir más de lo que se envió.");
                    }
                    
                    $transferItem->update(['quantity_received' => $receivedQty]);
                    
                    if ($receivedQty < $transferItem->quantity_sent) {
                        $allComplete = false;
                    }
                    
                    if ($receivedQty > 0) {
                        // Crear el nuevo lote en la sucursal de destino si no existe, o incrementar uno existente
                        // Lo más sencillo es clonar el lote pero con la nueva branch_id
                        $newBatch = \App\Models\Batch::firstOrCreate(
                            [
                                'branch_id' => $transfer->to_branch_id,
                                'product_id' => $transferItem->batch->product_id,
                                'batch_number' => $transferItem->batch->batch_number,
                                'expiration_date' => $transferItem->batch->expiration_date,
                            ],
                            [
                                'cost_price' => $transferItem->batch->cost_price,
                                'selling_price' => $transferItem->batch->selling_price,
                                'stock' => 0
                            ]
                        );
                        
                        $newBatch->increment('stock', $receivedQty);
                        
                        // Registrar el movimiento de entrada
                        \App\Models\InventoryMovement::create([
                            'branch_id' => $transfer->to_branch_id,
                            'batch_id' => $newBatch->id,
                            'user_id' => auth()->id(),
                            'type' => 'transfer_in',
                            'quantity' => $receivedQty,
                            'notes' => "Recepción de traspaso #{$transfer->id}",
                        ]);
                    }
                    
                    // Si se recibió de menos, regresar el restante a la sucursal de origen
                    $missingQty = $transferItem->quantity_sent - $receivedQty;
                    if ($missingQty > 0) {
                        $transferItem->batch->increment('stock', $missingQty);
                        \App\Models\InventoryMovement::create([
                            'branch_id' => $transfer->from_branch_id,
                            'batch_id' => $transferItem->batch_id,
                            'user_id' => auth()->id(),
                            'type' => 'adjustment',
                            'quantity' => $missingQty,
                            'notes' => "Devolución automática por faltante en traspaso #{$transfer->id}",
                        ]);
                    }
                }
                
                $transfer->update([
                    'status' => $allComplete ? 'received' : 'partial',
                    'received_by' => auth()->id(),
                    'received_at' => now(),
                    'notes' => $request->notes ? ($transfer->notes . "\n[Nota de Recepción] " . $request->notes) : $transfer->notes
                ]);

                \App\Models\TransferLog::create([
                    'transfer_id' => $transfer->id,
                    'user_id' => auth()->id(),
                    'action' => $allComplete ? 'Recibido Completamente' : 'Recibido con Faltantes',
                ]);
            }

            \DB::commit();
            return back()->with('success', 'El traspaso fue procesado correctamente.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
