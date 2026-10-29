<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashRegisterSession;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\Sale;
use App\Models\DailyExpense;
use App\Exports\VentasExport;
use App\Exports\CaducidadesExport;
use App\Exports\InventarioExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReporteController extends Controller
{
    /**
     * Vista principal con pestanas de Ventas y Caducidades.
     */
    public function index(Request $request)
    {
        $branches = Branch::orderBy('name')->get();

        // ---------- FILTROS VENTAS ----------
        $fechaDesde = $request->get('fecha_desde', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaHasta = $request->get('fecha_hasta', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $sucursalId = $request->get('sucursal_id', 'all');

        // ---------- FILTROS CADUCIDADES ----------
        $cadSucursalId  = $request->get('cad_sucursal_id', 'all');
        $cadDias        = $request->get('cad_dias', '30');
        $cadTab         = $request->get('tab', 'ventas');   // ventas | caducidades | inventario

        // ---------- FILTROS INVENTARIO ----------
        $invSucursalId = $request->get('inv_sucursal_id', 'all');

        // ============================
        // DATOS DE VENTAS
        // ============================
        $sessionsQuery = CashRegisterSession::with(['user', 'branch', 'sales', 'dailyExpenses'])
            ->whereNotNull('end_time')
            ->whereDate('end_time', '>=', $fechaDesde)
            ->whereDate('end_time', '<=', $fechaHasta);

        if ($sucursalId !== 'all') {
            $sessionsQuery->where('branch_id', $sucursalId);
        }

        $sessions = $sessionsQuery->get();

        // --- KPIs globales ---
        $totalVentas      = $sessions->sum('total_sales');
        $totalEfectivo    = $sessions->sum('cash_sales');
        $totalTarjeta     = $sessions->sum('card_sales');
        $totalTransfer    = $sessions->sum('transfer_sales');
        $totalGastos      = $sessions->sum(fn($s) => $s->dailyExpenses->sum('amount'));
        $gananciaNeta     = $totalVentas - $totalGastos;
        $totalTx          = Sale::whereIn('cash_register_session_id', $sessions->pluck('id'))->count();
        $ticketPromedio   = $totalTx > 0 ? round($totalVentas / $totalTx, 2) : 0;

        // --- Ventas por sucursal (grafica de barras) ---
        $ventasPorSucursal = $sessions->groupBy('branch_id')->map(function ($group) {
            return [
                'nombre' => optional($group->first()->branch)->name ?? 'Sin sucursal',
                'total'  => round($group->sum('total_sales'), 2),
            ];
        })->values();

        // --- Desglose metodos de pago (grafica pastel) ---
        $metodoPago = [
            'Efectivo'       => round($totalEfectivo, 2),
            'Tarjeta'        => round($totalTarjeta, 2),
            'Transferencia'  => round($totalTransfer, 2),
        ];

        // --- Top 10 productos mas vendidos ---
        $topProductos = Sale::with('product')
            ->whereIn('cash_register_session_id', $sessions->pluck('id'))
            ->whereNotNull('product_id')
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total_price) as total_monto')
            ->groupBy('product_id')
            ->orderByDesc('total_monto')
            ->limit(10)
            ->get();

        // --- Ranking cajeros ---
        $rankingCajeros = $sessions->groupBy('user_id')->map(function ($group) {
            return [
                'cajero' => optional($group->first()->user)->name ?? 'Desconocido',
                'total'  => round($group->sum('total_sales'), 2),
                'turnos' => $group->count(),
            ];
        })->sortByDesc('total')->values();

        // --- Ventas por dia (para grafica de linea) ---
        $ventasPorDia = $sessions->groupBy(fn($s) => Carbon::parse($s->end_time)->format('Y-m-d'))
            ->map(fn($g, $date) => ['fecha' => $date, 'total' => round($g->sum('total_sales'), 2)])
            ->sortBy('fecha')->values();

        // --- Filas para la tabla detallada ---
        $tablaVentas = $sessions->map(function ($s) {
            $gastos = $s->dailyExpenses->sum('amount');
            $txCount = $s->sales->count();
            return [
                'sucursal'       => optional($s->branch)->name ?? '-',
                'fecha'          => Carbon::parse($s->end_time)->format('d/m/Y'),
                'cajero'         => optional($s->user)->name ?? '-',
                'ticket_prom'    => $txCount > 0 ? round($s->total_sales / $txCount, 2) : 0,
                'efectivo'       => round($s->cash_sales, 2),
                'tarjeta'        => round($s->card_sales, 2),
                'transferencia'  => round($s->transfer_sales, 2),
                'total'          => round($s->total_sales, 2),
                'gastos'         => round($gastos, 2),
                'neta'           => round($s->total_sales - $gastos, 2),
                'transacciones'  => $txCount,
            ];
        })->sortByDesc('fecha')->values();

        // ============================
        // DATOS DE CADUCIDADES
        // ============================
        $today = Carbon::today();
        $limite = $cadDias === 'vencidos' ? $today : $today->copy()->addDays((int) $cadDias);

        $batchQuery = Batch::with(['product', 'branch'])
            ->where('stock', '>', 0)
            ->whereNotNull('expiration_date');

        if ($cadSucursalId !== 'all') {
            $batchQuery->where('branch_id', $cadSucursalId);
        }

        if ($cadDias === 'vencidos') {
            $batchQuery->where('expiration_date', '<', $today->format('Y-m-d'));
        } else {
            $batchQuery->where('expiration_date', '<=', $limite->format('Y-m-d'));
        }

        $tablaCaducidades = $batchQuery->orderBy('expiration_date')->get()->map(function ($b) use ($today) {
            $exp  = Carbon::parse($b->expiration_date);
            $dias = $today->diffInDays($exp, false);
            return [
                'producto'        => optional($b->product)->name ?? '-',
                'sku'             => optional($b->product)->sku ?? '-',
                'lote'            => $b->batch_number,
                'sucursal'        => optional($b->branch)->name ?? '-',
                'rack'            => trim(($b->rack ?? '') . ' / ' . ($b->shelf ?? ''), ' /'),
                'stock'           => $b->stock,
                'fecha_cad'       => $exp->format('d/m/Y'),
                'dias_restantes'  => (int) $dias,
                'estado'          => $dias < 0 ? 'VENCIDO' : ($dias <= 30 ? 'CRITICO' : ($dias <= 60 ? 'PROXIMO' : 'ADVERTENCIA')),
            ];
        });

        $cadVencidos  = $tablaCaducidades->where('estado', 'VENCIDO')->count();
        $cadCriticos  = $tablaCaducidades->where('estado', 'CRITICO')->count();
        $cadProximos  = $tablaCaducidades->where('estado', 'PROXIMO')->count();
        $cadAdvertencia = $tablaCaducidades->where('estado', 'ADVERTENCIA')->count();

        // ============================
        // DATOS DE INVENTARIO
        // ============================
        $invQuery = Batch::with(['product', 'branch'])->where('stock', '>', 0);
        if ($invSucursalId !== 'all') {
            $invQuery->where('branch_id', $invSucursalId);
        }
        $invBatches = $invQuery->get();

        $invTotalPiezas = $invBatches->sum('stock');
        $invInversion = $invBatches->sum(fn($b) => $b->stock * $b->cost_price);
        $invValorPublico = $invBatches->sum(fn($b) => $b->stock * $b->selling_price);
        $invSkusUnicos = $invBatches->pluck('product_id')->unique()->count();

        // Distribución por sucursal
        $invPorSucursal = $invBatches->groupBy('branch_id')->map(function ($group) {
            return [
                'nombre' => optional($group->first()->branch)->name ?? 'Sin sucursal',
                'piezas' => $group->sum('stock'),
                'valor'  => $group->sum(fn($b) => $b->stock * $b->cost_price),
            ];
        })->values();

        // Productos bajo stock (<= 5 unidades de un lote)
        $invBajoStock = $invBatches->filter(fn($b) => $b->stock <= 5)->sortBy('stock')->take(50)->values();

        return view('reportes.index', compact(
            'branches',
            // ventas
            'fechaDesde', 'fechaHasta', 'sucursalId',
            'totalVentas', 'totalEfectivo', 'totalTarjeta', 'totalTransfer',
            'totalGastos', 'gananciaNeta', 'totalTx', 'ticketPromedio',
            'ventasPorSucursal', 'metodoPago', 'topProductos',
            'rankingCajeros', 'ventasPorDia', 'tablaVentas',
            // caducidades
            'cadTab', 'cadSucursalId', 'cadDias',
            'tablaCaducidades', 'cadVencidos', 'cadCriticos', 'cadProximos', 'cadAdvertencia',
            // inventario
            'invSucursalId', 'invTotalPiezas', 'invInversion', 'invValorPublico', 'invSkusUnicos',
            'invPorSucursal', 'invBajoStock'
        ));
    }

    /**
     * Exportar ventas a Excel.
     */
    public function exportarVentas(Request $request)
    {
        $fechaDesde = $request->get('fecha_desde', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaHasta = $request->get('fecha_hasta', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $sucursalId = $request->get('sucursal_id', 'all');

        $sessionsQuery = CashRegisterSession::with(['user', 'branch', 'sales', 'dailyExpenses'])
            ->whereNotNull('end_time')
            ->whereDate('end_time', '>=', $fechaDesde)
            ->whereDate('end_time', '<=', $fechaHasta);

        if ($sucursalId !== 'all') {
            $sessionsQuery->where('branch_id', $sucursalId);
        }

        $sessions = $sessionsQuery->get();

        $totalVentas = round($sessions->sum('total_sales'), 2);
        $totalGastos = round($sessions->sum(fn($s) => $s->dailyExpenses->sum('amount')), 2);
        $gananciaNeta = round($totalVentas - $totalGastos, 2);

        $rows = $sessions->map(function ($s) {
            $gastos  = $s->dailyExpenses->sum('amount');
            $txCount = $s->sales->count();
            return [
                optional($s->branch)->name ?? '-',
                Carbon::parse($s->end_time)->format('d/m/Y'),
                optional($s->user)->name ?? '-',
                $txCount > 0 ? round($s->total_sales / $txCount, 2) : 0,
                round($s->cash_sales, 2),
                round($s->card_sales, 2),
                round($s->transfer_sales, 2),
                round($s->total_sales, 2),
                round($gastos, 2),
                round($s->total_sales - $gastos, 2),
                $txCount,
            ];
        })->toArray();

        // Top 10 productos
        $topRows = Sale::with('product')
            ->whereIn('cash_register_session_id', $sessions->pluck('id'))
            ->whereNotNull('product_id')
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total_price) as total_monto')
            ->groupBy('product_id')
            ->orderByDesc('total_monto')
            ->limit(10)
            ->get()
            ->map(fn($tp, $i) => [
                $i + 1,
                optional($tp->product)->name ?? 'Producto eliminado',
                (int) $tp->total_qty,
                round($tp->total_monto, 2),
            ])->values()->toArray();

        // Ranking cajeros
        $rankingRows = $sessions->groupBy('user_id')->map(function ($group) {
            return [
                'cajero' => optional($group->first()->user)->name ?? 'Desconocido',
                'total'  => round($group->sum('total_sales'), 2),
                'turnos' => $group->count(),
            ];
        })->sortByDesc('total')->values()
        ->map(fn($c, $i) => [$i + 1, $c['cajero'], $c['turnos'], $c['total']])
        ->values()->toArray();

        $filename = 'reporte_ventas_' . $fechaDesde . '_a_' . $fechaHasta . '.xlsx';
        return Excel::download(new VentasExport($rows, $topRows, $rankingRows, $totalVentas, $totalGastos, $gananciaNeta), $filename);
    }

    /**
     * Exportar caducidades a Excel.
     */
    public function exportarCaducidades(Request $request)
    {
        $cadSucursalId = $request->get('cad_sucursal_id', 'all');
        $cadDias       = $request->get('cad_dias', '30');
        $today         = Carbon::today();
        $limite        = $cadDias === 'vencidos' ? $today : $today->copy()->addDays((int) $cadDias);

        $batchQuery = Batch::with(['product', 'branch'])
            ->where('stock', '>', 0)
            ->whereNotNull('expiration_date');

        if ($cadSucursalId !== 'all') {
            $batchQuery->where('branch_id', $cadSucursalId);
        }

        if ($cadDias === 'vencidos') {
            $batchQuery->where('expiration_date', '<', $today->format('Y-m-d'));
        } else {
            $batchQuery->where('expiration_date', '<=', $limite->format('Y-m-d'));
        }

        $rows = $batchQuery->orderBy('expiration_date')->get()->map(function ($b) use ($today) {
            $exp  = Carbon::parse($b->expiration_date);
            $dias = (int) $today->diffInDays($exp, false);
            return [
                optional($b->product)->name ?? '-',
                optional($b->product)->sku ?? '-',
                $b->batch_number,
                optional($b->branch)->name ?? '-',
                trim(($b->rack ?? '') . ' / ' . ($b->shelf ?? ''), ' /'),
                $b->stock,
                $exp->format('d/m/Y'),
                $dias,
                $dias < 0 ? 'VENCIDO' : ($dias <= 30 ? 'CRITICO' : ($dias <= 60 ? 'PROXIMO' : 'ADVERTENCIA')),
            ];
        })->toArray();

        return Excel::download(new CaducidadesExport($rows), 'reporte_caducidades.xlsx');
    }

    /**
     * Exportar inventario a Excel.
     */
    public function exportarInventario(Request $request)
    {
        $invSucursalId = $request->get('inv_sucursal_id', 'all');

        $batchQuery = Batch::with(['product.category', 'branch'])->where('stock', '>', 0);
        
        if ($invSucursalId !== 'all') {
            $batchQuery->where('branch_id', $invSucursalId);
        }

        $rows = $batchQuery->get()->map(function ($b) {
            return [
                optional($b->product)->name ?? '-',
                optional($b->product)->sku ?? '-',
                optional(optional($b->product)->category)->name ?? '-',
                $b->batch_number,
                optional($b->branch)->name ?? '-',
                trim(($b->rack ?? '') . ' / ' . ($b->shelf ?? ''), ' /'),
                $b->stock,
                round($b->cost_price, 2),
                round($b->selling_price, 2),
                round($b->stock * $b->cost_price, 2),
                round($b->stock * $b->selling_price, 2),
            ];
        })->toArray();

        return Excel::download(new InventarioExport($rows), 'reporte_inventario.xlsx');
    }
}
