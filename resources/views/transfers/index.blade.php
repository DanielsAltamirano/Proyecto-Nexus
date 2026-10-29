<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Traspasos de Inventario
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Administra el envío y recepción de mercancía entre sucursales.
                    </p>
                </div>
                <div class="mt-5 sm:mt-0 flex gap-3">
                    <a href="{{ route('transfers.request') }}" class="group btn-group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-blue-600 rounded-xl hover:bg-blue-700 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-500/30">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Solicitar Traspaso</span>
                    </a>
                    <a href="{{ route('transfers.create') }}" class="group btn-group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-green-700 rounded-xl hover:bg-green-800 hover:-translate-y-1 hover:shadow-lg hover:shadow-green-500/30">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Nuevo Traspaso</span>
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $months = $transfers->map(function($t) {
            return $t->created_at->format('Y-m');
        })->unique()->sortDesc();
        
        $monthLabels = [
            '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr', '05' => 'May', '06' => 'Jun',
            '07' => 'Jul', '08' => 'Ago', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
        ];
    @endphp

    <div class="py-8" x-data="{ 
        search: '',
        statusFilter: '',
        monthFilter: '{{ now()->format("Y-m") }}',
        checkRow(searchStr, status, dateStr) {
            let matchSearch = this.search === '' || searchStr.toLowerCase().includes(this.search.toLowerCase());
            let matchStatus = this.statusFilter === '' || status === this.statusFilter;
            let matchMonth = this.monthFilter === '' || dateStr.startsWith(this.monthFilter);
            return matchSearch && matchStatus && matchMonth;
        }
    }" @scanner-result.window="if ($event.detail.target === 'search') search = $event.detail.value">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 flex items-center p-4 text-green-800 bg-green-50 border border-green-200 rounded-xl shadow-sm" role="alert">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Filtros y Búsqueda -->
            <div class="mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center gap-4 transition-all">
                <!-- Search input -->
                <div class="flex-1 flex items-center focus-within:text-green-600 bg-gray-50/50 rounded-xl border border-gray-200 overflow-hidden">
                    <div class="pl-4 pr-2 text-gray-400">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" x-model="search" placeholder="Buscar por folio, sucursal, medicamento..." class="w-full py-3 border-none focus:ring-0 text-sm text-gray-700 bg-transparent placeholder-gray-400" autocomplete="off">
                    <button x-show="search !== ''" @click="search = ''" class="px-2 text-gray-400 hover:text-gray-600 focus:outline-none" style="display: none;"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    <button type="button" @click="$dispatch('open-scanner-search')" class="p-3 bg-gray-100 text-gray-600 hover:bg-green-100 hover:text-green-700 font-bold border-l border-gray-200 transition-colors flex items-center justify-center group" title="Escanear Código">
                        <svg class="w-6 h-6 transform group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="currentColor">
                            <rect x="3" y="5" width="2" height="14"></rect>
                            <rect x="7" y="5" width="1" height="14"></rect>
                            <rect x="10" y="5" width="3" height="14"></rect>
                            <rect x="15" y="5" width="1" height="14"></rect>
                            <rect x="18" y="5" width="2" height="14"></rect>
                            <rect x="22" y="5" width="1" height="14"></rect>
                        </svg>
                    </button>
                </div>

                <!-- Status Filter -->
                <div class="flex-shrink-0">
                    <select x-model="statusFilter" class="w-full md:w-40 border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 py-2">
                        <option value="">Todos los estados</option>
                        <option value="pending">Solicitud</option>
                        <option value="pending_override">Por autorizar</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="empaquetado">Empaquetado</option>
                        <option value="en_transito">En Tránsito</option>
                        <option value="received">Recibido</option>
                        <option value="partial">Parcial</option>
                        <option value="rejected">Rechazado</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>

                <!-- Month Filter -->
                <div class="flex-shrink-0">
                    <select x-model="monthFilter" class="w-full md:w-40 border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 py-2">
                        <option value="">Cualquier fecha</option>
                        @foreach($months as $m)
                            @php
                                $parts = explode('-', $m);
                                $label = $monthLabels[$parts[1]] . ' ' . $parts[0];
                            @endphp
                            <option value="{{ $m }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl transition-all duration-300 hover:shadow-md">
                <div class="p-0 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Folio / Fecha</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ruta</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Enviado por</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($transfers as $transfer)
                                @php
                                    $productNames = $transfer->items->map(function($item) {
                                        return $item->product ? $item->product->name : ($item->batch && $item->batch->product ? $item->batch->product->name : '');
                                    })->filter()->implode(' ');
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors duration-200"
                                    x-show="checkRow(`TR-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }} {{ addslashes($transfer->fromBranch?->name ?? 'N/A') }} {{ addslashes($transfer->toBranch?->name ?? 'N/A') }} {{ addslashes($transfer->creator?->name ?? 'Sistema') }} {{ addslashes($productNames) }}`, '{{ $transfer->status }}', '{{ $transfer->created_at->format('Y-m') }}')">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">#TR-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-xs text-gray-500">{{ $transfer->created_at->format('d M Y, h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center text-sm">
                                            <span class="font-semibold text-gray-700 w-24 truncate" title="{{ $transfer->fromBranch?->name ?? 'N/A' }}">{{ $transfer->fromBranch?->name ?? 'N/A' }}</span>
                                            <svg class="w-4 h-4 mx-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            <span class="font-semibold text-green-700 w-24 truncate" title="{{ $transfer->toBranch?->name ?? 'N/A' }}">{{ $transfer->toBranch?->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $transfer->creator?->name ?? 'Sistema' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($transfer->status === 'en_transito')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800">En Tránsito</span>
                                        @elseif($transfer->status === 'en_proceso')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-purple-100 text-purple-800">En Proceso</span>
                                        @elseif($transfer->status === 'empaquetado')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-indigo-100 text-indigo-800">Empaquetado</span>
                                        @elseif($transfer->status === 'sent')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800">Enviado (Legado)</span>
                                        @elseif($transfer->status === 'received')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800">Recibido</span>
                                        @elseif($transfer->status === 'partial')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800">Parcial</span>
                                        @elseif($transfer->status === 'rejected')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800">Rechazado</span>
                                        @elseif($transfer->status === 'cancelled')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800">Cancelado</span>
                                        @elseif($transfer->status === 'pending')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">Solicitud</span>
                                        @elseif($transfer->status === 'pending_override')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-orange-100 text-orange-800 border border-orange-200">Por Autorizar</span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($transfer->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('transfers.show', $transfer) }}" class="text-green-600 hover:text-green-900 transition-colors bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg border border-green-200">Ver Detalles</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                            <p>No hay traspasos registrados.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal del Lector de Código de Barras -->
    <x-barcode-scanner-modal targetModel="search" />
</x-app-layout>
