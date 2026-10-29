<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Inventario y Existencias
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Control de existencias, caducidades y ubicación física. Ordenado alfabéticamente y luego por fecha de vencimiento.
                    </p>
                </div>
                @if(!auth()->user()->hasRole('Personal de Mostrador'))
                <div class="mt-5 sm:mt-0">
                    <a href="{{ route('batches.create') }}" class="btn-group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-green-700 rounded-xl hover:bg-green-800 hover:-translate-y-1 hover:shadow-lg hover:shadow-green-500/30">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Ingresar Nueva Existencia</span>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ 
        search: '{{ addslashes(request('search')) }}',
        branchFilter: '{{ request('branch_id') }}',
        perPage: '{{ request('per_page', '25') }}',
        isLoading: false,
        abortController: null,
        checkRow(searchStr, branchIdStr) {
            let matchSearch = this.search === '' || searchStr.toLowerCase().includes(this.search.toLowerCase());
            let matchBranch = this.branchFilter === '' || branchIdStr.includes(this.branchFilter);
            return matchSearch && matchBranch;
        },
        async fetchResults() {
            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();
            
            let url = new URL('{{ route('batches.index') }}');
            let query = (this.search || '').trim();
            if (query) url.searchParams.set('search', query);
            if (this.branchFilter) url.searchParams.set('branch_id', this.branchFilter);
            if (this.perPage) url.searchParams.set('per_page', this.perPage);
            // Siempre busca desde la página 1 para no perder resultados
            url.searchParams.set('page', '1');
            
            try {
                let res = await fetch(url.toString(), { 
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: this.abortController.signal
                });
                let text = await res.text();
                let parser = new DOMParser();
                let doc = parser.parseFromString(text, 'text/html');
                
                let newTable = doc.querySelector('#table-container');
                if (newTable) {
                    let container = document.querySelector('#table-container');
                    container.innerHTML = newTable.innerHTML;
                    if (window.Alpine) {
                        container.querySelectorAll('[x-data]').forEach(el => {
                            window.Alpine.initTree(el);
                        });
                    }
                }
                
                // Actualiza la URL sin recargar la página
                window.history.replaceState({}, '', url.toString());
            } catch (e) {
                if (e.name !== 'AbortError') {
                    console.error(e);
                }
            }
        }
    }" @scanner-result.window="if ($event.detail.target === 'search') { search = $event.detail.value; fetchResults(); }">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Alertas de Sesión -->
            @if (session('success'))
                <div class="mb-6 flex items-center p-4 text-green-800 bg-green-50 border border-green-200 rounded-xl shadow-sm animate-pulse" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Filtros de Búsqueda -->
            <div class="mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center gap-4 transition-all">
                <!-- Search input (replicado de la blade de productos) -->
                <div class="flex-1 flex items-center bg-gray-50/50 rounded-xl border border-gray-200 focus-within:border-green-500 focus-within:ring-1 focus-within:ring-green-500 transition-all overflow-hidden">
                    <div class="pl-4 pr-2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" 
                           id="search" 
                           x-model="search" 
                           placeholder="Buscar por Nombre, SKU, código de lote..." 
                           class="w-full py-3 border-none focus:ring-0 text-sm text-gray-700 bg-transparent placeholder-gray-400" 
                           autocomplete="off" 
                           @input.debounce.300ms="fetchResults()"
                           @keydown.enter.prevent="fetchResults()">
                    
                    <button type="button" tabindex="-1" x-show="search !== ''" @click="search = ''; fetchResults()" class="px-2 text-gray-400 hover:text-gray-600 focus:outline-none" style="display: none;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    
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

                @if(auth()->user()->hasGlobalInventoryAccess())
                <!-- Branch Filter -->
                <div class="flex-shrink-0">
                    <select id="branchFilter" x-model="branchFilter" class="w-full md:w-64 border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 py-2" @change="fetchResults()">
                        <option value="">-- Todas las Sucursales --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <!-- Items Per Page -->
                <div class="flex-shrink-0">
                    <select id="perPageFilter" x-model="perPage" class="w-full md:w-auto border-gray-200 rounded-xl text-sm focus:ring-green-500 focus:border-green-500 py-2" @change="fetchResults()">
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
            </div>

            <div id="table-container">
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl transition-all duration-300 hover:shadow-md">
                <div class="p-0 text-gray-900 overflow-x-auto">
                    
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Medicamento</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ubicación</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Precios (C/U/V)</th>
                                @if(auth()->user()->hasRole(['superadmin', 'admin']))
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider" title="Ganancia proyectada por el stock actual">Ganancia Esperada</th>
                                @endif
                                <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lote / Caducidad</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        @forelse ($batches as $productBatches)
                            @php
                                $firstBatch = $productBatches->first();
                                $product = $firstBatch->product;
                                $branch = $firstBatch->branch;
                                $totalStock = $productBatches->sum('stock');

                                // LÓGICA DE STOCK TOTAL
                                $isLowStock = $totalStock <= 5 && $totalStock > 0;
                                $isOutOfStock = $totalStock == 0;

                                // COSTO PROMEDIO PONDERADO DINÁMICO
                                if ($totalStock > 0) {
                                    $avgCost = $productBatches->sum(fn($b) => $b->stock * $b->cost_price) / $totalStock;
                                } else {
                                    $avgCost = $productBatches->avg('cost_price');
                                }

                                // PRECIO DE VENTA (Sin promediar + Alerta si difiere respecto a inventario existente)
                                $uniqueSellingPrices = $productBatches->pluck('selling_price')->unique();
                                $hasPriceMismatch = $uniqueSellingPrices->count() > 1;
                                $baseSellingPrice = $productBatches->sortBy('id')->first()->selling_price;
                                $avgProfit = $baseSellingPrice - $avgCost;

                                // UBICACIONES ÚNICAS
                                $uniqueRacks = $productBatches->pluck('rack')->filter()->unique();
                                $uniqueShelves = $productBatches->pluck('shelf')->filter()->unique();
                                $hasMultipleLocations = ($uniqueRacks->count() > 1 || $uniqueShelves->count() > 1) || ($productBatches->count() > 1 && ($uniqueRacks->count() > 0 || $uniqueShelves->count() > 0));

                                // CADUCIDAD (Para resumen)
                                $closestExpirationBatch = $productBatches->filter(fn($b) => !empty($b->expiration_date))->sortBy('expiration_date')->first();
                                $hasExpiration = $closestExpirationBatch !== null;
                                $isExpired = false;
                                $isExpiringSoon = false;
                                $isGoodExpiration = false;
                                if ($hasExpiration) {
                                    $expirationDate = \Carbon\Carbon::parse($closestExpirationBatch->expiration_date);
                                    $monthsLeft = now()->floatDiffInMonths($expirationDate, false);
                                    $isExpired = $monthsLeft < 0 || $expirationDate->isPast();
                                    $isExpiringSoon = !$isExpired && $monthsLeft <= 18;
                                    $isGoodExpiration = !$isExpired && $monthsLeft > 18;
                                }
                            @endphp
                            <tbody x-data="{ open: false }"
                                   class="bg-white divide-y divide-gray-100 border-b border-gray-100">
                                <!-- Fila Principal Resumida del Producto -->
                                <tr @if($productBatches->count() > 1) @click="open = !open" title="Haz clic para ver el desglose de lotes y ubicaciones" class="hover:bg-blue-50/50 transition-colors duration-200 cursor-pointer {{ $isExpired ? 'bg-red-50/30' : '' }}" @else class="transition-colors duration-200 {{ $isExpired ? 'bg-red-50/30' : '' }}" @endif>
                                    <!-- Columna: SKU -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs font-bold font-mono rounded-lg bg-gray-100 text-gray-800 border border-gray-200 shadow-2xs">
                                            {{ $product->sku }}
                                        </span>
                                    </td>

                                    <!-- Columna: Medicamento -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                                    {{ $product->name }}
                                                    @if($productBatches->count() > 1)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200" title="Este producto tiene múltiples lotes">
                                                            {{ $productBatches->count() }} lotes
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500 mt-0.5"><span class="font-semibold text-gray-600">{{ $product->type }}</span></div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Columna: Stock Total -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($isOutOfStock)
                                            <span class="px-3 py-1 inline-flex text-sm font-bold rounded-full bg-red-50 text-red-700 border border-red-100">0</span>
                                        @elseif($isLowStock)
                                            <span class="px-3 py-1 inline-flex text-sm font-bold rounded-full bg-orange-50 text-orange-700 border border-orange-100">{{ $totalStock }}</span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-sm font-bold rounded-full bg-gray-100 text-gray-700 border border-gray-200">{{ $totalStock }}</span>
                                        @endif
                                    </td>

                                    <!-- Columna: Ubicación Física Resumida -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <div>
                                                <div class="font-bold text-gray-900 text-sm mb-1">
                                                    {{ $branch ? $branch->name : 'Sin Asignar' }}
                                                </div>
                                                @if($productBatches->count() > 1 && $hasMultipleLocations)
                                                    <span class="inline-flex items-center text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                                                        Múltiples ubicaciones (ver más)
                                                    </span>
                                                @elseif($firstBatch->rack || $firstBatch->shelf)
                                                    <div class="flex flex-col text-xs text-gray-600 bg-gray-50 p-1.5 rounded border border-gray-100">
                                                        @if($firstBatch->rack) <span><span class="font-semibold text-gray-400">RACK:</span> <span class="font-bold">{{ $firstBatch->rack }}</span></span> @endif
                                                        @if($firstBatch->shelf) <span><span class="font-semibold text-gray-400">NIVEL:</span> <span class="font-bold">{{ $firstBatch->shelf }}</span></span> @endif
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 italic text-xs">Sin ubicación en estante</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Columna: Precios (Costo Promedio Ponderado, Venta Sin Promediar) -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <div class="text-gray-500 text-xs" title="Costo promedio ponderado del inventario">C: ${{ number_format($avgCost, 2) }}</div>
                                        <div class="text-xs font-semibold {{ $avgProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-0.5" title="Utilidad por unidad (Venta - Costo Promedio)">U: ${{ number_format($avgProfit, 2) }}</div>
                                        <div class="font-bold text-gray-900 mt-0.5" title="Precio de venta en inventario">V: ${{ number_format($baseSellingPrice, 2) }}</div>
                                        @if($hasPriceMismatch)
                                            <div class="mt-1.5 inline-flex items-center gap-1 px-2 py-1 text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300 rounded-lg shadow-sm animate-pulse cursor-pointer" @click.stop="open = true" title="El nuevo lote ingresado difiere en precio de venta respecto al inventario existente. Haz clic para revisar.">
                                                <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <span>¡Precio nuevo difiere!</span>
                                            </div>
                                        @endif
                                    </td>

                                    @if(auth()->user()->hasRole(['superadmin', 'admin']))
                                    <!-- Columna: Ganancia Esperada Total -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        @php
                                            $expectedProfit = $productBatches->sum(fn($b) => ($b->selling_price - $b->cost_price) * $b->stock);
                                        @endphp
                                        <div class="font-bold text-lg {{ $expectedProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            ${{ number_format($expectedProfit, 2) }}
                                        </div>
                                    </td>
                                    @endif

                                    <!-- Columna: Lote / Caducidad Resumida -->
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        @if($productBatches->count() > 1)
                                            <span class="px-2.5 py-1 inline-flex items-center text-xs font-bold rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-sm">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                {{ $productBatches->count() }} lotes disponibles
                                            </span>
                                            @if($hasExpiration)
                                                <div class="mt-1 text-[11px] font-semibold {{ $isExpired ? 'text-red-600 font-bold' : ($isExpiringSoon ? 'text-orange-600' : 'text-gray-500') }}">
                                                    Próx. venc: {{ \Carbon\Carbon::parse($closestExpirationBatch->expiration_date)->format('M Y') }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-xs font-bold font-mono text-gray-900 uppercase">{{ $firstBatch->batch_number ?? 'S/L' }}</div>
                                            <div class="mt-1 flex flex-col gap-1 items-start">
                                                @if(!$hasExpiration)
                                                    <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-bold rounded bg-gray-100 text-gray-700 border border-gray-200 shadow-sm" title="Producto general sin fecha de caducidad">
                                                        Sin caducidad
                                                    </span>
                                                @elseif($isExpired)
                                                    <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-bold rounded bg-red-100 text-red-800 border border-red-200 shadow-sm" title="Producto caducado">
                                                        Cad: {{ $expirationDate->format('M Y') }}
                                                    </span>
                                                @elseif($isExpiringSoon)
                                                    <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-bold rounded bg-orange-100 text-orange-800 border border-orange-200 shadow-sm" title="Próximo a vencer (Faltan {{ round($monthsLeft, 1) }} meses)">
                                                        Vence: {{ $expirationDate->format('M Y') }}
                                                    </span>
                                                @elseif($isGoodExpiration)
                                                    <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-bold rounded bg-green-100 text-green-800 border border-green-200 shadow-sm" title="Caducidad óptima (Faltan {{ round($monthsLeft, 1) }} meses)">
                                                        Vence: {{ $expirationDate->format('M Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Columna: Acciones Resumidas -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($productBatches->count() === 1)
                                            @if(auth()->user()->hasRole(['superadmin', 'admin', 'compras']))
                                                <button type="button" onclick="openHistoryModal({{ $firstBatch->id }}, '{{ addslashes($firstBatch->batch_number ?? 'S/L') }}', '{{ addslashes($product->name) }}')" class="text-green-600 hover:text-green-800 mx-1.5 transition-colors font-bold">Trazabilidad</button>
                                            @endif
                                            <a href="{{ route('batches.edit', $firstBatch) }}" class="text-blue-500 hover:text-blue-700 mx-1.5 transition-colors font-bold">Editar</a>
                                            @if(auth()->user()->hasRole(['superadmin', 'admin']))
                                                <form action="{{ route('batches.destroy', $firstBatch) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="text-red-400 hover:text-red-600 transition-colors mx-1.5 font-bold" @click.prevent="$dispatch('open-reason-modal', { title: 'Eliminar Lote Definitivamente', message: '¿Estás seguro de eliminar este lote? Ingresa el motivo.', form: $el.closest('form') })">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('batches.request_edit', $firstBatch) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <input type="hidden" name="type" value="delete">
                                                    <button type="button" class="text-red-400 hover:text-red-600 transition-colors mx-1.5 font-bold" @click.prevent="$dispatch('open-reason-modal', { title: 'Solicitar Eliminación de Lote', message: 'No tienes permisos para eliminar directamente. Se enviará una solicitud al administrador. Ingresa el motivo.', form: $el.closest('form') })">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <button type="button" @click.stop="open = !open" class="inline-flex items-center px-3 py-1.5 border border-blue-200 rounded-xl text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition-all shadow-sm">
                                                <span x-text="open ? 'Ocultar Lotes' : 'Ver {{ $productBatches->count() }} Lotes'">Ver {{ $productBatches->count() }} Lotes</span>
                                                <svg class="ml-1 w-4 h-4 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                                @if($productBatches->count() > 1)
                                <!-- Fila Expandible: Acordeón con Desglose de Lotes -->
                                <tr x-show="open" x-cloak class="bg-gradient-to-b from-gray-50 to-white border-t border-gray-200">
                                    <td colspan="{{ auth()->user()->hasRole(['superadmin', 'admin']) ? 8 : 7 }}" class="p-4 sm:p-6">
                                        <div class="bg-white rounded-2xl border border-blue-100 shadow-md overflow-hidden p-4">
                                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100">
                                                <h4 class="text-xs font-extrabold text-blue-900 uppercase tracking-wider flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                    Desglose de Lotes para: <span class="text-gray-900">{{ $product->name }}</span> en <span class="text-blue-600">{{ $branch->name }}</span>
                                                </h4>
                                                <span class="text-xs font-bold text-gray-500">Total en sucursal: {{ $totalStock }} uds</span>
                                            </div>

                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-100">
                                                    <thead class="bg-gray-50/80 rounded-lg">
                                                        <tr>
                                                            <th class="px-4 py-2.5 text-left text-[11px] font-extrabold text-gray-600 uppercase tracking-wider">Lote / Caducidad</th>
                                                            <th class="px-4 py-2.5 text-center text-[11px] font-extrabold text-gray-600 uppercase tracking-wider">Stock</th>
                                                            <th class="px-4 py-2.5 text-left text-[11px] font-extrabold text-gray-600 uppercase tracking-wider">Ubicación Física</th>
                                                            <th class="px-4 py-2.5 text-right text-[11px] font-extrabold text-gray-600 uppercase tracking-wider">Costo Lote</th>
                                                            <th class="px-4 py-2.5 text-right text-[11px] font-extrabold text-gray-600 uppercase tracking-wider">Venta Lote</th>
                                                            <th class="px-4 py-2.5 text-right text-[11px] font-extrabold text-gray-600 uppercase tracking-wider">Acciones Lote</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-100 bg-white">
                                                        @foreach($productBatches as $batch)
                                                            @php
                                                                $batchHasExp = !empty($batch->expiration_date);
                                                                $bExpired = false;
                                                                $bExpSoon = false;
                                                                $bGoodExp = false;
                                                                if ($batchHasExp) {
                                                                    $bExpDate = \Carbon\Carbon::parse($batch->expiration_date);
                                                                    $bMonths = now()->floatDiffInMonths($bExpDate, false);
                                                                    $bExpired = $bMonths < 0 || $bExpDate->isPast();
                                                                    $bExpSoon = !$bExpired && $bMonths <= 18;
                                                                    $bGoodExp = !$bExpired && $bMonths > 18;
                                                                }
                                                            @endphp
                                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                                <td class="px-4 py-3 whitespace-nowrap">
                                                                    <div class="text-xs font-bold font-mono text-gray-900 uppercase">{{ $batch->batch_number ?? 'S/L' }}</div>
                                                                    <div class="mt-1">
                                                                        @if(!$batchHasExp)
                                                                            <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-bold rounded bg-gray-100 text-gray-700 border border-gray-200">Sin caducidad</span>
                                                                        @elseif($bExpired)
                                                                            <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-bold rounded bg-red-100 text-red-800 border border-red-200">Cad: {{ $bExpDate->format('M Y') }}</span>
                                                                        @elseif($bExpSoon)
                                                                            <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-bold rounded bg-orange-100 text-orange-800 border border-orange-200" title="Faltan {{ round($bMonths, 1) }} meses">Vence: {{ $bExpDate->format('M Y') }}</span>
                                                                        @elseif($bGoodExp)
                                                                            <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-bold rounded bg-green-100 text-green-800 border border-green-200" title="Faltan {{ round($bMonths, 1) }} meses">Vence: {{ $bExpDate->format('M Y') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                                    <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full {{ $batch->stock == 0 ? 'bg-red-50 text-red-700 border border-red-100' : ($batch->stock <= 5 ? 'bg-orange-50 text-orange-700 border border-orange-100' : 'bg-green-50 text-green-700 border border-green-100') }}">
                                                                        {{ $batch->stock }} uds
                                                                    </span>
                                                                </td>
                                                                <td class="px-4 py-3 whitespace-nowrap">
                                                                    @if($batch->rack || $batch->shelf)
                                                                        <div class="text-xs font-medium text-gray-800">
                                                                            @if($batch->rack) <span class="font-bold text-gray-600">Rack:</span> {{ $batch->rack }} @endif
                                                                            @if($batch->shelf) <span class="ml-2 font-bold text-gray-600">Nivel:</span> {{ $batch->shelf }} @endif
                                                                        </div>
                                                                    @else
                                                                        <span class="text-gray-400 italic text-xs">Sin ubicación en estante</span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium text-gray-600">
                                                                    ${{ number_format($batch->cost_price, 2) }}
                                                                </td>
                                                                <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-bold {{ ($hasPriceMismatch && $batch->selling_price != $baseSellingPrice) ? 'text-amber-800 bg-amber-50/80 rounded border border-amber-300' : 'text-gray-900' }}">
                                                                    ${{ number_format($batch->selling_price, 2) }}
                                                                    @if($hasPriceMismatch && $batch->selling_price != $baseSellingPrice)
                                                                        <span class="block text-[9px] text-amber-600 font-normal">Dif. a inventario</span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium">
                                                                    @if(auth()->user()->hasRole(['superadmin', 'admin', 'compras']))
                                                                        <button type="button" onclick="openHistoryModal({{ $batch->id }}, '{{ addslashes($batch->batch_number ?? 'S/L') }}', '{{ addslashes($batch->product->name) }}')" class="text-green-600 hover:text-green-800 mx-1.5 font-bold">Trazabilidad</button>
                                                                    @endif
                                                                    <a href="{{ route('batches.edit', $batch) }}" class="text-blue-600 hover:text-blue-800 mx-1.5 font-bold">Editar</a>
                                                                    @if(auth()->user()->hasRole(['superadmin', 'admin']))
                                                                        <form action="{{ route('batches.destroy', $batch) }}" method="POST" class="inline-block">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button" class="text-red-500 hover:text-red-700 transition-colors mx-1.5 font-bold" @click.prevent="$dispatch('open-reason-modal', { title: 'Eliminar Lote Específico', message: '¿Estás seguro de eliminar este lote específico? Ingresa el motivo.', form: $el.closest('form') })">
                                                                                Eliminar
                                                                            </button>
                                                                        </form>
                                                                    @else
                                                                        <form action="{{ route('batches.request_edit', $batch) }}" method="POST" class="inline-block">
                                                                            @csrf
                                                                            <input type="hidden" name="type" value="delete">
                                                                            <button type="button" class="text-red-500 hover:text-red-700 transition-colors mx-1.5 font-bold" @click.prevent="$dispatch('open-reason-modal', { title: 'Solicitar Eliminación', message: 'No tienes permisos para eliminar. Se enviará una solicitud. Ingresa el motivo.', form: $el.closest('form') })">
                                                                                Eliminar
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        @empty
                            <tbody class="bg-white">
                                <tr>
                                    <td colspan="{{ auth()->user()->hasRole(['superadmin', 'admin']) ? 8 : 7 }}" class="px-6 py-24 text-center">
                                        <div class="flex flex-col items-center justify-center transform transition-all duration-500 hover:scale-105">
                                            <div class="relative p-5 bg-green-50 rounded-full border border-green-100 mb-5 shadow-inner">
                                                <div class="absolute inset-0 bg-green-700 rounded-full opacity-10 animate-ping"></div>
                                                <svg class="w-12 h-12 text-green-700 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">Inventario vacío</h3>
                                            <p class="text-gray-500 text-sm mb-8 max-w-sm leading-relaxed">No hay productos registrados actualmente en el sistema. Realiza tu primera captura de mercancía.</p>
                                            
                                            @if(!auth()->user()->hasRole('Personal de Mostrador'))
                                            <a href="{{ route('batches.create') }}" class="inline-flex items-center text-white font-semibold bg-green-700 hover:bg-green-800 px-6 py-3 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5">
                                                + Registrar Ingreso
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        @endforelse
                    </table>

                    @if($batches->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50" x-show="search === @js((string)request('search', '')) && branchFilter === @js((string)request('branch_id', ''))">
                            {{ $batches->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Búsqueda Global: Resultados en otras sucursales -->
            @if(isset($externalBatches) && $externalBatches->isNotEmpty())
                <div class="mt-8 bg-blue-50 border border-blue-100 rounded-2xl p-6 shadow-sm" x-show="search !== ''" style="display: none;">
                    <h3 class="font-extrabold text-xl text-blue-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                        Resultados en otras sucursales
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($externalBatches as $extBatch)
                            <div class="bg-white p-4 rounded-xl shadow-sm border border-blue-100 flex flex-col justify-between"
                                 x-show="search === '' || `{{ addslashes($extBatch->product->name) }} {{ addslashes($extBatch->product->sku) }} {{ addslashes($extBatch->batch_number) }}`.toLowerCase().includes(search.toLowerCase())">
                                <div>
                                    <div class="font-bold text-gray-900">{{ $extBatch->product->name }}</div>
                                    <div class="text-xs text-gray-500 mb-2">Sucursal: <span class="font-semibold text-blue-700">{{ $extBatch->branch->name }}</span></div>
                                    <div class="text-sm">Stock disponible: <span class="font-bold text-gray-800">{{ $extBatch->stock }}</span></div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('transfers.request', ['product_id' => $extBatch->product_id, 'from_branch_id' => $extBatch->branch_id]) }}" class="inline-flex w-full justify-center items-center px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition-colors">
                                        Solicitar Traspaso
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- PENDIENTE (NO TERMINADO): Módulo "Solicitar a Compras" oculto temporalmente hasta nuevo aviso.
                 Para reactivarlo, quitar este comentario Blade y el de cierre más abajo (junto con el modal).
            <!-- Búsqueda Global: Sin stock en ninguna sucursal -->
            <div class="mt-8 bg-orange-50 border border-orange-100 rounded-2xl p-6 shadow-sm text-center" x-show="search !== ''" style="display: none;">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-100 text-orange-600 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="font-extrabold text-xl text-orange-900 mb-2">¿No encuentras el producto?</h3>
                <p class="text-orange-700 mb-6">Si no encuentras el producto <strong><span x-text="search"></span></strong>, puedes solicitarlo a compras.</p>
                
                <button @click="$dispatch('open-request-purchase', { productName: search })" class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-bold rounded-xl hover:bg-orange-700 transition-colors shadow-sm hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Solicitar a Compras
                </button>
            </div>
            --}}

            </div>
            </div> <!-- End of table-container -->

        </div>
    </div>

    {{-- PENDIENTE (NO TERMINADO): Modal "Solicitar a Compras" oculto temporalmente hasta nuevo aviso.
    <!-- Modal para Solicitar a Compras -->
    <div x-data="{
        showModal: false,
        productId: null,
        productName: '',
        quantity: 1,
        notes: ''
    }"
    @open-request-purchase.window="
        showModal = true;
        productId = $event.detail.productId;
        productName = $event.detail.productName;
        quantity = 1;
        notes = '';
    "
    class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak x-show="showModal">
        
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.away="showModal = false"
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                    
                    <form action="{{ route('product_requests.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" x-model="productId">
                        
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg font-bold leading-6 text-gray-900" id="modal-title">Solicitar Producto a Compras</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 mb-4">
                                            Estás solicitando <strong x-text="productName" class="text-gray-900"></strong>. Este pedido será revisado por el área de compras o almacén central.
                                        </p>
                                        
                                        <div class="mb-4">
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Cantidad Requerida <span class="text-red-500">*</span></label>
                                            <div class="flex items-center justify-center gap-3 mt-1">
                                                <button type="button" @click="quantity = Math.max(1, parseInt(quantity || 1) - 1)" class="w-10 h-10 flex items-center justify-center rounded-xl bg-orange-100 text-orange-700 hover:bg-orange-200 text-xl font-black border border-orange-200 transition-colors select-none" tabindex="-1">−</button>
                                                <input type="number" name="quantity" x-model="quantity" min="1" required class="w-20 text-center font-bold text-gray-900 rounded-xl border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 shadow-sm" @wheel.prevent>
                                                <button type="button" @click="quantity = parseInt(quantity || 1) + 1" class="w-10 h-10 flex items-center justify-center rounded-xl bg-orange-100 text-orange-700 hover:bg-orange-200 text-xl font-black border border-orange-200 transition-colors select-none" tabindex="-1">+</button>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Notas / Justificación <span class="text-gray-400 text-xs font-normal">(Opcional)</span></label>
                                            <textarea name="notes" x-model="notes" rows="2" class="w-full border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg shadow-sm" placeholder="Ej. Urgente para paciente programado..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                            <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-orange-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 sm:ml-3 sm:w-auto transition-colors">
                                Enviar Solicitud
                            </button>
                            <button type="button" @click="showModal = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    --}}

    <!-- Modal de Historial de Lote -->
    <div x-data="{
        showHistoryModal: false,
        historyBatchId: null,
        historyBatchNumber: '',
        historyProductName: '',
        histories: [],
        isLoading: false,
        selectedMonth: '',
        fieldTranslations: {
            'selling_price': 'Precio Venta',
            'cost_price': 'Precio Costo',
            'stock': 'Existencia',
            'expiration_date': 'Caducidad',
            'batch_number': 'Lote',
            'notes': 'Notas',
            'branch_id': 'Sucursal',
            'product_id': 'Producto',
            'status': 'Estado'
        },
        get availableMonths() {
            if (!this.histories.length) return [];
            let months = new Set();
            this.histories.forEach(h => {
                let date = new Date(h.created_at);
                let yyyy = date.getFullYear();
                let mm = String(date.getMonth() + 1).padStart(2, '0');
                months.add(`${yyyy}-${mm}`);
            });
            return Array.from(months).sort((a,b) => b.localeCompare(a)).map(m => {
                const [y, mo] = m.split('-');
                const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return { value: m, label: monthNames[parseInt(mo)-1] + ' ' + y };
            });
        },
        get filteredHistories() {
            if (!this.selectedMonth) return this.histories;
            return this.histories.filter(h => {
                let date = new Date(h.created_at);
                let yyyy = date.getFullYear();
                let mm = String(date.getMonth() + 1).padStart(2, '0');
                return `${yyyy}-${mm}` === this.selectedMonth;
            });
        }
    }"
    @open-history-modal.window="
        showHistoryModal = true;
        historyBatchId = $event.detail.batchId;
        historyBatchNumber = $event.detail.batchNumber;
        historyProductName = $event.detail.productName;
        histories = [];
        isLoading = true;
        selectedMonth = '';
        
        fetch(`/batches/${historyBatchId}/history`)
            .then(res => res.json())
            .then(data => {
                histories = data;
                isLoading = false;
            })
            .catch(err => {
                console.error(err);
                isLoading = false;
            });
    "
    class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak x-show="showHistoryModal">
        <div x-show="showHistoryModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showHistoryModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.away="showHistoryModal = false"
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-100">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-5 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-bold leading-6 text-gray-900" id="modal-title">Trazabilidad e Historial</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-3">
                                        Auditoría del Lote: <strong x-text="historyBatchNumber" class="text-gray-900 font-mono"></strong><br>
                                        Producto: <strong x-text="historyProductName" class="text-gray-900"></strong>
                                    </p>
                                    
                                    <!-- Filtro por mes -->
                                    <div class="mb-3 flex items-center justify-between bg-gray-50 p-2 rounded-lg border border-gray-100" x-show="!isLoading && histories.length > 0">
                                        <span class="text-xs font-bold text-gray-600">Filtrar temporalidad:</span>
                                        <select x-model="selectedMonth" class="text-xs border-gray-300 rounded-md py-1 px-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Todos los movimientos</option>
                                            <template x-for="month in availableMonths" :key="month.value">
                                                <option :value="month.value" x-text="month.label"></option>
                                            </template>
                                        </select>
                                    </div>
                                    
                                    <!-- Timeline de Historial -->
                                    <div class="mt-3 max-h-80 overflow-y-auto pr-2">
                                        <template x-if="isLoading">
                                            <div class="flex justify-center items-center py-8">
                                                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            </div>
                                        </template>

                                        <template x-if="!isLoading && histories.length === 0">
                                            <div class="text-center py-8 text-gray-500 text-sm">
                                                No hay registros de historial para este lote.
                                            </div>
                                        </template>

                                        <template x-if="!isLoading && filteredHistories.length === 0 && histories.length > 0">
                                            <div class="text-center py-6 text-gray-500 text-xs">
                                                No hay movimientos en el mes seleccionado.
                                            </div>
                                        </template>

                                        <ul role="list" class="-mb-6" x-show="!isLoading && filteredHistories.length > 0">
                                            <template x-for="(history, index) in filteredHistories" :key="history.id">
                                                <li>
                                                    <div class="relative pb-5">
                                                        <span x-show="index !== filteredHistories.length - 1" class="absolute left-3.5 top-3 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                        <div class="relative flex space-x-3">
                                                            <div>
                                                                <span class="h-7 w-7 rounded-full bg-gray-100 flex items-center justify-center ring-4 ring-white">
                                                                    <template x-if="history.type === 'history' && history.action === 'created'">
                                                                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                                    </template>
                                                                    <template x-if="history.type === 'history' && history.action === 'updated'">
                                                                        <svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                                    </template>
                                                                    <template x-if="history.type === 'movement'">
                                                                        <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                                    </template>
                                                                </span>
                                                            </div>
                                                            <div class="flex min-w-0 flex-1 justify-between space-x-3 pt-1">
                                                                <div>
                                                                    <p class="text-xs text-gray-500">
                                                                        <template x-if="history.type === 'history'">
                                                                            <span>
                                                                                <span x-text="history.action === 'created' ? 'Creación de registro' : 'Modificación'" class="font-medium text-gray-900"></span>
                                                                                por <span x-text="history.user ? history.user.name : 'Sistema'" class="font-bold text-gray-900"></span>
                                                                            </span>
                                                                        </template>
                                                                        <template x-if="history.type === 'movement'">
                                                                            <span>
                                                                                <span class="font-medium text-gray-900">Movimiento: <span x-text="history.action === 'transfer_out' ? 'Salida' : (history.action === 'transfer_in' ? 'Entrada' : history.action)" class="capitalize"></span></span>
                                                                                por <span x-text="history.user ? history.user.name : 'Sistema'" class="font-bold text-gray-900"></span>
                                                                            </span>
                                                                        </template>
                                                                    </p>
                                                                    
                                                                    <!-- Diff visual para Updates -->
                                                                    <template x-if="history.type === 'history' && (history.action === 'updated' || history.action === 'updated_via_request') && history.before_state && history.after_state">
                                                                        <div class="mt-1.5 text-[11px] bg-gray-50 rounded-md p-2 border border-gray-100 text-left">
                                                                            <template x-if="Object.keys(fieldTranslations).filter(k => history.before_state[k] !== undefined && String(history.before_state[k]) !== String(history.after_state[k])).length === 0">
                                                                                <p class="text-gray-400 italic text-[10px]">Sin cambios visibles registrados.</p>
                                                                            </template>
                                                                            <ul class="space-y-0.5">
                                                                                <template x-for="key in Object.keys(fieldTranslations).filter(k => history.before_state[k] !== undefined && String(history.before_state[k]) !== String(history.after_state[k]))">
                                                                                    <li class="flex flex-col sm:flex-row sm:items-center text-[10px]">
                                                                                        <span class="font-bold text-gray-700 w-20" x-text="(fieldTranslations[key] || key) + ':'"></span>
                                                                                        <span class="text-red-600 line-through mr-1" x-text="history.before_state[key] ?? 'nulo'"></span>
                                                                                        <svg class="w-2.5 h-2.5 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                                                        <span class="text-green-600 font-bold" x-text="history.after_state[key] ?? 'nulo'"></span>
                                                                                    </li>
                                                                                </template>
                                                                            </ul>
                                                                        </div>
                                                                    </template>

                                                                    <!-- Detalles de Movimiento / Traspaso -->
                                                                    <template x-if="history.type === 'movement'">
                                                                        <div class="mt-1.5 text-[11px] bg-indigo-50/50 rounded-md p-2 border border-indigo-100 text-left">
                                                                            <div class="flex items-center gap-2 mb-0.5">
                                                                                <span class="font-bold text-indigo-900">Cant:</span>
                                                                                <span class="px-1.5 py-0.5 bg-white rounded text-indigo-700 font-mono text-[10px] border border-indigo-200 font-bold" x-text="history.quantity + ' uds'"></span>
                                                                            </div>
                                                                            <div class="text-indigo-800 text-[10px] mt-1 leading-tight" x-text="history.notes"></div>
                                                                            
                                                                            <template x-if="history.transfer_url">
                                                                                <div class="mt-1.5">
                                                                                    <a :href="history.transfer_url" class="inline-flex items-center text-[10px] font-bold text-blue-700 hover:text-blue-900 bg-white px-2 py-1 rounded border border-blue-200 transition-colors shadow-sm">
                                                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                                                        Ver Traspaso &rarr;
                                                                                    </a>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                    </template>

                                                                </div>
                                                                <div class="whitespace-nowrap text-right text-[10px] text-gray-500">
                                                                    <time x-text="new Date(history.created_at).toLocaleString([], { dateStyle: 'short', timeStyle: 'short' })"></time>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        <button type="button" @click="showHistoryModal = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openHistoryModal(batchId, batchNumber, productName) {
            window.dispatchEvent(new CustomEvent('open-history-modal', {
                detail: { batchId, batchNumber, productName }
            }));
        }
    </script>
    <x-confirm-delete-modal />
    
    <!-- Modal con Motivo (Para eliminar o solicitar eliminación) -->
    <div x-data="{ show: false, title: '', message: '', formElement: null, reason: '' }"
         @open-reason-modal.window="show = true; title = $event.detail.title; message = $event.detail.message; formElement = $event.detail.form; reason = ''; setTimeout(() => $refs.reasonInput.focus(), 100)"
         x-show="show"
         class="fixed inset-0 z-[100] flex items-center justify-center"
         x-cloak
         style="display: none;">
        
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="show = false"></div>
        
        <div x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-gray-100 p-6 text-left transform transition-all">
            
            <div class="flex items-center gap-4 mb-4">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-gray-900" x-text="title"></h3>
                </div>
            </div>
            
            <p class="text-sm text-gray-500 mb-4" x-text="message"></p>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Motivo / Razón <span class="text-red-500">*</span></label>
                <textarea x-model="reason" x-ref="reasonInput" rows="3" placeholder="Ej. Lote dañado, error de captura..." class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 transition-colors"></textarea>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" @click="show = false" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors focus:outline-none">
                    Cancelar
                </button>
                <button type="button" @click="if(reason.trim() !== '') { 
                            let input = document.createElement('input'); 
                            input.type = 'hidden'; 
                            input.name = 'reason'; 
                            input.value = reason; 
                            formElement.appendChild(input); 
                            formElement.submit(); 
                        } else { alert('Debes ingresar un motivo'); $refs.reasonInput.focus(); }" 
                        class="px-5 py-2.5 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
    
    <!-- Modal del Lector de Código de Barras -->
    <x-barcode-scanner-modal targetModel="search" />
</x-app-layout>