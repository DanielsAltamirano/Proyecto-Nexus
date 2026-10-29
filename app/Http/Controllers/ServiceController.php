<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceEditRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'price' => 'required|numeric|min:0',
        ]);

        Service::create($request->all());

        return redirect()->back()->with('success', 'Servicio agregado correctamente.');
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'price' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        if ($user->hasRole('Doctor')) {
            ServiceEditRequest::create([
                'service_id' => $service->id,
                'user_id' => $user->id,
                'type' => 'update',
                'requested_changes' => [
                    'name' => $request->name,
                    'price' => $request->price,
                ],
                'reason' => $request->reason ?? 'Actualización solicitada por Doctor.',
            ]);
            return redirect()->back()->with('success', 'Solicitud de actualización enviada a administración.');
        }

        $service->update($request->all());
        return redirect()->back()->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Request $request, Service $service)
    {
        $user = Auth::user();

        if ($user->hasRole('Doctor')) {
            ServiceEditRequest::create([
                'service_id' => $service->id,
                'user_id' => $user->id,
                'type' => 'delete',
                'reason' => $request->reason ?? 'Eliminación solicitada por Doctor.',
            ]);
            return redirect()->back()->with('success', 'Solicitud de eliminación enviada a administración.');
        }

        $service->delete();
        return redirect()->back()->with('success', 'Servicio eliminado correctamente.');
    }

    public function approveEdit(ServiceEditRequest $serviceRequest)
    {
        if ($serviceRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'La solicitud ya fue procesada.');
        }

        $serviceRequest->update([
            'status' => 'approved',
            'resolved_by' => Auth::id(),
        ]);

        if ($serviceRequest->type === 'update') {
            $serviceRequest->service->update($serviceRequest->requested_changes);
        } elseif ($serviceRequest->type === 'delete') {
            $serviceRequest->service->delete();
        }

        return redirect()->back()->with('success', 'Solicitud aprobada y aplicada.');
    }

    public function rejectEdit(ServiceEditRequest $serviceRequest)
    {
        if ($serviceRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'La solicitud ya fue procesada.');
        }

        $serviceRequest->update([
            'status' => 'rejected',
            'resolved_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Solicitud rechazada.');
    }
}
