<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-blue-50 rounded-full opacity-70 blur-2xl"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-blue-900 to-blue-600 tracking-tight">
                        Edición de Lote
                    </h2>
                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Actualizando información del lote: <span class="font-mono font-bold ml-1 text-gray-700">{{ $batch->batch_number }}</span>
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
            
            <!-- Bloque de Errores de Validación -->
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100" x-data="{ requestModal: false }">
                <div class="p-8">
                    <form action="{{ route('batches.update', $batch) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Sección 1: Producto -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">1. Medicamento / Producto</h3>
                            <div class="w-full">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Producto Seleccionado</label>
                                <div class="mt-1 p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-100 text-blue-700 p-2 rounded-lg">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 font-bold uppercase block mb-0.5">SKU: {{ $batch->product->sku }} | Tipo: {{ $batch->product->type }}</span>
                                            <span class="font-bold text-gray-800 text-lg">{{ $batch->product->name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Enviamos el product_id de manera oculta ya que no se puede cambiar -->
                                <input type="hidden" name="product_id" value="{{ $batch->product_id }}">
                            </div>
                        </div>

                        <!-- Sección 2: Detalles del Lote -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">2. Detalles de Ingreso</h3>

                            @if(auth()->user()->hasRole('superadmin'))
                                <div class="mb-6">
                                    <label for="branch_id" class="block text-sm font-bold text-gray-700 mb-2">Sucursal <span class="text-red-500">*</span></label>
                                    <select name="branch_id" id="branch_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors">
                                        <option value="" disabled>-- Selecciona la Sucursal --</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id', $batch->branch_id) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Como Superadmin, puedes reubicar la mercancía a otra sucursal.</p>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="batch_number" class="block text-sm font-bold text-gray-700 mb-2">Número de Lote (Opcional)</label>
                                    <input type="text" name="batch_number" id="batch_number" value="{{ old('batch_number', $batch->batch_number) }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors uppercase {{ auth()->user()->hasRole(['Personal de Mostrador', 'compras']) ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ auth()->user()->hasRole(['Personal de Mostrador', 'compras']) ? 'readonly' : '' }}>
                                </div>
                                <div>
                                    <label for="expiration_date" class="block text-sm font-bold text-gray-700 mb-2">Fecha de Caducidad (Opcional)</label>
                                    <input type="date" name="expiration_date" id="expiration_date" value="{{ old('expiration_date', $batch->expiration_date ? \Carbon\Carbon::parse($batch->expiration_date)->format('Y-m-d') : '') }}" min="2000-01-01" max="2050-12-31" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors {{ auth()->user()->hasRole(['Personal de Mostrador', 'compras']) ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ auth()->user()->hasRole(['Personal de Mostrador', 'compras']) ? 'readonly' : '' }}>
                                </div>
                                <div>
                                    <label for="stock" class="block text-sm font-bold text-gray-700 mb-2">Unidades en Stock <span class="text-red-500">*</span></label>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="let v=document.getElementById('stock'); v.value=Math.max(0,parseInt(v.value||0)-1);" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-100 text-blue-700 hover:bg-blue-200 text-xl font-black border border-blue-200 transition-colors select-none {{ auth()->user()->hasRole(['Personal de Mostrador', 'compras']) ? 'hidden' : '' }}" tabindex="-1">−</button>
                                        <input type="number" name="stock" id="stock" value="{{ old('stock', $batch->stock) }}" min="0" required class="w-24 text-center rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors font-bold {{ auth()->user()->hasRole(['Personal de Mostrador', 'compras']) ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ auth()->user()->hasRole(['Personal de Mostrador', 'compras']) ? 'readonly' : '' }} @wheel.prevent>
                                        <button type="button" onclick="let v=document.getElementById('stock'); v.value=parseInt(v.value||0)+1;" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-100 text-blue-700 hover:bg-blue-200 text-xl font-black border border-blue-200 transition-colors select-none {{ auth()->user()->hasRole(['Personal de Mostrador', 'compras']) ? 'hidden' : '' }}" tabindex="-1">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 3: Precios -->
                        <div class="mb-8" x-data="priceCalculator()">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">3. Configuración de Precios</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @if(!auth()->user()->hasRole(['Personal de Mostrador', 'compras']))
                                    <div>
                                        <label for="cost_price" class="block text-sm font-bold text-gray-700 mb-2">Costo de Compra ($) <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <input type="number" name="cost_price" id="cost_price" x-model.number="cost" @input="calculateFromCost()" step="any" min="0" required class="pl-8 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Margen de Ganancia (%)</label>
                                        <div class="relative">
                                            <input type="number" x-model.number="margin" @input="calculateFromMargin()" step="any" placeholder="Ej. 30" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors pr-8">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">%</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div>
                                    <label for="selling_price" class="block text-sm font-bold text-gray-700 mb-2">Precio de Venta ($) <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" name="selling_price" id="selling_price" x-model.number="price" @input="calculateFromPrice()" step="any" min="0" required class="pl-8 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors {{ auth()->user()->hasRole(['Personal de Mostrador', 'compras']) ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ auth()->user()->hasRole(['Personal de Mostrador', 'compras']) ? 'readonly' : '' }}>
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
                                    <input type="text" name="rack" id="rack" value="{{ old('rack', $batch->rack) }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors">
                                </div>
                                <div>
                                    <label for="shelf" class="block text-sm font-bold text-gray-700 mb-2">Nivel / Estante</label>
                                    <input type="text" name="shelf" id="shelf" value="{{ old('shelf', $batch->shelf) }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-colors">
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-end pt-4 border-t border-gray-100 mt-6">
                            <a href="{{ route('batches.index') }}" class="px-6 py-3 mr-4 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                                Cancelar
                            </a>
                            
                            @if(auth()->user()->hasRole(['Personal de Mostrador', 'compras']))
                                <button type="button" @click="requestModal = true" class="px-6 py-3 mr-4 text-sm font-bold text-blue-700 bg-blue-100 rounded-xl hover:bg-blue-200 transition-colors shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                                    Solicitar Cambio
                                </button>
                                <button type="submit" class="px-8 py-3 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    Guardar Cambios (Ubicación)
                                </button>
                            @else
                                <button type="submit" class="px-8 py-3 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    Guardar Cambios
                                </button>
                            @endif
                        </div>
                    </form>
                    
                    @if(auth()->user()->hasRole(['Personal de Mostrador', 'compras']))
                        <!-- Modal de Petición (Movido fuera del formulario principal) -->
                        <div x-show="requestModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="requestModal" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                                    <div class="absolute inset-0 bg-gray-900 opacity-75 backdrop-blur-sm"></div>
                                </div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div x-show="requestModal" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                                    <form action="{{ route('batches.request_edit', $batch) }}" method="POST">
                                        @csrf
                                        <div class="bg-white px-8 pt-8 pb-6">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-blue-50 sm:mx-0 sm:h-12 sm:w-12">
                                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-6 sm:text-left w-full">
                                                    <h3 class="text-xl leading-6 font-extrabold text-gray-900" id="modal-title">
                                                        Solicitar Autorización
                                                    </h3>
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-500 font-medium">Ingresa los valores nuevos que necesitas autorizar. Puedes dejar en blanco lo que no quieras cambiar.</p>
                                                    </div>
                                                    <div class="mt-6 space-y-4">
                                                        <div>
                                                            <label class="block text-sm font-bold text-gray-700 mb-1">Nuevo Stock</label>
                                                            <div class="flex items-center gap-2">
                                                                <button type="button" onclick="let v=document.getElementById('req_stock'); v.value=Math.max(0,parseInt(v.value||0)-1);" class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-100 text-blue-700 hover:bg-blue-200 text-lg font-black border border-blue-200 transition-colors select-none" tabindex="-1">−</button>
                                                                <input type="number" id="req_stock" name="requested_stock" value="{{ $batch->stock }}" min="0" class="w-20 text-center rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 font-bold" @wheel.prevent>
                                                                <button type="button" onclick="let v=document.getElementById('req_stock'); v.value=parseInt(v.value||0)+1;" class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-100 text-blue-700 hover:bg-blue-200 text-lg font-black border border-blue-200 transition-colors select-none" tabindex="-1">+</button>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-bold text-gray-700 mb-1">Nuevo Precio de Venta ($)</label>
                                                            <input type="number" step="any" name="requested_selling_price" value="{{ $batch->selling_price }}" min="0" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-bold text-gray-700 mb-1">Nueva Fecha de Caducidad</label>
                                                            <input type="date" name="requested_expiration_date" value="{{ $batch->expiration_date ? \Carbon\Carbon::parse($batch->expiration_date)->format('Y-m-d') : '' }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-bold text-gray-700 mb-1">Motivo / Justificación <span class="text-red-500">*</span></label>
                                                            <textarea name="reason" required rows="3" placeholder="Ej. Cambio de presentación, ajuste de inventario..." class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-8 py-5 sm:flex sm:flex-row-reverse border-t border-gray-100">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                                Enviar Petición
                                            </button>
                                            <button type="button" @click="requestModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                                Cancelar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- SCRIPT AJAX Y ALPINE -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('priceCalculator', () => ({
                cost: {{ old('cost_price', $batch->cost_price) }},
                price: {{ old('selling_price', $batch->selling_price) }},
                margin: null,

                init() {
                    if(this.cost > 0 && this.price > 0) {
                        this.calculateFromPrice();
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
</x-app-layout>