<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Ingreso al inventario
                    </h2>
                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Registra una nueva entrada de productos al inventario.
                    </p>
                </div>
                <div>
                    <a href="{{ route('batches.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                        &larr; Volver al inventario
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="mb-6 flex items-center p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <ul class="list-disc list-inside text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-8">
                    <form action="{{ route('batches.store') }}" method="POST">
                        @csrf
                        
                        <!-- Sección 1: Producto -->
                        <div class="mb-8" x-data="productSearch()" @scanner-result.window="if($event.detail.target === 'searchQuery'){ searchQuery = $event.detail.value; $nextTick(() => { showDropdown = true; filterProducts(); handleEnter(); }); }">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">1. Selección de Medicamento / Producto</h3>
                            
                            <div class="w-full flex flex-col sm:flex-row gap-4 items-end">
                                <div class="flex-grow w-full relative">
                                    <label for="product_search" class="block text-sm font-bold text-gray-700 mb-2">Buscar Producto <span class="text-red-500">*</span></label>
                                    <div class="relative flex items-center bg-white rounded-xl border border-gray-300 shadow-sm focus-within:border-green-500 focus-within:ring-1 focus-within:ring-green-500 transition-all overflow-hidden">
                                        <div class="pl-4 pr-2 text-gray-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </div>
                                        <input type="text" x-model="searchQuery" @input="filterProducts" @focus="showDropdown = true; filterProducts()" @click="showDropdown = true; filterProducts()" @keydown.enter.prevent="handleEnter" placeholder="Escribe el nombre o SKU..." class="w-full py-3 border-none focus:ring-0 text-gray-900" autocomplete="off">
                                        <button type="button" @click="$dispatch('open-scanner-searchquery')" class="p-3.5 bg-gray-50 text-gray-600 hover:bg-green-100 hover:text-green-700 font-bold border-l border-gray-200 transition-colors flex items-center justify-center group" title="Escanear Código">
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
                                    <input type="hidden" name="product_id" x-model="selectedProductId" required>
                                    
                                    <!-- Dropdown de resultados -->
                                    <div x-show="showDropdown && filteredProducts.length > 0" @click.away="showDropdown = false" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                                        <template x-for="product in filteredProducts" :key="product.id">
                                            <div @click="selectProduct(product)" class="px-4 py-2 hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-0">
                                                <div class="font-bold text-gray-800" x-text="product.name"></div>
                                                <div class="text-xs text-gray-500">SKU: <span x-text="product.sku"></span> | Tipo: <span x-text="product.type"></span></div>
                                            </div>
                                        </template>
                                    </div>
                                    <div x-show="showDropdown && filteredProducts.length === 0 && searchQuery.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg px-4 py-3 text-sm text-gray-500" style="display: none;">
                                        No se encontraron productos. Registra uno nuevo.
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                    <button type="button" onclick="openGeneralProductModal()" class="flex-1 sm:flex-none w-full sm:w-auto group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-blue-500 rounded-xl hover:bg-blue-600 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-400/30 whitespace-nowrap">
                                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        <span>Nuevo Producto</span>
                                    </button>
                                    <button type="button" onclick="openProductModal()" class="flex-1 sm:flex-none w-full sm:w-auto group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-green-700 rounded-xl hover:bg-green-800 hover:-translate-y-1 hover:shadow-lg hover:shadow-green-500/30 whitespace-nowrap">
                                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        <span>Nuevo Medicamento</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Producto Seleccionado -->
                            <div x-show="selectedProductName" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-xl flex items-center justify-between" style="display: none;">
                                <div>
                                    <span class="text-xs text-green-600 font-bold uppercase block mb-0.5">Producto Seleccionado:</span>
                                    <span class="font-bold text-gray-800" x-text="selectedProductName"></span>
                                </div>
                                <button type="button" @click="clearSelection()" class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Sección 2: Detalles del Lote -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">2. Detalles de Ingreso</h3>
                            
                            @if(auth()->user()->hasGlobalInventoryAccess())
                                <div class="mb-6 bg-yellow-50 p-4 rounded-xl border border-yellow-200">
                                    <label for="branch_id" class="block text-sm font-bold text-gray-700 mb-2">Sucursal de Destino <span class="text-red-500">*</span></label>
                                    <select name="branch_id" id="branch_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors">
                                        <option value="" disabled {{ old('branch_id') ? '' : 'selected' }}>-- Selecciona la Sucursal --</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-yellow-700 mt-2 font-semibold">📍 Tienes acceso global: asegúrate de seleccionar correctamente a qué sucursal ingresará este stock.</p>
                                </div>
                            @endif

                            <div class="mb-4 p-3 bg-blue-50 text-blue-800 rounded-xl text-sm border border-blue-100 flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span><strong>Nota:</strong> Los campos de <em>Lote</em> y <em>Fecha de Caducidad</em> son <strong>opcionales</strong> y deben dejarse vacíos para productos para los que no aplican (abarrotes, equipo médico básico, etc.).</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="batch_number" class="block text-sm font-bold text-gray-700 mb-2">Número de Lote (Opcional)</label>
                                    <input type="text" name="batch_number" id="batch_number" value="{{ old('batch_number') }}" placeholder="Ej. J26E035" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors uppercase">
                                </div>
                                <div>
                                    <label for="expiration_date" class="block text-sm font-bold text-gray-700 mb-2">Fecha de Caducidad (Opcional)</label>
                                    <input type="date" name="expiration_date" id="expiration_date" value="{{ old('expiration_date') }}" min="{{ date('Y-m-d') }}" max="2050-12-31" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors">
                                </div>
                                <div>
                                    <label for="stock" class="block text-sm font-bold text-gray-700 mb-2">Unidades Recibidas <span class="text-red-500">*</span></label>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="let v=document.getElementById('stock'); v.value=Math.max(0,parseInt(v.value||0)-1);" class="w-10 h-10 flex items-center justify-center rounded-xl bg-green-100 text-green-700 hover:bg-green-200 text-xl font-black border border-green-200 transition-colors select-none" tabindex="-1">−</button>
                                        <input type="number" name="stock" id="stock" value="{{ old('stock') }}" min="0" required class="w-24 text-center rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors font-bold" @wheel.prevent>
                                        <button type="button" onclick="let v=document.getElementById('stock'); v.value=parseInt(v.value||0)+1;" class="w-10 h-10 flex items-center justify-center rounded-xl bg-green-100 text-green-700 hover:bg-green-200 text-xl font-black border border-green-200 transition-colors select-none" tabindex="-1">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 3: Precios -->
                        <div class="mb-8" x-data="priceCalculator()">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">3. Configuración de Precios</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="cost_price" class="block text-sm font-bold text-gray-700 mb-2">Costo de Compra ($) <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" name="cost_price" id="cost_price" x-model.number="cost" @input="calculateFromCost()" step="any" min="0" required placeholder="0.00" class="pl-8 block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Margen de Ganancia (%)</label>
                                    <div class="relative">
                                        <input type="number" x-model.number="margin" @input="calculateFromMargin()" step="any" placeholder="Ej. 30" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors pr-8">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label for="selling_price" class="block text-sm font-bold text-gray-700 mb-2">Precio de Venta ($) <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" name="selling_price" id="selling_price" x-model.number="price" @input="calculateFromPrice()" step="any" min="0" required placeholder="0.00" class="pl-8 block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 4: Ubicación en Bodega -->
                        <div class="mb-8 bg-gray-50 p-6 rounded-xl border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                4. Ubicación Física (Opcional)
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="rack" class="block text-sm font-bold text-gray-700 mb-2">Rack / Pasillo</label>
                                    <input type="text" name="rack" id="rack" value="{{ old('rack') }}" placeholder="Ej. Rack 2" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors">
                                </div>
                                <div>
                                    <label for="shelf" class="block text-sm font-bold text-gray-700 mb-2">Nivel / Estante</label>
                                    <input type="text" name="shelf" id="shelf" value="{{ old('shelf') }}" placeholder="Ej. 4 y 5" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-colors">
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-end pt-4 border-t border-gray-100 mt-6">
                            <a href="{{ route('batches.index') }}" class="px-6 py-3 mr-4 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="px-8 py-3 text-sm font-bold text-white bg-green-700 rounded-xl hover:bg-green-800 transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                Registrar Ingreso al Inventario
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL AJAX: REGISTRO DE MEDICAMENTO -->
    <div id="productModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" onclick="closeProductModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6 border-t-4 border-t-green-600">
                <div class="flex justify-between items-center mb-5 pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center" id="modal-title">
                        <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Registrar Nuevo Medicamento
                    </h3>
                    <button type="button" onclick="closeProductModal()" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Cerrar</span>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div id="modalAlert" class="hidden mb-4 p-3 rounded-lg text-sm font-medium"></div>

                <form id="ajaxProductForm">
                    <!-- Ocultar campo type y forzar a que sea siempre médico (GEN/PAT) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div x-data="{ sku: '' }" @scanner-result.window="if($event.detail.target === 'ajaxsku'){ sku = $event.detail.value; }">
                            <label class="block text-sm font-bold text-gray-700 mb-1">SKU (Código de Barras) <span class="text-red-500">*</span></label>
                            <div class="relative flex items-center bg-white rounded-xl border border-gray-300 focus-within:border-green-500 focus-within:ring-1 focus-within:ring-green-500 transition-all overflow-hidden">
                                <input type="text" id="ajax_sku" x-model="sku" required class="w-full border-none focus:ring-0 p-3 bg-transparent text-gray-900 text-sm">
                                <button type="button" @click="$dispatch('open-scanner-ajaxsku')" class="p-3 bg-gray-50 text-gray-600 hover:bg-green-100 hover:text-green-700 font-bold border-l border-gray-200 transition-colors flex items-center justify-center group" title="Escanear Código">
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
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nombre Comercial <span class="text-red-500">*</span></label>
                            <input type="text" id="ajax_name" required class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tipo de Medicamento <span class="text-red-500">*</span></label>
                            <select id="ajax_type" required class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                                <option value="GEN">GEN (Genérico)</option>
                                <option value="PAT">PAT (Patente)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Sustancia Activa <span class="text-red-500">*</span></label>
                            <input type="text" id="ajax_active_ingredient" required class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Presentación (Vía Admin/Gramaje) <span class="text-red-500">*</span></label>
                            <input type="text" id="ajax_presentation" required placeholder="Ej. Caja con 20 tabs" class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Categoría <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-2">
                                <select id="ajax_category_id" required class="flex-1 w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                                    <option value="" disabled selected>-- Selecciona --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="openCategoryModal()" class="px-3 py-2 bg-gray-100 hover:bg-green-50 text-gray-700 hover:text-green-700 font-bold rounded-xl border border-gray-200 hover:border-green-300 transition-colors shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Proveedor <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-2">
                                <select id="ajax_provider_id" required class="flex-1 w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                                    <option value="" disabled selected>-- Selecciona --</option>
                                    @foreach($providers as $prov)
                                        <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="openProviderModal()" class="px-3 py-2 bg-gray-100 hover:bg-green-50 text-gray-700 hover:text-green-700 font-bold rounded-xl border border-gray-200 hover:border-green-300 transition-colors shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>
                        <div class="md:col-span-2 flex flex-col sm:flex-row gap-4 mt-2">
                            <label class="flex items-center text-sm font-bold text-gray-700 cursor-pointer">
                                <input type="checkbox" id="ajax_prescription_required" value="1" class="w-4 h-4 text-green-600 bg-white border-gray-300 rounded focus:ring-green-500 mr-2">
                                Requiere Receta
                            </label>
                            <label class="flex items-center text-sm font-bold text-gray-700 cursor-pointer">
                                <input type="checkbox" id="ajax_is_controlled" value="1" class="w-4 h-4 text-red-600 bg-white border-gray-300 rounded focus:ring-red-500 mr-2">
                                Medicamento Controlado
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeProductModal()" class="px-4 py-2 bg-white text-gray-700 font-bold rounded-xl border border-gray-300 hover:bg-gray-50 transition-colors">Cancelar</button>
                        <button type="button" id="btnSaveProduct" class="px-4 py-2 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition-colors">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL AJAX: REGISTRO DE PRODUCTO GENERAL -->
    <div id="generalProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" onclick="closeGeneralProductModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6 border-t-4 border-t-blue-600">
                <div class="flex justify-between items-center mb-5 pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Registrar Nuevo Producto General
                    </h3>
                    <button type="button" onclick="closeGeneralProductModal()" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Cerrar</span>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div id="modalAlertGeneral" class="hidden mb-4 p-3 rounded-lg text-sm font-medium"></div>

                <form id="ajaxGeneralProductForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div x-data="{ skuGen: '' }" @scanner-result.window="if($event.detail.target === 'ajaxskugen'){ skuGen = $event.detail.value; }">
                            <label class="block text-sm font-bold text-gray-700 mb-1">SKU (Código de Barras) <span class="text-red-500">*</span></label>
                            <div class="relative flex items-center bg-white rounded-xl border border-gray-300 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-all overflow-hidden">
                                <input type="text" id="ajax_sku_gen" x-model="skuGen" required class="w-full border-none focus:ring-0 p-3 bg-transparent text-gray-900 text-sm">
                                <button type="button" @click="$dispatch('open-scanner-ajaxskugen')" class="p-3 bg-gray-50 text-gray-600 hover:bg-blue-100 hover:text-blue-700 font-bold border-l border-gray-200 transition-colors flex items-center justify-center group" title="Escanear Código">
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
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Producto <span class="text-red-500">*</span></label>
                            <input type="text" id="ajax_name_gen" required placeholder="Ej. Coca Cola 600ml" class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <input type="hidden" id="ajax_type_gen" value="OTR">
                        <input type="hidden" id="ajax_active_ingredient_gen" value="N/A">
                        <input type="hidden" id="ajax_presentation_gen" value="N/A">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Categoría <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-2">
                                <select id="ajax_category_id_gen" required class="flex-1 w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="" disabled selected>-- Selecciona --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="openCategoryModal()" class="px-3 py-2 bg-gray-100 hover:bg-blue-50 text-gray-700 hover:text-blue-700 font-bold rounded-xl border border-gray-200 hover:border-blue-300 transition-colors shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Proveedor <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-2">
                                <select id="ajax_provider_id_gen" required class="flex-1 w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="" disabled selected>-- Selecciona --</option>
                                    @foreach($providers as $prov)
                                        <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="openProviderModal()" class="px-3 py-2 bg-gray-100 hover:bg-blue-50 text-gray-700 hover:text-blue-700 font-bold rounded-xl border border-gray-200 hover:border-blue-300 transition-colors shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeGeneralProductModal()" class="px-4 py-2 bg-white text-gray-700 font-bold rounded-xl border border-gray-300 hover:bg-gray-50 transition-colors">Cancelar</button>
                        <button type="button" id="btnSaveGeneralProduct" class="px-4 py-2 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL CATEGORIA -->
    <div id="categoryModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" onclick="closeCategoryModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border-t-4 border-t-green-600">
                <div class="flex justify-between items-center mb-5 pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-900">Nueva Categoría</h3>
                    <button type="button" onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-500"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <div id="categoryModalAlert" class="hidden mb-4 p-3 rounded-lg text-sm font-medium"></div>
                <form id="ajaxCategoryForm">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" id="ajax_cat_name" required class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200">Cancelar</button>
                        <button type="submit" id="btnSaveCategory" class="px-6 py-2 bg-green-700 text-white font-bold rounded-xl hover:bg-green-800">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL PROVEEDOR -->
    <div id="providerModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" onclick="closeProviderModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border-t-4 border-t-green-600">
                <div class="flex justify-between items-center mb-5 pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-900">Nuevo Proveedor</h3>
                    <button type="button" onclick="closeProviderModal()" class="text-gray-400 hover:text-gray-500"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <div id="providerModalAlert" class="hidden mb-4 p-3 rounded-lg text-sm font-medium"></div>
                <form id="ajaxProviderForm">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre de la Empresa <span class="text-red-500">*</span></label>
                        <input type="text" id="ajax_prov_name" required class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Teléfono</label>
                        <input type="text" id="ajax_prov_phone" class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Dirección</label>
                        <input type="text" id="ajax_prov_address" class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeProviderModal()" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200">Cancelar</button>
                        <button type="submit" id="btnSaveProvider" class="px-6 py-2 bg-green-700 text-white font-bold rounded-xl hover:bg-green-800">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPT AJAX PARA LOS MODALES -->
    <script>
        // Funciones para Producto Médico
        const productModal = document.getElementById('productModal');
        const modalAlert = document.getElementById('modalAlert');
        const btnProduct = document.getElementById('btnSaveProduct');

        function openProductModal() {
            document.getElementById('ajaxProductForm').reset();
            document.getElementById('modalAlert').classList.add('hidden');
            productModal.classList.remove('hidden');
        }

        function closeProductModal() {
            productModal.classList.add('hidden');
        }

        btnProduct.addEventListener('click', async function() {
            btnProduct.disabled = true;
            btnProduct.innerHTML = 'Guardando...';
            modalAlert.classList.add('hidden');

            const payload = {
                sku: document.getElementById('ajax_sku').value,
                name: document.getElementById('ajax_name').value,
                type: document.getElementById('ajax_type').value,
                category_id: document.getElementById('ajax_category_id').value,
                provider_id: document.getElementById('ajax_provider_id').value,
                active_ingredient: document.getElementById('ajax_active_ingredient').value,
                presentation: document.getElementById('ajax_presentation').value,
                prescription_required: document.getElementById('ajax_prescription_required').checked ? 1 : 0,
                is_controlled: document.getElementById('ajax_is_controlled').checked ? 1 : 0,
            };

            try {
                const response = await fetch("{{ route('products.storeApi') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();

                if (response.ok) {
                    window.dispatchEvent(new CustomEvent('product-created', { 
                        detail: { 
                            id: data.product.id, 
                            name: data.product.name, 
                            sku: data.product.sku, 
                            type: data.product.type 
                        } 
                    }));
                    closeProductModal();
                } else {
                    modalAlert.classList.remove('hidden');
                    modalAlert.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'border');
                    let errors = data.message || 'Error al guardar.';
                    if(data.errors) {
                        errors = Object.values(data.errors).map(e => e.join(', ')).join('<br>');
                    }
                    modalAlert.innerHTML = errors;
                }
            } catch (error) {
                modalAlert.classList.remove('hidden');
                modalAlert.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'border');
                modalAlert.innerHTML = 'Error de conexión con el servidor.';
            } finally {
                btnProduct.disabled = false;
                btnProduct.innerHTML = 'Guardar';
            }
        });

        // Funciones para Producto General
        const generalProductModal = document.getElementById('generalProductModal');
        const modalAlertGeneral = document.getElementById('modalAlertGeneral');
        const btnSaveGeneralProduct = document.getElementById('btnSaveGeneralProduct');

        function openGeneralProductModal() {
            document.getElementById('ajaxGeneralProductForm').reset();
            document.getElementById('modalAlertGeneral').classList.add('hidden');
            generalProductModal.classList.remove('hidden');
        }

        function closeGeneralProductModal() {
            generalProductModal.classList.add('hidden');
        }

        btnSaveGeneralProduct.addEventListener('click', async function() {
            btnSaveGeneralProduct.disabled = true;
            btnSaveGeneralProduct.innerHTML = 'Guardando...';
            modalAlertGeneral.classList.add('hidden');

            const payload = {
                sku: document.getElementById('ajax_sku_gen').value,
                name: document.getElementById('ajax_name_gen').value,
                type: document.getElementById('ajax_type_gen').value,
                category_id: document.getElementById('ajax_category_id_gen').value,
                provider_id: document.getElementById('ajax_provider_id_gen').value,
                active_ingredient: document.getElementById('ajax_active_ingredient_gen').value,
                presentation: document.getElementById('ajax_presentation_gen').value,
                prescription_required: 0,
                is_controlled: 0,
            };

            try {
                const response = await fetch("{{ route('products.storeApi') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();

                if (response.ok) {
                    window.dispatchEvent(new CustomEvent('product-created', { 
                        detail: { 
                            id: data.product.id, 
                            name: data.product.name, 
                            sku: data.product.sku, 
                            type: data.product.type 
                        } 
                    }));
                    closeGeneralProductModal();
                } else {
                    modalAlertGeneral.classList.remove('hidden');
                    modalAlertGeneral.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'border');
                    let errors = data.message || 'Error al guardar.';
                    if(data.errors) {
                        errors = Object.values(data.errors).map(e => e.join(', ')).join('<br>');
                    }
                    modalAlertGeneral.innerHTML = errors;
                }
            } catch (error) {
                modalAlertGeneral.classList.remove('hidden');
                modalAlertGeneral.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'border');
                modalAlertGeneral.innerHTML = 'Error de conexión con el servidor.';
            } finally {
                btnSaveGeneralProduct.disabled = false;
                btnSaveGeneralProduct.innerHTML = 'Guardar Producto';
            }
        });

        // Funciones para Categoría
        const catModal = document.getElementById('categoryModal');
        const catForm = document.getElementById('ajaxCategoryForm');
        const catAlert = document.getElementById('categoryModalAlert');
        const btnCat = document.getElementById('btnSaveCategory');

        function openCategoryModal() {
            catModal.classList.remove('hidden');
            catForm.reset();
            catAlert.classList.add('hidden');
        }

        function closeCategoryModal() {
            catModal.classList.add('hidden');
        }

        catForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            btnCat.disabled = true;
            btnCat.innerHTML = 'Guardando...';
            catAlert.classList.add('hidden');

            try {
                const response = await fetch("{{ route('categories.storeApi') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ name: document.getElementById('ajax_cat_name').value })
                });
                const data = await response.json();

                if (response.ok) {
                    const select1 = document.getElementById('ajax_category_id');
                    if (select1) select1.append(new Option(data.category.name, data.category.id, true, true));
                    
                    const select2 = document.getElementById('ajax_category_id_gen');
                    if (select2) select2.append(new Option(data.category.name, data.category.id, true, true));
                    
                    closeCategoryModal();
                } else {
                    catAlert.classList.remove('hidden');
                    catAlert.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'border');
                    catAlert.innerHTML = data.message || 'Error al guardar.';
                }
            } catch (error) {
                catAlert.classList.remove('hidden');
                catAlert.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'border');
                catAlert.innerHTML = 'Error de conexión.';
            } finally {
                btnCat.disabled = false;
                btnCat.innerHTML = 'Guardar';
            }
        });

        // Funciones para Proveedor
        const provModal = document.getElementById('providerModal');
        const provForm = document.getElementById('ajaxProviderForm');
        const provAlert = document.getElementById('providerModalAlert');
        const btnProv = document.getElementById('btnSaveProvider');

        function openProviderModal() {
            provModal.classList.remove('hidden');
            provForm.reset();
            provAlert.classList.add('hidden');
        }

        function closeProviderModal() {
            provModal.classList.add('hidden');
        }

        provForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            btnProv.disabled = true;
            btnProv.innerHTML = 'Guardando...';
            provAlert.classList.add('hidden');

            try {
                const response = await fetch("{{ route('providers.storeApi') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        name: document.getElementById('ajax_prov_name').value,
                        phone: document.getElementById('ajax_prov_phone').value,
                        address: document.getElementById('ajax_prov_address').value
                    })
                });
                const data = await response.json();

                if (response.ok) {
                    const select1 = document.getElementById('ajax_provider_id');
                    if (select1) select1.append(new Option(data.provider.name, data.provider.id, true, true));
                    
                    const select2 = document.getElementById('ajax_provider_id_gen');
                    if (select2) select2.append(new Option(data.provider.name, data.provider.id, true, true));
                    
                    closeProviderModal();
                } else {
                    provAlert.classList.remove('hidden');
                    provAlert.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'border');
                    provAlert.innerHTML = data.message || 'Error al guardar.';
                }
            } catch (error) {
                provAlert.classList.remove('hidden');
                provAlert.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'border');
                provAlert.innerHTML = 'Error de conexión.';
            } finally {
                btnProv.disabled = false;
                btnProv.innerHTML = 'Guardar';
            }
        });

        // Alpine Components
        document.addEventListener('alpine:init', () => {
            Alpine.data('productSearch', () => ({
                searchQuery: '',
                selectedProductId: '',
                selectedProductName: '',
                showDropdown: false,
                allProducts: @json($products),
                filteredProducts: [],
                
                init() {
                    // Si viene un valor anterior (ej. error de validación)
                    let oldId = '{{ old('product_id') }}';
                    if(oldId) {
                        let prod = this.allProducts.find(p => p.id == oldId);
                        if(prod) this.selectProduct(prod);
                    }

                    // Escuchar cuando se crea un nuevo producto
                    window.addEventListener('product-created', (e) => {
                        let newProd = e.detail;
                        this.allProducts.push(newProd);
                        this.selectProduct(newProd);
                    });
                },

                filterProducts() {
                    if (this.searchQuery === '') {
                        this.filteredProducts = [];
                        return;
                    }
                    
                    const q = this.searchQuery.toLowerCase().trim();
                    
                    this.filteredProducts = this.allProducts.filter(p => 
                        p.name.toLowerCase().includes(q) || 
                        (p.sku && p.sku.toLowerCase().includes(q))
                    ).slice(0, 10); // Límite de 10
                },

                handleEnter() {
                    if (this.searchQuery === '') return;
                    
                    const q = this.searchQuery.toLowerCase().trim();
                    const exactMatch = this.allProducts.find(p => p.sku && p.sku.toLowerCase() === q);
                    
                    if (exactMatch) {
                        this.selectProduct(exactMatch);
                    } else if (this.filteredProducts.length === 1) {
                        this.selectProduct(this.filteredProducts[0]);
                    }
                },

                selectProduct(product) {
                    this.selectedProductId = product.id;
                    this.selectedProductName = `[${product.sku}] ${product.name} - ${product.type}`;
                    this.searchQuery = '';
                    this.showDropdown = false;
                },

                clearSelection() {
                    this.selectedProductId = '';
                    this.selectedProductName = '';
                    this.searchQuery = '';
                    this.$refs.searchInput?.focus();
                }
            }));

            Alpine.data('priceCalculator', () => ({
                cost: {{ old('cost_price', 0) }},
                price: {{ old('selling_price', 0) }},
                margin: null,

                init() {
                    if(this.cost > 0 && this.price > 0) {
                        this.calculateFromPrice(); // Para establecer el margen inicial visual
                    }
                },

                calculateFromCost() {
                    if (this.margin !== null && this.margin !== "") {
                        this.price = Number((this.cost * (1 + (this.margin / 100))).toFixed(2));
                    }
                },

                calculateFromMargin() {
                    if (this.cost > 0) {
                        this.price = Number((this.cost * (1 + (this.margin / 100))).toFixed(2));
                    }
                },

                calculateFromPrice() {
                    if (this.cost > 0) {
                        this.margin = Number((((this.price / this.cost) - 1) * 100).toFixed(2));
                    }
                }
            }));
        });
    </script>
    
    <!-- Modal del Lector de Código de Barras -->
    <x-barcode-scanner-modal targetModel="searchQuery" />
    <x-barcode-scanner-modal targetModel="ajaxsku" />
    <x-barcode-scanner-modal targetModel="ajaxskugen" />
</x-app-layout>