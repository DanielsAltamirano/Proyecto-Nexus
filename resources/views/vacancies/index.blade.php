<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-purple-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>

            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-purple-900 to-purple-600 tracking-tight">
                        Muro de Vacantes
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Únete al equipo de Nexus ERP
                    </p>
                </div>
                @if(auth()->check() && auth()->user()->hasRole(['admin', 'superadmin']))
                    <div class="mt-5 sm:mt-0 flex flex-col sm:flex-row gap-3 w-full sm:w-auto justify-end">
                        <a href="{{ route('admin.vacancies.index') }}"
                            class="group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-violet-500 rounded-xl hover:bg-violet-600 hover:-translate-y-1 hover:shadow-lg hover:shadow-violet-400/30 whitespace-nowrap">
                            <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span>Gestión de Vacantes</span>
                        </a>
                        <a href="{{ route('admin.vacancies.create') }}"
                            class="group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-purple-700 rounded-xl hover:bg-purple-800 hover:-translate-y-1 hover:shadow-lg hover:shadow-purple-500/30 whitespace-nowrap">
                            <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Nueva Vacante</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        showModal: false,
        selectedVacancy: null,
        selectedSchema: [],
        openApplyModal(vacancy, schema) {
            this.selectedVacancy = vacancy;
            this.selectedSchema = schema || [];
            this.showModal = true;
        }
    }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 flex items-center p-4 text-green-800 bg-green-50 border border-green-200 rounded-xl shadow-sm" role="alert">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 flex items-center p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm" role="alert">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm" role="alert">
                    <p class="font-bold mb-1">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-6">
                @forelse($vacancies as $vacancy)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        {{-- Admin Edit/Delete bar --}}
                        @if(auth()->check() && auth()->user()->hasRole(['admin', 'superadmin']))
                        <div class="flex items-center justify-end gap-2 px-4 py-2 bg-gray-50 border-b border-gray-100 text-xs font-bold">
                            <a href="{{ route('admin.vacancies.edit', $vacancy) }}" class="text-blue-600 hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Editar
                            </a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('admin.vacancies.index') }}" class="text-indigo-600 hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Buzón ({{ $vacancy->applications_count ?? 0 }})
                            </a>
                            <span class="text-gray-300">|</span>
                            <form action="{{ route('admin.vacancies.destroy', $vacancy) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta vacante?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                        @endif

                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-600 to-purple-800 flex items-center justify-center text-white font-extrabold text-lg shadow-sm flex-shrink-0">
                                    NX
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $vacancy->title }}</h3>
                                    <p class="text-sm text-gray-500">Nexus ERP • {{ $vacancy->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            <div class="mt-4 text-gray-700 leading-relaxed whitespace-pre-line">
                                {{ $vacancy->description }}
                            </div>

                            @if($vacancy->requirements)
                            <div class="mt-4 bg-purple-50 border border-purple-100 p-4 rounded-xl">
                                <h4 class="font-bold text-purple-900 mb-1.5 text-sm">Requisitos:</h4>
                                <p class="text-sm text-purple-800 whitespace-pre-line">{{ $vacancy->requirements }}</p>
                            </div>
                            @endif

                            <div class="mt-6 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    @if($vacancy->form_schema && count($vacancy->form_schema) > 0)
                                        <span class="text-xs bg-gray-100 text-gray-600 font-semibold px-3 py-1 rounded-full">
                                            {{ count($vacancy->form_schema) }} pregunta{{ count($vacancy->form_schema) > 1 ? 's' : '' }} adicional{{ count($vacancy->form_schema) > 1 ? 'es' : '' }}
                                        </span>
                                    @endif
                                    <span class="text-xs bg-gray-100 text-gray-600 font-semibold px-3 py-1 rounded-full">CV requerido (PDF)</span>
                                </div>
                                @if(in_array($vacancy->id, $userApplications))
                                    <span class="inline-flex items-center gap-1.5 bg-purple-50 text-purple-700 font-bold py-2 px-5 rounded-xl text-sm border border-purple-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Solicitud enviada
                                    </span>
                                @else
                                    <button
                                        @click="openApplyModal({{ json_encode(['id' => $vacancy->id, 'title' => $vacancy->title]) }}, {{ json_encode($vacancy->form_schema ?? []) }})"
                                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded-xl shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md">
                                        Postularse
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-16 text-center rounded-2xl shadow-sm border border-gray-100">
                        <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <h3 class="text-xl font-bold text-gray-600">No hay vacantes disponibles en este momento.</h3>
                        <p class="text-gray-400 mt-2">Mantente atento a nuestras próximas actualizaciones.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- MODAL DE POSTULACIÓN --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>

                <div class="relative inline-block text-left align-bottom bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full overflow-hidden"
                     x-show="showModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">

                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 to-blue-400"></div>

                    <div class="px-6 pt-6 pb-4 flex justify-between items-start border-b border-gray-100">
                        <div>
                            <h3 class="text-lg font-extrabold text-gray-900">Postularse a esta vacante</h3>
                            <p class="text-sm text-gray-500 mt-0.5" x-text="selectedVacancy?.title"></p>
                        </div>
                        <button @click="showModal = false" class="text-gray-300 hover:text-red-500 transition-colors ml-4 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                        @auth
                            @if(auth()->user()->hasRole('cliente'))
                                <form :action="`/vacantes/${selectedVacancy?.id}/apply`" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    {{-- Preguntas dinámicas del formulario de esta vacante --}}
                                    <div class="space-y-5 mb-6">
                                        <template x-for="(field, index) in selectedSchema" :key="index">
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-1.5">
                                                    <span x-text="field.label"></span>
                                                    <span x-show="field.required" class="text-red-500 ml-0.5">*</span>
                                                </label>

                                                <template x-if="field.type === 'text'">
                                                    <input type="text" :name="'answers['+index+']'"
                                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                        :required="field.required">
                                                </template>

                                                <template x-if="field.type === 'textarea'">
                                                    <textarea :name="'answers['+index+']'" rows="3"
                                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                        :required="field.required"></textarea>
                                                </template>

                                                <template x-if="field.type === 'checkbox'">
                                                    <div class="flex items-center gap-5 mt-1.5">
                                                        <label class="flex items-center gap-2 cursor-pointer">
                                                            <input type="radio" :name="'answers['+index+']'" value="Sí"
                                                                class="border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                                :required="field.required">
                                                            <span class="text-sm text-gray-600">Sí</span>
                                                        </label>
                                                        <label class="flex items-center gap-2 cursor-pointer">
                                                            <input type="radio" :name="'answers['+index+']'" value="No"
                                                                class="border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                                :required="field.required">
                                                            <span class="text-sm text-gray-600">No</span>
                                                        </label>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <div x-show="selectedSchema.length === 0" class="text-center py-3 text-sm text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                            Esta vacante no tiene preguntas adicionales.
                                        </div>
                                    </div>

                                    {{-- Subida de CV --}}
                                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                                        <label class="block text-sm font-bold text-blue-900 mb-2">
                                            Curriculum Vitae (PDF) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="file" name="cv_file" accept=".pdf" required
                                            class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                                              file:text-sm file:font-bold file:bg-blue-100 file:text-blue-700
                                              hover:file:bg-blue-200 cursor-pointer transition-all">
                                        <p class="text-xs text-blue-600 mt-2 font-medium">Máximo 5MB · Formato estrictamente PDF.</p>
                                    </div>

                                    <div class="flex justify-end gap-3 mt-6">
                                        <button type="button" @click="showModal = false"
                                            class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-100 transition-all">
                                            Cancelar
                                        </button>
                                        <button type="submit"
                                            class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all">
                                            Enviar Solicitud
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="text-center py-6">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <p class="text-gray-700 font-bold">Tu cuenta no es de tipo "Cliente"</p>
                                    <p class="text-sm text-gray-500 mt-1">Para postularte debes tener una cuenta de cliente registrada.</p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-6">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <p class="text-gray-700 font-bold mb-4">Inicia sesión para postularte</p>
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('login') }}" class="px-5 py-2 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-sm transition-all">Iniciar Sesión</a>
                                    <a href="{{ route('register') }}" class="px-5 py-2 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 shadow-sm transition-all">Registrarse</a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
