<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of staff users.
     */
    public function indexStaff()
    {
        // Get users who are NOT 'cliente'.
        // Exclude superadmins if current user is not superadmin using the scope.
        $users = User::withoutSuperAdmins()
            ->whereHas('roles', function ($q) {
                $q->where('name', '!=', 'cliente');
            })
            ->with('roles')
            ->get();

        $roles = Role::withoutSuperAdmins()->get();
        // Filtrar bodega ya que no se le puede asignar personal
        $branches = \App\Models\Branch::where('is_warehouse', false)->get();

        return view('users.staff', compact('users', 'roles', 'branches'));
    }

    /**
     * Display a listing of client users.
     */
    public function indexClients()
    {
        // Get users who have 'cliente' role or no role at all.
        $users = User::withoutSuperAdmins()
            ->where(function ($q) {
                $q->whereHas('roles', function ($subQ) {
                    $subQ->where('name', 'cliente');
                })->orWhereDoesntHave('roles');
            })
            ->with('roles')
            ->get();

        $roles = Role::withoutSuperAdmins()->get();
        $pointsValue = \Illuminate\Support\Facades\Cache::get('points_value', 1);

        return view('users.clients', compact('users', 'roles', 'pointsValue'));
    }

    /**
     * Update the global points value.
     */
    public function updatePointsValue(Request $request)
    {
        $request->validate([
            'points_value' => 'required|numeric|min:0',
        ]);

        \Illuminate\Support\Facades\Cache::forever('points_value', $request->points_value);

        return back()->with('success', 'Valor de puntos actualizado correctamente.');
    }

    /**
     * Update the role and branch of the specified user.
     */
    public function updateStaff(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Prevención: No se puede asignar personal a la bodega
        if ($request->branch_id) {
            $branch = \App\Models\Branch::find($request->branch_id);
            if ($branch && $branch->is_warehouse) {
                return back()->with('error', 'No puedes asignar personal a la Bodega.');
            }
        }

        // Prevención extra: Un admin no puede asignarle superadmin a nadie si él no lo es
        if ($request->role === 'superadmin' && !auth()->user()->hasRole('superadmin')) {
            return back()->with('error', 'No tienes permisos para asignar este rol.');
        }

        // Prevención: No modificar a un superadmin si no se es superadmin
        if ($user->hasRole('superadmin') && !auth()->user()->hasRole('superadmin')) {
            return back()->with('error', 'Acción no permitida.');
        }

        // Prevención: No puedes cambiarte tu propio rol
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes cambiar tu propio rol.');
        }

        // Sync the new role
        $user->syncRoles([$request->role]);

        // Update the branch_id
        $user->update(['branch_id' => $request->branch_id]);

        return back()->with('success', 'Personal actualizado correctamente.');
    }

    /**
     * Store a newly created staff member.
     */
    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        if ($request->role === 'superadmin' && !auth()->user()->hasRole('superadmin')) {
            return back()->with('error', 'No tienes permisos para asignar el rol de superadmin.');
        }

        if ($request->branch_id) {
            $branch = \App\Models\Branch::find($request->branch_id);
            if ($branch && $branch->is_warehouse) {
                return back()->with('error', 'No puedes asignar personal a la Bodega.');
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'branch_id' => $request->branch_id,
        ]);

        $user->assignRole($request->role);

        return back()->with('success', 'Personal registrado correctamente.');
    }

    /**
     * Remove (soft delete) the specified user.
     */
    public function destroy(User $user)
    {
        // Prevención: No puedes darte de baja a ti mismo
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes darte de baja a ti mismo.');
        }

        // Prevención: No eliminar a un superadmin si no se es superadmin
        if ($user->hasRole('superadmin') && !auth()->user()->hasRole('superadmin')) {
            return back()->with('error', 'Acción no permitida.');
        }

        $user->delete();

        return back()->with('success', 'Usuario dado de baja exitosamente.');
    }

    public function getByBarcode($barcode)
    {
        $user = User::where('barcode', $barcode)->first();
        if (!$user) {
            return response()->json(['error' => 'No se encontró un cliente con este código.'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'barcode' => $user->barcode,
            'points' => $user->points,
            'visits' => $user->visits,
        ]);
    }
}
