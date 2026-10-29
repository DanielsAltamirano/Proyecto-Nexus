<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-blue-50 rounded-full opacity-70 blur-2xl"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-blue-900 to-blue-600 tracking-tight">
                        Solicitar Traspaso
                    </h2>
                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                        Solicitar mercancía a otra sucursal
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

    <div class="py-8" x-data="requestForm()" @scanner-result.window="if($event.detail.target === 'scanQuery'){ scanQuery = $event.detail.value; $nextTick(() => handleScan()); }">
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
                    <form action="{{ route('transfers.storeRequest') }}" method="POST" id="requestForm">
                        @csrf
                        
                        <!-- Sección 1: Sucursales -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">1. ¿A quién solicitas?</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                                <div>
                                    <label for="from_branch_id" class="block text-sm font-bold text-gray-700 mb-2">Pedir a (Sucursal de Origen) <span class="text-red-500">*</span></label>
                                    <select name="from_branch_id" id="from_branch_id" x-model="fromBranchId" @change="revalidateAll()" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors">
                                        <option value="" disabled {{ !isset($prefillFromBranchId) ? 'selected' : '' }}>-- Selecciona la Sucursal --</option>
                                        @foreach($branches as $branch)
                                            <!-- Validar que no se muestre la bodega si es empleado de mostrador -->
                                            @if(auth()->user()->hasRole('Personal de Mostrador') && $branch->is_warehouse)
                                                @continue
                                            @endif
                                            
                                            <!-- Validar que no se pueda pedir a sí mismo -->
                                            <option value="{{ $branch->id }}" x-show="toBranchId != {{ $branch->id }}" {{ (isset($prefillFromBranchId) && $prefillFromBranchId == $branch->id) ? 'selected' : '' }}>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="hidden md:flex justify-center mb-3">
                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </div>

                                <div class="md:col-start-2">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Recibe (Tu Sucursal)</label>
                                    @if($toBranchId)
                                        <!-- Empleado normal, solo ve su sucursal como destino -->
                                        @php
                                            $dest = $branches->firstWhere('id', $toBranchId);
                                        @endphp
                                        <input type="hidden" name="to_branch_id" value="{{ $toBranchId }}">
                                        <div class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 font-semibold border shadow-sm">
                                            {{ $dest ? $dest->name : 'Desconocida' }}
                                        </div>
                                    @else
                                        <!-- Superadmin -->
                                        <select name="to_branch_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors">
                                            <option value="" disabled>-- Selecciona el Destino --</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Mensaje de Error Dinámico de Alpine -->
                        <div x-show="errorMessage" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="mb-6 flex items-center p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm" style="display: none;">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-bold" x-text="errorMessage"></span>
                            <button type="button" @click="errorMessage = ''" class="ml-auto text-red-500 hover:text-red-700 focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Sección 2: Selección de Productos -->
                        <div class="mb-8 bg-blue-50/50 p-6 rounded-2xl border border-blue-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                2. Productos a Solicitar
                            </h3>
                            
                            <!-- Buscador Producto (Escáner de código de barras y búsqueda manual) -->
                            <div class="mb-6 relative">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Escanear o Buscar Producto (SKU o Nombre)</label>
                                <div class="relative max-w-2xl flex items-center bg-white rounded-xl border border-gray-300 shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-all overflow-hidden">
                                    <div class="pl-4 pr-2 text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" x-model="scanQuery" @input="filterProducts" @focus="showDropdown = true" @click="showDropdown = true" @keydown.enter.prevent="handleScan" placeholder="Escribe el nombre o escanea el código de barras aquí..." class="w-full py-3 border-none focus:ring-0 text-lg">
                                    <button type="button" @click="$dispatch('open-scanner-scanquery')" class="p-3.5 bg-gray-50 text-gray-600 hover:bg-blue-100 hover:text-blue-700 font-bold border-l border-gray-200 transition-colors flex items-center justify-center group" title="Escanear Código">
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
                                            <div @click="selectProductManual(prod)" class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 flex justify-between items-center">
                                                <div>
                                                    <div class="font-bold text-gray-800" x-text="prod.name"></div>
                                                    <div class="text-xs text-gray-500">SKU: <span x-text="prod.sku"></span></div>
                                                </div>
                                                <div class="text-sm font-bold text-blue-700 bg-blue-100 px-2 py-1 rounded">
                                                    Máx Disp: <span x-text="getMaxStock(prod.id)"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 mt-3 font-medium bg-blue-50/50 inline-block p-2.5 rounded-lg border border-blue-100">
                                    <span class="text-blue-700 font-bold mr-1">Tip:</span> Al escanear, el producto se agrega a la solicitud. También puedes escribir el nombre para buscarlo y darle clic.
                                </p>
                            </div>

                            <!-- Lista de items a solicitar -->
                            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">

                                <!-- === VISTA MÓVIL: Tarjetas (visible solo en pantallas chicas) === -->
                                <div class="md:hidden">
                                    <template x-if="items.length === 0">
                                        <p class="px-4 py-8 text-center text-gray-500 text-sm">Aún no has agregado productos a la solicitud.</p>
                                    </template>
                                    <template x-for="(item, index) in items" :key="index">
                                        <div class="p-4 border-b border-gray-100 last:border-b-0">
                                            <div class="flex items-start justify-between gap-2 mb-3">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-bold text-gray-400 font-mono" x-text="item.product.sku"></p>
                                                    <p class="text-sm font-bold text-gray-900 leading-tight" x-text="item.product.name"></p>
                                                </div>
                                                <button type="button" @click="removeItem(index)" class="flex-shrink-0 text-red-500 hover:text-red-700 p-1.5 hover:bg-red-50 rounded-lg transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="text-xs font-bold text-blue-600" x-text="`Máx disponible: ${getMaxStock(item.product.id)}`"></span>
                                                <div class="flex items-center gap-2">
                                                    <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product.id">
                                                    <button type="button" @click="item.quantity = Math.max(1, parseInt(item.quantity||1)-1)" class="w-11 h-11 flex items-center justify-center rounded-xl bg-blue-100 text-blue-700 hover:bg-blue-200 text-xl font-black border border-blue-200 transition-colors select-none" tabindex="-1">−</button>
                                                    <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" @input="validateStock(item)" :max="getMaxStock(item.product.id)" min="1" class="w-16 text-center rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-base py-2 text-gray-900 font-bold" required @wheel.prevent>
                                                    <button type="button" @click="item.quantity = Math.min(getMaxStock(item.product.id), parseInt(item.quantity||1)+1); validateStock(item)" class="w-11 h-11 flex items-center justify-center rounded-xl bg-blue-100 text-blue-700 hover:bg-blue-200 text-xl font-black border border-blue-200 transition-colors select-none" tabindex="-1">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- === VISTA ESCRITORIO: Tabla (visible solo en md+) === -->
                                <table class="hidden md:table min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">SKU</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Producto</th>
                                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Cantidad</th>
                                            <th class="px-4 py-3 text-right"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr x-show="items.length === 0">
                                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 text-sm">Aún no has agregado productos a la solicitud.</td>
                                        </tr>
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-bold text-gray-900" x-text="item.product.sku"></td>
                                                <td class="px-4 py-3 text-sm text-gray-600" x-text="item.product.name"></td>
                                                <td class="px-4 py-3 text-center">
                                                    <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product.id">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <button type="button" @click="item.quantity = Math.max(1, parseInt(item.quantity||1)-1)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 text-lg font-black border border-blue-200 transition-colors select-none" tabindex="-1">−</button>
                                                        <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" @input="validateStock(item)" :max="getMaxStock(item.product.id)" min="1" class="w-16 text-center rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm py-1.5 text-gray-900 font-bold" required @wheel.prevent>
                                                        <button type="button" @click="item.quantity = Math.min(getMaxStock(item.product.id), parseInt(item.quantity||1)+1); validateStock(item)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 text-lg font-black border border-blue-200 transition-colors select-none" tabindex="-1">+</button>
                                                    </div>
                                                    <div class="text-xs text-blue-600 font-bold mt-1 text-center" x-text="`Máx: ${getMaxStock(item.product.id)}`"></div>
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
                            <label for="notes" class="block text-sm font-bold text-gray-700 mb-2">Notas de la Solicitud (Opcional)</label>
                            <textarea name="notes" id="notes" rows="3" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors" placeholder="Justificación de la solicitud, urgencia, etc."></textarea>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-end pt-4 border-t border-gray-100 mt-6">
                            <a href="{{ route('transfers.index') }}" class="px-6 py-3 mr-4 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" :disabled="items.length === 0" class="px-8 py-3 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5">
                                Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script de AlpineJS para manejar la lista de items -->
    <script>
        function requestForm() {
            return {
                allProducts: @json($products),
                allStocks: @json($stocks),
                selectedProductId: '',
                scanQuery: '',
                showDropdown: false,
                filteredProducts: [],
                fromBranchId: '{{ $prefillFromBranchId ?? '' }}',
                toBranchId: '{{ $toBranchId ?? '' }}',
                items: [],
                errorMessage: '',

                showError(msg) {
                    this.errorMessage = msg;
                    setTimeout(() => {
                        if (this.errorMessage === msg) {
                            this.errorMessage = '';
                        }
                    }, 5000);
                },

                init() {
                    const prefillProductId = '{{ $prefillProductId ?? '' }}';
                    if (prefillProductId) {
                        this.selectedProductId = prefillProductId;
                        // Simular que lo encontró
                        const prod = this.allProducts.find(p => p.id == prefillProductId);
                        if (prod) {
                            setTimeout(() => this.addProductByObject(prod), 100);
                        }
                    }
                },

                getMaxStock(productId) {
                    if (!this.fromBranchId) return 0;
                    const stockRecord = this.allStocks.find(s => s.branch_id == this.fromBranchId && s.product_id == productId);
                    return stockRecord ? stockRecord.total_stock : 0;
                },

                validateStock(item) {
                    let max = this.getMaxStock(item.product.id);
                    if (item.quantity > max) {
                        item.quantity = max;
                    }
                },

                revalidateAll() {
                    this.items.forEach(item => this.validateStock(item));
                },

                filterProducts() {
                    if (!this.fromBranchId) {
                        this.showError("Por favor selecciona primero la sucursal a la que vas a pedir la mercancía.");
                        this.scanQuery = '';
                        return;
                    }
                    if (this.scanQuery === '') {
                        this.filteredProducts = [];
                        return;
                    }
                    
                    const q = this.scanQuery.toLowerCase().trim();
                    this.filteredProducts = this.allProducts.filter(p => 
                        p.name.toLowerCase().includes(q) || 
                        (p.sku && p.sku.toLowerCase().includes(q))
                    ).slice(0, 10);
                },

                handleScan() {
                    if (!this.fromBranchId) {
                        this.showError("Por favor selecciona primero la sucursal a la que vas a pedir la mercancía.");
                        this.scanQuery = '';
                        return;
                    }
                    
                    const q = this.scanQuery.trim().toLowerCase();
                    if (!q) return;

                    const exactMatch = this.allProducts.find(p => p.sku && p.sku.toLowerCase() === q);
                    
                    if (exactMatch) {
                        this.addProductByObject(exactMatch);
                        this.clearSearch();
                    } else if (this.filteredProducts.length === 1) {
                        this.addProductByObject(this.filteredProducts[0]);
                        this.clearSearch();
                    } else {
                        this.showError("Producto no encontrado.");
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

                addProductByObject(product) {
                    if (!this.fromBranchId) {
                        this.showError("Por favor selecciona primero la sucursal a la que vas a pedir la mercancía.");
                        return;
                    }

                    let maxStock = this.getMaxStock(product.id);
                    if (maxStock < 1) {
                        this.showError("La sucursal seleccionada no cuenta con existencias de este producto.");
                        return;
                    }

                    const existingItem = this.items.find(i => i.product.id === product.id);
                    if (existingItem) {
                        if (existingItem.quantity < maxStock) {
                            existingItem.quantity++;
                        } else {
                            this.showError("Has alcanzado el límite de stock disponible (" + maxStock + ") para " + product.name);
                        }
                    } else {
                        this.items.push({
                            product: product,
                            quantity: 1
                        });
                    }
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                }
            }
        }
    </script>
    
    <!-- Modal del Lector de Código de Barras -->
    <x-barcode-scanner-modal targetModel="scanQuery" />
</x-app-layout>
