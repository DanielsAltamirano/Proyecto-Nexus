<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Constructor de Solicitud de Empleo') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="formBuilder()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                @if(session('success'))
                    <div class="mb-4 bg-green-50 text-green-800 p-4 rounded-lg font-bold">
                        {{ session('success') }}
                    </div>
                @endif
                
                <div class="mb-6 border-b pb-4">
                    <h3 class="text-lg font-bold text-gray-800">Preguntas Dinámicas</h3>
                    <p class="text-sm text-gray-600">
                        Agrega las preguntas que los candidatos deberán responder al postularse a cualquier vacante. (El CV en formato PDF se pedirá obligatoriamente al final sin necesidad de agregarlo aquí).
                    </p>
                </div>

                <!-- Form Builder UI -->
                <div class="space-y-4 mb-8">
                    <template x-for="(field, index) in schema" :key="index">
                        <div class="flex items-center gap-4 bg-gray-50 p-4 rounded border border-gray-200">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase">Pregunta</label>
                                <input type="text" x-model="field.label" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Ej. ¿Por qué quieres trabajar aquí?">
                            </div>
                            <div class="w-48">
                                <label class="block text-xs font-bold text-gray-500 uppercase">Tipo</label>
                                <select x-model="field.type" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="text">Respuesta Corta</option>
                                    <option value="textarea">Respuesta Larga</option>
                                    <option value="checkbox">Casilla (Sí/No)</option>
                                </select>
                            </div>
                            <div class="flex flex-col items-center justify-center pt-5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="field.required" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="text-sm font-bold text-gray-700">Obligatorio</span>
                                </label>
                            </div>
                            <div class="pt-5 pl-2">
                                <button type="button" @click="removeField(index)" class="text-red-500 hover:text-red-700 bg-red-50 p-2 rounded-full" title="Eliminar pregunta">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mb-8">
                    <button type="button" @click="addField" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Agregar Pregunta
                    </button>
                </div>

                <form action="{{ route('admin.form_template.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="schema" :value="JSON.stringify(schema)">
                    
                    <div class="flex justify-end pt-4 border-t">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow focus:outline-none">
                            Guardar Formulario
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('formBuilder', () => ({
                schema: @json($template->schema ?? []),
                
                init() {
                    if(this.schema.length === 0) {
                        this.addField();
                    }
                },
                
                addField() {
                    this.schema.push({
                        label: '',
                        type: 'text',
                        required: true
                    });
                },
                
                removeField(index) {
                    this.schema.splice(index, 1);
                }
            }));
        });
    </script>
</x-app-layout>
