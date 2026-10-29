<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminVacancyController extends Controller
{
    public function index()
    {
        $vacancies = \App\Models\Vacancy::withCount('applications')->orderBy('created_at', 'desc')->get();
        return view('admin.vacancies.index', compact('vacancies'));
    }

    public function create()
    {
        return view('admin.vacancies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'requirements' => 'nullable|string',
            'form_schema'  => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $validated['created_by']  = auth()->id();
        $validated['form_schema'] = $request->form_schema
            ? json_decode($request->form_schema, true)
            : [];

        \App\Models\Vacancy::create($validated);

        return redirect()->route('admin.vacancies.index')->with('success', 'Vacante creada y publicada exitosamente.');
    }

    public function edit(\App\Models\Vacancy $vacancy)
    {
        return view('admin.vacancies.edit', compact('vacancy'));
    }

    public function update(Request $request, \App\Models\Vacancy $vacancy)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'requirements' => 'nullable|string',
            'form_schema'  => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $validated['form_schema'] = $request->form_schema
            ? json_decode($request->form_schema, true)
            : [];

        $vacancy->update($validated);

        return redirect()->route('admin.vacancies.index')->with('success', 'Vacante actualizada exitosamente.');
    }

    public function destroy(\App\Models\Vacancy $vacancy)
    {
        $vacancy->delete();
        return redirect()->route('admin.vacancies.index')->with('success', 'Vacante eliminada.');
    }

    // Visor seguro de PDF (solo admin/superadmin)
    public function viewCv(\App\Models\JobApplication $application)
    {
        if (!Storage::disk('private')->exists($application->cv_path)) {
            abort(404, 'CV no encontrado.');
        }

        $file     = Storage::disk('private')->path($application->cv_path);
        $filename = 'cv_' . str_replace(' ', '_', $application->applicant->name) . '.pdf';

        return response()->file($file, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function updateStatus(Request $request, \App\Models\JobApplication $application)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,rejected,accepted',
        ]);
        $application->update($validated);
        return back()->with('success', 'Estado actualizado.');
    }

    // JSON endpoint para el modal AJAX del buzón
    public function applicationsJson(\App\Models\Vacancy $vacancy)
    {
        $apps = $vacancy->applications()
            ->with('applicant:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($a) => [
                'id'             => $a->id,
                'applicant'      => $a->applicant,
                'answers'        => $a->answers ?? [],
                'cv_path'        => $a->cv_path,
                'status'         => $a->status,
                'created_at_human' => $a->created_at->diffForHumans(),
            ]);

        return response()->json($apps);
    }
}
