<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>

            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Gestión de Vacantes
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Administra las ofertas laborales publicadas en el muro público.
                    </p>
                </div>
                <div class="mt-5 sm:mt-0 flex flex-col sm:flex-row gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('vacancies.public.index') }}"
                        class="group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-blue-500 rounded-xl hover:bg-blue-600 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-400/30 whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span>Volver al Muro</span>
                    </a>
                    <a href="{{ route('admin.vacancies.create') }}"
                        class="group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-green-700 rounded-xl hover:bg-green-800 hover:-translate-y-1 hover:shadow-lg hover:shadow-green-500/30 whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Nueva Vacante</span>
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Alpine scope: tabla de vacantes + modal de buzón --}}
    <div class="py-8" x-data="{
        buzonModal: false,
        buzonVacancy: null,
        buzonApps: [],
        buzonLoading: false,
        detailModal: false,
        detailApp: null,

        async openBuzon(vacancyId, vacancyTitle) {
            this.buzonLoading = true;
            this.buzonApps = [];
            this.buzonVacancy = { id: vacancyId, title: vacancyTitle };
            this.buzonModal = true;
            const resp = await fetch(`/admin/vacancies/${vacancyId}/applications-json`);
            this.buzonApps = await resp.json();
            this.buzonLoading = false;
        },

        openDetail(app) {
            this.detailApp = app;
            this.detailModal = true;
        },

        statusLabel(s) {
            const map = { pending: 'Pendiente', reviewed: 'Revisado', accepted: 'Aceptado', rejected: 'Rechazado' };
            return map[s] || s;
        },

        statusColor(s) {
            const map = {
                pending:  'bg-yellow-100 text-yellow-800',
                reviewed: 'bg-blue-100 text-blue-800',
                accepted: 'bg-green-100 text-green-800',
                rejected: 'bg-red-100 text-red-800',
            };
            return map[s] || 'bg-gray-100 text-gray-700';
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center p-4 text-green-800 bg-green-50 border border-green-200 rounded-xl shadow-sm" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Título</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Solicitudes</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Preguntas</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Publicada</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($vacancies as $vacancy)
                            <tr class="hover:bg-gray-50/80 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $vacancy->title }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ Str::limit($vacancy->description, 60) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($vacancy->is_active)
                                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                                    @else
                                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactiva</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-gray-700">{{ $vacancy->applications_count }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ count($vacancy->form_schema ?? []) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $vacancy->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    {{-- Buzón --}}
                                    <button
                                        @click="openBuzon({{ $vacancy->id }}, '{{ addslashes($vacancy->title) }}')"
                                        class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 font-bold transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        Buzón
                                        @if($vacancy->applications_count > 0)
                                            <span class="ml-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $vacancy->applications_count }}</span>
                                        @endif
                                    </button>
                                    {{-- Editar --}}
                                    <a href="{{ route('admin.vacancies.edit', $vacancy) }}" class="text-blue-600 hover:text-blue-900 font-bold transition-colors">Editar</a>
                                    {{-- Eliminar --}}
                                    <form action="{{ route('admin.vacancies.destroy', $vacancy) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="text-red-500 hover:text-red-700 font-bold transition-colors"
                                            @click.prevent="$dispatch('open-confirm-delete', { message: '¿Eliminar la vacante «{{ addslashes($vacancy->title) }}»? Esta acción también eliminará todas sus solicitudes.', form: $el.closest('form') })">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">
                                    No hay vacantes publicadas aún.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- ===== MODAL BUZÓN DE SOLICITUDES ===== --}}
        <div x-show="buzonModal" class="fixed inset-0 z-50 overflow-y-auto" style="display:none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-70 backdrop-blur-sm" @click="buzonModal = false"></div>

                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden"
                     x-show="buzonModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 scale-95">

                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-600 to-indigo-400"></div>

                    {{-- Header del modal --}}
                    <div class="flex justify-between items-center px-6 pt-6 pb-4 border-b border-gray-100">
                        <div>
                            <h3 class="text-lg font-extrabold text-gray-900">Buzón de Solicitudes</h3>
                            <p class="text-sm text-gray-500 mt-0.5" x-text="buzonVacancy?.title"></p>
                        </div>
                        <button @click="buzonModal = false" class="text-gray-300 hover:text-red-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    {{-- Contenido --}}
                    <div class="max-h-[65vh] overflow-y-auto">
                        {{-- Loading --}}
                        <div x-show="buzonLoading" class="flex items-center justify-center py-16 text-gray-400">
                            <svg class="animate-spin w-8 h-8 mr-3 text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Cargando solicitudes...
                        </div>

                        {{-- Sin solicitudes --}}
                        <div x-show="!buzonLoading && buzonApps.length === 0" class="py-14 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p class="font-medium">No hay solicitudes para esta vacante.</p>
                        </div>

                        {{-- Lista de candidatos --}}
                        <div x-show="!buzonLoading && buzonApps.length > 0" class="divide-y divide-gray-100">
                            <template x-for="app in buzonApps" :key="app.id">
                                <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-4">
                                        {{-- Avatar --}}
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-extrabold text-sm flex-shrink-0"
                                             x-text="app.applicant?.name?.charAt(0)?.toUpperCase() ?? '?'"></div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900" x-text="app.applicant?.name"></p>
                                            <p class="text-xs text-gray-400" x-text="app.applicant?.email"></p>
                                            <p class="text-xs text-gray-400 mt-0.5" x-text="app.created_at_human"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 flex-shrink-0">
                                        {{-- Badge de estado --}}
                                        <span class="text-xs font-bold px-2.5 py-1 rounded-full" :class="statusColor(app.status)" x-text="statusLabel(app.status)"></span>

                                        {{-- Ver respuestas + CV --}}
                                        <button @click="openDetail(app)"
                                            class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Ver
                                        </button>

                                        {{-- Cambiar estado --}}
                                        <form :action="`/admin/vacancies/applications/${app.id}/status`" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <select name="status" @change="$el.closest('form').submit()"
                                                class="text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 py-1 pr-7"
                                                :value="app.status">
                                                <option value="pending"  :selected="app.status==='pending'">Pendiente</option>
                                                <option value="reviewed" :selected="app.status==='reviewed'">Revisado</option>
                                                <option value="accepted" :selected="app.status==='accepted'">Aceptado</option>
                                                <option value="rejected" :selected="app.status==='rejected'">Rechazado</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                        <button @click="buzonModal = false"
                            class="px-5 py-2 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-100 transition-all">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== MODAL DETALLE DEL CANDIDATO ===== --}}
        <div x-show="detailModal" class="fixed inset-0 z-[60] overflow-y-auto" style="display:none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="detailModal = false"></div>

                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden"
                     x-show="detailModal"
                     x-transition:enter="transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">

                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 to-indigo-500"></div>

                    <div class="flex justify-between items-center px-6 pt-6 pb-4 border-b border-gray-100">
                        <div>
                            <h3 class="text-base font-extrabold text-gray-900" x-text="detailApp?.applicant?.name"></h3>
                            <p class="text-xs text-gray-500" x-text="detailApp?.applicant?.email"></p>
                        </div>
                        <button @click="detailModal = false" class="text-gray-300 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="px-6 py-5 max-h-[60vh] overflow-y-auto space-y-4">
                        {{-- Respuestas al formulario --}}
                        <template x-for="(ans, i) in (detailApp?.answers ?? [])" :key="i">
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1" x-text="ans.question"></p>
                                <p class="text-sm text-gray-800 bg-gray-50 rounded-lg p-3 border border-gray-100" x-text="ans.value || '—'"></p>
                            </div>
                        </template>
                        <div x-show="!detailApp?.answers || detailApp.answers.length === 0" class="text-sm text-gray-400 text-center py-4">
                            Esta vacante no tenía preguntas adicionales.
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                        <a :href="`/admin/vacancies/applications/${detailApp?.id}/cv`" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Ver CV (PDF)
                        </a>
                        <button @click="detailModal = false"
                            class="px-5 py-2 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-100 transition-all">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
