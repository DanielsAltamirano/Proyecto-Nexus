<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Services\AuditOutboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Sale;
use App\Models\Batch;
use App\Models\Client;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class SaleController extends Controller
{
    /**
     * Registrar Venta (POST /api/v1/ventas)
     * Procesa una transacción de venta con validación de stock relacional (> 0),
     * exigencia de cedula_medica en controlados (Solicitada -> Validación -> Surtida)
     * y bitácora en MongoDB con respaldo en Patrón Outbox.
     */
    public function store(StoreSaleRequest $request)
    {
        $total = (float) $request->input('total');
        $clientId = $request->input('id_cliente');
        $qty = (int) $request->input('quantity', 1);
        $productId = $request->input('product_id');
        $cedulaMedica = $request->input('cedula_medica');

        // Validar si el cliente existe en CRM o en Users
        $clientName = 'Cliente General';
        if ($clientId) {
            $crmClient = Client::find($clientId);
            if ($crmClient) {
                $clientName = $crmClient->nombre . ($crmClient->empresa ? " ({$crmClient->empresa})" : "");
            } else {
                $userClient = User::find($clientId);
                if ($userClient) {
                    $clientName = $userClient->name;
                }
            }
        }

        // Usuario cajero / responsable
        $userId = auth()->id();
        if (!$userId) {
            $defaultUser = User::first();
            $userId = $defaultUser ? $defaultUser->id : 1;
        }

        $receiptId = 'TK-' . date('Ymd') . '-' . strtoupper(Str::random(5));
        $unitPrice = $total / max(1, $qty);
        $product = $productId ? Product::find($productId) : null;
        $statusHistory = [Sale::STATUS_SOLICITADA];

        $sale = DB::transaction(function () use (
            $receiptId, $clientId, $userId, $productId, $product,
            $qty, $unitPrice, $total, $request, $cedulaMedica, &$statusHistory
        ) {
            $batchId = null;

            if ($product) {
                $batch = Batch::where('product_id', $product->id)
                    ->where('stock', '>', 0)
                    ->where(function ($q) {
                        $q->where('expiration_date', '>', now())
                          ->orWhereNull('expiration_date');
                    })
                    ->orderBy('expiration_date', 'asc')
                    ->lockForUpdate()
                    ->first();

                if (!$batch || $batch->stock < $qty) {
                    abort(422, "Venta bloqueada: Stock insuficiente o igual a 0 para '{$product->name}'.");
                }

                $batchId = $batch->id;
                $batch->decrement('stock', $qty);
            }

            $saleRecord = Sale::create([
                'receipt_id'     => $receiptId,
                'client_id'      => $clientId,
                'user_id'        => $userId,
                'product_id'     => $productId,
                'batch_id'       => $batchId,
                'quantity'       => $qty,
                'unit_price'     => $unitPrice,
                'total_price'    => $total,
                'amount_paid'    => $total,
                'change_amount'  => 0,
                'payment_method' => $request->input('payment_method', 'efectivo'),
                'cash_payment'   => $total,
                'cedula_medica'  => $cedulaMedica,
                'status'         => Sale::STATUS_SOLICITADA,
            ]);

            if ($product && $product->is_controlled) {
                $saleRecord->transitionTo(Sale::STATUS_VALIDACION);
                $statusHistory[] = Sale::STATUS_VALIDACION;
            }

            $saleRecord->transitionTo(Sale::STATUS_SURTIDA);
            $statusHistory[] = Sale::STATUS_SURTIDA;

            return $saleRecord;
        });

        // Patrón Outbox hacia MongoDB
        AuditOutboxService::recordSaleAudit($sale, $product, [
            'status_history' => $statusHistory,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transacción de venta procesada exitosamente.',
            'data'    => [
                'id_venta'       => $sale->id,
                'folio'          => $sale->receipt_id,
                'id_cliente'     => $sale->client_id,
                'cliente_nombre' => $clientName,
                'total'          => (float) $sale->total_price,
                'metodo_pago'    => $sale->payment_method,
                'status'         => $sale->status,
                'status_flow'    => $statusHistory,
                'cedula_medica'  => $sale->cedula_medica,
                'fecha'          => $sale->created_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Consultar Venta (GET /api/v1/ventas/{id})
     * Obtiene el detalle de una venta específica.
     * Argumento: id_venta (entero)
     * Responsable: Cristian A. Perez
     */
    public function show($id)
    {
        $sale = Sale::with(['product', 'user'])->find($id);

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => "La venta con ID {$id} no fue encontrada.",
            ], 404);
        }

        // Obtener datos del cliente si existe
        $clientInfo = null;
        if ($sale->client_id) {
            $crmClient = Client::find($sale->client_id);
            if ($crmClient) {
                $clientInfo = [
                    'id'       => $crmClient->id,
                    'nombre'   => $crmClient->nombre,
                    'empresa'  => $crmClient->empresa,
                    'telefono' => $crmClient->telefono,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id_venta'       => $sale->id,
                'folio'          => $sale->receipt_id,
                'total'          => (float) $sale->total_price,
                'cantidad'       => $sale->quantity,
                'metodo_pago'    => $sale->payment_method,
                'fecha'          => $sale->created_at->format('Y-m-d H:i:s'),
                'cliente'        => $clientInfo,
                'producto'       => $sale->product ? [
                    'id'     => $sale->product->id,
                    'nombre' => $sale->product->name,
                    'sku'    => $sale->product->sku,
                ] : null,
                'atendido_por'   => $sale->user ? $sale->user->name : 'N/A',
            ],
        ], 200);
    }

    /**
     * Listar Ventas (GET /api/v1/ventas)
     * Obtiene el historial paginado de ventas.
     * Argumento: fecha_inicio, fecha_fin
     * Responsable: Daniels J. Altamirano
     */
    public function index(Request $request)
    {
        $query = Sale::with(['product', 'user'])->orderBy('created_at', 'desc');

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $perPage = (int) $request->input('per_page', 15);
        $ventas = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $ventas->items(),
            'meta'    => [
                'total_registros' => $ventas->total(),
                'pagina_actual'   => $ventas->currentPage(),
                'por_pagina'      => $ventas->perPage(),
                'total_paginas'   => $ventas->lastPage(),
                'filtros'         => [
                    'fecha_inicio' => $request->fecha_inicio,
                    'fecha_fin'    => $request->fecha_fin,
                ],
            ],
        ], 200);
    }
}
