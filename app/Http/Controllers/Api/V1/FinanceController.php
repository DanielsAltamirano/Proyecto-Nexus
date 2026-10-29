<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OperationalExpense;
use App\Models\Sale;
use Carbon\Carbon;

class FinanceController extends Controller
{
    /**
     * Registrar Gasto (POST /api/v1/finanzas/gastos)
     * Registra un gasto operativo de la empresa.
     * Argumento: JSON { "concepto": "Luz", "monto": 1200 }
     * Responsable: Juan M. Martinez
     */
    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'concepto' => 'required|string|max:255',
            'monto'    => 'required|numeric|min:0.01',
            'fecha'    => 'nullable|date',
        ]);

        $fecha = $request->filled('fecha') ? $request->input('fecha') : now()->toDateString();

        $expense = OperationalExpense::create([
            'concepto' => $validated['concepto'],
            'monto'    => $validated['monto'],
            'fecha'    => $fecha,
            'user_id'  => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gasto operativo registrado exitosamente en el sistema.',
            'data'    => [
                'id_gasto' => $expense->id,
                'concepto' => $expense->concepto,
                'monto'    => (float) $expense->monto,
                'fecha'    => $expense->fecha->format('Y-m-d'),
            ],
        ], 201);
    }

    /**
     * Reporte Financiero (GET /api/v1/finanzas/reporte)
     * Retorna ingresos y egresos del periodo.
     * Argumento: mes (int), anio (int)
     * Responsable: Gabriel Cazares
     */
    public function report(Request $request)
    {
        $mes = (int) $request->input('mes', now()->month);
        $anio = (int) $request->input('anio', now()->year);

        // Validar mes y año
        if ($mes < 1 || $mes > 12 || $anio < 2000 || $anio > 2100) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros inválidos. mes debe estar entre 1-12 y anio válido.',
            ], 422);
        }

        $startDate = Carbon::createFromDate($anio, $mes, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($anio, $mes, 1)->endOfMonth();

        // 1. Calcular Ingresos (Total de ventas del mes)
        $totalIngresos = (float) Sale::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_price');

        $conteoVentas = Sale::whereBetween('created_at', [$startDate, $endDate])->count();

        // 2. Calcular Egresos (Gastos operativos del mes)
        $totalEgresos = (float) OperationalExpense::whereBetween('fecha', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('monto');

        $conteoGastos = OperationalExpense::whereBetween('fecha', [$startDate->toDateString(), $endDate->toDateString()])->count();

        // 3. Balance neto
        $balanceNeto = $totalIngresos - $totalEgresos;

        return response()->json([
            'success' => true,
            'periodo' => [
                'mes'            => $mes,
                'anio'           => $anio,
                'fecha_inicio'   => $startDate->toDateString(),
                'fecha_fin'      => $endDate->toDateString(),
            ],
            'resumen' => [
                'total_ingresos' => round($totalIngresos, 2),
                'total_egresos'  => round($totalEgresos, 2),
                'balance_neto'   => round($balanceNeto, 2),
                'estado'         => $balanceNeto >= 0 ? 'Superávit' : 'Déficit',
            ],
            'metricas' => [
                'numero_transacciones_ventas' => $conteoVentas,
                'numero_registros_gastos'     => $conteoGastos,
            ],
        ], 200);
    }
}
