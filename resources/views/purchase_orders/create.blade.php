<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-blue-50 rounded-full opacity-70 blur-2xl"></div>
            
            <div class="relative z-10 flex items-center">
                <a href="{{ route('purchase-orders.index') }}" class="mr-4 p-2.5 bg-white border border-gray-200 rounded-full text-gray-500 hover:text-blue-700 hover:bg-blue-50 hover:border-blue-200 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-2xl sm:text-3xl text-transparent bg-clip-text bg-gradient-to-r from-blue-900 to-blue-600 tracking-tight">
                        Generar Orden de Compra
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Selecciona el proveedor y los productos a solicitar.</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="purchaseOrderForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl mb-6 shadow-sm">
                    <strong class="font-bold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Ocurrieron errores al procesar la solicitud:
                    </strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('purchase-orders.store') }}" method="POST" id="purchaseOrderForm">
                @csrf
                
                @if(isset($productRequest))
                    <input type="hidden" name="product_request_id" value="{{ $productRequest->id }}">
                    <div class="bg-orange-50 border border-orange-200 text-orange-800 rounded-xl mb-6 p-4 shadow-sm flex items-start">
                        <svg class="w-6 h-6 mr-3 mt-0.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <strong class="font-bold block">Generando orden para solicitud #REQ-{{ str_pad($productRequest->id, 5, '0', STR_PAD_LEFT) }}</strong>
                            <p class="text-sm mt-1">El producto solicitado ha sido agregado automáticamente a la orden. Al finalizar, la solicitud cambiará su estado a "Surtido".</p>
                        </div>
                    </div>
                @endif
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-8 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center border-b pb-3">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Datos Generales
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Proveedor <span class="text-red-500">*</span></label>
                            <select name="provider_id" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm cursor-pointer">
                                <option value="" disabled selected>Selecciona un proveedor...</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Notas / Comentarios (Opcional)</label>
                            <textarea name="notes" rows="2" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm resize-none" placeholder="Condiciones de pago, entrega, etc."></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center justify-between border-b pb-3">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            Artículos a Comprar
                        </span>
                        
                        <div class="flex items-center">
                            <select x-model="selectedProductId" class="rounded-l-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" style="min-width: 250px;">
                                <option value="" disabled selected>Buscar producto para agregar...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-sku="{{ $product->sku }}">
                                        {{ $product->sku }} - {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" @click="addProduct" class="px-4 py-2 bg-gray-800 text-white font-bold rounded-r-xl hover:bg-gray-700 transition border border-gray-800">
                                Agregar
                            </button>
                        </div>
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-y border-gray-200">
                                    <th class="p-4 text-sm font-bold text-gray-600">SKU</th>
                                    <th class="p-4 text-sm font-bold text-gray-600">Producto</th>
                                    <th class="p-4 text-sm font-bold text-gray-600 w-32">Costo Unit. ($)</th>
                                    <th class="p-4 text-sm font-bold text-gray-600 w-32">Cantidad</th>
                                    <th class="p-4 text-sm font-bold text-gray-600 text-right">Subtotal ($)</th>
                                    <th class="p-4 text-sm font-bold text-gray-600 text-center w-16">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="p-4 font-mono text-sm text-gray-500" x-text="item.sku"></td>
                                        <td class="p-4 font-bold text-gray-800" x-text="item.name"></td>
                                        <td class="p-4">
                                            <input type="number" :name="'products['+index+'][unit_price]'" x-model.number="item.unit_price" step="0.01" min="0" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 px-2 py-1">
                                        </td>
                                        <td class="p-4">
                                            <input type="hidden" :name="'products['+index+'][id]'" :value="item.id">
                                            <div class="flex items-center gap-1">
                                                <button type="button" @click="item.quantity = Math.max(1, parseInt(item.quantity || 1) - 1)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 text-lg font-black border border-blue-200 transition-colors select-none" tabindex="-1">−</button>
                                                <input type="number" :name="'products['+index+'][quantity]'" x-model.number="item.quantity" min="1" required class="w-16 rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 px-1 py-1 text-center font-bold" @wheel.prevent>
                                                <button type="button" @click="item.quantity = parseInt(item.quantity || 1) + 1" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 text-lg font-black border border-blue-200 transition-colors select-none" tabindex="-1">+</button>
                                            </div>
                                        </td>
                                        <td class="p-4 text-right font-bold text-gray-900" x-text="formatCurrency(item.quantity * item.unit_price)"></td>
                                        <td class="p-4 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="items.length === 0">
                                    <td colspan="6" class="p-8 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Aún no hay productos en la orden. Utiliza el buscador de arriba.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot x-show="items.length > 0">
                                <tr class="bg-blue-50">
                                    <td colspan="4" class="p-4 text-right font-extrabold text-blue-900 uppercase">Total Estimado:</td>
                                    <td class="p-4 text-right font-extrabold text-xl text-blue-700" x-text="formatCurrency(calculateTotal())"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-8 flex justify-end gap-4 border-t pt-6">
                        <a href="{{ route('purchase-orders.index') }}" class="px-6 py-3 bg-white border border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition">Cancelar</a>
                        <button type="submit" :disabled="items.length === 0" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Guardar y Generar PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function purchaseOrderForm() {
            return {
                selectedProductId: '',
                items: [
                    @if(isset($productRequest))
                    {
                        id: '{{ $productRequest->product->id }}',
                        sku: '{{ $productRequest->product->sku }}',
                        name: '{!! addslashes($productRequest->product->name) !!}',
                        quantity: {{ $productRequest->quantity }},
                        unit_price: 0
                    }
                    @endif
                ],
                
                addProduct() {
                    if (!this.selectedProductId) return;
                    
                    // Comprobar si ya existe
                    const exists = this.items.find(i => i.id == this.selectedProductId);
                    if (exists) {
                        exists.quantity++;
                        this.selectedProductId = '';
                        return;
                    }

                    // Obtener nombre y sku
                    const select = document.querySelector(`select[x-model="selectedProductId"]`);
                    const option = select.options[select.selectedIndex];
                    
                    this.items.push({
                        id: this.selectedProductId,
                        sku: option.dataset.sku,
                        name: option.dataset.name,
                        quantity: 1,
                        unit_price: 0
                    });
                    
                    this.selectedProductId = '';
                },
                
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                
                calculateTotal() {
                    return this.items.reduce((total, item) => total + (item.quantity * item.unit_price), 0);
                },
                
                formatCurrency(value) {
                    return '$' + parseFloat(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                }
            }
        }
    </script>
</x-app-layout>
