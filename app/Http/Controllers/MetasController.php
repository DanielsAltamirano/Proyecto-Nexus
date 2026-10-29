<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class MetasController extends Controller
{
    public function index()
    {
        $branches = Branch::with('goal')
            ->where('is_warehouse', false)
            ->orderBy('name')
            ->get();
        return view('metas.index', compact('branches'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'metas'                     => 'required|array',
            'metas.*.branch_id'         => 'required|exists:branches,id',
            'metas.*.morning_goal'      => 'nullable|numeric|min:0',
            'metas.*.afternoon_goal'    => 'nullable|numeric|min:0',
            'metas.*.is_shared_goal'    => 'nullable|boolean',
        ]);

        foreach ($validated['metas'] as $item) {
            \App\Models\BranchGoal::updateOrCreate(
                ['branch_id' => $item['branch_id']],
                [
                    'morning_goal'   => $item['morning_goal']   ?? 0,
                    'afternoon_goal' => $item['afternoon_goal'] ?? 0,
                    'is_shared_goal' => $item['is_shared_goal'] ?? false,
                ]
            );
        }

        return back()->with('success', 'Metas actualizadas correctamente.');
    }
}
