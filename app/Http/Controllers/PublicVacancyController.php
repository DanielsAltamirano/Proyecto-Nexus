<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicVacancyController extends Controller
{
    public function index()
    {
        $vacancies = \App\Models\Vacancy::where('is_active', true)
            ->withCount('applications')
            ->orderBy('created_at', 'desc')
            ->get();

        $userApplications = [];
        if (auth()->check()) {
            $userApplications = \App\Models\JobApplication::where('user_id', auth()->id())
                ->pluck('vacancy_id')
                ->toArray();
        }

        return view('vacancies.index', compact('vacancies', 'userApplications'));
    }

    public function apply(Request $request, \App\Models\Vacancy $vacancy)
    {
        if (!auth()->check() || !auth()->user()->hasRole('cliente')) {
            return back()->with('error', 'Debes iniciar sesión como cliente para aplicar.');
        }

        if (!$vacancy->is_active) {
            return back()->with('error', 'Esta vacante ya no está disponible.');
        }

        $alreadyApplied = \App\Models\JobApplication::where('vacancy_id', $vacancy->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($alreadyApplied) {
            return back()->with('error', 'Ya has enviado una solicitud para esta vacante.');
        }

        $request->validate([
            'cv_file' => 'required|file|mimes:pdf|max:5120',
            'answers' => 'nullable|array',
        ], [
            'cv_file.required' => 'Debes adjuntar tu CV en formato PDF.',
            'cv_file.mimes'    => 'El CV debe ser estrictamente un archivo PDF.',
            'cv_file.max'      => 'El CV no debe pesar más de 5MB.',
        ]);

        // Almacenamiento en disco privado (configurado en filesystems.php)
        $path = $request->file('cv_file')->store('cvs', 'private');

        // Empareja respuestas con las preguntas del formulario de esta vacante
        $answersData = [];
        $schema = $vacancy->form_schema ?? [];
        if (!empty($schema) && $request->answers) {
            foreach ($schema as $index => $field) {
                $answersData[] = [
                    'question' => $field['label'] ?? '',
                    'value'    => $request->answers[$index] ?? '',
                ];
            }
        }

        \App\Models\JobApplication::create([
            'vacancy_id' => $vacancy->id,
            'user_id'    => auth()->id(),
            'answers'    => $answersData,
            'cv_path'    => $path,
            'status'     => 'pending',
        ]);

        return back()->with('success', '¡Tu solicitud ha sido enviada exitosamente! Nos pondremos en contacto contigo.');
    }
}
