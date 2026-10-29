<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Catálogo de Medicamentos y Productos
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Gestiona la base de datos de productos de la farmacia.
                    </p>
                </div>
                <div class="mt-5 sm:mt-0 flex flex-col sm:flex-row gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('products.create_general') }}" class="w-full sm:w-[270px] group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-blue-500 rounded-xl hover:bg-blue-600 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-400/30 whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Registrar Producto</span>
                    </a>
                    <a href="{{ route('products.create') }}" class="w-full sm:w-[270px] group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-green-700 rounded-xl hover:bg-green-800 hover:-translate-y-1 hover:shadow-lg hover:shadow-green-500/30 whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Registrar Medicamento</span>
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        stockModal: false,
        stockProductName: '',
        stockBatches: [],
        search: '{{ addslashes(request('search')) }}',
        perPage: '{{ request('per_page', '25') }}',
        abortController: null,
        async fetchResults() {
            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();
            
            let url = new URL('{{ route('products.index') }}');
            let query = (this.search || '').trim();
            if (query) url.searchParams.set('search', query);
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
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 flex items-center p-4 text-green-800 bg-green-50 border border-green-200 rounded-xl shadow-sm animate-pulse" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            <!-- Alerta de Error -->
            @elseif(session('error'))
                <div class="mb-6 flex items-center p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm animate-pulse" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Barra de Búsqueda y Escáner -->
            <div class="mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center gap-4 transition-all">
                <div class="flex-1 flex items-center bg-gray-50/50 rounded-xl border border-gray-200 focus-within:border-green-500 focus-within:ring-1 focus-within:ring-green-500 transition-all overflow-hidden">
                    <div class="pl-4 pr-2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" 
                           id="search" 
                           x-model="search" 
                           placeholder="Buscar por Nombre, SKU, Ingrediente..." 
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
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Detalles Clínicos</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Proveedor</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($products as $product)
                                <tr class="hover:bg-gray-50/80 transition-colors duration-200 cursor-pointer" @click="
                                    stockProductName = '{{ addslashes($product->name) }}'; 
                                    stockBatches = {{ json_encode($product->batches->groupBy('branch_id')->map(function($group, $branchId) { $first = $group->sortBy('id')->first(); $racks = $group->pluck('rack')->filter()->unique()->implode(', '); $shelves = $group->pluck('shelf')->filter()->unique()->implode(', '); return ['id' => $branchId ?: $first->id, 'stock' => $group->sum('stock'), 'branch' => $first->branch->name ?? 'Desconocida', 'rack' => $racks ?: 'N/A', 'shelf' => $shelves ?: 'N/A', 'selling_price' => number_format($first->selling_price, 2), 'batches_count' => $group->count(), 'lots' => $group->map(function($b) { return ['id' => $b->id, 'batch_number' => $b->batch_number ?: 'Sin Lote', 'stock' => $b->stock, 'rack' => $b->rack ?: 'N/A', 'shelf' => $b->shelf ?: 'N/A', 'selling_price' => number_format($b->selling_price, 2)]; })->values()]; })->values()) }}; 
                                    stockModal = true;
                                ">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono">
                                        {{ $product->sku }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-bold text-gray-900">{{ $product->name }}</div>
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded {{ $product->type === 'PAT' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ $product->type }}
                                            </span>
                                        </div>
                                        @if($product->presentation)
                                            <div class="text-xs text-gray-500 mt-0.5">{{ $product->presentation }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal max-w-xs">
                                        @if($product->active_ingredient)
                                            <div class="text-sm text-gray-700 font-medium break-words">{{ $product->active_ingredient }}</div>
                                        @else
                                            <div class="text-sm text-gray-400 italic">N/A</div>
                                        @endif
                                        <div class="flex gap-1 mt-1">
                                            @if($product->is_controlled)
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-100 text-red-700 border border-red-200">CONTROLADO</span>
                                            @endif
                                            @if($product->prescription_required)
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-orange-100 text-orange-700 border border-orange-200">RECETA OBLIGATORIA</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2.5 py-1 inline-flex text-xs font-medium rounded-md bg-green-50 text-green-700 border border-green-100 shadow-sm">
                                            {{ $product->category->name ?? 'Sin Categoría' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $product->provider->name ?? 'Sin Proveedor' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" @click.stop>
                                        <a href="{{ route('products.edit', $product) }}" class="text-blue-500 hover:text-blue-700 mx-3 transition-colors">Editar</a>
                                        
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-400 hover:text-red-600 transition-colors" @click.prevent="$dispatch('open-confirm-delete', { message: '¿Eliminar este medicamento del catálogo?', form: $el.closest('form') })">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-24 text-center">
                                        <div class="flex flex-col items-center justify-center transform transition-all duration-500 hover:scale-105">
                                            <div class="relative p-5 bg-green-50 rounded-full border border-green-100 mb-5 shadow-inner">
                                                <div class="absolute inset-0 bg-green-700 rounded-full opacity-10 animate-ping"></div>
                                                <svg class="w-12 h-12 text-green-700 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">Tu catálogo está vacío</h3>
                                            <p class="text-gray-500 text-sm mb-8 max-w-sm leading-relaxed">No has agregado ningún medicamento aún. Comienza registrando el primero para dar de alta el inventario.</p>
                                            
                                            <a href="{{ route('products.create') }}" class="inline-flex items-center text-white font-semibold bg-green-700 hover:bg-green-800 px-6 py-3 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5">
                                                + Registrar el primer producto
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($products->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50" x-show="search === @js((string)request('search', ''))">
                            {{ $products->links() }}
                        </div>
                    @endif

                </div>
            </div>
            </div>


        <!-- STOCK MODAL -->
        <div x-show="stockModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak style="display: none;">
            <!-- Backdrop -->
            <div x-show="stockModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="stockModal = false"></div>
            
            <!-- Modal content -->
            <div x-show="stockModal" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-4 overflow-hidden border border-gray-100 flex flex-col max-h-[90vh]">
                
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-800 to-green-500"></div>
                
                <div class="flex justify-between items-center p-6 border-b border-gray-50 flex-shrink-0">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900" x-text="stockProductName"></h3>
                        <p class="text-sm text-gray-500 mt-1">Detalle de Existencias y Precios</p>
                    </div>
                    <button @click="stockModal = false" class="text-gray-400 hover:text-red-500 transition-colors focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-grow bg-gray-50/30">
                    <!-- Cuando no hay stock -->
                    <template x-if="stockBatches.length === 0">
                        <div class="text-center py-10 bg-white rounded-xl border border-gray-100">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Sin existencias</h3>
                            <p class="text-sm text-gray-500 mt-1">Este producto no cuenta con unidades registradas en bodega actualmente.</p>
                        </div>
                    </template>

                    <!-- Cuando hay stock -->
                    <template x-if="stockBatches.length > 0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <template x-for="batch in stockBatches" :key="batch.id">
                                <div x-data="{ showLots: false }" class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm transition-all" :class="{ 'cursor-pointer hover:shadow-md hover:border-indigo-200 ring-2 ring-indigo-500/10': batch.batches_count > 1, 'hover:shadow-md': batch.batches_count <= 1 }" @click="if(batch.batches_count > 1) showLots = !showLots">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center gap-2">
                                            <div class="bg-green-100 text-green-700 p-1.5 rounded-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            </div>
                                            <div>
                                                <span class="text-xs text-gray-500 font-bold uppercase block">Sucursal</span>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="text-sm font-bold text-gray-900" x-text="batch.branch"></span>
                                                    <template x-if="batch.batches_count > 1">
                                                        <div class="flex items-center gap-1">
                                                            <span class="px-1.5 py-0.5 text-[10px] font-extrabold rounded bg-indigo-100 text-indigo-700 border border-indigo-200" x-text="batch.batches_count + ' lotes'"></span>
                                                            <span class="text-[10px] font-bold text-indigo-600 underline flex items-center gap-0.5" x-text="showLots ? 'Ocultar racks' : 'Ver racks y lotes'"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center justify-center px-2.5 py-1 text-sm font-bold text-blue-700 bg-blue-50 rounded-lg border border-blue-100">
                                                <span x-text="batch.stock"></span>&nbsp;uds
                                            </span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-gray-50">
                                        <div>
                                            <span class="text-xs text-gray-500 block">Ubicación</span>
                                            <span class="text-sm font-medium text-gray-800">
                                                Rack <span x-text="batch.rack"></span> / Nivel <span x-text="batch.shelf"></span>
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs text-gray-500 block">Precio Público</span>
                                            <span class="text-lg font-black text-green-600">$<span x-text="batch.selling_price"></span></span>
                                        </div>
                                    </div>

                                    <!-- Desglose por Lote / Rack cuando hay varios -->
                                    <div x-show="showLots" x-transition x-cloak class="mt-4 pt-3 border-t border-indigo-100 bg-indigo-50/40 -mx-5 -mb-5 p-4 rounded-b-xl space-y-2" @click.stop>
                                        <div class="text-xs font-bold text-indigo-900 uppercase flex items-center justify-between">
                                            <span>Desglose por Ubicación y Lote</span>
                                            <span class="text-[10px] font-normal text-indigo-600">Total: <span x-text="batch.batches_count"></span> lotes</span>
                                        </div>
                                        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                            <template x-for="lot in batch.lots" :key="lot.id">
                                                <div class="bg-white p-2.5 rounded-lg border border-indigo-100/80 shadow-2xs flex justify-between items-center text-xs">
                                                    <div>
                                                        <div class="font-bold text-gray-800" x-text="'Lote: ' + lot.batch_number"></div>
                                                        <div class="text-[11px] text-gray-600 flex items-center gap-1.5 mt-1">
                                                            <span class="inline-flex items-center gap-1 bg-gray-100 px-2 py-0.5 rounded text-gray-700 font-bold border border-gray-200">
                                                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                Rack <span x-text="lot.rack"></span> / Nivel <span x-text="lot.shelf"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <span class="font-extrabold text-blue-700 bg-blue-50 px-2 py-1 rounded border border-blue-100 block"><span x-text="lot.stock"></span> uds</span>
                                                        <div class="text-[10px] text-green-600 font-bold mt-1">$<span x-text="lot.selling_price"></span></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end flex-shrink-0">
                    <button @click="stockModal = false" class="px-6 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-100 transition-all focus:outline-none focus:ring-2 focus:ring-gray-200">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal del Lector de Código de Barras -->
    <x-barcode-scanner-modal targetModel="search" />
</x-app-layout>