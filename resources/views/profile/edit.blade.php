<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Columna Izquierda: Tarjeta de Puntos -->
                <div class="lg:col-span-1 space-y-6" x-data="{ showZoom: false }">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col items-center">
                        <h3 class="text-lg font-extrabold text-gray-900 w-full mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                            Mi Tarjeta Nexus
                        </h3>

                        <!-- Diseño tipo Tarjeta de Crédito -->
                        <div @click="showZoom = true" class="cursor-pointer w-full max-w-sm aspect-[1.58] bg-gradient-to-tr from-purple-950 via-purple-900 to-indigo-950 rounded-2xl shadow-2xl relative overflow-hidden text-white flex flex-col p-5 transform transition-all duration-300 hover:scale-105 hover:shadow-purple-900/20">
                            <!-- Decoración de fondo de la tarjeta -->
                            <div class="absolute -right-16 -top-16 w-48 h-48 bg-purple-500 rounded-full mix-blend-overlay filter blur-3xl opacity-40 pointer-events-none"></div>
                            <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-violet-500 rounded-full mix-blend-overlay filter blur-3xl opacity-40 pointer-events-none"></div>

                            <!-- Top: Chip y Logo -->
                            <div class="flex justify-between items-start relative z-10 w-full">
                                <!-- Contactless Icon -->
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('img/nexusbanner.jpeg') }}" alt="Nexus ERP" class="h-6 object-contain rounded opacity-95">
                                </div>
                                <svg class="w-6 h-6 text-purple-200 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07m-4.243-2.829a1 1 0 11-1.414-1.414 1 1 0 011.414 1.414z"></path></svg>
                            </div>

                            <!-- Middle: Barcode -->
                            <div class="w-full bg-white/95 p-1.5 rounded-lg flex flex-col items-center justify-center relative z-10 mt-auto mb-3 shadow-inner min-h-[56px]">
                                @if($user->barcode)
                                    <svg id="loyaltyBarcode" class="w-full h-10"></svg>
                                    <p class="text-gray-800 text-[9px] font-mono mt-0.5 font-bold tracking-[0.2em]">{{ $user->barcode }}</p>
                                @else
                                    <p class="text-gray-400 text-xs font-bold tracking-widest mt-2">CÓDIGO PENDIENTE</p>
                                @endif
                            </div>

                            <!-- Bottom: Info -->
                            <div class="flex justify-between items-end relative z-10 w-full mt-auto">
                                <div class="overflow-hidden">
                                    <p class="text-[8px] text-purple-200 uppercase tracking-widest mb-0.5 opacity-80">Titular de la Tarjeta</p>
                                    <p class="font-medium text-xs tracking-widest uppercase truncate max-w-[160px]">{{ $user->name }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-[8px] text-purple-200 uppercase tracking-widest mb-0.5 opacity-80">Puntos</p>
                                    <p class="font-black text-base text-yellow-400 leading-none">{{ number_format($user->points) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen estadístico -->
                        <div class="w-full mt-6 grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex flex-col items-center justify-center">
                                <span class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Visitas</span>
                                <span class="text-2xl font-black text-purple-600">{{ number_format($user->visits) }}</span>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex flex-col items-center justify-center">
                                <span class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Puntos</span>
                                <span class="text-2xl font-black text-yellow-500">{{ number_format($user->points) }}</span>
                            </div>
                        </div>

                        <div class="w-full mt-6 pt-6 border-t border-gray-100">
                            <p class="text-xs text-center text-gray-400">
                                Haz clic en la tarjeta para agrandarla o mostrarla al realizar tus compras.
                            </p>
                        </div>
                    </div>

                    <!-- Modal Zoom Tarjeta -->
                    <div x-show="showZoom" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div x-show="showZoom" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity backdrop-blur-sm" @click="showZoom = false"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            
                            <div x-show="showZoom"
                                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                 class="inline-block align-bottom bg-transparent text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle w-full max-w-2xl">
                                
                                <div class="w-full aspect-[1.3] sm:aspect-[1.58] bg-gradient-to-tr from-purple-950 via-purple-900 to-indigo-950 rounded-2xl sm:rounded-3xl shadow-2xl relative overflow-hidden text-white flex flex-col p-5 sm:p-10">
                                    <button @click="showZoom = false" class="absolute top-2 right-2 sm:top-4 sm:right-4 text-white/50 hover:text-white z-50 p-2">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>

                                    <!-- Decoración de fondo de la tarjeta ampliada -->
                                    <div class="absolute -right-24 -top-24 w-72 h-72 bg-purple-500 rounded-full mix-blend-overlay filter blur-3xl opacity-40 pointer-events-none"></div>
                                    <div class="absolute -left-24 -bottom-24 w-72 h-72 bg-violet-500 rounded-full mix-blend-overlay filter blur-3xl opacity-40 pointer-events-none"></div>

                                    <!-- Top: Chip y Logo -->
                                    <div class="flex justify-between items-start relative z-10 w-full">
                                        <!-- Contactless Icon -->
                                        <div class="flex items-center gap-4">
                                            <img src="{{ asset('img/nexusbanner.jpeg') }}" alt="Nexus ERP" class="h-7 sm:h-10 object-contain rounded opacity-95">
                                        </div>
                                        <svg class="w-7 h-7 sm:w-10 sm:h-10 text-purple-200 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07m-4.243-2.829a1 1 0 11-1.414-1.414 1 1 0 011.414 1.414z"></path></svg>
                                    </div>

                                    <!-- Middle: Barcode -->
                                    <div class="w-full bg-white/95 p-3 sm:p-4 rounded-xl flex flex-col items-center justify-center relative z-10 mt-auto mb-4 sm:mb-6 shadow-inner">
                                        <svg id="loyaltyBarcodeZoom" class="w-full h-16 sm:h-24"></svg>
                                        <p class="text-gray-800 text-sm sm:text-lg font-mono mt-1 sm:mt-2 font-bold tracking-[0.2em]">{{ $user->barcode }}</p>
                                    </div>

                                    <!-- Bottom: Info -->
                                    <div class="flex justify-between items-end relative z-10 w-full mt-auto">
                                        <div class="overflow-hidden">
                                            <p class="text-[10px] sm:text-xs text-purple-200 uppercase tracking-widest mb-1 opacity-80">Titular de la Tarjeta</p>
                                            <p class="font-medium text-base sm:text-xl tracking-widest uppercase truncate max-w-[200px] sm:max-w-[300px]">{{ $user->name }}</p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <p class="text-[10px] sm:text-xs text-purple-200 uppercase tracking-widest mb-1 opacity-80">Puntos</p>
                                            <p class="font-black text-2xl sm:text-3xl text-yellow-400 leading-none">{{ number_format($user->points) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Últimos Tickets -->
                    @php
                        $myTickets = \App\Models\Sale::where('client_id', $user->id)
                            ->selectRaw('receipt_id, MAX(created_at) as created_at, SUM(total_price) as total_price')
                            ->groupBy('receipt_id')
                            ->orderByDesc('created_at')
                            ->take(10)
                            ->get();
                    @endphp

                    @if($myTickets->count() > 0)
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                            <h4 class="text-md font-bold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Mis Últimos Tickets
                            </h4>
                            <div class="space-y-3">
                                @foreach($myTickets as $ticket)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100 hover:bg-gray-100 transition-colors">
                                        <div>
                                            <p class="font-bold text-sm text-gray-800">{{ $ticket->receipt_id }}</p>
                                            <p class="text-xs text-gray-500">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="text-right flex flex-col items-end">
                                            <p class="font-black text-green-600 mb-1.5">${{ number_format($ticket->total_price, 2) }}</p>
                                            <a href="{{ route('sales.ticket', $ticket->receipt_id) }}" onclick="window.openCenteredPopup(this.href, 'Ticket', 450, 720); return false;" target="_blank" class="text-[10px] uppercase font-bold text-blue-600 hover:text-blue-800 bg-blue-100 hover:bg-blue-200 px-2.5 py-1 rounded-md transition-colors flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                Ver Ticket
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Columna Derecha: Formularios de Configuración -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white shadow-sm border border-gray-100 rounded-3xl overflow-hidden">
                        <div class="p-6 sm:p-8">
                            <div class="max-w-2xl">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow-sm border border-gray-100 rounded-3xl overflow-hidden">
                        <div class="p-6 sm:p-8">
                            <div class="max-w-2xl">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50/50 shadow-sm border border-red-100 rounded-3xl overflow-hidden">
                        <div class="p-6 sm:p-8">
                            <div class="max-w-2xl">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if ("{{ $user->barcode }}") {
                JsBarcode("#loyaltyBarcode", "{{ $user->barcode }}", {
                    format: "CODE128",
                    lineColor: "#1e3a8a",
                    width: 2,
                    height: 50,
                    displayValue: false,
                    margin: 0
                });
                JsBarcode("#loyaltyBarcodeZoom", "{{ $user->barcode }}", {
                    format: "CODE128",
                    lineColor: "#1e3a8a",
                    width: 3,
                    height: 100,
                    displayValue: false,
                    margin: 0
                });
            }
        });
    </script>
</x-app-layout>
