<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Muestra el catálogo de conceptos de gastos.
     */
    public function index()
    {
        $expenses = Expense::orderBy('id', 'asc')->get();
        return view('expenses.index', compact('expenses'));
    }

    /**
     * Guarda un nuevo concepto de gasto en el catálogo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:120|unique:expenses,name',
            'description' => 'nullable|string|max:255',
        ]);

        Expense::create($request->only('name', 'description'));

        return redirect()->route('expenses.index')->with('success', 'Concepto de gasto creado correctamente.');
    }

    /**
     * Actualiza un concepto de gasto.
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'name'        => 'required|string|max:120|unique:expenses,name,' . $expense->id,
            'description' => 'nullable|string|max:255',
        ]);

        $expense->update($request->only('name', 'description'));

        return redirect()->route('expenses.index')->with('success', 'Concepto de gasto actualizado correctamente.');
    }

    /**
     * Elimina un concepto de gasto (solo si no tiene gastos registrados).
     */
    public function destroy(Expense $expense)
    {
        if ($expense->dailyExpenses()->count() > 0) {
            return redirect()->route('expenses.index')
                ->with('error', 'No se puede eliminar este concepto porque ya tiene gastos registrados.');
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Concepto de gasto eliminado.');
    }

    /**
     * Devuelve el catálogo de gastos como JSON (para el POS).
     */
    public function apiList()
    {
        $expenses = Expense::orderBy('name')->get(['id', 'name', 'description']);
        return response()->json($expenses);
    }
}
