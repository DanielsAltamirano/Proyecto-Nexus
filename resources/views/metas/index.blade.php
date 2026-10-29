<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-purple-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>

            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Metas de Venta
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Establece la meta de venta diaria por sucursal y turno. Se revisa cada semana.
                    </p>
                    <div class="mt-4 inline-flex items-center px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100">
                        <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-xs font-bold text-blue-800">
                            Revisión de la semana del {{ now()->startOfWeek()->translatedFormat('d \d\e F') }} al {{ now()->endOfWeek()->translatedFormat('d \d\e F \d\e Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center p-4 text-green-800 bg-green-50 border border-green-200 rounded-xl shadow-sm" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('metas.update') }}" method="POST">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-2xl">

                    {{-- Header de tabla --}}
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                        <div class="grid grid-cols-12 gap-4 items-center">
                            <div class="col-span-4">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Sucursal</p>
                            </div>
                            <div class="col-span-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-amber-400 shadow-sm shadow-amber-300"></div>
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Turno Matutino (MXN)</p>
                                </div>
                            </div>
                            <div class="col-span-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-indigo-400 shadow-sm shadow-indigo-300"></div>
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Turno Vespertino (MXN)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filas por sucursal --}}
                    <div class="divide-y divide-gray-100">
                        @forelse($branches as $index => $branch)
                            @php $goal = $branch->goal ?? null; @endphp
                            <div class="px-6 py-5 hover:bg-gray-50/60 transition-colors duration-150">
                                <div class="grid grid-cols-12 gap-4 items-center">

                                    {{-- Nombre de sucursal --}}
                                    <div class="col-span-4 flex flex-col justify-center">
                                        <input type="hidden" name="metas[{{ $index }}][branch_id]" value="{{ $branch->id }}">
                                        <input type="hidden" name="metas[{{ $index }}][is_shared_goal]" value="0">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-green-600 to-green-800 flex items-center justify-center text-white font-extrabold text-sm shadow-sm flex-shrink-0">
                                                {{ strtoupper(substr($branch->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">{{ $branch->name }}</p>
                                                <label class="inline-flex items-center mt-1 cursor-pointer group">
                                                    <input type="checkbox" name="metas[{{ $index }}][is_shared_goal]" value="1" class="rounded text-green-600 focus:ring-green-500 w-3.5 h-3.5 border-gray-300" {{ ($goal?->is_shared_goal) ? 'checked' : '' }}>
                                                    <span class="ml-1.5 text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-green-700 transition-colors">Meta Compartida (Todo el día)</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Meta matutino --}}
                                    <div class="col-span-4">
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-amber-500 font-bold text-sm">$</span>
                                            <input
                                                type="number"
                                                name="metas[{{ $index }}][morning_goal]"
                                                min="0"
                                                step="any"
                                                value="{{ old('metas.'.$index.'.morning_goal', $goal?->morning_goal ?? '') }}"
                                                placeholder="0.00"
                                                class="block w-full rounded-xl border-gray-300 pl-8 pr-4 py-2.5 text-sm shadow-sm focus:border-amber-400 focus:ring-amber-400 transition-colors"
                                            >
                                        </div>
                                    </div>

                                    {{-- Meta vespertino --}}
                                    <div class="col-span-4">
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-indigo-500 font-bold text-sm">$</span>
                                            <input
                                                type="number"
                                                name="metas[{{ $index }}][afternoon_goal]"
                                                min="0"
                                                step="any"
                                                value="{{ old('metas.'.$index.'.afternoon_goal', $goal?->afternoon_goal ?? '') }}"
                                                placeholder="0.00"
                                                class="block w-full rounded-xl border-gray-300 pl-8 pr-4 py-2.5 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400 transition-colors"
                                            >
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-14 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <p class="font-bold text-sm">No hay sucursales registradas.</p>
                                <p class="text-xs mt-1">Agrega sucursales desde el módulo de Personal y Sucursales.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Botones --}}
                @if($branches->count() > 0)
                <div class="flex items-center justify-end gap-4 mt-6">
                    <p class="text-sm text-gray-400 mr-auto">
                        <svg class="w-4 h-4 inline-block mr-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Las metas son diarias por turno. Revisa y ajusta cada semana según sea necesario.
                    </p>
                    <button type="submit"
                        class="group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-green-700 rounded-xl hover:bg-green-800 hover:-translate-y-1 hover:shadow-lg hover:shadow-green-500/30 whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Metas
                    </button>
                </div>
                @endif

            </form>
        </div>
    </div>
</x-app-layout>
