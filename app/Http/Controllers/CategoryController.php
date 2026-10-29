<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       // Traemos todas las categorías ordenadas por nombre
        $categories = Category::orderBy('name', 'asc')->get();
        
        // Pasamos la variable a la vista usando compact()
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validar los datos que envía el usuario
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ], [
            // Mensajes personalizados en español
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.unique' => 'Ya existe una categoría con este nombre en el sistema.',
        ]);

        // 2. Guardar en la base de datos
        Category::create([
            'name' => $request->name,
        ]);

        // 3. Redirigir de vuelta al listado con un mensaje de éxito
        return redirect()->route('categories.index')->with('success', 'Categoría creada correctamente.');
    }
    /**
     * Store a newly created resource via AJAX.
     */
    public function storeApi(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ], [
            'name.unique' => 'Ya existe una categoría con este nombre.',
            'name.required' => 'El nombre es obligatorio.',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Categoría creada correctamente.'
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
 // Cambia (string $id) por (Category $category)
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    // Cambia (Request $request, string $id) por (Request $request, Category $category)
    public function update(Request $request, Category $category)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        'description' => 'nullable|string', // Valida que la descripción sea opcional pero texto
    ]);

    // Opción A: Actualizar todo lo que viene en el request (si el fillable está bien)
    //$category->update($request->all());

    // Opción B (Más segura): Asignar manualmente
    $category->name = $request->name;
    $category->description = $request->description;
    $category->save();
    
    return redirect()->route('categories.index')->with('success', 'Categoría actualizada correctamente.');
}

    // Cambia (string $id) por (Category $category)
    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Categoría eliminada.');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', 'No se puede eliminar: hay medicamentos registrados en esta categoría.');
        }
    }
}
