<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Batch;
use App\Http\Requests\StoreSaleRequest;
use App\Services\AuditOutboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Services\PromoService;

class SaleController extends Controller
{
    // Mostrar el historial de ventas
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Sale::with(['product', 'batch', 'user', 'batch.branch', 'service', 'cashRegisterSession.branch']);
        
        // Filters
        $selectedBranch = $request->input('branch_id', '');
        
        // Initial load defaults to current month. If explicitly passed 'all', it will not filter.
        if (!$request->has('month')) {
            $selectedMonth = now()->format('Y-m');
        } else {
            $selectedMonth = $request->input('month', '');
        }

        // Apply Month filter
        if ($selectedMonth && $selectedMonth !== 'all') {
            $year = substr($selectedMonth, 0, 4);
            $month = substr($selectedMonth, 5, 2);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        }

        // Si es Personal de Mostrador, solo ver sus propias ventas de hoy
        if ($user->hasRole('Personal de Mostrador')) {
            $query->where('user_id', $user->id)
                  ->whereDate('created_at', now()->toDateString());
        } elseif ($user->hasRole('compras')) {
            // El rol de compras ve sus propias ventas
            $query->where('user_id', $user->id);
        } elseif (!$user->hasRole(['superadmin', 'admin'])) {
            // Otros roles que no sean superadmin/admin ven los lotes de su sucursal
            $query->whereHas('batch', function($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        } else {
            // Apply Branch filter for superadmin/admin
            if ($selectedBranch) {
                $query->where(function($q) use ($selectedBranch) {
                    $q->whereHas('batch', function($qb) use ($selectedBranch) {
                        $qb->where('branch_id', $selectedBranch);
                    })->orWhereHas('cashRegisterSession', function($qs) use ($selectedBranch) {
                        $qs->where('branch_id', $selectedBranch);
                    });
                });
            }
        }
        
        $sales = $query->latest()->get();
        
        // Group by receipt_id for displaying as tickets
        $tickets = $sales->groupBy(function($sale) {
            return $sale->receipt_id ?: 'LEGACY-' . $sale->id;
        });

        $branches = [];
        if ($user->hasRole(['superadmin', 'admin'])) {
            $branches = \App\Models\Branch::where('is_warehouse', false)->get();
        }

        // Generate months list based on oldest sale
        $firstSale = Sale::oldest('created_at')->first();
        $monthsList = collect();
        if ($firstSale) {
            $current = now()->startOfMonth();
            $oldest = $firstSale->created_at->startOfMonth();
            while ($current >= $oldest) {
                $monthsList->push($current->format('Y-m'));
                $current->subMonth();
            }
        } else {
            $monthsList->push(now()->format('Y-m'));
        }

        return view('sales.index', compact('tickets', 'branches', 'selectedBranch', 'selectedMonth', 'user', 'monthsList'));
    }

    // Mostrar la pantalla del Punto de Venta
    public function create(PromoService $promoService)
    {
        $user = auth()->user();
        $cutoffDate = now()->subDays(60);
        
        // Enviamos a la vista los productos con stock disponible.
        // Ordenados por popularidad reciente: número de ventas en los últimos 60 días.
        $products = Product::whereHas('batches', function ($query) use ($user) {
            $query->where('stock', '>', 0)
                  ->where(function($q) {
                      $q->where('expiration_date', '>', now())
                        ->orWhereNull('expiration_date');
                  });
            
            if (!$user->hasRole(['superadmin', 'admin', 'compras'])) {
                $query->where('branch_id', $user->branch_id);
            }
        })->with(['batches' => function ($query) use ($user) {
            $query->where('stock', '>', 0)
                  ->where(function($q) {
                      $q->where('expiration_date', '>', now())
                        ->orWhereNull('expiration_date');
                  })
                  ->orderBy('expiration_date', 'asc');
            
            if (!$user->hasRole(['superadmin', 'admin', 'compras'])) {
                $query->where('branch_id', $user->branch_id);
            }
        }])
        ->withCount(['sales' => function ($q) use ($cutoffDate) {
            // Contamos solo ventas recientes (últimos 60 días) para que el catálogo
            // refleje los productos más populares ACTUALMENTE, no históricamente.
            $q->where('created_at', '>=', $cutoffDate);
        }])
        ->orderByDesc('sales_count')
        ->get();

        $services = \App\Models\Service::withCount(['sales' => function ($q) use ($cutoffDate) {
            $q->where('created_at', '>=', $cutoffDate);
        }])
            ->orderByDesc('sales_count')
            ->get();

        $activePromos = $promoService->getActivePromos();
        $settings = $promoService->getSettings();
        
        $todayName = $activePromos['current_day'];
        $shift = $activePromos['current_shift'];
        $userBranchId = $user->branch_id ?? null;

        $isGenericoPromo = $activePromos['generico'] ?? false;
        $genericoDiscount = $settings['generico_discount'] ?? 25;
        
        $isRecetaPromo = $activePromos['receta'] ?? false;
        $recetaDiscount = $settings['receta_discount'] ?? 20;

        $products->each(function ($p) use ($promoService, $todayName, $shift, $userBranchId) {
            $is4x3 = (bool) ($p->is_promo_4x3 && $promoService->checkProductPromoTime($p, 'promo_4x3', $todayName, $shift, $userBranchId));
            $isVol = (bool) ($p->is_promo_volumen && $promoService->checkProductPromoTime($p, 'promo_volumen', $todayName, $shift, $userBranchId));
            $p->setAttribute('is_promo_4x3_active', $is4x3);
            $p->setAttribute('is_promo_volumen_active', $isVol);

            // Pre-cálculo para evitar iteraciones de lotes en JavaScript
            $firstBatch = $p->batches->first();
            $p->setAttribute('total_stock', (int) $p->batches->sum('stock'));
            $p->setAttribute('base_price', (float) ($firstBatch?->selling_price ?? 0));
        });

        $services->each(function ($s) {
            $s->setAttribute('total_stock', 999);
            $s->setAttribute('base_price', (float) $s->price);
            $s->setAttribute('is_promo_4x3_active', false);
            $s->setAttribute('is_promo_volumen_active', false);
        });

        $is4x3Promo = $products->contains(fn($p) => !empty($p->is_promo_4x3_active));
        $isVolumenPromo = $products->contains(fn($p) => !empty($p->is_promo_volumen_active));

        return view('sales.pos', compact(
            'products', 'services', 
            'isGenericoPromo', 'genericoDiscount', 
            'isRecetaPromo', 'recetaDiscount', 
            'is4x3Promo', 'isVolumenPromo',
            'todayName', 'shift'
        ));
    }


    // Procesar la venta y descontar inventario (FIFO) de un carrito completo
    public function store(StoreSaleRequest $request, PromoService $promoService)
    {
        $cart = is_array($request->cart) ? $request->cart : json_decode($request->cart, true);
        if(!is_array($cart) || empty($cart)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'El carrito está vacío.'], 422);
            }
            return redirect()->back()->with('error', 'El carrito está vacío.')->withInput();
        }

        $user = auth()->user();
        $branchId = $user->hasRole(['superadmin', 'admin', 'compras']) ? $request->branch_id : $user->branch_id;
        
        if (!$branchId) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Debe seleccionar una sucursal para la venta.'], 422);
            }
            return redirect()->back()->with('error', 'Debe seleccionar una sucursal para la venta.')->withInput();
        }

        try {
            $auditQueue = [];

            // Iniciamos la transacción segura en MySQL
            DB::transaction(function () use ($cart, $branchId, $request, $user, $promoService, &$auditQueue) {
                
                $session = \App\Models\CashRegisterSession::where('user_id', $user->id)
                    ->where('branch_id', $branchId)
                    ->where('status', 'open')
                    ->first();

                if (!$session) {
                    throw new \Exception("Debes abrir un turno (Fondo de Caja) antes de poder cobrar en esta sucursal.");
                }

                $receiptId = 'TK-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
                $request->merge(['receipt_id' => $receiptId]); // Para usarlo luego en el return
                
                $client = null;
                if ($request->filled('client_barcode')) {
                    $client = \App\Models\User::where('barcode', $request->client_barcode)->first();
                }

                $totalSalePrice = 0; // Para calcular puntos al final

                // Saldos disponibles para pagos mixtos
                $availableCash = $request->payment_method === 'mixto' ? (float) $request->mixed_cash : 0;
                $availableCard = $request->payment_method === 'mixto' ? (float) $request->mixed_card : 0;
                $availableTransfer = $request->payment_method === 'mixto' ? (float) $request->mixed_transfer : 0;
                
                foreach ($cart as $item) {
                    $quantityToSell = $item['quantity'];
                    $isService = $item['is_service'] ?? false;

                    if ($isService) {
                        $serviceId = $item['id'];
                        $service = \App\Models\Service::find($serviceId);
                        
                        if (!$service) {
                            throw new \Exception("Servicio no encontrado.");
                        }

                        // Usar el precio que envió el POS (puede tener descuento)
                        $unitPrice = isset($item['price']) ? (float) $item['price'] : (float) $service->price;
                        $lineTotal = $unitPrice * $quantityToSell;

                        // Distribuir el pago para esta línea
                        $lineCash = 0;
                        $lineCard = 0;
                        $lineTransfer = 0;

                        if ($request->payment_method === 'mixto') {
                            $remainingLine = $lineTotal;

                            $lineCash = min($availableCash, $remainingLine);
                            $availableCash -= $lineCash;
                            $remainingLine -= $lineCash;

                            $lineCard = min($availableCard, $remainingLine);
                            $availableCard -= $lineCard;
                            $remainingLine -= $lineCard;

                            $lineTransfer = min($availableTransfer, $remainingLine);
                            $availableTransfer -= $lineTransfer;
                        } else {
                            if ($request->payment_method === 'efectivo') $lineCash = $lineTotal;
                            elseif ($request->payment_method === 'tarjeta') $lineCard = $lineTotal;
                            elseif ($request->payment_method === 'transferencia') $lineTransfer = $lineTotal;
                        }

                        // Registrar la venta del servicio sin stock ni lotes
                        $serviceSale = Sale::create([
                            'receipt_id'        => $receiptId,
                            'service_id'        => $serviceId,
                            'user_id'           => Auth::id(),
                            'client_id'         => $client ? $client->id : null,
                            'quantity'          => $quantityToSell,
                            'total_price'       => $lineTotal,
                            'unit_price'        => $unitPrice,
                            'original_unit_price' => null,
                            'discount_pct'      => null,
                            'payment_method'    => $request->payment_method,
                            'amount_paid'       => $request->amount_paid,
                            'change_amount'     => $request->change_amount,
                            'cash_payment'      => $lineCash,
                            'card_payment'      => $lineCard,
                            'transfer_payment'  => $lineTransfer,
                            'cash_register_session_id' => $session->id,
                            'has_receta_completa' => false,
                            'status'            => Sale::STATUS_SURTIDA,
                        ]);

                        $auditQueue[] = [
                            'sale'           => $serviceSale,
                            'product'        => null,
                            'branch_id'      => $branchId,
                            'status_history' => [Sale::STATUS_SOLICITADA, Sale::STATUS_SURTIDA],
                        ];

                        $totalSalePrice += $lineTotal;
                        continue; // Pasar al siguiente item del carrito
                    }

                    // Es un producto, lógica de lotes (FIFO)
                    $productId = $item['id'];
                    $productModel = Product::find($productId);

                    if (!$productModel) {
                        throw new \Exception("El medicamento seleccionado no existe.");
                    }

                    // Regla de Negocio: Validar cédula médica si es Controlado
                    $cedulaMedica = trim((string) ($item['cedula_medica'] ?? $request->input('cedula_medica', '')));
                    if ($productModel->is_controlled && $cedulaMedica === '') {
                        throw new \Exception("El campo cedula_medica es obligatorio para vender el medicamento controlado: {$productModel->name}.");
                    }

                    // 1. Obtener lotes disponibles ordenados por caducidad (FIFO) de la sucursal correcta
                    $batches = Batch::where('product_id', $productId)
                        ->where('branch_id', $branchId)
                        ->where('stock', '>', 0)
                        ->where(function($q) {
                            $q->where('expiration_date', '>', now())
                              ->orWhereNull('expiration_date');
                        }) // Protege contra venta de caducados pero permite sin caducidad
                        ->orderBy('expiration_date', 'asc')
                        ->lockForUpdate() // Evita que otro usuario venda el mismo lote al mismo instante
                        ->get();

                    // 2. Bloqueo de venta si el stock relacional es igual a 0 o insuficiente
                    $totalAvailable = (int) $batches->sum('stock');
                    if ($totalAvailable <= 0) {
                        throw new \Exception("Venta bloqueada: El stock relacional para '{$productModel->name}' es igual a 0.");
                    }
                    if ($totalAvailable < $quantityToSell) {
                        throw new \Exception("Stock insuficiente para: {$productModel->name}. Solo hay {$totalAvailable} unidades aptas.");
                    }

                    // 3. Lógica FIFO y validación segura de precios
                    $basePrice = (float) $batches->first()->selling_price;
                    $applyReceta = !empty($item['applyRecetaCompleta']);
                    
                    $pricing = $promoService->calculateItemPricing($productModel, $quantityToSell, $basePrice, $applyReceta, $branchId);
                    
                    $cartUnitPrice       = $pricing['finalUnitPrice'];
                    $cartOriginalPrice   = $pricing['originalPrice'];
                    $cartDiscountPct     = $pricing['discountPct'];
                    $cartLineTotal       = $pricing['lineTotal'];

                    $remainingQtyToSell = $quantityToSell;
                    $remainingLineTotal = $cartLineTotal;

                    foreach ($batches as $batch) {
                        if ($remainingQtyToSell <= 0) break;

                        // ¿Cuánto podemos tomar de este lote específico?
                        $qtyToTakeFromBatch = min($batch->stock, $remainingQtyToSell);

                        if ($cartLineTotal !== null) {
                            $salePrice = round($remainingLineTotal * ($qtyToTakeFromBatch / $remainingQtyToSell), 2);
                            $remainingLineTotal -= $salePrice;
                            $unitPrice = round($salePrice / $qtyToTakeFromBatch, 2);
                        } else {
                            // Precio unitario cobrado: usar el del carrito si existe; fallback al lote
                            $unitPrice = $cartUnitPrice ?? (float) $batch->selling_price;
                            $salePrice = $unitPrice * $qtyToTakeFromBatch;
                        }

                        // Distribuir el pago para esta línea
                        $lineCash = 0;
                        $lineCard = 0;
                        $lineTransfer = 0;

                        if ($request->payment_method === 'mixto') {
                            $remainingLine = $salePrice;

                            $lineCash = min($availableCash, $remainingLine);
                            $availableCash -= $lineCash;
                            $remainingLine -= $lineCash;

                            $lineCard = min($availableCard, $remainingLine);
                            $availableCard -= $lineCard;
                            $remainingLine -= $lineCard;

                            $lineTransfer = min($availableTransfer, $remainingLine);
                            $availableTransfer -= $lineTransfer;
                        } else {
                            if ($request->payment_method === 'efectivo') $lineCash = $salePrice;
                            elseif ($request->payment_method === 'tarjeta') $lineCard = $salePrice;
                            elseif ($request->payment_method === 'transferencia') $lineTransfer = $salePrice;
                        }

                        // Estado inicial: Solicitada
                        $sale = Sale::create([
                            'receipt_id'               => $receiptId,
                            'product_id'               => $productId,
                            'batch_id'                 => $batch->id,
                            'user_id'                  => Auth::id(),
                            'client_id'                => $client ? $client->id : null,
                            'quantity'                 => $qtyToTakeFromBatch,
                            'total_price'              => $salePrice,
                            'unit_price'               => $unitPrice,
                            'original_unit_price'      => $cartOriginalPrice,
                            'discount_pct'             => $cartDiscountPct,
                            'payment_method'           => $request->payment_method,
                            'amount_paid'              => $request->amount_paid,
                            'change_amount'            => $request->change_amount,
                            'cash_payment'             => $lineCash,
                            'card_payment'             => $lineCard,
                            'transfer_payment'         => $lineTransfer,
                            'cash_register_session_id' => $session->id,
                            'has_receta_completa'      => $applyReceta,
                            'cedula_medica'            => $cedulaMedica ?: null,
                            'status'                   => Sale::STATUS_SOLICITADA,
                        ]);

                        $statusHistory = [Sale::STATUS_SOLICITADA];

                        // Si es controlado, pasa por el estado Validación tras verificar cedula_medica
                        if ($productModel->is_controlled) {
                            $sale->transitionTo(Sale::STATUS_VALIDACION);
                            $statusHistory[] = Sale::STATUS_VALIDACION;
                        }

                        $totalSalePrice += $salePrice;
                        
                        // Registrar el movimiento de salida
                        \App\Models\InventoryMovement::create([
                            'branch_id' => $branchId,
                            'batch_id' => $batch->id,
                            'user_id' => Auth::id(),
                            'type' => 'out', // Venta
                            'quantity' => $qtyToTakeFromBatch,
                            'notes' => 'Venta en POS',
                        ]);

                        // Descontar físicamente el stock de ese lote y marcar como Surtida
                        $batch->decrement('stock', $qtyToTakeFromBatch);
                        $sale->transitionTo(Sale::STATUS_SURTIDA);
                        $statusHistory[] = Sale::STATUS_SURTIDA;

                        $auditQueue[] = [
                            'sale'           => $sale,
                            'product'        => $productModel,
                            'branch_id'      => $branchId,
                            'status_history' => $statusHistory,
                        ];
                        
                        // Restar a lo que aún nos falta surtir
                        $remainingQtyToSell -= $qtyToTakeFromBatch;
                    }
                }

                if ($client && $totalSalePrice > 0) {
                    $pointsValue = \Illuminate\Support\Facades\Cache::get('points_value', 1);
                    $earnedPoints = floor($totalSalePrice * $pointsValue);
                    $client->increment('visits');
                    $client->increment('points', $earnedPoints);
                }
            });

            // Patrón Outbox: Una vez confirmada la transacción en MySQL,
            // intentamos registrar la bitácora inmutable en MongoDB. Si falla, se guarda en 'audit_outboxes'.
            foreach ($auditQueue as $entry) {
                AuditOutboxService::recordSaleAudit(
                    $entry['sale'],
                    $entry['product'],
                    [
                        'branch_id'      => $entry['branch_id'],
                        'status_history' => $entry['status_history'],
                    ]
                );
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venta registrada exitosamente.',
                    'ticket_id' => $request->receipt_id,
                    'ticket_url' => route('sales.ticket', $request->receipt_id),
                ]);
            }

            return redirect()->route('pos')
                ->with('success', 'Venta registrada exitosamente.')
                ->with('ticket_id', $request->receipt_id)
                ->with('clear_ui_ticket_id', $request->ui_ticket_id);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar la venta: ' . $e->getMessage(),
                ], 422);
            }
            return redirect()->back()->with('error', 'Error al procesar la venta: ' . $e->getMessage())->withInput();
        }
    }

    public function showTicket($receipt_id)
    {
        $items = Sale::with(['product', 'batch', 'user.branch', 'batch.branch', 'service'])->where('receipt_id', $receipt_id)->get();
        
        if ($items->isEmpty()) {
            return abort(404, 'Ticket no encontrado');
        }

        $firstItem = $items->first();
        
        // Seguridad: Si es cliente, solo puede ver sus propios tickets
        if (auth()->user()->hasRole('cliente') && $firstItem->client_id !== auth()->id()) {
            return abort(403, 'No tienes permiso para ver este ticket.');
        }

        $date = $firstItem->created_at->format('d/m/Y H:i:s');
        $cashier = $firstItem->user?->name ?? 'Cajero no disponible';
        
        // El ticket toma el nombre de la sucursal del lote, de la caja o del usuario
        $branch = $firstItem->batch?->branch?->name 
            ?? $firstItem->cashRegisterSession?->branch?->name 
            ?? $firstItem->user?->branch?->name 
            ?? 'Desconocida';
        
        $payment_method = $firstItem->payment_method ?? 'efectivo';
        $amount_paid = $firstItem->amount_paid;
        $change_amount = $firstItem->change_amount;
        $total = $items->sum('total_price');
        
        $mixed_cash = $items->sum('cash_payment');
        $mixed_card = $items->sum('card_payment');
        $mixed_transfer = $items->sum('transfer_payment');

        return view('sales.ticket', compact('receipt_id', 'items', 'date', 'cashier', 'branch', 'payment_method', 'amount_paid', 'change_amount', 'total', 'mixed_cash', 'mixed_card', 'mixed_transfer'));
    }
}