<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Nuevo Traspaso
                    </h2>
                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Envío de medicamentos entre sucursales
                    </p>
                </div>
                <div>
                    <a href="{{ route('transfers.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                        &larr; Volver al historial
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="transferForm()" @scanner-result.window="if($event.detail.target === 'scanQuery'){ scanQuery = $event.detail.value; $nextTick(() => handleScan()); } else if($event.detail.target === 'scanQueryBatch'){ scanQueryBatch = $event.detail.value; $nextTick(() => handleScanBatch()); }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="mb-6 flex items-center p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm">
                    <ul class="list-disc list-inside text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 flex items-center p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm" role="alert">
                    <span class="text-sm font-bold">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-8">
                    <form action="{{ route('transfers.store') }}" method="POST" id="transferForm" @submit.prevent="submitForm">
                        @csrf
                        <input type="hidden" name="items_json" id="itemsJsonInput">
                        
                        <!-- Sección 1: Sucursales -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">1. Origen y Destino</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Sucursal de Origen</label>
                                    @if($fromBranchId)
                                        <!-- Empleado normal, solo ve su sucursal -->
                                        @php
                                            $origin = $branches->firstWhere('id', $fromBranchId);
                                        @endphp
                                        <input type="hidden" name="from_branch_id" value="{{ $fromBranchId }}">
                                        <div class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 font-semibold border shadow-sm">
                                            {{ $origin ? $origin->name : 'Desconocida' }}
                                        </div>
                                    @else
                                        <!-- Superadmin -->
                                        <select name="from_branch_id" x-model="fromBranchId" @change="updateBatches" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors">
                                            <option value="" disabled>-- Selecciona el Origen --</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                
                                <div class="hidden md:flex justify-center mb-3">
                                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </div>

                                <div class="md:col-start-2">
                                    <label for="to_branch_id" class="block text-sm font-bold text-gray-700 mb-2">Sucursal de Destino <span class="text-red-500">*</span></label>
                                    <select name="to_branch_id" id="to_branch_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors">
                                        <option value="" disabled selected>-- Selecciona el Destino --</option>
                                        @foreach($branches as $branch)
                                            <!-- Validar que no se muestre la bodega si es empleado de mostrador -->
                                            @if(auth()->user()->hasRole('Personal de Mostrador') && $branch->is_warehouse)
                                                @continue
                                            @endif
                                            
                                            <!-- Validar que no se muestre la sucursal de origen actual usando Alpine x-show -->
                                            <option value="{{ $branch->id }}" x-show="fromBranchId != {{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Opciones de Envío -->
                        <div class="mb-6 flex justify-center">
                            <div class="inline-flex bg-gray-100 rounded-xl p-1 shadow-inner">
                                <button type="button" @click="setMode('product')" :class="{'bg-white shadow text-green-700': mode === 'product', 'text-gray-500 hover:text-gray-700': mode !== 'product'}" class="px-6 py-2.5 rounded-lg font-bold text-sm transition-all duration-200">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Envío Automático (Recomendado)
                                </button>
                                <button type="button" @click="setMode('batch')" :class="{'bg-white shadow text-green-700': mode === 'batch', 'text-gray-500 hover:text-gray-700': mode !== 'batch'}" class="px-6 py-2.5 rounded-lg font-bold text-sm transition-all duration-200">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    Lote Manual
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="mode" x-model="mode">

                        <!-- Mensaje de Error Dinámico de Alpine -->
                        <div x-show="errorMessage" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="mb-6 flex items-center p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm" style="display: none;">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-bold" x-text="errorMessage"></span>
                            <button type="button" @click="errorMessage = ''" class="ml-auto text-red-500 hover:text-red-700 focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Sección 2: Selección de Medicamentos -->
                        <div class="mb-8 bg-green-50/50 p-6 rounded-2xl border border-green-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <span x-text="mode === 'product' ? '2. Productos a Transferir (FIFO)' : '2. Lotes a Transferir'"></span>
                            </h3>
                            
                            <!-- Buscador Producto (Escáner de código de barras y búsqueda manual) -->
                            <div x-show="mode === 'product'" class="mb-6 relative">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Escanear o Buscar Producto (SKU o Nombre)</label>
                                <div class="relative max-w-2xl flex items-center bg-white rounded-xl border border-gray-300 shadow-sm focus-within:border-green-500 focus-within:ring-1 focus-within:ring-green-500 transition-all overflow-hidden">
                                    <div class="pl-4 pr-2 text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" x-model="scanQuery" @input="filterProducts" @focus="showDropdown = true" @click="showDropdown = true" @keydown.enter.prevent="handleScan" placeholder="Escribe el nombre o escanea el código de barras aquí..." class="w-full py-3 border-none focus:ring-0 text-lg">
                                    <button type="button" @click="$dispatch('open-scanner-scanquery')" class="p-3.5 bg-gray-50 text-gray-600 hover:bg-green-100 hover:text-green-700 font-bold border-l border-gray-200 transition-colors flex items-center justify-center group" title="Escanear Código">
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
                                    
                                    <!-- Dropdown de resultados -->
                                    <div x-show="showDropdown && filteredProducts.length > 0" @click.away="showDropdown = false" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                                        <template x-for="prod in filteredProducts" :key="prod.id">
                                            <div @click="selectProductManual(prod)" class="px-4 py-3 hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-0 flex justify-between items-center">
                                                <div>
                                                    <div class="font-bold text-gray-800" x-text="prod.name"></div>
                                                    <div class="text-xs text-gray-500">SKU: <span x-text="prod.sku"></span></div>
                                                </div>
                                                <div class="text-sm font-bold text-green-700 bg-green-100 px-2 py-1 rounded">
                                                    Stock: <span x-text="prod.total_stock"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 mt-3 font-medium bg-green-50/50 inline-block p-2.5 rounded-lg border border-green-100">
                                    <span class="text-green-700 font-bold mr-1">Tip:</span> Al escanear, el producto se agrega a la lista inferior automáticamente. También puedes escribir el nombre para buscarlo y darle clic.
                                </p>
                            </div>

                            <!-- Buscador Lote -->
                            <div x-show="mode === 'batch'" class="mb-6 relative" style="display: none;">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Escanear o Buscar Lote (SKU, Nombre o Núm. Lote)</label>
                                <div class="relative max-w-2xl flex items-center bg-white rounded-xl border border-gray-300 shadow-sm focus-within:border-green-500 focus-within:ring-1 focus-within:ring-green-500 transition-all overflow-hidden">
                                    <div class="pl-4 pr-2 text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" x-model="scanQueryBatch" @input="filterBatches" @focus="showDropdownBatch = true" @click="showDropdownBatch = true" @keydown.enter.prevent="handleScanBatch" placeholder="Escribe el nombre, SKU o escanea el lote aquí..." class="w-full py-3 border-none focus:ring-0 text-lg">
                                    <button type="button" @click="$dispatch('open-scanner-scanquerybatch')" class="p-3.5 bg-gray-50 text-gray-600 hover:bg-green-100 hover:text-green-700 font-bold border-l border-gray-200 transition-colors flex items-center justify-center group" title="Escanear Código">
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
                                    
                                    <!-- Dropdown de resultados -->
                                    <div x-show="showDropdownBatch && filteredBatches.length > 0" @click.away="showDropdownBatch = false" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                                        <template x-for="batch in filteredBatches" :key="batch.id">
                                            <div @click="selectBatchManual(batch)" class="px-4 py-3 hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-0 flex justify-between items-center">
                                                <div>
                                                    <div class="font-bold text-gray-800" x-text="`[${batch.product.sku}] ${batch.product.name}`"></div>
                                                    <div class="text-xs text-gray-500">Lote: <span class="font-bold text-gray-700" x-text="batch.batch_number"></span></div>
                                                </div>
                                                <div class="text-sm font-bold text-green-700 bg-green-100 px-2 py-1 rounded">
                                                    Stock: <span x-text="batch.stock"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 mt-3 font-medium bg-green-50/50 inline-block p-2.5 rounded-lg border border-green-100">
                                    <span class="text-green-700 font-bold mr-1">Tip:</span> Al escanear o seleccionar, el lote se agrega a la lista inferior automáticamente.
                                </p>
                            </div>

                            <p x-show="mode === 'batch'" class="text-sm text-yellow-600 font-bold mb-4 bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                                Nota: Elegir lotes manualmente enviará una solicitud de autorización a los administradores si no cuentas con los permisos necesarios.
                            </p>

                            <!-- Lista de items a transferir -->
                            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Medicamento</th>
                                            <th x-show="mode === 'batch'" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Lote</th>
                                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Stock Disp.</th>
                                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Cantidad</th>
                                            <th class="px-4 py-3 text-right"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr x-show="items.length === 0">
                                            <td :colspan="mode === 'batch' ? 5 : 4" class="px-4 py-8 text-center text-gray-500 text-sm">Aún no has agregado medicamentos al traspaso.</td>
                                        </tr>
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-bold text-gray-900" x-text="mode === 'product' ? item.product.name : item.batch.product.name"></td>
                                                <td x-show="mode === 'batch'" class="px-4 py-3 text-sm text-gray-600 font-mono" x-text="mode === 'batch' ? item.batch.batch_number : ''"></td>
                                                <td class="px-4 py-3 text-sm text-center font-bold text-gray-600" x-text="mode === 'product' ? item.product.total_stock : item.batch.stock"></td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <button type="button" @click="item.quantity = Math.max(1, parseInt(item.quantity||1)-1)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-100 text-green-700 hover:bg-green-200 text-lg font-black border border-green-200 transition-colors select-none" tabindex="-1">−</button>
                                                        <input type="number" x-model.number="item.quantity" min="1" :max="mode === 'product' ? item.product.total_stock : item.batch.stock" class="w-16 text-center rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 text-sm py-1.5 text-gray-900 font-bold" required @wheel.prevent>
                                                        <button type="button" @click="item.quantity = Math.min(mode === 'product' ? item.product.total_stock : item.batch.stock, parseInt(item.quantity||1)+1)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-100 text-green-700 hover:bg-green-200 text-lg font-black border border-green-200 transition-colors select-none" tabindex="-1">+</button>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 p-1">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Sección 3: Notas -->
                        <div class="mb-8">
                            <label for="notes" class="block text-sm font-bold text-gray-700 mb-2">Notas del Traspaso (Opcional)</label>
                            <textarea name="notes" id="notes" rows="3" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors" placeholder="Motivo del traspaso, quién lo transportará, etc."></textarea>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-end pt-4 border-t border-gray-100 mt-6">
                            <a href="{{ route('transfers.index') }}" class="px-6 py-3 mr-4 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" :disabled="items.length === 0 || submitting" class="px-8 py-3 text-sm font-bold text-white bg-green-700 rounded-xl hover:bg-green-800 transition-colors shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5">
                                Generar Traspaso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script de AlpineJS para manejar la lista de items -->
    <script>
        function transferForm() {
            return {
                mode: 'product', // 'product' o 'batch'
                allBatches: @json($batches), 
                availableBatches: [],
                availableProducts: [],
                selectedBatchId: '',
                scanQuery: '',
                scanQueryBatch: '',
                showDropdown: false,
                showDropdownBatch: false,
                filteredProducts: [],
                filteredBatches: [],
                items: [],
                fromBranchId: '{{ $fromBranchId ?? '' }}',
                errorMessage: '',
                submitting: false,

                showError(msg) {
                    this.errorMessage = msg;
                    setTimeout(() => {
                        if (this.errorMessage === msg) {
                            this.errorMessage = '';
                        }
                    }, 5000); // Ocultar después de 5 segundos
                },

                init() {
                    this.updateBatches();
                },

                setMode(m) {
                    this.mode = m;
                    this.items = []; // Resetea items al cambiar de modo
                },

                updateBatches() {
                    if (!this.fromBranchId) {
                        this.availableBatches = [];
                    } else {
                        if ('{{ auth()->user()->hasRole(["superadmin", "admin", "compras"]) }}') {
                            this.availableBatches = this.allBatches.filter(b => b.branch_id == this.fromBranchId);
                        } else {
                            this.availableBatches = this.allBatches;
                        }
                    }
                    
                    // Calcular productos disponibles agrupando lotes
                    let prodMap = {};
                    this.availableBatches.forEach(b => {
                        if (!prodMap[b.product.id]) {
                            prodMap[b.product.id] = { ...b.product, total_stock: 0 };
                        }
                        prodMap[b.product.id].total_stock += b.stock;
                    });
                    this.availableProducts = Object.values(prodMap);
                    
                    // Limpiar lista si cambia el origen
                    this.items = [];
                },

                filterProducts() {
                    if (this.scanQuery === '') {
                        this.filteredProducts = [];
                        return;
                    }
                    
                    const q = this.scanQuery.toLowerCase().trim();
                    this.filteredProducts = this.availableProducts.filter(p => 
                        p.name.toLowerCase().includes(q) || 
                        (p.sku && p.sku.toLowerCase().includes(q))
                    ).slice(0, 10);
                },

                handleScan() {
                    const q = this.scanQuery.trim().toLowerCase();
                    if (!q) return;

                    // Buscar el producto por SKU exacto
                    const exactMatch = this.availableProducts.find(p => p.sku && p.sku.toLowerCase() === q);
                    
                    if (exactMatch) {
                        this.addProductByObject(exactMatch);
                        this.clearSearch();
                    } else if (this.filteredProducts.length === 1) {
                        this.addProductByObject(this.filteredProducts[0]);
                        this.clearSearch();
                    } else {
                        this.showError("Producto no encontrado o sin stock disponible en esta sucursal para enviar.");
                    }
                },

                selectProductManual(prod) {
                    this.addProductByObject(prod);
                    this.clearSearch();
                },
                
                clearSearch() {
                    this.scanQuery = '';
                    this.showDropdown = false;
                    this.filteredProducts = [];
                },

                addProductByObject(prod) {
                    const existingItem = this.items.find(i => i.product && i.product.id === prod.id);
                    
                    if (existingItem) {
                        if (existingItem.quantity < prod.total_stock) {
                            existingItem.quantity++;
                        } else {
                            this.showError("Límite máximo de stock alcanzado (" + prod.total_stock + ") para " + prod.name);
                        }
                    } else {
                        this.items.push({
                            product: prod,
                            quantity: 1
                        });
                    }
                },

                addBatchByObject(batch) {
                    if (this.items.find(i => i.batch && i.batch.id === batch.id)) {
                        this.showError("Este lote ya fue agregado a la lista.");
                        return;
                    }

                    this.items.push({
                        batch: batch,
                        quantity: 1
                    });
                },

                filterBatches() {
                    if (this.scanQueryBatch === '') {
                        this.filteredBatches = [];
                        return;
                    }
                    
                    const q = this.scanQueryBatch.toLowerCase().trim();
                    this.filteredBatches = this.availableBatches.filter(b => 
                        b.product.name.toLowerCase().includes(q) || 
                        (b.product.sku && b.product.sku.toLowerCase().includes(q)) ||
                        (b.batch_number && b.batch_number.toLowerCase().includes(q))
                    ).slice(0, 10);
                },

                handleScanBatch() {
                    const q = this.scanQueryBatch.trim().toLowerCase();
                    if (!q) return;

                    const exactMatch = this.availableBatches.find(b => 
                        (b.batch_number && b.batch_number.toLowerCase() === q) || 
                        (b.product.sku && b.product.sku.toLowerCase() === q)
                    );
                    
                    if (exactMatch) {
                        this.addBatchByObject(exactMatch);
                        this.clearSearchBatch();
                    } else if (this.filteredBatches.length === 1) {
                        this.addBatchByObject(this.filteredBatches[0]);
                        this.clearSearchBatch();
                    } else {
                        this.showError("Lote no encontrado o sin stock disponible.");
                    }
                },

                selectBatchManual(batch) {
                    this.addBatchByObject(batch);
                    this.clearSearchBatch();
                },

                clearSearchBatch() {
                    this.scanQueryBatch = '';
                    this.showDropdownBatch = false;
                    this.filteredBatches = [];
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },

                submitForm() {
                    if (this.submitting) return;
                    if (this.items.length === 0) {
                        this.showError("Debes agregar al menos un medicamento al traspaso.");
                        return;
                    }
                    const toBranch = document.getElementById('to_branch_id').value;
                    if (!toBranch) {
                        this.showError("Selecciona la sucursal de destino.");
                        return;
                    }
                    if (!this.fromBranchId) {
                        this.showError("Selecciona la sucursal de origen.");
                        return;
                    }
                    // form.submit() omite las validaciones HTML, así que validamos cantidades aquí
                    for (const item of this.items) {
                        const qty = parseInt(item.quantity);
                        const stock = this.mode === 'product' ? item.product.total_stock : item.batch.stock;
                        const name = this.mode === 'product' ? item.product.name : item.batch.product.name;
                        if (!Number.isInteger(qty) || qty < 1) {
                            this.showError("La cantidad de " + name + " debe ser al menos 1.");
                            return;
                        }
                        if (qty > stock) {
                            this.showError("La cantidad de " + name + " (" + qty + ") excede el stock disponible (" + stock + ").");
                            return;
                        }
                        item.quantity = qty;
                    }
                    this.submitting = true;
                    document.getElementById('itemsJsonInput').value = JSON.stringify(this.items);
                    document.getElementById('transferForm').submit();
                }
            }
        }
    </script>
    
    <!-- Modales del Lector de Código de Barras -->
    <x-barcode-scanner-modal targetModel="scanQuery" />
    <x-barcode-scanner-modal targetModel="scanQueryBatch" />
</x-app-layout>
