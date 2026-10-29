<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ProductRequest;
use App\Models\ProductRequestHistory;
use App\Models\Branch;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductRequestController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:superadmin|admin|compras', only: ['approveFromBodega', 'createPurchaseOrder']),
        ];
    }

    public function index()
    {
        $user = auth()->user();

        // Product requests
        $requestsQuery = ProductRequest::with(['product', 'branch', 'user'])
            ->orderBy('created_at', 'desc');
            
        if (!$user->hasRole(['superadmin', 'admin', 'compras'])) {
            $requestsQuery->where('user_id', $user->id);
        }
        $requests = $requestsQuery->paginate(50);

        // Batch Edit Requests
        $batchEditQuery = \App\Models\BatchEditRequest::with(['batch.product', 'batch.branch', 'user', 'resolver'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc');
            
        if (!$user->hasRole(['superadmin', 'admin'])) {
            $batchEditQuery->where('user_id', $user->id);
        }
        $batchEditRequests = $batchEditQuery->paginate(25, ['*'], 'batch_requests_page');

        // Service Edit Requests
        $serviceEditQuery = \App\Models\ServiceEditRequest::with(['service', 'user'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc');
            
        if (!$user->hasRole(['superadmin', 'admin'])) {
            $serviceEditQuery->where('user_id', $user->id);
        }
        $serviceEditRequests = $serviceEditQuery->paginate(25, ['*'], 'service_requests_page');

        return view('product_requests.index', compact('requests', 'batchEditRequests', 'serviceEditRequests'));
    }

    public function show(ProductRequest $productRequest)
    {
        $productRequest->load(['product', 'branch', 'user', 'histories.user', 'transfer', 'purchaseOrder']);
        
        // Verificar stock en bodega
        $bodega = Branch::where('is_warehouse', true)->first();
        $bodegaStock = 0;
        
        if ($bodega) {
            $bodegaStock = Batch::where('branch_id', $bodega->id)
                ->where('product_id', $productRequest->product_id)
                ->sum('stock');
        }

        return view('product_requests.show', compact('productRequest', 'bodega', 'bodegaStock'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $productRequest = ProductRequest::create([
                'product_id' => $request->product_id,
                'branch_id' => auth()->user()->branch_id,
                'user_id' => auth()->id(),
                'quantity' => $request->quantity,
                'notes' => $request->notes,
                'status' => 'pending'
            ]);

            ProductRequestHistory::create([
                'product_request_id' => $productRequest->id,
                'user_id' => auth()->id(),
                'action' => 'Creado',
                'details' => 'Solicitud iniciada por ' . auth()->user()->name
            ]);
        });

        return redirect()->back()->with('success', 'Solicitud enviada al área de compras exitosamente.');
    }

    public function approveFromBodega(ProductRequest $productRequest)
    {
        $bodega = Branch::where('is_warehouse', true)->first();
        if (!$bodega) {
            return redirect()->back()->with('error', 'No hay una bodega configurada en el sistema.');
        }

        DB::transaction(function () use ($productRequest, $bodega) {
            // Crear el Traspaso
            $transfer = \App\Models\Transfer::create([
                'from_branch_id' => $bodega->id,
                'to_branch_id' => $productRequest->branch_id,
                'created_by' => auth()->id(),
                'status' => 'pending',
                'notes' => 'Traspaso automático por Solicitud a Compras #' . $productRequest->id,
            ]);

            \App\Models\TransferItem::create([
                'transfer_id' => $transfer->id,
                'product_id' => $productRequest->product_id,
                'quantity' => $productRequest->quantity,
            ]);

            // Actualizar la solicitud
            $productRequest->update([
                'status' => 'fulfilled',
                'transfer_id' => $transfer->id,
            ]);

            ProductRequestHistory::create([
                'product_request_id' => $productRequest->id,
                'user_id' => auth()->id(),
                'action' => 'Enviado desde Bodega',
                'details' => 'Se generó el Traspaso #' . $transfer->id . ' para surtir esta solicitud.'
            ]);
        });

        return redirect()->route('product_requests.show', $productRequest)->with('success', 'Traspaso generado exitosamente desde la Bodega.');
    }

    public function createPurchaseOrder(ProductRequest $productRequest)
    {
        // Redirigir a crear Orden de Compra, pasando el ID de la solicitud
        // De esta forma, PurchaseOrderController sabrá a qué solicitud vincular la compra.
        return redirect()->route('purchase-orders.create', ['product_request_id' => $productRequest->id]);
    }
}
