<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-orange-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
                 x-data="{
                     isTodayGenerico: @json((bool)$isTodayGenerico),
                     isTodayReceta: @json((bool)$isTodayReceta),
                     isToday4x3: @json((bool)$isToday4x3),
                     isTodayVolumen: @json((bool)$isTodayVolumen),
                     get activeCount() {
                         return (this.isTodayGenerico ? 1 : 0) + (this.isTodayReceta ? 1 : 0) + (this.isToday4x3 ? 1 : 0) + (this.isTodayVolumen ? 1 : 0);
                     }
                 }"
                 @promos-status-updated.window="
                     if ($event.detail.isToday4x3 !== undefined) isToday4x3 = $event.detail.isToday4x3;
                     if ($event.detail.isTodayVolumen !== undefined) isTodayVolumen = $event.detail.isTodayVolumen;
                     if ($event.detail.isTodayGenerico !== undefined) isTodayGenerico = $event.detail.isTodayGenerico;
                     if ($event.detail.isTodayReceta !== undefined) isTodayReceta = $event.detail.isTodayReceta;
                 ">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Gestión de Promociones
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z"/></svg>
                        Administra las promociones activas. Los cambios se reflejan inmediatamente en el POS.
                    </p>
                </div>
                
                <template x-if="activeCount > 1">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 text-blue-800 font-bold text-sm border border-blue-200 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        MÚLTIPLES PROMOS ACTIVAS HOY
                    </span>
                </template>
                <template x-if="activeCount === 1">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-green-50 text-green-800 font-bold text-sm border border-green-200 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        PROMOS ACTIVAS HOY
                    </span>
                </template>
                <template x-if="activeCount === 0">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 text-gray-500 font-medium text-sm border border-gray-200">
                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                        Sin promos activas hoy
                    </span>
                </template>
            </div>

            <div class="mt-4 bg-emerald-50/70 border border-emerald-200/80 p-3.5 rounded-xl flex items-center gap-3 text-xs text-emerald-900 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><strong>Reglas de negocio:</strong> La Promo de Genéricos y Receta Completa son <u>mutuamente excluyentes</u> (no pueden coincidir el mismo día). Los productos configurados en 4x3 o Volumen quedan <u>excluidos de cualquier otra promo</u> durante sus días activos.</span>
            </div>
        </div>
    </x-slot>

    <div class="py-10" x-data="promosManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Notificación de guardado --}}
            <div x-show="saved" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-200" x-transition:leave-end="opacity-0" style="display:none;"
                class="flex items-center p-4 text-green-800 bg-green-50 border border-green-200 rounded-xl shadow-sm" role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="font-medium" x-text="savedMsg"></span>
            </div>

            {{-- ====== CARD: Lunes de Genéricos (Acordeón) ====== --}}
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-2xl" x-data="{ openGenericos: false }">

                {{-- Header verde — clickeable --}}
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-5 flex items-center justify-between cursor-pointer select-none"
                     @click="openGenericos = !openGenericos"
                     role="button"
                     :aria-expanded="openGenericos"
                     aria-controls="body-genericos">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-white">Promo de Genéricos</h3>
                            <p class="text-emerald-100 text-sm mt-0.5">Descuento automático en medicamentos tipo GEN</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @if($isTodayGenerico)
                            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 rounded-xl text-white text-xs font-bold border border-white/30">
                                <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse"></span>
                                ACTIVA AHORA
                            </span>
                        @else
                            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-black/20 rounded-xl text-white/70 text-xs font-bold">
                                <span class="w-2 h-2 rounded-full bg-white/50"></span>
                                Activa los días seleccionados
                            </span>
                        @endif
                        {{-- Chevron indicador de acordeón --}}
                        <div class="w-8 h-8 bg-white/15 rounded-lg flex items-center justify-center border border-white/20 transition-transform duration-300"
                             :class="{ 'rotate-180': openGenericos }">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Cuerpo desplegable --}}
                <div id="body-genericos"
                     x-show="openGenericos"
                     x-transition:enter="transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="divide-y divide-gray-100">

                    {{-- Fila: Descuento --}}
                    <div class="px-6 py-5 flex flex-col md:flex-row md:items-center gap-4">
                        <div class="md:w-48 flex-shrink-0">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Porcentaje de descuento</p>
                            <p class="text-xs text-gray-400 mt-0.5">Aplicado sobre el precio de venta</p>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                            <div class="w-32 h-16 bg-emerald-50 border-2 border-emerald-200 rounded-xl flex flex-col items-center justify-center">
                                <span class="text-3xl font-black text-emerald-700 leading-none" x-text="discount + '%'">{{ $genericoDiscount }}%</span>
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" @click="if(discount > 1) discount--" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-black text-base transition-colors">−</button>
                                    <input type="number" x-model.number="discount" min="1" max="99" class="w-20 text-center font-bold text-lg border border-gray-300 rounded-lg py-1.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                                    <button type="button" @click="if(discount < 99) discount++" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-black text-base transition-colors">+</button>
                                </div>
                                <p class="text-xs text-gray-400 ml-1">Usa los botones o escribe directamente el porcentaje</p>
                            </div>
                        </div>
                    </div>

                    {{-- Fila: Días de activación --}}
                    <div class="px-6 py-5 flex flex-col md:flex-row md:items-start gap-4">
                        <div class="md:w-48 flex-shrink-0">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Días de activación</p>
                            <p class="text-xs text-gray-400 mt-0.5">Excluyente con Receta Completa</p>
                        </div>
                        <div class="flex flex-1 flex-col gap-2">
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'] as $day)
                                <button type="button" @click="toggleGenericoDay('{{ $day }}')"
                                    :class="selectedDays.includes('{{ $day }}') 
                                        ? 'bg-emerald-100 text-emerald-800 border-emerald-400 ring-2 ring-emerald-400/20 shadow-sm' 
                                        : (recetaSelectedDays.includes('{{ $day }}') 
                                            ? 'bg-gray-100 text-gray-400 border-dashed border-gray-300 hover:border-amber-400 hover:text-amber-700' 
                                            : 'bg-gray-50 text-gray-600 border-gray-200 hover:border-emerald-300 hover:text-emerald-700')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border transition-all duration-150 select-none">
                                    <span>{{ $day }}</span>
                                    <template x-if="recetaSelectedDays.includes('{{ $day }}')">
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-blue-100 text-blue-700 font-semibold">(En Receta)</span>
                                    </template>
                                </button>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-gray-400">Genéricos y Receta Completa no pueden activarse el mismo día. Haz clic en un día ocupado para transferirlo.</p>
                        </div>
                    </div>

                    {{-- Fila: Turnos --}}
                    <div class="px-6 py-5 flex flex-col md:flex-row md:items-start gap-4 border-b border-gray-100">
                        <div class="md:w-48 flex-shrink-0">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Turnos aplicables</p>
                            <p class="text-xs text-gray-400 mt-0.5">Selecciona en qué turnos aplica</p>
                        </div>
                        <div class="flex flex-1 flex-col gap-3">
                            <div class="flex flex-wrap gap-3">
                                <label class="cursor-pointer flex-1 sm:flex-none">
                                    <input type="checkbox" class="hidden peer" value="morning" x-model="selectedShifts">
                                    <span class="flex items-center justify-center sm:inline-flex gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition-all duration-150
                                        peer-checked:bg-amber-50 peer-checked:text-amber-700 peer-checked:border-amber-300
                                        bg-gray-50 text-gray-500 border-gray-200 hover:border-amber-300 hover:text-amber-600 select-none">
                                        ☀️ Turno Matutino
                                    </span>
                                </label>
                                <label class="cursor-pointer flex-1 sm:flex-none">
                                    <input type="checkbox" class="hidden peer" value="afternoon" x-model="selectedShifts">
                                    <span class="flex items-center justify-center sm:inline-flex gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition-all duration-150
                                        peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-checked:border-indigo-300
                                        bg-gray-50 text-gray-500 border-gray-200 hover:border-indigo-300 hover:text-indigo-600 select-none">
                                        🌙 Turno Vespertino
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Fila: Guardar Configuración Promo Genéricos --}}
                    <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-end gap-3 bg-gray-50">
                        <button type="button" @click="saveSettings()" :disabled="savingSettings" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-green-700 hover:bg-green-800 text-white font-bold rounded-xl transition-all duration-200 text-sm disabled:opacity-60 hover:-translate-y-0.5 hover:shadow-md hover:shadow-green-500/30">
                            <svg x-show="!savingSettings" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <svg x-show="savingSettings" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span x-text="savingSettings ? 'Guardando...' : 'Guardar configuración'"></span>
                        </button>
                    </div>

                    {{-- Fila: Productos afectados --}}
                    <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="sm:w-48 flex-shrink-0">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Productos afectados</p>
                            <p class="text-xs text-gray-400 mt-0.5">Medicamentos tipo GEN con stock</p>
                        </div>
                        <button type="button" @click="openModal()"
                            class="flex items-center gap-4 p-4 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 hover:border-amber-300 hover:shadow-sm transition-all duration-200 group text-left w-full sm:max-w-sm">
                            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div>
                                <p class="font-black text-gray-900 text-2xl leading-none">{{ $genericCount }}</p>
                                <p class="text-amber-700 text-xs font-bold mt-0.5">medicamentos genéricos activos</p>
                                <p class="text-gray-400 text-xs mt-1">Clic para ver la lista completa →</p>
                            </div>
                        </button>
                    </div>

                </div>
            </div>

            {{-- ====== CARD: Receta Completa (Acordeón) ====== --}}
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-2xl mt-6" x-data="{ openReceta: false }">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 flex items-center justify-between cursor-pointer select-none"
                     @click="openReceta = !openReceta"
                     role="button"
                     :aria-expanded="openReceta"
                     aria-controls="body-receta">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-white">Receta Completa</h3>
                            <p class="text-blue-100 text-sm mt-0.5">Descuento seleccionable en medicamentos tipo GEN</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @if($isTodayReceta)
                            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 rounded-xl text-white text-xs font-bold border border-white/30">
                                <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse"></span>
                                ACTIVA AHORA
                            </span>
                        @else
                            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-black/20 rounded-xl text-white/70 text-xs font-bold">
                                <span class="w-2 h-2 rounded-full bg-white/50"></span>
                                Activa los días seleccionados
                            </span>
                        @endif
                        {{-- Chevron indicador de acordeón --}}
                        <div class="w-8 h-8 bg-white/15 rounded-lg flex items-center justify-center border border-white/20 transition-transform duration-300"
                             :class="{ 'rotate-180': openReceta }">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Cuerpo desplegable --}}
                <div id="body-receta"
                     x-show="openReceta"
                     x-transition:enter="transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="divide-y divide-gray-100">

                    {{-- Fila: Descuento Receta --}}
                    <div class="px-6 py-5 flex flex-col md:flex-row md:items-center gap-4">
                        <div class="md:w-48 flex-shrink-0">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Porcentaje de descuento</p>
                            <p class="text-xs text-gray-400 mt-0.5">Aplicado a los seleccionados en el POS</p>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                            <div class="w-32 h-16 bg-blue-50 border-2 border-blue-200 rounded-xl flex flex-col items-center justify-center">
                                <span class="text-3xl font-black text-blue-700 leading-none" x-text="recetaDiscount + '%'">{{ $recetaDiscount }}%</span>
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" @click="if(recetaDiscount > 1) recetaDiscount--" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-black text-base transition-colors">−</button>
                                    <input type="number" x-model.number="recetaDiscount" min="1" max="99" class="w-20 text-center font-bold text-lg border border-gray-300 rounded-lg py-1.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <button type="button" @click="if(recetaDiscount < 99) recetaDiscount++" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-black text-base transition-colors">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Fila: Días Receta --}}
                    <div class="px-6 py-5 flex flex-col md:flex-row md:items-start gap-4">
                        <div class="md:w-48 flex-shrink-0">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Días de activación</p>
                            <p class="text-xs text-gray-400 mt-0.5">Excluyente con Genéricos</p>
                        </div>
                        <div class="flex flex-1 flex-col gap-2">
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'] as $day)
                                <button type="button" @click="toggleRecetaDay('{{ $day }}')"
                                    :class="recetaSelectedDays.includes('{{ $day }}') 
                                        ? 'bg-blue-100 text-blue-800 border-blue-400 ring-2 ring-blue-400/20 shadow-sm' 
                                        : (selectedDays.includes('{{ $day }}') 
                                            ? 'bg-gray-100 text-gray-400 border-dashed border-gray-300 hover:border-amber-400 hover:text-amber-700' 
                                            : 'bg-gray-50 text-gray-600 border-gray-200 hover:border-blue-300 hover:text-blue-700')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border transition-all duration-150 select-none">
                                    <span>{{ $day }}</span>
                                    <template x-if="selectedDays.includes('{{ $day }}')">
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-emerald-100 text-emerald-700 font-semibold">(En Genéricos)</span>
                                    </template>
                                </button>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-gray-400">Receta Completa y Genéricos no pueden activarse el mismo día. Haz clic en un día ocupado para transferirlo.</p>
                        </div>
                    </div>

                    {{-- Fila: Turnos Receta --}}
                    <div class="px-6 py-5 flex flex-col md:flex-row md:items-start gap-4 border-b border-gray-100">
                        <div class="md:w-48 flex-shrink-0">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Turnos aplicables</p>
                        </div>
                        <div class="flex flex-1 flex-col gap-3">
                            <div class="flex flex-wrap gap-3">
                                <label class="cursor-pointer flex-1 sm:flex-none">
                                    <input type="checkbox" class="hidden peer" value="morning" x-model="recetaSelectedShifts">
                                    <span class="flex items-center justify-center sm:inline-flex gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition-all duration-150
                                        peer-checked:bg-amber-50 peer-checked:text-amber-700 peer-checked:border-amber-300
                                        bg-gray-50 text-gray-500 border-gray-200 hover:border-amber-300 hover:text-amber-600 select-none">
                                        ☀️ Turno Matutino
                                    </span>
                                </label>
                                <label class="cursor-pointer flex-1 sm:flex-none">
                                    <input type="checkbox" class="hidden peer" value="afternoon" x-model="recetaSelectedShifts">
                                    <span class="flex items-center justify-center sm:inline-flex gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition-all duration-150
                                        peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-checked:border-indigo-300
                                        bg-gray-50 text-gray-500 border-gray-200 hover:border-indigo-300 hover:text-indigo-600 select-none">
                                        🌙 Turno Vespertino
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Fila: Guardar Configuración Receta --}}
                    <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-end gap-3 bg-gray-50">
                        <button type="button" @click="saveRecetaSettings()" :disabled="savingRecetaSettings" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl transition-all duration-200 text-sm disabled:opacity-60 hover:-translate-y-0.5 hover:shadow-md hover:shadow-blue-500/30">
                            <svg x-show="!savingRecetaSettings" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <svg x-show="savingRecetaSettings" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span x-text="savingRecetaSettings ? 'Guardando...' : 'Guardar configuración'"></span>
                        </button>
                    </div>
                </div>
            </div>

        {{-- ====== CARD: Promo 4x3 (Acordeón) ====== --}}
        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-2xl mt-6" x-data="{ open4x3: false }">
            <div class="bg-gradient-to-r from-purple-600 to-fuchsia-600 px-6 py-5 flex items-center justify-between cursor-pointer select-none"
                 @click="open4x3 = !open4x3"
                 role="button"
                 :aria-expanded="open4x3"
                 aria-controls="body-4x3">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-white">Promo 4 x 3</h3>
                        <p class="text-purple-100 text-sm mt-0.5">LLeva 4 y paga 3 en productos seleccionados.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <span x-show="isToday4x3" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 rounded-xl text-white text-xs font-bold border border-white/30">
                        <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse"></span>
                        ACTIVA AHORA
                    </span>
                    <span x-show="!isToday4x3" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-black/20 rounded-xl text-white/70 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-white/50"></span>
                        Activa los días seleccionados
                    </span>
                    {{-- Chevron indicador de acordeón --}}
                    <div class="w-8 h-8 bg-white/15 rounded-lg flex items-center justify-center border border-white/20 transition-transform duration-300"
                         :class="{ 'rotate-180': open4x3 }">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Cuerpo desplegable --}}
            <div id="body-4x3"
                 x-show="open4x3"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="divide-y divide-gray-100">



                {{-- Fila: Productos afectados Promo 4x3 --}}
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="sm:w-48 flex-shrink-0">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Productos afectados</p>
                        <p class="text-xs text-gray-400 mt-0.5">Productos agregados manualmente a esta promo</p>
                    </div>
                    <button type="button" @click="openPromo4x3Modal()"
                        class="flex items-center gap-4 p-4 bg-purple-50 border border-purple-200 rounded-xl hover:bg-purple-100 hover:border-purple-300 hover:shadow-sm transition-all duration-200 group text-left w-full sm:max-w-sm">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <p class="font-black text-gray-900 text-2xl leading-none" x-text="promo4x3Count"></p>
                            <p class="text-purple-700 text-xs font-bold mt-0.5">productos en lista de promo 4x3</p>
                            <p class="text-gray-400 text-xs mt-1">Clic para ver y editar la lista completa →</p>
                        </div>
                    </button>
                </div>

            </div>
        </div>

        {{-- ====== CARD: Promo x Volumen (Acordeón) ====== --}}
        <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-2xl" x-data="{ openVolumenCard: false }">
            {{-- Header cyan --}}
            <div class="bg-gradient-to-r from-cyan-600 to-teal-500 px-6 py-5 flex items-center justify-between cursor-pointer select-none"
                 @click="openVolumenCard = !openVolumenCard"
                 role="button"
                 :aria-expanded="openVolumenCard"
                 aria-controls="body-volumen">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-white">Promo x Volumen</h3>
                        <p class="text-cyan-100 text-sm mt-0.5">Descuentos especiales por cantidad (exclusivos)</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <span x-show="isTodayVolumen" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 rounded-xl text-white text-xs font-bold border border-white/30">
                        <span class="w-2 h-2 rounded-full bg-cyan-300 animate-pulse"></span>
                        ACTIVA AHORA
                    </span>
                    <span x-show="!isTodayVolumen" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-black/20 rounded-xl text-white/70 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-white/50"></span>
                        Activa los días seleccionados
                    </span>
                    <div class="w-8 h-8 bg-white/15 rounded-lg flex items-center justify-center border border-white/20 transition-transform duration-300"
                         :class="{ 'rotate-180': openVolumenCard }">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Cuerpo desplegable --}}
            <div id="body-volumen"
                 x-show="openVolumenCard"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="divide-y divide-gray-100">



                {{-- Fila: Productos afectados Promo Volumen --}}
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="sm:w-48 flex-shrink-0">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Productos afectados</p>
                        <p class="text-xs text-gray-400 mt-0.5">Productos con precio especial por volumen</p>
                    </div>
                    <button type="button" @click="openPromoVolumenModal()"
                        class="flex items-center gap-4 p-4 bg-cyan-50 border border-cyan-200 rounded-xl hover:bg-cyan-100 hover:border-cyan-300 hover:shadow-sm transition-all duration-200 group text-left w-full sm:max-w-sm">
                        <div class="w-10 h-10 bg-cyan-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <p class="font-black text-gray-900 text-2xl leading-none" x-text="promoVolumenCount"></p>
                            <p class="text-cyan-700 text-xs font-bold mt-0.5">productos con promo por volumen</p>
                            <p class="text-gray-400 text-xs mt-1">Clic para ver y editar la lista completa →</p>
                        </div>
                    </button>
                </div>

            </div>
        </div>

        </div>

        {{-- ====== Modal: Promo 4x3 Productos (Menú Drilldown) ====== --}}
        <div x-show="showPromo4x3Modal" style="display:none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-modal="true" role="dialog">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showPromo4x3Modal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showPromo4x3Modal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showPromo4x3Modal"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                     class="inline-block align-bottom bg-slate-50/50 backdrop-blur-xl rounded-3xl text-left overflow-hidden shadow-2xl border border-gray-200/80 transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl w-full">

                    {{-- Header vibrante del modal 4x3 --}}
                    <div class="px-6 py-5 bg-white border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-purple-500/25 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Promo 4x3 (Lleva 4, Paga 3)</h3>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-purple-100 text-purple-800 border border-purple-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                                        <span x-text="promo4x3Count + ' activos'"></span>
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">La 4ta pieza es gratis. Configura con drilldown los días, sucursales y turnos aplicables por producto.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-center">
                            <button type="button" @click="showPromo4x3Modal = false" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Barra de búsqueda y selector de pestañas --}}
                    <div class="px-6 py-4 bg-white/70 backdrop-blur-md border-b border-gray-100 flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text"
                                   x-model="promo4x3Search"
                                   x-on:input.debounce.400ms="searchPromo4x3"
                                   placeholder="Buscar medicamento o producto por nombre, código o SKU..."
                                   class="block w-full pl-10 pr-9 py-2.5 bg-gray-50/80 border border-gray-200 rounded-2xl text-sm font-medium text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                            <button type="button"
                                    x-show="promo4x3Search.length > 0"
                                    @click="promo4x3Search = ''; searchPromo4x3()"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Filtro de visualización (Pills) --}}
                        <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl self-start md:self-auto text-xs font-bold">
                            <button type="button"
                                    @click="promo4x3Tab = 'all'"
                                    :class="promo4x3Tab === 'all' ? 'bg-white text-gray-900 shadow-2xs' : 'text-gray-500 hover:text-gray-700'"
                                    class="px-3 py-1.5 rounded-lg transition-all">
                                Todos los resultados
                            </button>
                            <button type="button"
                                    @click="promo4x3Tab = 'active'"
                                    :class="promo4x3Tab === 'active' ? 'bg-purple-600 text-white shadow-2xs' : 'text-gray-500 hover:text-gray-700'"
                                    class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5">
                                <span>Solo activos</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="promo4x3Tab === 'active' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600'" x-text="promo4x3Count"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Contenedor de lista de productos (Drilldown Cards) --}}
                    <div class="p-6 overflow-y-auto space-y-3.5" style="max-height: calc(85vh - 210px);">
                        {{-- Loading --}}
                        <template x-if="loadingPromo4x3Modal">
                            <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                                <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 animate-spin text-purple-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-700">Consultando catálogo y promociones...</span>
                                <span class="text-xs text-gray-400 mt-1">Sincronizando reglas de 4x3 por producto</span>
                            </div>
                        </template>

                        {{-- Empty state --}}
                        <template x-if="!loadingPromo4x3Modal && filtered4x3Products.length === 0">
                            <div class="py-16 text-center bg-white rounded-3xl border border-dashed border-gray-200 p-8">
                                <div class="w-14 h-14 mx-auto rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-3">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                </div>
                                <p class="text-base font-extrabold text-gray-800" x-text="promo4x3Search ? 'No se encontraron coincidencias' : 'No hay productos configurados en esta lista'"></p>
                                <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto" x-text="promo4x3Search ? 'Prueba escribiendo otro término de búsqueda o limpia el buscador.' : 'Escribe en el buscador de arriba para localizar productos y habilitar su Promo 4x3.'"></p>
                            </div>
                        </template>

                        {{-- Lista de tarjetas Drilldown 4x3 --}}
                        <template x-if="!loadingPromo4x3Modal && filtered4x3Products.length > 0">
                            <div class="space-y-3">
                                <template x-for="product in filtered4x3Products" :key="product.id">
                                    <div class="bg-white rounded-2xl border transition-all duration-200 overflow-hidden shadow-xs hover:shadow-md"
                                         :class="product.is_promo_4x3
                                             ? (product._expanded ? 'border-purple-400 ring-2 ring-purple-400/20 shadow-md' : 'border-purple-300 bg-white ring-1 ring-purple-100')
                                             : (product._expanded ? 'border-gray-400 shadow-md' : 'border-gray-200/90 hover:border-gray-300')">

                                        {{-- ====== FILA PLEGADA (Siempre visible) ====== --}}
                                        <div class="p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                                            {{-- 1. Detalle del Producto --}}
                                            <div class="flex items-center gap-3.5 flex-1 min-w-0">
                                                <div class="w-11 h-11 rounded-2xl flex-shrink-0 flex items-center justify-center font-black text-sm shadow-2xs transition-transform"
                                                     :class="product.is_promo_4x3 ? 'bg-gradient-to-tr from-purple-600 to-indigo-600 text-white' : 'bg-gray-100 text-gray-600'">
                                                    <span x-text="product.name.charAt(0)"></span>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <h4 class="text-sm sm:text-base font-black text-gray-900 tracking-tight truncate" x-text="product.name"></h4>
                                                        <template x-if="product.is_promo_4x3">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-purple-100 text-purple-800 border border-purple-200">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                                                Activo en 4x3
                                                            </span>
                                                        </template>
                                                        <template x-if="product.is_promo_4x3 && isProductActiveToday(product.promo_4x3_days)">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-2xs">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                                Activo Hoy
                                                            </span>
                                                        </template>
                                                        <template x-if="product.is_promo_volumen">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-cyan-100 text-cyan-800 border border-cyan-200">
                                                                <svg class="w-3 h-3 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                                En Promo x Volumen
                                                            </span>
                                                        </template>
                                                    </div>
                                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-gray-500 font-medium">
                                                        <span x-show="product.sku" class="text-gray-400">SKU: <strong class="text-gray-600" x-text="product.sku"></strong></span>
                                                        <span x-show="product.category" class="inline-block px-1.5 py-0.2 bg-gray-100 rounded text-[11px] text-gray-600" x-text="product.category"></span>
                                                        <span x-show="product.regular_price">Precio regular: <strong class="text-gray-700" x-text="'$' + parseFloat(product.regular_price || 0).toFixed(2)"></strong></span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- 2. Mecánica 4x3 (Lleva 4 y paga 3 / Ahorro) --}}
                                            <div class="flex items-center gap-3">
                                                <div class="bg-purple-50/80 hover:bg-purple-100/60 px-4 py-2.5 rounded-2xl border border-purple-200/80 transition-colors flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center font-black text-xs shadow-2xs">
                                                        4x3
                                                    </div>
                                                    <div>
                                                        <div class="text-[11px] font-black text-purple-900 uppercase tracking-wider">Lleva 4, Paga 3</div>
                                                        <div class="text-[10px] text-purple-700 font-bold" x-text="product.regular_price > 0 ? 'Paga 3 x $' + parseFloat(product.regular_price).toFixed(2) + ' (Ahorro 25%)' : '4ta pieza 100% bonificada'"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- 3. Toggle, Botón de Actualizar y Botón Drilldown --}}
                                            <div class="flex items-center justify-end gap-2.5 pt-2 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                                                {{-- Toggle Switch --}}
                                                <button type="button"
                                                        @click="togglePromo4x3(product, true)"
                                                        :class="product.is_promo_4x3 ? 'bg-purple-600' : 'bg-gray-200'"
                                                        class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                                                        title="Activar o desactivar promo 4x3 para este producto">
                                                    <span aria-hidden="true"
                                                          :class="product.is_promo_4x3 ? 'translate-x-5' : 'translate-x-0'"
                                                          class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                                                </button>

                                                {{-- Botón Actualizar --}}
                                                <button type="button"
                                                        x-show="product.is_promo_4x3"
                                                        @click="togglePromo4x3(product, false)"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-xs font-black rounded-xl shadow-xs hover:shadow transition-all active:scale-95">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Actualizar</span>
                                                </button>

                                                {{-- Botón Drilldown (Desplegar / Plegar) --}}
                                                <button type="button"
                                                        @click="toggle4x3Drilldown(product)"
                                                        class="p-2 sm:px-3 sm:py-2 rounded-xl text-xs font-black transition-all flex items-center gap-1.5"
                                                        :class="product._expanded ? 'bg-purple-100 text-purple-900 border border-purple-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200'"
                                                        :title="product._expanded ? 'Plegar detalles' : 'Desplegar reglas'">
                                                    <span class="hidden sm:inline" x-text="product._expanded ? 'Ocultar reglas' : 'Configurar'"></span>
                                                    <svg class="w-4 h-4 transform transition-transform duration-300"
                                                         :class="{ 'rotate-180 text-purple-700': product._expanded }"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- ====== MENÚ DRILLDOWN DESPLEGADO 4x3 (3 COLUMNAS) ====== --}}
                                        <div x-show="product._expanded"
                                             x-transition:enter="transition ease-out duration-250"
                                             x-transition:enter-start="opacity-0 -translate-y-2"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100 translate-y-0"
                                             x-transition:leave-end="opacity-0 -translate-y-2"
                                             class="border-t border-purple-100 bg-gradient-to-b from-purple-50/60 via-slate-50/70 to-white p-5 sm:p-6 rounded-b-2xl">

                                            {{-- Header de la barra drilldown --}}
                                            <div class="flex items-center gap-2 pb-4 mb-5 border-b border-gray-200/80">
                                                <span class="w-2.5 h-2.5 rounded-full bg-purple-500 animate-ping"></span>
                                                <span class="text-xs font-black uppercase tracking-wider text-purple-900">Reglas específicas de activación 4x3</span>
                                                <span class="text-xs text-gray-400 font-medium">— define días, sucursales y turnos aplicables</span>
                                            </div>

                                            {{-- GRID DE 3 COLUMNAS: Izquierda: Días | Centro: Sucursales | Derecha: Turnos --}}
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                                                {{-- COLUMNA 1 (IZQUIERDA): Días a aplicar --}}
                                                <div class="bg-white p-4 sm:p-4.5 rounded-2xl border border-purple-100/90 shadow-2xs flex flex-col justify-between">
                                                    <div>
                                                        <div class="flex items-center justify-between mb-3">
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center flex-shrink-0">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                </div>
                                                                <div>
                                                                    <h5 class="text-xs font-black uppercase tracking-wider text-gray-900">1. Días a Aplicar</h5>
                                                                    <p class="text-[10px] text-gray-400 font-medium">Lunes a Domingo</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Atajos rápidos de días --}}
                                                        <div class="flex items-center gap-1 mb-3 bg-gray-100/80 p-1 rounded-xl text-[10px] font-black">
                                                            <button type="button" @click="set4x3QuickDays(product, 'all')" class="flex-1 py-1 rounded-lg hover:bg-white text-gray-600 hover:text-purple-700 transition-all text-center">Todos</button>
                                                            <button type="button" @click="set4x3QuickDays(product, 'weekdays')" class="flex-1 py-1 rounded-lg hover:bg-white text-gray-600 hover:text-purple-700 transition-all text-center">Lun - Vie</button>
                                                            <button type="button" @click="set4x3QuickDays(product, 'weekend')" class="flex-1 py-1 rounded-lg hover:bg-white text-gray-600 hover:text-purple-700 transition-all text-center">Fin de sem</button>
                                                        </div>

                                                        {{-- Chips de los días individuales --}}
                                                        <div class="grid grid-cols-2 gap-1.5">
                                                            <template x-for="day in ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo']" :key="day">
                                                                <button type="button"
                                                                        @click="toggle4x3Day(product, day)"
                                                                        class="flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-bold transition-all border text-left"
                                                                        :class="is4x3DayActive(product, day)
                                                                            ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white border-transparent shadow-2xs'
                                                                            : 'bg-gray-50/70 hover:bg-white text-gray-600 border-gray-200 hover:border-purple-300'">
                                                                    <span x-text="day"></span>
                                                                    <svg x-show="is4x3DayActive(product, day)" class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500 font-medium">
                                                        <span>Días activos:</span>
                                                        <span class="font-black text-purple-700" x-text="(product.promo_4x3_days || []).length + ' de 7 seleccionados'"></span>
                                                    </div>
                                                </div>

                                                {{-- COLUMNA 2 (CENTRO): Sucursales --}}
                                                <div class="bg-white p-4 sm:p-4.5 rounded-2xl border border-indigo-100/90 shadow-2xs flex flex-col justify-between">
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-3">
                                                            <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center flex-shrink-0">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                            </div>
                                                            <div>
                                                                <h5 class="text-xs font-black uppercase tracking-wider text-gray-900">2. Sucursales</h5>
                                                                <p class="text-[10px] text-gray-400 font-medium">Ubicaciones participantes</p>
                                                            </div>
                                                        </div>

                                                        {{-- Opción destacada Todas las sucursales --}}
                                                        <button type="button"
                                                                @click="toggle4x3Branch(product, 'all')"
                                                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-black transition-all border mb-2 text-left"
                                                                :class="is4x3BranchActive(product, 'all')
                                                                    ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white border-transparent shadow-2xs'
                                                                    : 'bg-purple-50/50 hover:bg-purple-100/70 text-purple-800 border-purple-200 hover:border-purple-300'">
                                                            <div class="flex items-center gap-2">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                <span>Todas las sucursales</span>
                                                            </div>
                                                            <span class="text-[10px] px-2 py-0.5 rounded-full" :class="is4x3BranchActive(product, 'all') ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-800'">Global</span>
                                                        </button>

                                                        {{-- Lista de sucursales individuales --}}
                                                        <div class="space-y-1.5 max-h-44 overflow-y-auto pr-1">
                                                            @foreach($branches as $branch)
                                                            <button type="button"
                                                                    @click="toggle4x3Branch(product, '{{ $branch->id }}')"
                                                                    class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-all border text-left"
                                                                    :class="is4x3BranchActive(product, '{{ $branch->id }}')
                                                                        ? 'bg-indigo-600 text-white border-transparent shadow-2xs'
                                                                        : 'bg-gray-50/70 hover:bg-white text-gray-700 border-gray-200 hover:border-indigo-300'">
                                                                <span class="truncate">{{ $branch->name }}</span>
                                                                <svg x-show="is4x3BranchActive(product, '{{ $branch->id }}')" class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                            </button>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500 font-medium">
                                                        <span>Cobertura:</span>
                                                        <span class="font-black text-indigo-700" x-text="is4x3BranchActive(product, 'all') ? 'Todas las sucursales' : (product.promo_4x3_branches || []).length + ' seleccionada(s)'"></span>
                                                    </div>
                                                </div>

                                                {{-- COLUMNA 3 (DERECHA): Turnos --}}
                                                <div class="bg-white p-4 sm:p-4.5 rounded-2xl border border-violet-100/90 shadow-2xs flex flex-col justify-between">
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-3">
                                                            <div class="w-8 h-8 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center flex-shrink-0">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            </div>
                                                            <div>
                                                                <h5 class="text-xs font-black uppercase tracking-wider text-gray-900">3. Turnos</h5>
                                                                <p class="text-[10px] text-gray-400 font-medium">Horarios de venta</p>
                                                            </div>
                                                        </div>

                                                        <div class="space-y-2.5">
                                                            {{-- Turno Matutino --}}
                                                            <div @click="toggle4x3Shift(product, 'morning')"
                                                                 class="cursor-pointer p-3 rounded-xl border transition-all flex items-center justify-between select-none"
                                                                 :class="is4x3ShiftActive(product, 'morning')
                                                                     ? 'bg-amber-50/70 border-amber-300 ring-1 ring-amber-200 shadow-2xs'
                                                                     : 'bg-gray-50/70 border-gray-200 hover:border-gray-300 opacity-60'">
                                                                <div class="flex items-center gap-2.5">
                                                                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                                                    </div>
                                                                    <div>
                                                                        <div class="text-xs font-black text-gray-900">Turno Matutino</div>
                                                                        <div class="text-[10px] text-gray-500 font-medium">07:00 AM - 03:00 PM</div>
                                                                    </div>
                                                                </div>
                                                                <div class="w-5 h-5 rounded-md flex items-center justify-center transition-colors"
                                                                     :class="is4x3ShiftActive(product, 'morning') ? 'bg-amber-500 text-white' : 'border border-gray-300 bg-white'">
                                                                    <svg x-show="is4x3ShiftActive(product, 'morning')" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                                </div>
                                                            </div>

                                                            {{-- Turno Vespertino --}}
                                                            <div @click="toggle4x3Shift(product, 'afternoon')"
                                                                 class="cursor-pointer p-3 rounded-xl border transition-all flex items-center justify-between select-none"
                                                                 :class="is4x3ShiftActive(product, 'afternoon')
                                                                     ? 'bg-purple-50/70 border-purple-300 ring-1 ring-purple-200 shadow-2xs'
                                                                     : 'bg-gray-50/70 border-gray-200 hover:border-gray-300 opacity-60'">
                                                                <div class="flex items-center gap-2.5">
                                                                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                                                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                                                    </div>
                                                                    <div>
                                                                        <div class="text-xs font-black text-gray-900">Turno Vespertino</div>
                                                                        <div class="text-[10px] text-gray-500 font-medium">03:00 PM - 11:00 PM</div>
                                                                    </div>
                                                                </div>
                                                                <div class="w-5 h-5 rounded-md flex items-center justify-center transition-colors"
                                                                     :class="is4x3ShiftActive(product, 'afternoon') ? 'bg-purple-600 text-white' : 'border border-gray-300 bg-white'">
                                                                    <svg x-show="is4x3ShiftActive(product, 'afternoon')" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500 font-medium">
                                                        <span>Turnos activos:</span>
                                                        <span class="font-black text-purple-700" x-text="(product.promo_4x3_shifts || []).length + ' de 2 turnos'"></span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Footer del modal 4x3 --}}
                    <div class="px-6 py-4 bg-white border-t border-gray-100 flex items-center justify-between">
                        <div class="text-xs text-gray-400 font-medium hidden sm:block">
                            La bonificación de la 4ta pieza se aplica automáticamente al registrar 4 o más unidades en caja.
                        </div>
                        <button type="button" @click="showPromo4x3Modal = false" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-black transition-colors">
                            Cerrar ventana
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== Modal: Promo Volumen Productos (Menú Drilldown) ====== --}}
        <div x-show="showPromoVolumenModal" style="display:none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-modal="true" role="dialog">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showPromoVolumenModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showPromoVolumenModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showPromoVolumenModal"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                     class="inline-block align-bottom bg-slate-50/50 backdrop-blur-xl rounded-3xl text-left overflow-hidden shadow-2xl border border-gray-200/80 transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl w-full">

                    {{-- Header vibrante del modal --}}
                    <div class="px-6 py-5 bg-white border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-600 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-cyan-500/25 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Promo por Volumen</h3>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-cyan-100 text-cyan-800 border border-cyan-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-pulse"></span>
                                        <span x-text="promoVolumenCount + ' activos'"></span>
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">Precios escalonados con drilldown para programar días, sucursales y turnos por producto.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-center">
                            <button type="button" @click="showPromoVolumenModal = false" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Barra de búsqueda y selector de pestañas --}}
                    <div class="px-6 py-4 bg-white/70 backdrop-blur-md border-b border-gray-100 flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text"
                                   x-model="promoVolumenSearch"
                                   x-on:input.debounce.400ms="searchPromoVolumen"
                                   placeholder="Buscar por nombre, código de barras o SKU del producto..."
                                   class="block w-full pl-10 pr-9 py-2.5 bg-gray-50/80 border border-gray-200 rounded-2xl text-sm font-medium text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all">
                            <button type="button"
                                    x-show="promoVolumenSearch.length > 0"
                                    @click="promoVolumenSearch = ''; searchPromoVolumen()"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Filtro de visualización (Pills) --}}
                        <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl self-start md:self-auto text-xs font-bold">
                            <button type="button"
                                    @click="promoVolumenTab = 'all'"
                                    :class="promoVolumenTab === 'all' ? 'bg-white text-gray-900 shadow-2xs' : 'text-gray-500 hover:text-gray-700'"
                                    class="px-3 py-1.5 rounded-lg transition-all">
                                Todos los resultados
                            </button>
                            <button type="button"
                                    @click="promoVolumenTab = 'active'"
                                    :class="promoVolumenTab === 'active' ? 'bg-cyan-600 text-white shadow-2xs' : 'text-gray-500 hover:text-gray-700'"
                                    class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5">
                                <span>Solo activos</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="promoVolumenTab === 'active' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600'" x-text="promoVolumenCount"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Contenedor de lista de productos (Drilldown Cards) --}}
                    <div class="p-6 overflow-y-auto space-y-3.5" style="max-height: calc(85vh - 210px);">
                        {{-- Loading --}}
                        <template x-if="loadingPromoVolumenModal">
                            <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                                <div class="w-12 h-12 rounded-2xl bg-cyan-50 flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 animate-spin text-cyan-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-700">Consultando catálogo y promociones...</span>
                                <span class="text-xs text-gray-400 mt-1">Sincronizando reglas de volumen por producto</span>
                            </div>
                        </template>

                        {{-- Empty state --}}
                        <template x-if="!loadingPromoVolumenModal && filteredVolumenProducts.length === 0">
                            <div class="py-16 text-center bg-white rounded-3xl border border-dashed border-gray-200 p-8">
                                <div class="w-14 h-14 mx-auto rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center mb-3">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                </div>
                                <p class="text-base font-extrabold text-gray-800" x-text="promoVolumenSearch ? 'No se encontraron coincidencias' : 'No hay productos configurados en esta lista'"></p>
                                <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto" x-text="promoVolumenSearch ? 'Prueba escribiendo otro término de búsqueda o limpia el buscador.' : 'Escribe en el buscador de arriba para localizar productos y habilitar su precio especial por volumen.'"></p>
                            </div>
                        </template>

                        {{-- Lista de tarjetas Drilldown --}}
                        <template x-if="!loadingPromoVolumenModal && filteredVolumenProducts.length > 0">
                            <div class="space-y-3">
                                <template x-for="product in filteredVolumenProducts" :key="product.id">
                                    <div class="bg-white rounded-2xl border transition-all duration-200 overflow-hidden shadow-xs hover:shadow-md"
                                         :class="product.is_promo_volumen
                                             ? (product._expanded ? 'border-cyan-400 ring-2 ring-cyan-400/20 shadow-md' : 'border-cyan-300 bg-white ring-1 ring-cyan-100')
                                             : (product._expanded ? 'border-gray-400 shadow-md' : 'border-gray-200/90 hover:border-gray-300')">

                                        {{-- ====== FILA PLEGADA (Siempre visible) ====== --}}
                                        <div class="p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                                            {{-- 1. Detalle del Producto --}}
                                            <div class="flex items-center gap-3.5 flex-1 min-w-0">
                                                <div class="w-11 h-11 rounded-2xl flex-shrink-0 flex items-center justify-center font-black text-sm shadow-2xs transition-transform"
                                                     :class="product.is_promo_volumen ? 'bg-gradient-to-tr from-cyan-600 to-teal-500 text-white' : 'bg-gray-100 text-gray-600'">
                                                    <span x-text="product.name.charAt(0)"></span>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <h4 class="text-sm sm:text-base font-black text-gray-900 tracking-tight truncate" x-text="product.name"></h4>
                                                        <template x-if="product.is_promo_volumen">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-cyan-100 text-cyan-800 border border-cyan-200">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                                                                Activo
                                                            </span>
                                                        </template>
                                                        <template x-if="product.is_promo_volumen && isProductActiveToday(product.promo_volumen_days)">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-2xs">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                                Activo Hoy
                                                            </span>
                                                        </template>
                                                        <template x-if="product.is_promo_4x3">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">
                                                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                                En Promo 4x3
                                                            </span>
                                                        </template>
                                                    </div>
                                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-gray-500 font-medium">
                                                        <span x-show="product.sku" class="text-gray-400">SKU: <strong class="text-gray-600" x-text="product.sku"></strong></span>
                                                        <span x-show="product.category" class="inline-block px-1.5 py-0.2 bg-gray-100 rounded text-[11px] text-gray-600" x-text="product.category"></span>
                                                        <span>Precio regular: <strong class="text-gray-700" x-text="'$' + parseFloat(product.regular_price || 0).toFixed(2)"></strong></span>
                                                        {{-- Badge de Ahorro dinámico --}}
                                                        <template x-if="getPromoSavings(product)">
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                                                Ahorro <strong x-text="'$' + getPromoSavings(product).diff + ' (' + getPromoSavings(product).pct + '%)'"></strong>
                                                            </span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- 2. Mínimo para aplicar el precio por volumen --}}
                                            <div class="flex items-center justify-between sm:justify-start gap-4">
                                                <div class="bg-slate-50 hover:bg-cyan-50/50 p-2.5 rounded-2xl border border-gray-200 transition-colors flex flex-col items-center">
                                                    <span class="text-[9px] uppercase font-black tracking-wider text-gray-400 mb-1">Mínimo para aplicar</span>
                                                    <div class="flex items-center gap-1.5">
                                                        <button type="button"
                                                                @click="if(product.promo_volumen_min_qty > 2) product.promo_volumen_min_qty--"
                                                                class="w-7 h-7 rounded-lg bg-white border border-gray-200 hover:border-cyan-400 hover:text-cyan-700 text-gray-600 flex items-center justify-center font-black text-sm shadow-2xs active:scale-95 transition-all">-</button>
                                                        <input type="number"
                                                               x-model.number="product.promo_volumen_min_qty"
                                                               @wheel.prevent min="2"
                                                               class="w-12 text-center text-sm font-black text-gray-900 border-0 bg-transparent p-0 focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                                        <button type="button"
                                                                @click="product.promo_volumen_min_qty++"
                                                                class="w-7 h-7 rounded-lg bg-white border border-gray-200 hover:border-cyan-400 hover:text-cyan-700 text-gray-600 flex items-center justify-center font-black text-sm shadow-2xs active:scale-95 transition-all">+</button>
                                                        <span class="text-[11px] font-bold text-gray-400 ml-0.5">pzas</span>
                                                    </div>
                                                </div>

                                                {{-- 3. Precio por Volumen --}}
                                                <div class="bg-slate-50 hover:bg-cyan-50/50 p-2.5 rounded-2xl border border-gray-200 transition-colors flex flex-col items-center">
                                                    <span class="text-[9px] uppercase font-black tracking-wider text-gray-400 mb-1">Precio x volumen</span>
                                                    <div class="flex items-center gap-1">
                                                        <button type="button"
                                                                @click="if(product.promo_volumen_price > 0) product.promo_volumen_price = Math.max(0, (parseFloat(product.promo_volumen_price || 0) - 1)).toFixed(2)"
                                                                class="w-7 h-7 rounded-lg bg-white border border-gray-200 hover:border-cyan-400 hover:text-cyan-700 text-gray-600 flex items-center justify-center font-black text-sm shadow-2xs active:scale-95 transition-all">-</button>
                                                        <div class="flex items-center font-black text-cyan-700 text-sm">
                                                            <span class="text-xs mr-0.5">$</span>
                                                            <input type="number"
                                                                   step="0.01"
                                                                   x-model.number="product.promo_volumen_price"
                                                                   @wheel.prevent min="0"
                                                                   class="w-16 text-center text-sm font-black text-cyan-700 border-0 bg-transparent p-0 focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                                        </div>
                                                        <button type="button"
                                                                @click="product.promo_volumen_price = (parseFloat(product.promo_volumen_price || 0) + 1).toFixed(2)"
                                                                class="w-7 h-7 rounded-lg bg-white border border-gray-200 hover:border-cyan-400 hover:text-cyan-700 text-gray-600 flex items-center justify-center font-black text-sm shadow-2xs active:scale-95 transition-all">+</button>
                                                        <span class="text-[11px] font-bold text-gray-400 ml-0.5">c/u</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- 4. Toggle, Botón de Actualizar y Botón Drilldown --}}
                                            <div class="flex items-center justify-end gap-2.5 pt-2 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                                                {{-- Toggle Switch --}}
                                                <button type="button"
                                                        @click="togglePromoVolumen(product, true)"
                                                        :class="product.is_promo_volumen ? 'bg-cyan-600' : 'bg-gray-200'"
                                                        class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500"
                                                        title="Activar o desactivar promo para este producto">
                                                    <span aria-hidden="true"
                                                          :class="product.is_promo_volumen ? 'translate-x-5' : 'translate-x-0'"
                                                          class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                                                </button>

                                                {{-- Botón Actualizar existente --}}
                                                <button type="button"
                                                        x-show="product.is_promo_volumen"
                                                        @click="togglePromoVolumen(product, false)"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-gradient-to-r from-cyan-600 to-teal-600 hover:from-cyan-500 hover:to-teal-500 text-white text-xs font-black rounded-xl shadow-xs hover:shadow transition-all active:scale-95">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Actualizar</span>
                                                </button>

                                                {{-- Botón Drilldown (Desplegar / Plegar) --}}
                                                <button type="button"
                                                        @click="toggleVolumenDrilldown(product)"
                                                        class="p-2 sm:px-3 sm:py-2 rounded-xl text-xs font-black transition-all flex items-center gap-1.5"
                                                        :class="product._expanded ? 'bg-cyan-100 text-cyan-900 border border-cyan-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200'"
                                                        :title="product._expanded ? 'Plegar detalles' : 'Desplegar reglas'">
                                                    <span class="hidden sm:inline" x-text="product._expanded ? 'Ocultar reglas' : 'Configurar'"></span>
                                                    <svg class="w-4 h-4 transform transition-transform duration-300"
                                                         :class="{ 'rotate-180 text-cyan-700': product._expanded }"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- ====== MENÚ DRILLDOWN DESPLEGADO (3 COLUMNAS) ====== --}}
                                        <div x-show="product._expanded"
                                             x-transition:enter="transition ease-out duration-250"
                                             x-transition:enter-start="opacity-0 -translate-y-2"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100 translate-y-0"
                                             x-transition:leave-end="opacity-0 -translate-y-2"
                                             class="border-t border-cyan-100 bg-gradient-to-b from-cyan-50/60 via-slate-50/70 to-white p-5 sm:p-6 rounded-b-2xl">

                                            {{-- Header de la barra drilldown --}}
                                            <div class="flex items-center gap-2 pb-4 mb-5 border-b border-gray-200/80">
                                                <span class="w-2.5 h-2.5 rounded-full bg-cyan-500 animate-ping"></span>
                                                <span class="text-xs font-black uppercase tracking-wider text-cyan-900">Reglas específicas de activación</span>
                                                <span class="text-xs text-gray-400 font-medium">— define días, sucursales y turnos aplicables</span>
                                            </div>

                                            {{-- GRID DE 3 COLUMNAS: Izquierda: Días | Centro: Sucursales | Derecha: Turnos --}}
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                                                {{-- COLUMNA 1 (IZQUIERDA): Días a aplicar --}}
                                                <div class="bg-white p-4 sm:p-4.5 rounded-2xl border border-cyan-100/90 shadow-2xs flex flex-col justify-between">
                                                    <div>
                                                        <div class="flex items-center justify-between mb-3">
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-8 h-8 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center flex-shrink-0">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                </div>
                                                                <div>
                                                                    <h5 class="text-xs font-black uppercase tracking-wider text-gray-900">1. Días a Aplicar</h5>
                                                                    <p class="text-[10px] text-gray-400 font-medium">Lunes a Domingo</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Atajos rápidos de días --}}
                                                        <div class="flex items-center gap-1 mb-3 bg-gray-100/80 p-1 rounded-xl text-[10px] font-black">
                                                            <button type="button" @click="setVolumenQuickDays(product, 'all')" class="flex-1 py-1 rounded-lg hover:bg-white text-gray-600 hover:text-cyan-700 transition-all text-center">Todos</button>
                                                            <button type="button" @click="setVolumenQuickDays(product, 'weekdays')" class="flex-1 py-1 rounded-lg hover:bg-white text-gray-600 hover:text-cyan-700 transition-all text-center">Lun - Vie</button>
                                                            <button type="button" @click="setVolumenQuickDays(product, 'weekend')" class="flex-1 py-1 rounded-lg hover:bg-white text-gray-600 hover:text-cyan-700 transition-all text-center">Fin de sem</button>
                                                        </div>

                                                        {{-- Chips de los días individuales --}}
                                                        <div class="grid grid-cols-2 gap-1.5">
                                                            <template x-for="day in ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo']" :key="day">
                                                                <button type="button"
                                                                        @click="toggleVolumenDay(product, day)"
                                                                        class="flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-bold transition-all border text-left"
                                                                        :class="isVolumenDayActive(product, day)
                                                                            ? 'bg-gradient-to-r from-cyan-600 to-teal-600 text-white border-transparent shadow-2xs'
                                                                            : 'bg-gray-50/70 hover:bg-white text-gray-600 border-gray-200 hover:border-cyan-300'">
                                                                    <span x-text="day"></span>
                                                                    <svg x-show="isVolumenDayActive(product, day)" class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500 font-medium">
                                                        <span>Días activos:</span>
                                                        <span class="font-black text-cyan-700" x-text="(product.promo_volumen_days || []).length + ' de 7 seleccionados'"></span>
                                                    </div>
                                                </div>

                                                {{-- COLUMNA 2 (CENTRO): Sucursales --}}
                                                <div class="bg-white p-4 sm:p-4.5 rounded-2xl border border-teal-100/90 shadow-2xs flex flex-col justify-between">
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-3">
                                                            <div class="w-8 h-8 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center flex-shrink-0">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                            </div>
                                                            <div>
                                                                <h5 class="text-xs font-black uppercase tracking-wider text-gray-900">2. Sucursales</h5>
                                                                <p class="text-[10px] text-gray-400 font-medium">Ubicaciones participantes</p>
                                                            </div>
                                                        </div>

                                                        {{-- Opción destacada Todas las sucursales --}}
                                                        <button type="button"
                                                                @click="toggleVolumenBranch(product, 'all')"
                                                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-black transition-all border mb-2 text-left"
                                                                :class="isVolumenBranchActive(product, 'all')
                                                                    ? 'bg-gradient-to-r from-teal-600 to-cyan-600 text-white border-transparent shadow-2xs'
                                                                    : 'bg-teal-50/50 hover:bg-teal-100/70 text-teal-800 border-teal-200 hover:border-teal-300'">
                                                            <div class="flex items-center gap-2">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                <span>Todas las sucursales</span>
                                                            </div>
                                                            <span class="text-[10px] px-2 py-0.5 rounded-full" :class="isVolumenBranchActive(product, 'all') ? 'bg-white/20 text-white' : 'bg-teal-100 text-teal-800'">Global</span>
                                                        </button>

                                                        {{-- Lista de sucursales individuales --}}
                                                        <div class="space-y-1.5 max-h-44 overflow-y-auto pr-1">
                                                            @foreach($branches as $branch)
                                                            <button type="button"
                                                                    @click="toggleVolumenBranch(product, '{{ $branch->id }}')"
                                                                    class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-all border text-left"
                                                                    :class="isVolumenBranchActive(product, '{{ $branch->id }}')
                                                                        ? 'bg-cyan-600 text-white border-transparent shadow-2xs'
                                                                        : 'bg-gray-50/70 hover:bg-white text-gray-700 border-gray-200 hover:border-cyan-300'">
                                                                <span class="truncate">{{ $branch->name }}</span>
                                                                <svg x-show="isVolumenBranchActive(product, '{{ $branch->id }}')" class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                            </button>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500 font-medium">
                                                        <span>Cobertura:</span>
                                                        <span class="font-black text-teal-700" x-text="isVolumenBranchActive(product, 'all') ? 'Todas las sucursales' : (product.promo_volumen_branches || []).length + ' seleccionada(s)'"></span>
                                                    </div>
                                                </div>

                                                {{-- COLUMNA 3 (DERECHA): Turnos --}}
                                                <div class="bg-white p-4 sm:p-4.5 rounded-2xl border border-indigo-100/90 shadow-2xs flex flex-col justify-between">
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-3">
                                                            <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center flex-shrink-0">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            </div>
                                                            <div>
                                                                <h5 class="text-xs font-black uppercase tracking-wider text-gray-900">3. Turnos</h5>
                                                                <p class="text-[10px] text-gray-400 font-medium">Horarios de venta</p>
                                                            </div>
                                                        </div>

                                                        <div class="space-y-2.5">
                                                            {{-- Turno Matutino --}}
                                                            <div @click="toggleVolumenShift(product, 'morning')"
                                                                 class="cursor-pointer p-3 rounded-xl border transition-all flex items-center justify-between select-none"
                                                                 :class="isVolumenShiftActive(product, 'morning')
                                                                     ? 'bg-amber-50/70 border-amber-300 ring-1 ring-amber-200 shadow-2xs'
                                                                     : 'bg-gray-50/70 border-gray-200 hover:border-gray-300 opacity-60'">
                                                                <div class="flex items-center gap-2.5">
                                                                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                                                    </div>
                                                                    <div>
                                                                        <div class="text-xs font-black text-gray-900">Turno Matutino</div>
                                                                        <div class="text-[10px] text-gray-500 font-medium">07:00 AM - 03:00 PM</div>
                                                                    </div>
                                                                </div>
                                                                <div class="w-5 h-5 rounded-md flex items-center justify-center transition-colors"
                                                                     :class="isVolumenShiftActive(product, 'morning') ? 'bg-amber-500 text-white' : 'border border-gray-300 bg-white'">
                                                                    <svg x-show="isVolumenShiftActive(product, 'morning')" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                                </div>
                                                            </div>

                                                            {{-- Turno Vespertino --}}
                                                            <div @click="toggleVolumenShift(product, 'afternoon')"
                                                                 class="cursor-pointer p-3 rounded-xl border transition-all flex items-center justify-between select-none"
                                                                 :class="isVolumenShiftActive(product, 'afternoon')
                                                                     ? 'bg-indigo-50/70 border-indigo-300 ring-1 ring-indigo-200 shadow-2xs'
                                                                     : 'bg-gray-50/70 border-gray-200 hover:border-gray-300 opacity-60'">
                                                                <div class="flex items-center gap-2.5">
                                                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                                                    </div>
                                                                    <div>
                                                                        <div class="text-xs font-black text-gray-900">Turno Vespertino</div>
                                                                        <div class="text-[10px] text-gray-500 font-medium">03:00 PM - 11:00 PM</div>
                                                                    </div>
                                                                </div>
                                                                <div class="w-5 h-5 rounded-md flex items-center justify-center transition-colors"
                                                                     :class="isVolumenShiftActive(product, 'afternoon') ? 'bg-indigo-500 text-white' : 'border border-gray-300 bg-white'">
                                                                    <svg x-show="isVolumenShiftActive(product, 'afternoon')" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500 font-medium">
                                                        <span>Turnos activos:</span>
                                                        <span class="font-black text-indigo-700" x-text="(product.promo_volumen_shifts || []).length + ' de 2 turnos'"></span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Footer del modal --}}
                    <div class="px-6 py-4 bg-white border-t border-gray-100 flex items-center justify-between">
                        <div class="text-xs text-gray-400 font-medium hidden sm:block">
                            Los cambios de volumen se aplican en tiempo real en la caja y ventas.
                        </div>
                        <button type="button" @click="showPromoVolumenModal = false" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-black transition-colors">
                            Cerrar ventana
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== Modal: Medicamentos Genéricos ====== --}}
        <div x-show="showModal" style="display:none;" class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="showModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-100">

                    {{-- Header del modal --}}
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-extrabold text-gray-900">Medicamentos Genéricos con Stock</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Estos productos reciben el descuento del lunes</p>
                        </div>
                        <button type="button" @click="showModal = false" class="p-2 rounded-lg hover:bg-gray-200 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Buscador dentro del modal --}}
                    <div class="px-6 pt-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" x-model="modalSearch" placeholder="Buscar medicamento..." class="block w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>

                    {{-- Tabla --}}
                    <div class="overflow-y-auto" style="max-height: 400px;">
                        <template x-if="loadingModal">
                            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                                <svg class="w-8 h-8 animate-spin text-green-500 mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <p class="text-sm font-medium">Cargando medicamentos...</p>
                            </div>
                        </template>
                        <template x-if="!loadingModal">
                            <div>
                                {{-- Header de tabla --}}
                                <div class="px-6 py-2 border-b border-gray-100 bg-gray-50">
                                    <div class="grid grid-cols-12 gap-2">
                                        <div class="col-span-5"><p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Medicamento</p></div>
                                        <div class="col-span-2 text-center"><p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Stock</p></div>
                                        <div class="col-span-2 text-right"><p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Precio</p></div>
                                        <div class="col-span-3 text-right"><p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Con descuento</p></div>
                                    </div>
                                </div>
                                <div class="divide-y divide-gray-50">
                                    <template x-for="p in filteredModalProducts" :key="p.sku">
                                        <div class="px-6 py-3 hover:bg-gray-50 transition-colors">
                                            <div class="grid grid-cols-12 gap-2 items-center">
                                                <div class="col-span-5">
                                                    <p class="text-sm font-bold text-gray-900 truncate" x-text="p.name"></p>
                                                    <p class="text-xs text-gray-400" x-text="p.sku + ' · ' + p.category"></p>
                                                </div>
                                                <div class="col-span-2 text-center">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-700" x-text="p.stock + ' uds'"></span>
                                                </div>
                                                <div class="col-span-2 text-right">
                                                    <span class="text-sm font-bold text-gray-500 line-through" x-text="'$' + p.price.toFixed(2)"></span>
                                                </div>
                                                <div class="col-span-3 text-right">
                                                    <span class="text-sm font-black text-emerald-600" x-text="'$' + p.discounted.toFixed(2)"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="filteredModalProducts.length === 0 && !loadingModal">
                                        <div class="px-6 py-12 text-center text-gray-400">
                                            <p class="font-bold text-sm">Sin resultados</p>
                                            <p class="text-xs mt-1">No hay medicamentos que coincidan con la búsqueda.</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Footer del modal --}}
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                        <p class="text-xs text-gray-500" x-text="filteredModalProducts.length + ' medicamentos mostrados'"></p>
                        <button type="button" @click="showModal = false" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-bold text-gray-700 bg-white rounded-xl hover:bg-gray-50 transition-colors">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Custom Confirm Modal --}}
        <div x-show="confirmModal.show" style="display:none;" class="fixed inset-0 z-[130] overflow-y-auto" aria-modal="true" role="dialog" x-cloak>
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="confirmModal.show" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="confirmModal.onCancel()"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="confirmModal.show"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                    <div class="bg-white px-5 pt-6 pb-4 sm:p-6 sm:pb-5">
                        <div class="sm:flex sm:items-start gap-4">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 ring-4 ring-amber-50/50 sm:mx-0 sm:h-11 sm:w-11">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 tracking-tight" id="modal-title">Aviso de Exclusividad</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600 leading-relaxed" x-text="confirmModal.message"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50/80 px-5 py-3.5 border-t border-gray-100 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="button" @click="confirmModal.onConfirm()" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-red-600 text-sm font-bold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto transition-colors">
                            Continuar
                        </button>
                        <button type="button" @click="confirmModal.onCancel()" class="mt-2 sm:mt-0 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 sm:w-auto transition-colors">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Custom In-App Alert Modal (Reemplazo moderno de window.alert) --}}
        <div x-show="alertModal.show" style="display:none;" class="fixed inset-0 z-[140] overflow-y-auto" aria-modal="true" role="dialog" x-cloak>
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="alertModal.show" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" 
                     @click="alertModal.show = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="alertModal.show"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100 relative">
                    
                    <div class="bg-white px-6 pt-6 pb-5">
                        <div class="sm:flex sm:items-start gap-4">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl sm:mx-0 shadow-sm"
                                 :class="{
                                     'bg-amber-50 text-amber-600 ring-4 ring-amber-50/60': alertModal.type === 'warning',
                                     'bg-rose-50 text-rose-600 ring-4 ring-rose-50/60': alertModal.type === 'error',
                                     'bg-blue-50 text-blue-600 ring-4 ring-blue-50/60': alertModal.type === 'info',
                                     'bg-emerald-50 text-emerald-600 ring-4 ring-emerald-50/60': alertModal.type === 'success'
                                 }">
                                <template x-if="alertModal.type === 'warning'">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </template>
                                <template x-if="alertModal.type === 'error'">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </template>
                                <template x-if="alertModal.type === 'success'">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </template>
                                <template x-if="alertModal.type === 'info'">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </template>
                            </div>

                            <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-bold text-gray-900 tracking-tight" x-text="alertModal.title"></h3>
                                    <button @click="alertModal.show = false" class="text-gray-400 hover:text-gray-500 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div class="mt-2.5">
                                    <p class="text-sm text-gray-600 leading-relaxed font-normal whitespace-pre-line" x-text="alertModal.message"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50/80 px-6 py-3.5 border-t border-gray-100 sm:flex sm:flex-row-reverse">
                        <button type="button" 
                                @click="alertModal.show = false" 
                                class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-2"
                                :class="{
                                    'bg-amber-600 hover:bg-amber-700 text-white focus:ring-amber-500 shadow-amber-600/20': alertModal.type === 'warning',
                                    'bg-rose-600 hover:bg-rose-700 text-white focus:ring-rose-500 shadow-rose-600/20': alertModal.type === 'error',
                                    'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500 shadow-blue-600/20': alertModal.type === 'info',
                                    'bg-emerald-600 hover:bg-emerald-700 text-white focus:ring-emerald-500 shadow-emerald-600/20': alertModal.type === 'success'
                                }">
                            Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sistema de Notificaciones Toast Flotante In-App --}}
        <div class="fixed top-6 right-6 z-[150] max-w-sm w-full pointer-events-none" x-cloak>
            <div x-show="toast.show"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-[-16px] opacity-0 scale-95"
                 x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 style="display:none;"
                 class="pointer-events-auto bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex items-start p-4 relative ring-1 ring-black/5"
                 :class="{
                     'border-l-4 border-l-emerald-500': toast.type === 'success',
                     'border-l-4 border-l-amber-500': toast.type === 'warning',
                     'border-l-4 border-l-rose-500': toast.type === 'error',
                     'border-l-4 border-l-blue-500': toast.type === 'info'
                 }">
                <div class="flex-shrink-0 mt-0.5">
                    <template x-if="toast.type === 'success'">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </template>
                </div>
                <div class="ml-3 flex-1 pr-2">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400" x-text="toast.type === 'success' ? 'Éxito' : (toast.type === 'warning' ? 'Atención' : (toast.type === 'error' ? 'Error' : 'Aviso'))"></p>
                    <p class="text-sm font-medium text-gray-800 mt-0.5 leading-snug" x-text="toast.message"></p>
                </div>
                <button type="button" @click="toast.show = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

    </div>

    <script>
        function promosManager() {
            return {
                confirmModal: {
                    show: false,
                    message: '',
                    onConfirm: () => {},
                    onCancel: () => {}
                },
                showConfirm(msg, onConfirm, onCancel = () => {}) {
                    this.confirmModal.message = msg;
                    this.confirmModal.onConfirm = () => {
                        this.confirmModal.show = false;
                        onConfirm();
                    };
                    this.confirmModal.onCancel = () => {
                        this.confirmModal.show = false;
                        onCancel();
                    };
                    this.confirmModal.show = true;
                },

                alertModal: {
                    show: false,
                    title: 'Atención',
                    message: '',
                    type: 'warning'
                },
                showAlert(msg, title = 'Atención', type = 'warning') {
                    this.alertModal.message = msg;
                    this.alertModal.title = title;
                    this.alertModal.type = type;
                    this.alertModal.show = true;
                },

                toast: {
                    show: false,
                    message: '',
                    type: 'info',
                    timeout: null
                },
                showToast(msg, type = 'info', duration = 4000) {
                    if (this.toast.timeout) clearTimeout(this.toast.timeout);
                    this.toast.message = msg;
                    this.toast.type = type;
                    this.toast.show = true;
                    this.toast.timeout = setTimeout(() => {
                        this.toast.show = false;
                    }, duration);
                },

                isToday4x3: @json((bool)$isToday4x3),
                isTodayVolumen: @json((bool)$isTodayVolumen),
                todayName: @json($todayName ?? ''),
                isProductActiveToday(days) {
                    return Array.isArray(days) && days.includes(this.todayName);
                },

                discount: {{ $genericoDiscount ?? 25 }},
                selectedDays: @json($genericoDays ?? []),
                selectedShifts: @json($genericoShifts ?? []),
                saving: false,
                savingSettings: false,

                recetaDiscount: {{ $recetaDiscount ?? 20 }},
                recetaSelectedDays: @json($recetaDays ?? ['Miércoles']),
                recetaSelectedShifts: @json($recetaShifts ?? ['morning', 'afternoon']),
                savingReceta: false,
                savingRecetaSettings: false,

                saved: false,
                savedMsg: '',
                showModal: false,
                loadingModal: false,
                modalProducts: [],
                modalSearch: '',

                get filteredModalProducts() {
                    if (!this.modalSearch) return this.modalProducts;
                    const q = this.modalSearch.toLowerCase();
                    return this.modalProducts.filter(p =>
                        p.name.toLowerCase().includes(q) ||
                        (p.sku && p.sku.toLowerCase().includes(q)) ||
                        (p.category && p.category.toLowerCase().includes(q))
                    );
                },

                showSaved(msg) {
                    this.savedMsg = msg;
                    this.saved = true;
                    setTimeout(() => this.saved = false, 4000);
                    this.showToast(msg, 'success');
                },

                toggleGenericoDay(day) {
                    if (this.selectedDays.includes(day)) {
                        this.selectedDays = this.selectedDays.filter(d => d !== day);
                    } else {
                        if (this.recetaSelectedDays.includes(day)) {
                            this.showConfirm(`El día "${day}" ya está activo en Receta Completa. Como son promociones mutuamente excluyentes, ¿deseas removerlo de Receta Completa y asignarlo a Genéricos?`, () => {
                                this.recetaSelectedDays = this.recetaSelectedDays.filter(d => d !== day);
                                this.selectedDays.push(day);
                            });
                            return;
                        }
                        this.selectedDays.push(day);
                    }
                },

                toggleRecetaDay(day) {
                    if (this.recetaSelectedDays.includes(day)) {
                        this.recetaSelectedDays = this.recetaSelectedDays.filter(d => d !== day);
                    } else {
                        if (this.selectedDays.includes(day)) {
                            this.showConfirm(`El día "${day}" ya está activo en la Promo de Genéricos. Como son promociones mutuamente excluyentes, ¿deseas removerlo de Genéricos y asignarlo a Receta Completa?`, () => {
                                this.selectedDays = this.selectedDays.filter(d => d !== day);
                                this.recetaSelectedDays.push(day);
                            });
                            return;
                        }
                        this.recetaSelectedDays.push(day);
                    }
                },

                saveSettings() {
                    if (this.selectedDays.length === 0) { 
                        this.showAlert('Debes seleccionar al menos un día de la semana para la promoción de Genéricos.', 'Días requeridos', 'warning'); 
                        return; 
                    }
                    if (this.selectedShifts.length === 0) { 
                        this.showAlert('Debes seleccionar al menos un turno (matutino o vespertino) para la promoción de Genéricos.', 'Turnos requeridos', 'warning'); 
                        return; 
                    }
                    
                    const conflicts = this.selectedDays.filter(d => this.recetaSelectedDays.includes(d));
                    if (conflicts.length > 0) {
                        this.showAlert('Genéricos y Receta Completa son promociones mutuamente excluyentes. No pueden activarse el mismo día: ' + conflicts.join(', '), 'Conflicto de Promociones', 'warning');
                        return;
                    }

                    this.savingSettings = true;
                    fetch('{{ route('promos.updateSettings') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                        body: JSON.stringify({ discount: this.discount, days: this.selectedDays, shifts: this.selectedShifts })
                    })
                    .then(r => r.ok ? r.json() : r.json().then(err => Promise.reject(err)))
                    .then(data => { 
                        if (data.success) { 
                            this.showSaved('Configuración de Genéricos guardada correctamente.'); 
                            setTimeout(() => window.location.reload(), 1000); 
                        } 
                    })
                    .catch(err => {
                        this.showAlert(err.message || (err.errors ? Object.values(err.errors).flat().join('\n') : 'Ocurrió un error al guardar la configuración.'), 'Error al Guardar', 'error');
                    })
                    .finally(() => this.savingSettings = false);
                },

                saveRecetaSettings() {
                    if (this.recetaSelectedDays.length === 0) { 
                        this.showAlert('Debes seleccionar al menos un día de la semana para la promoción de Receta Completa.', 'Días requeridos', 'warning'); 
                        return; 
                    }
                    if (this.recetaSelectedShifts.length === 0) { 
                        this.showAlert('Debes seleccionar al menos un turno (matutino o vespertino) para la promoción de Receta Completa.', 'Turnos requeridos', 'warning'); 
                        return; 
                    }

                    const conflicts = this.recetaSelectedDays.filter(d => this.selectedDays.includes(d));
                    if (conflicts.length > 0) {
                        this.showAlert('Receta Completa y Genéricos son promociones mutuamente excluyentes. No pueden activarse el mismo día: ' + conflicts.join(', '), 'Conflicto de Promociones', 'warning');
                        return;
                    }

                    this.savingRecetaSettings = true;
                    fetch('{{ route('promos.updateRecetaSettings') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                        body: JSON.stringify({ discount: this.recetaDiscount, days: this.recetaSelectedDays, shifts: this.recetaSelectedShifts })
                    })
                    .then(r => r.ok ? r.json() : r.json().then(err => Promise.reject(err)))
                    .then(data => { 
                        if (data.success) { 
                            this.showSaved('Configuración de Receta Completa guardada correctamente.'); 
                            setTimeout(() => window.location.reload(), 1000); 
                        } 
                    })
                    .catch(err => {
                        this.showAlert(err.message || (err.errors ? Object.values(err.errors).flat().join('\n') : 'Ocurrió un error al guardar la configuración.'), 'Error al Guardar', 'error');
                    })
                    .finally(() => this.savingRecetaSettings = false);
                },

                openModal() {
                    this.showModal = true;
                    this.modalSearch = '';
                    if (this.modalProducts.length > 0) return;
                    this.loadingModal = true;
                    fetch('{{ route('promos.genericProducts') }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(data => { this.modalProducts = data; })
                    .catch(() => this.showAlert('No fue posible cargar el catálogo de medicamentos genéricos.', 'Error de Conexión', 'error'))
                    .finally(() => this.loadingModal = false);
                },

                promo4x3Count: {{ $promo4x3Count ?? 0 }},
                
                showPromo4x3Modal: false,
                loadingPromo4x3Modal: false,
                promo4x3Products: [],
                promo4x3Search: '',
                promo4x3Tab: 'all',

                get filtered4x3Products() {
                    if (this.promo4x3Tab === 'active') {
                        return this.promo4x3Products.filter(p => p.is_promo_4x3);
                    }
                    return this.promo4x3Products;
                },

                searchPromo4x3() {
                    this.loadingPromo4x3Modal = true;
                    fetch(`{{ route('promos.searchProducts4x3') }}?q=${encodeURIComponent(this.promo4x3Search)}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(data => { 
                        this.promo4x3Products = data.map(p => {
                            if (!p.promo_4x3_days || !Array.isArray(p.promo_4x3_days)) p.promo_4x3_days = ['Jueves'];
                            if (!p.promo_4x3_shifts || !Array.isArray(p.promo_4x3_shifts)) p.promo_4x3_shifts = ['morning', 'afternoon'];
                            if (!p.promo_4x3_branches || !Array.isArray(p.promo_4x3_branches)) p.promo_4x3_branches = ['all'];
                            p._expanded = false;
                            return p;
                        }); 
                    })
                    .catch(() => this.showAlert('No fue posible cargar los medicamentos para Promo 4x3.', 'Error de Conexión', 'error'))
                    .finally(() => this.loadingPromo4x3Modal = false);
                },

                openPromo4x3Modal() {
                    this.showPromo4x3Modal = true;
                    this.promo4x3Search = '';
                    this.promo4x3Tab = 'all';
                    this.searchPromo4x3(); // Initial load: returns active ones
                },

                toggle4x3Drilldown(product) {
                    product._expanded = !product._expanded;
                },

                toggle4x3Day(product, day) {
                    if (!Array.isArray(product.promo_4x3_days)) product.promo_4x3_days = [];
                    const idx = product.promo_4x3_days.indexOf(day);
                    if (idx > -1) {
                        product.promo_4x3_days.splice(idx, 1);
                    } else {
                        product.promo_4x3_days.push(day);
                    }
                },

                is4x3DayActive(product, day) {
                    return Array.isArray(product.promo_4x3_days) && product.promo_4x3_days.includes(day);
                },

                set4x3QuickDays(product, type) {
                    if (type === 'all') {
                        product.promo_4x3_days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                    } else if (type === 'weekdays') {
                        product.promo_4x3_days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                    } else if (type === 'weekend') {
                        product.promo_4x3_days = ['Sábado', 'Domingo'];
                    }
                },

                toggle4x3Branch(product, branchVal) {
                    if (!Array.isArray(product.promo_4x3_branches)) product.promo_4x3_branches = [];
                    if (branchVal === 'all') {
                        product.promo_4x3_branches = ['all'];
                        return;
                    }
                    product.promo_4x3_branches = product.promo_4x3_branches.filter(b => b !== 'all');
                    const strVal = String(branchVal);
                    const idx = product.promo_4x3_branches.findIndex(b => String(b) === strVal);
                    if (idx > -1) {
                        product.promo_4x3_branches.splice(idx, 1);
                        if (product.promo_4x3_branches.length === 0) {
                            product.promo_4x3_branches = ['all'];
                        }
                    } else {
                        product.promo_4x3_branches.push(branchVal);
                    }
                },

                is4x3BranchActive(product, branchVal) {
                    if (!Array.isArray(product.promo_4x3_branches)) return false;
                    if (branchVal === 'all') return product.promo_4x3_branches.includes('all');
                    return product.promo_4x3_branches.map(String).includes(String(branchVal));
                },

                toggle4x3Shift(product, shift) {
                    if (!Array.isArray(product.promo_4x3_shifts)) product.promo_4x3_shifts = [];
                    const idx = product.promo_4x3_shifts.indexOf(shift);
                    if (idx > -1) {
                        product.promo_4x3_shifts.splice(idx, 1);
                    } else {
                        product.promo_4x3_shifts.push(shift);
                    }
                },

                is4x3ShiftActive(product, shift) {
                    return Array.isArray(product.promo_4x3_shifts) && product.promo_4x3_shifts.includes(shift);
                },

                togglePromo4x3(product, isToggleAction = true) {
                    let nextState = product.is_promo_4x3;
                    if (isToggleAction) {
                        nextState = !product.is_promo_4x3;
                    }

                    const executeToggle = () => {
                        const originalState = product.is_promo_4x3;
                        product.is_promo_4x3 = nextState;
                        
                        if(originalState !== nextState) {
                            if(nextState) { 
                                this.promo4x3Count++; 
                                product._expanded = true; // Auto-abrir drilldown al activar
                            } else { 
                                this.promo4x3Count--; 
                            }
                        }
                        
                        let payload = { is_promo_4x3: product.is_promo_4x3 };
                        if (product.is_promo_4x3) {
                            if (!product.promo_4x3_days || product.promo_4x3_days.length === 0) { 
                                this.showAlert('Debes seleccionar al menos un día de la semana para activar este producto en Promo 4x3.', 'Días requeridos', 'warning'); 
                                product.is_promo_4x3 = originalState; 
                                return; 
                            }
                            if (!product.promo_4x3_shifts || product.promo_4x3_shifts.length === 0) { 
                                this.showAlert('Debes seleccionar al menos un turno para activar este producto en Promo 4x3.', 'Turnos requeridos', 'warning'); 
                                product.is_promo_4x3 = originalState; 
                                return; 
                            }
                            if (!product.promo_4x3_branches || product.promo_4x3_branches.length === 0) { 
                                this.showAlert('Debes seleccionar al menos una sucursal para activar este producto en Promo 4x3.', 'Sucursales requeridas', 'warning'); 
                                product.is_promo_4x3 = originalState; 
                                return; 
                            }
                            
                            payload.promo_4x3_days = product.promo_4x3_days;
                            payload.promo_4x3_shifts = product.promo_4x3_shifts;
                            payload.promo_4x3_branches = product.promo_4x3_branches;
                        }

                        fetch(`/promos/4x3-products/${product.id}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                            body: JSON.stringify(payload)
                        })
                        .then(r => r.ok ? r.json() : r.json().then(err => Promise.reject(err)))
                        .then(data => { 
                            if (!data.success) {
                                product.is_promo_4x3 = originalState;
                                if(originalState !== nextState) {
                                    if(originalState) { this.promo4x3Count++; } else { this.promo4x3Count--; }
                                }
                                this.showAlert('No fue posible actualizar el producto en Promo 4x3.', 'Error al Actualizar', 'error');
                            } else {
                                product.is_promo_4x3 = Boolean(data.is_promo_4x3);
                                if (data.isToday4x3 !== undefined) this.isToday4x3 = Boolean(data.isToday4x3);
                                if (data.isTodayVolumen !== undefined) this.isTodayVolumen = Boolean(data.isTodayVolumen);
                                if (data.promo4x3Count !== undefined) this.promo4x3Count = data.promo4x3Count;
                                if (data.promoVolumenCount !== undefined) this.promoVolumenCount = data.promoVolumenCount;

                                window.dispatchEvent(new CustomEvent('promos-status-updated', {
                                    detail: {
                                        isToday4x3: this.isToday4x3,
                                        isTodayVolumen: this.isTodayVolumen
                                    }
                                }));

                                if (product.is_promo_4x3) {
                                    if (product.is_promo_volumen) {
                                        product.is_promo_volumen = false;
                                        this.promoVolumenCount = Math.max(0, this.promoVolumenCount - 1);
                                    }
                                }
                                this.showSaved('Producto actualizado correctamente en Promo 4x3.');
                            }
                        })
                        .catch(err => {
                            product.is_promo_4x3 = originalState;
                            if(originalState !== nextState) {
                                if(originalState) { this.promo4x3Count++; } else { this.promo4x3Count--; }
                            }
                            let msg = err.message || 'Error de conexión.';
                            if (err.errors) {
                                msg = Object.values(err.errors).flat().join('\n');
                            }
                            this.showAlert(msg, 'Error al Actualizar', 'error');
                        });
                    };

                    if (nextState && product.is_promo_volumen) {
                        this.showConfirm('Este producto ya está en la Promo x Volumen. Si lo agregas a 4x3, se eliminará de Volumen. ¿Deseas continuar?', () => {
                            executeToggle();
                        });
                        return;
                    }

                    executeToggle();
                },

                openVolumen: false,
                promoVolumenCount: {{ $promoVolumenCount ?? 0 }},
                
                showPromoVolumenModal: false,
                loadingPromoVolumenModal: false,
                promoVolumenProducts: [],
                promoVolumenSearch: '',
                promoVolumenTab: 'all',

                get filteredVolumenProducts() {
                    if (this.promoVolumenTab === 'active') {
                        return this.promoVolumenProducts.filter(p => p.is_promo_volumen);
                    }
                    return this.promoVolumenProducts;
                },

                searchPromoVolumen() {
                    this.loadingPromoVolumenModal = true;
                    fetch(`{{ route('promos.searchProductsVolumen') }}?q=${encodeURIComponent(this.promoVolumenSearch)}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(data => { 
                        this.promoVolumenProducts = data.map(p => {
                            if (!p.promo_volumen_days || !Array.isArray(p.promo_volumen_days)) p.promo_volumen_days = ['Viernes'];
                            if (!p.promo_volumen_shifts || !Array.isArray(p.promo_volumen_shifts)) p.promo_volumen_shifts = ['morning', 'afternoon'];
                            if (!p.promo_volumen_branches || !Array.isArray(p.promo_volumen_branches)) p.promo_volumen_branches = ['all'];
                            if (!p.promo_volumen_min_qty) p.promo_volumen_min_qty = 3;
                            if (p.promo_volumen_price === null || p.promo_volumen_price === undefined) {
                                p.promo_volumen_price = p.regular_price ? (p.regular_price * 0.9).toFixed(2) : 0;
                            }
                            p._expanded = false;
                            return p;
                        }); 
                    })
                    .catch(() => this.showAlert('No fue posible cargar los medicamentos para Promo por Volumen.', 'Error de Conexión', 'error'))
                    .finally(() => this.loadingPromoVolumenModal = false);
                },

                openPromoVolumenModal() {
                    this.showPromoVolumenModal = true;
                    this.promoVolumenSearch = '';
                    this.promoVolumenTab = 'all';
                    this.searchPromoVolumen(); // Initial load: returns active ones
                },

                toggleVolumenDrilldown(product) {
                    product._expanded = !product._expanded;
                },

                toggleVolumenDay(product, day) {
                    if (!Array.isArray(product.promo_volumen_days)) product.promo_volumen_days = [];
                    const idx = product.promo_volumen_days.indexOf(day);
                    if (idx > -1) {
                        product.promo_volumen_days.splice(idx, 1);
                    } else {
                        product.promo_volumen_days.push(day);
                    }
                },

                isVolumenDayActive(product, day) {
                    return Array.isArray(product.promo_volumen_days) && product.promo_volumen_days.includes(day);
                },

                setVolumenQuickDays(product, type) {
                    if (type === 'all') {
                        product.promo_volumen_days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                    } else if (type === 'weekdays') {
                        product.promo_volumen_days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                    } else if (type === 'weekend') {
                        product.promo_volumen_days = ['Sábado', 'Domingo'];
                    }
                },

                toggleVolumenBranch(product, branchVal) {
                    if (!Array.isArray(product.promo_volumen_branches)) product.promo_volumen_branches = [];
                    if (branchVal === 'all') {
                        product.promo_volumen_branches = ['all'];
                        return;
                    }
                    product.promo_volumen_branches = product.promo_volumen_branches.filter(b => b !== 'all');
                    const strVal = String(branchVal);
                    const idx = product.promo_volumen_branches.findIndex(b => String(b) === strVal);
                    if (idx > -1) {
                        product.promo_volumen_branches.splice(idx, 1);
                        if (product.promo_volumen_branches.length === 0) {
                            product.promo_volumen_branches = ['all'];
                        }
                    } else {
                        product.promo_volumen_branches.push(branchVal);
                    }
                },

                isVolumenBranchActive(product, branchVal) {
                    if (!Array.isArray(product.promo_volumen_branches)) return false;
                    if (branchVal === 'all') return product.promo_volumen_branches.includes('all');
                    return product.promo_volumen_branches.map(String).includes(String(branchVal));
                },

                toggleVolumenShift(product, shift) {
                    if (!Array.isArray(product.promo_volumen_shifts)) product.promo_volumen_shifts = [];
                    const idx = product.promo_volumen_shifts.indexOf(shift);
                    if (idx > -1) {
                        product.promo_volumen_shifts.splice(idx, 1);
                    } else {
                        product.promo_volumen_shifts.push(shift);
                    }
                },

                isVolumenShiftActive(product, shift) {
                    return Array.isArray(product.promo_volumen_shifts) && product.promo_volumen_shifts.includes(shift);
                },

                getPromoSavings(product) {
                    const reg = parseFloat(product.regular_price || 0);
                    const vol = parseFloat(product.promo_volumen_price || 0);
                    if (reg > 0 && vol > 0 && vol < reg) {
                        const diff = (reg - vol).toFixed(2);
                        const pct = Math.round(((reg - vol) / reg) * 100);
                        return { diff, pct };
                    }
                    return null;
                },

                togglePromoVolumen(product, toggle = true) {
                    let nextState = product.is_promo_volumen;
                    if (toggle) {
                        nextState = !product.is_promo_volumen;
                    }

                    if (nextState && (!product.promo_volumen_min_qty || !product.promo_volumen_price)) {
                        this.showAlert('Debes ingresar la cantidad mínima de piezas y el precio por volumen antes de activar este producto.', 'Datos Incompletos', 'warning');
                        return;
                    }

                    const executeToggle = () => {
                        const originalState = product.is_promo_volumen;
                        product.is_promo_volumen = nextState;
                        
                        if(originalState !== nextState) {
                            if(nextState) { 
                                this.promoVolumenCount++; 
                                product._expanded = true; // Auto-abrir drilldown al activar
                            } else { 
                                this.promoVolumenCount--; 
                            }
                        }
                        
                        let payload = {
                            is_promo_volumen: product.is_promo_volumen,
                            promo_volumen_min_qty: product.promo_volumen_min_qty,
                            promo_volumen_price: product.promo_volumen_price
                        };
                        if (product.is_promo_volumen) {
                            if (!product.promo_volumen_days || product.promo_volumen_days.length === 0) { 
                                this.showAlert('Debes seleccionar al menos un día de la semana para activar este producto en Promo Volumen.', 'Días requeridos', 'warning'); 
                                product.is_promo_volumen = originalState; 
                                return; 
                            }
                            if (!product.promo_volumen_shifts || product.promo_volumen_shifts.length === 0) { 
                                this.showAlert('Debes seleccionar al menos un turno para activar este producto en Promo Volumen.', 'Turnos requeridos', 'warning'); 
                                product.is_promo_volumen = originalState; 
                                return; 
                            }
                            if (!product.promo_volumen_branches || product.promo_volumen_branches.length === 0) { 
                                this.showAlert('Debes seleccionar al menos una sucursal para activar este producto en Promo Volumen.', 'Sucursales requeridas', 'warning'); 
                                product.is_promo_volumen = originalState; 
                                return; 
                            }
                            
                            payload.promo_volumen_days = product.promo_volumen_days;
                            payload.promo_volumen_shifts = product.promo_volumen_shifts;
                            payload.promo_volumen_branches = product.promo_volumen_branches;
                        }

                        fetch(`/promos/volumen-products/${product.id}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                            body: JSON.stringify(payload)
                        })
                        .then(r => r.ok ? r.json() : r.json().then(err => Promise.reject(err)))
                        .then(data => { 
                            if (!data.success) {
                                product.is_promo_volumen = originalState;
                                if(originalState !== nextState) {
                                    if(originalState) { this.promoVolumenCount++; } else { this.promoVolumenCount--; }
                                }
                                this.showAlert('No fue posible actualizar el producto en Promo Volumen.', 'Error al Actualizar', 'error');
                            } else {
                                product.is_promo_volumen = Boolean(data.is_promo_volumen);
                                if (data.isTodayVolumen !== undefined) this.isTodayVolumen = Boolean(data.isTodayVolumen);
                                if (data.isToday4x3 !== undefined) this.isToday4x3 = Boolean(data.isToday4x3);
                                if (data.promoVolumenCount !== undefined) this.promoVolumenCount = data.promoVolumenCount;
                                if (data.promo4x3Count !== undefined) this.promo4x3Count = data.promo4x3Count;

                                window.dispatchEvent(new CustomEvent('promos-status-updated', {
                                    detail: {
                                        isToday4x3: this.isToday4x3,
                                        isTodayVolumen: this.isTodayVolumen
                                    }
                                }));

                                if (product.is_promo_volumen) {
                                    if (product.is_promo_4x3) {
                                        product.is_promo_4x3 = false;
                                        this.promo4x3Count = Math.max(0, this.promo4x3Count - 1);
                                    }
                                }
                                this.showSaved('Producto actualizado correctamente en Promo Volumen.');
                            }
                        })
                        .catch(err => {
                            product.is_promo_volumen = originalState;
                            if(originalState !== nextState) {
                                if(originalState) { this.promoVolumenCount++; } else { this.promoVolumenCount--; }
                            }
                            let msg = err.message || 'Error de conexión.';
                            if (err.errors) {
                                msg = Object.values(err.errors).flat().join('\n');
                            }
                            this.showAlert(msg, 'Error al Actualizar', 'error');
                        });
                    };

                    if (nextState && product.is_promo_4x3) {
                        this.showConfirm('Este producto ya está en la Promo 4x3. Si lo agregas a Volumen, se eliminará de la Promo 4x3. ¿Deseas continuar?', () => {
                            executeToggle();
                        });
                        return;
                    }

                    executeToggle();
                }
            }
        }
    </script>
</x-app-layout>
