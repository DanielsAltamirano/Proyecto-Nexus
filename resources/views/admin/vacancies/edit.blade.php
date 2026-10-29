<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-blue-50 rounded-full opacity-70 blur-2xl"></div>
            <div class="relative z-10">
                <h2 class="font-extrabold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-blue-900 to-blue-600">
                    Editar Vacante
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $vacancy->title }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10" x-data="formBuilder(@js($vacancy->form_schema ?? []))">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form action="{{ route('admin.vacancies.update', $vacancy) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- SECCIÓN 1: INFORMACIÓN --}}
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-2xl p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-5 border-b pb-3">Información de la Vacante</h3>
                    <div class="space-y-5">
                        <div>
                            <label for="title" class="block text-sm font-bold text-gray-700 mb-1">Título del Puesto <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $vacancy->title) }}"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" required>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-bold text-gray-700 mb-1">Descripción del Puesto <span class="text-red-500">*</span></label>
                            <textarea name="description" id="description" rows="5"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" required>{{ old('description', $vacancy->description) }}</textarea>
                        </div>

                        <div>
                            <label for="requirements" class="block text-sm font-bold text-gray-700 mb-1">Requisitos Adicionales</label>
                            <textarea name="requirements" id="requirements" rows="3"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">{{ old('requirements', $vacancy->requirements) }}</textarea>
                        </div>

                        <div>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                    class="h-5 w-5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    {{ $vacancy->is_active ? 'checked' : '' }}>
                                <span class="text-sm font-bold text-gray-700">Publicada (visible en muro)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <br>

                {{-- SECCIÓN 2: CONSTRUCTOR DE FORMULARIO --}}
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-2xl p-6">
                    <div class="flex justify-between items-start mb-5 border-b pb-3">
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">Formulario de Postulación</h3>
                            <p class="text-sm text-gray-500 mt-0.5">El CV en PDF se solicita siempre. Agrega aquí las preguntas adicionales.</p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <template x-for="(field, index) in schema" :key="index">
                            <div class="flex items-start gap-3 bg-gray-50 p-4 rounded-xl border border-gray-200">
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Pregunta</label>
                                    <input type="text" x-model="field.label"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 text-sm">
                                </div>
                                <div class="w-44">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tipo</label>
                                    <select x-model="field.type"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 text-sm">
                                        <option value="text">Respuesta Corta</option>
                                        <option value="textarea">Respuesta Larga</option>
                                        <option value="checkbox">Casilla (Sí/No)</option>
                                    </select>
                                </div>
                                <div class="flex flex-col items-center justify-center pt-6">
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox" x-model="field.required"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm">
                                        <span class="text-xs font-bold text-gray-600">Requerido</span>
                                    </label>
                                </div>
                                <div class="flex items-center justify-center pt-5">
                                    <button type="button" @click="removeField(index)"
                                        class="text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-full transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="schema.length === 0" class="text-center py-6 text-gray-400 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                        <p class="text-sm font-medium">Sin preguntas adicionales. El candidato solo entregará su CV.</p>
                    </div>

                    <button type="button" @click="addField"
                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Agregar Pregunta
                    </button>

                    <input type="hidden" name="form_schema" :value="JSON.stringify(schema)">
                </div>

                <br>

                {{-- BOTONES --}}
                <div class="flex items-center justify-end gap-4 pb-4">
                    <a href="{{ route('admin.vacancies.index') }}"
                        class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-100 transition-all">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                        Guardar Cambios
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('formBuilder', (initialSchema) => ({
                schema: initialSchema || [],
                addField() {
                    this.schema.push({ label: '', type: 'text', required: true });
                },
                removeField(index) {
                    this.schema.splice(index, 1);
                }
            }));
        });
    </script>
</x-app-layout>
