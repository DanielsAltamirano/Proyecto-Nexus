<?php

namespace App\Http\Controllers;

use App\Models\CashRegisterSession;
use App\Models\CashRegisterAudit;
use App\Models\DailyExpense;
use App\Models\Sale;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashRegisterController extends Controller
{
    // Obtener la sesión activa para la sucursal
    public function checkSession(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $session = CashRegisterSession::where('user_id', Auth::id())
            ->where('branch_id', $request->branch_id)
            ->where('status', 'open')
            ->first();

        if ($session) {
            // Calcular ventas actuales para esa sesión (las que están en tabla sales con este session_id)
            $sales = Sale::where('cash_register_session_id', $session->id)->get();
            $expenses = \App\Models\DailyExpense::where('cash_register_session_id', $session->id)->sum('amount');

            return response()->json([
                'has_open_session' => true,
                'session' => [
                    'id' => $session->id,
                    'start_time' => $session->start_time->format('Y-m-d H:i:s'),
                    'starting_cash' => $session->starting_cash,
                    'cash_sales' => $sales->sum('cash_payment'),
                    'card_sales' => $sales->sum('card_payment'),
                    'transfer_sales' => $sales->sum('transfer_payment'),
                    'total_sales' => $sales->sum('total_price'),
                    'expenses_total' => $expenses,
                ],
                'weekly_progress' => $this->getWeeklyProgress($request->branch_id)
            ]);
        }

        return response()->json([
            'has_open_session' => false,
            'weekly_progress' => $this->getWeeklyProgress($request->branch_id)
        ]);
    }

    // Abrir una nueva sesión de caja
    public function openSession(Request $request)
    {
        \Log::info('openSession hit', $request->all());
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'starting_cash' => 'required|numeric|min:0',
        ]);

        // Verificar si ya hay una abierta
        $existing = CashRegisterSession::where('user_id', Auth::id())
            ->where('branch_id', $request->branch_id)
            ->where('status', 'open')
            ->first();

        if ($existing) {
            \Log::info('openSession existing', $existing->toArray());
            return response()->json(['success' => false, 'message' => 'Ya existe un turno abierto en esta sucursal.'], 400);
        }

        $session = CashRegisterSession::create([
            'user_id' => Auth::id(),
            'branch_id' => $request->branch_id,
            'start_time' => now(),
            'starting_cash' => $request->starting_cash,
            'status' => 'open',
        ]);
        \Log::info('openSession created', $session->toArray());

        return response()->json([
            'success' => true, 
            'session_id' => $session->id,
            'weekly_progress' => $this->getWeeklyProgress($request->branch_id)
        ]);
    }

    // Cerrar la sesión de caja
    public function closeSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:cash_register_sessions,id',
            'actual_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $session = CashRegisterSession::where('id', $request->session_id)
            ->where('user_id', Auth::id())
            ->where('status', 'open')
            ->firstOrFail();

        $sales = Sale::where('cash_register_session_id', $session->id)->get();
        
        $cash_sales = $sales->sum('cash_payment');
        $card_sales = $sales->sum('card_payment');
        $transfer_sales = $sales->sum('transfer_payment');
        $total_sales = $sales->sum('total_price');

        $expected_cash = $this->computeExpectedCash($session, $cash_sales);
        $difference = $this->computeDifference($request->actual_cash, $expected_cash);

        $session->update([
            'end_time'        => now(),
            'expected_cash'   => $expected_cash,
            'actual_cash'     => $request->actual_cash,
            'difference'      => $difference,
            'cash_sales'      => $cash_sales,
            'card_sales'      => $card_sales,
            'transfer_sales'  => $transfer_sales,
            'total_sales'     => $total_sales,
            'status'          => 'closed',
            'notes'           => $request->notes,
        ]);

        return response()->json(['success' => true, 'redirect_url' => route('cash_register.ticket', $session->id)]);
    }

    /**
     * Cierre manual de un turno abierto (solo superadmin).
     * No requiere conteo de billetes ni confirmación de pago al doctor: cierra con el efectivo
     * que el sistema calcula en caja (Debe haber = Contado, diferencia $0). Si el doctor no fue
     * confirmado, el pago se calcula como en el cierre normal (tope al efectivo disponible) y el
     * excedente queda como 'Doctor pendiente'.
     */
    public function manualClose(Request $request, $id)
    {
        abort_unless(Auth::user()->hasRole('superadmin'), 403);

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $session = CashRegisterSession::where('id', $id)
            ->where('status', 'open')
            ->firstOrFail();

        $sales = Sale::where('cash_register_session_id', $session->id)->get();

        $cash_sales = $sales->sum('cash_payment');
        $card_sales = $sales->sum('card_payment');
        $transfer_sales = $sales->sum('transfer_payment');
        $total_sales = $sales->sum('total_price');

        $servicesTotal = (float) $sales->whereNotNull('service_id')->sum('total_price');

        $expected_cash = $this->computeExpectedCash($session, $cash_sales);

        $notes = 'CIERRE MANUAL por ' . Auth::user()->name . ' el ' . now()->format('d/m/Y H:i');
        if ($request->filled('notes')) {
            $notes .= ' - Motivo: ' . trim($request->notes);
        }

        $data = [
            'end_time'       => now(),
            'expected_cash'  => $expected_cash,
            'actual_cash'    => $expected_cash,
            'difference'     => 0,
            'cash_sales'     => $cash_sales,
            'card_sales'     => $card_sales,
            'transfer_sales' => $transfer_sales,
            'total_sales'    => $total_sales,
            'status'         => 'closed',
            'notes'          => $notes,
        ];

        // Doctor sin confirmar: registrar el monto de servicios para que el ticket/turnos muestren el pendiente.
        if (!$session->doctor_paid_at && $servicesTotal > 0) {
            $data['doctor_payment_amount'] = $servicesTotal;
        }

        $session->update($data);

        return response()->json(['success' => true]);
    }

    /**
     * Registra un gasto operativo del día vinculado a la sesión activa.
     */
    public function registerDailyExpense(Request $request)
    {
        $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'amount'     => 'required|numeric|min:0.01',
            'notes'      => 'nullable|string|max:255',
            'branch_id'  => 'required|exists:branches,id',
        ]);

        $session = CashRegisterSession::where('user_id', Auth::id())
            ->where('branch_id', $request->branch_id)
            ->where('status', 'open')
            ->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'No hay un turno abierto en esta sucursal.'], 422);
        }

        // === Validación de límite de gasto en caja ===
        // Fórmula: Fondo Inicial + Ventas Efectivo - Gastos ya registrados - Reserva Doctor (servicios)
        // La reserva del doctor (total de servicios) SIEMPRE se descuenta, ya que ese dinero
        // debe estar disponible para el pago, haya sido confirmado o no.
        $cashSales = Sale::where('cash_register_session_id', $session->id)->sum('cash_payment');

        $alreadySpent = DailyExpense::where('cash_register_session_id', $session->id)->sum('amount');

        // Total de servicios del turno = reserva intocable para el doctor
        $servicesReserve = Sale::where('cash_register_session_id', $session->id)
            ->whereNotNull('service_id')
            ->sum('total_price');

        $availableCash = (float) $session->starting_cash
            + (float) $cashSales
            - (float) $alreadySpent
            - (float) $servicesReserve;

        if ((float) $request->amount > $availableCash) {
            return response()->json([
                'success' => false,
                'message' => 'El gasto no puede registrarse porque supera el efectivo disponible en caja en este momento. Ajusta el monto o cancela la operación.',
            ], 422);
        }
        // === Fin validación de límite ===

        $dailyExpense = DailyExpense::create([
            'expense_id'              => $request->expense_id,
            'cash_register_session_id' => $session->id,
            'user_id'                 => Auth::id(),
            'branch_id'               => $request->branch_id,
            'amount'                  => $request->amount,
            'notes'                   => $request->notes,
        ]);

        $dailyExpense->load('expense');

        $totalExpenses = DailyExpense::where('cash_register_session_id', $session->id)->sum('amount');

        return response()->json([
            'success'        => true,
            'expense'        => [
                'id'          => $dailyExpense->id,
                'concept'     => $dailyExpense->expense->name,
                'amount'      => (float) $dailyExpense->amount,
                'notes'       => $dailyExpense->notes,
                'created_at'  => $dailyExpense->created_at->format('H:i'),
            ],
            'total_expenses' => (float) $totalExpenses,
        ]);
    }



    /**
     * Devuelve el resumen de servicios y gastos de la sesión activa para el corte ciego.
     */
    public function getSessionSummary(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:cash_register_sessions,id',
        ]);

        $session = CashRegisterSession::where('id', $request->session_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Determinar turno por horario
        $now = $session->start_time ?? now();
        $startTime = $now->format('H:i:s');
        $isMorning = $startTime >= '07:00:00' && $startTime <= '14:59:59';
        $shiftStart = $isMorning ? '07:00:00' : '15:00:00';
        $shiftEnd   = $isMorning ? '14:59:59' : '23:59:59';

        // Servicios cobrados en este turno para esta sucursal
        $serviceSales = Sale::with('service')
            ->where('cash_register_session_id', $session->id)
            ->whereNotNull('service_id')
            ->get();

        $servicesTotal = $serviceSales->sum('total_price');

        $servicesList = $serviceSales->groupBy('service_id')->map(function ($group) {
            $first = $group->first();
            return [
                'name'     => $first->service?->name ?? 'Servicio',
                'quantity' => $group->sum('quantity'),
                'amount'   => (float) $group->sum('total_price'),
            ];
        })->values();


        // Gastos del día registrados en esta sesión
        $expenses = DailyExpense::with('expense')
            ->where('cash_register_session_id', $session->id)
            ->get();

        $expensesTotal = $expenses->sum('amount');

        $expensesList = $expenses->map(fn($e) => [
            'id'      => $e->id,
            'concept' => $e->expense?->name ?? 'Gasto',
            'amount'  => (float) $e->amount,
            'notes'   => $e->notes,
        ])->values();

        return response()->json([
            'services_total'          => (float) $servicesTotal,
            'services_list'           => $servicesList,
            'expenses_total'          => (float) $expensesTotal,
            'expenses_list'           => $expensesList,
            'doctor_paid'             => !is_null($session->doctor_paid_at),
            'doctor_payment_amount'   => (float) ($session->doctor_payment_amount ?? 0),
        ]);
    }

    /**
     * Confirma el pago al doctor para esta sesión (descuenta del efectivo esperado al cerrar).
     */
    public function confirmDoctorPayment(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:cash_register_sessions,id',
            'amount'     => 'required|numeric|min:0',
        ]);

        $session = CashRegisterSession::where('id', $request->session_id)
            ->where('user_id', Auth::id())
            ->where('status', 'open')
            ->firstOrFail();

        if ($session->doctor_paid_at) {
            return response()->json(['success' => false, 'message' => 'El pago al doctor ya fue confirmado para este turno.'], 422);
        }

        $session->update([
            'doctor_paid_at'          => now(),
            'doctor_payment_amount'   => $request->amount,
        ]);

        return response()->json([
            'success'                => true,
            'doctor_payment_amount'  => (float) $request->amount,
            'message'                => 'Pago al doctor confirmado. $' . number_format($request->amount, 2) . ' se descontarán del corte.',
        ]);
    }

    /**
     * Efectivo esperado en caja: Fondo + Efectivo - Gastos - Pago a doctor/servicios (en efectivo).
     * El pago al doctor sale de la caja solo hasta el efectivo disponible; el excedente
     * (servicios cobrados con tarjeta/transferencia) queda como pendiente y NO se resta,
     * por lo que el esperado nunca es negativo.
     * Misma fórmula que computedExpectedCash en el POS.
     */
    private function computeExpectedCash(CashRegisterSession $session, $cashSales): float
    {
        $expenses = (float) DailyExpense::where('cash_register_session_id', $session->id)->sum('amount');
        $servicesTotal = (float) Sale::where('cash_register_session_id', $session->id)
            ->whereNotNull('service_id')
            ->sum('total_price');

        $docPayment = $session->doctor_paid_at
            ? (float) ($session->doctor_payment_amount ?: $servicesTotal)
            : $servicesTotal;

        $available = max((float) $session->starting_cash + (float) $cashSales - $expenses, 0);
        $docCashPayment = min($docPayment, $available);

        return round($available - $docCashPayment, 2);
    }

    /**
     * Diferencia = Contado - Debería haber.
     */
    private function computeDifference($actualCash, float $expectedCash): float
    {
        return round((float) $actualCash - max($expectedCash, 0), 2);
    }

    public function showTicket($id)
    {
        $session = CashRegisterSession::with(['user', 'branch'])->findOrFail($id);
        return view('sales.cut_ticket', compact('session'));
    }

    /**
     * Registra un evento de auditoría: el usuario llegó al paso 2 del corte
     * sin haber cerrado el turno previamente (posible manipulación).
     */
    public function logBreakdownOpened(Request $request)
    {
        $request->validate([
            'session_id'  => 'required|exists:cash_register_sessions,id',
            'actual_cash' => 'nullable|numeric',
        ]);

        $session = CashRegisterSession::where('id', $request->session_id)
            ->where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$session) {
            return response()->json(['ok' => false], 404);
        }

        $difference = null;
        if ($request->filled('actual_cash')) {
            $sales = Sale::where('cash_register_session_id', $session->id)->get();
            $cash_sales = $sales->sum('cash_payment');
            $expected_cash = $this->computeExpectedCash($session, $cash_sales);
            $difference = $this->computeDifference($request->actual_cash, $expected_cash);
        }

        CashRegisterAudit::create([
            'user_id'                  => Auth::id(),
            'cash_register_session_id' => $session->id,
            'branch_id'                => $session->branch_id,
            'event'                    => 'breakdown_opened',
            'actual_cash'              => $request->actual_cash,
            'difference'               => $difference,
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Vista de Turnos: historial de sesiones de caja con alertas de auditoría.
     * Solo accesible para admin y superadmin.
     */
    public function turnosIndex(Request $request)
    {
        $user = Auth::user();

        $selectedBranch = $request->input('branch_id', '');
        if (!$request->has('month')) {
            $selectedMonth = now()->format('Y-m');
        } else {
            $selectedMonth = $request->input('month', '');
        }

        $query = CashRegisterSession::with(['user', 'branch', 'audits', 'dailyExpenses.expense', 'sales.service'])
            ->orderBy('start_time', 'desc');

        if ($selectedMonth && $selectedMonth !== 'all') {
            $year  = substr($selectedMonth, 0, 4);
            $month = substr($selectedMonth, 5, 2);
            $query->whereYear('start_time', $year)->whereMonth('start_time', $month);
        }

        if ($user->hasRole(['superadmin', 'admin'])) {
            if ($selectedBranch) {
                $query->where('branch_id', $selectedBranch);
            }
        } else {
            // Personal de mostrador y demás solo ven su sucursal
            $query->where('branch_id', $user->branch_id);
        }

        $sessions = $query->paginate(25)->withQueryString();

        $branches = Branch::where('is_warehouse', false)->get();

        // Generar lista de meses disponibles
        $firstSession = CashRegisterSession::oldest('start_time')->first();
        $monthsList   = collect();
        if ($firstSession) {
            $current = now()->startOfMonth();
            $oldest  = $firstSession->start_time->startOfMonth();
            while ($current >= $oldest) {
                $monthsList->push($current->format('Y-m'));
                $current->subMonth();
            }
        } else {
            $monthsList->push(now()->format('Y-m'));
        }

        return view('sales.turnos', compact('sessions', 'branches', 'selectedBranch', 'selectedMonth', 'monthsList', 'user'));
    }

    private function getWeeklyProgress($branch_id)
    {
        $now = now();
        $startOfDay = $now->copy()->startOfDay();
        $endOfDay   = $now->copy()->endOfDay();
        $currentTime = $now->format('H:i:s');

        $isMorning = $currentTime >= '07:00:00' && $currentTime <= '14:59:59';
        $shiftName = $isMorning ? 'Matutino' : 'Vespertino';

        $goal = \App\Models\BranchGoal::where('branch_id', $branch_id)->first();
        if (!$goal) {
            return ['target' => 0, 'sales' => 0, 'shift' => $shiftName, 'percentage' => 0];
        }

        if ($goal->is_shared_goal) {
            // Meta compartida: toda la venta del día suma a una sola meta diaria
            $target    = $goal->morning_goal;
            $shiftName = 'Todo el día';
        } else {
            $target = $isMorning ? $goal->morning_goal : $goal->afternoon_goal;
        }

        // Solo ventas de HOY para la sucursal
        $salesQuery = Sale::whereHas('cashRegisterSession', function ($q) use ($branch_id) {
            $q->where('branch_id', $branch_id);
        })->whereBetween('created_at', [$startOfDay, $endOfDay]);

        // Si no es meta compartida, filtrar por horario del turno
        if (!$goal->is_shared_goal) {
            if ($isMorning) {
                $salesQuery->whereTime('created_at', '>=', '07:00:00')
                           ->whereTime('created_at', '<=', '14:59:59');
            } else {
                $salesQuery->whereTime('created_at', '>=', '15:00:00')
                           ->whereTime('created_at', '<=', '22:59:59');
            }
        }

        $dailySales = $salesQuery->whereNull('service_id')->sum('total_price');
        $percentage = $target > 0 ? min(100, round(($dailySales / $target) * 100)) : 0;

        return [
            'target'     => (float) $target,
            'sales'      => (float) $dailySales,
            'shift'      => $shiftName,
            'percentage' => $percentage,
        ];
    }
}
