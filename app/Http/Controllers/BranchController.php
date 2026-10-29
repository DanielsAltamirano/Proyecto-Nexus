<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'maps_url' => 'nullable|url|max:255',
        ]);

        Branch::create($request->all());

        return redirect()->back()->with('success', 'Sucursal registrada exitosamente.');
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'maps_url' => 'nullable|url|max:255',
        ]);

        $branch->update($request->all());

        return redirect()->back()->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->back()->with('success', 'Sucursal eliminada exitosamente.');
    }
}
