<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl"></div>
            
            <div class="relative z-10 flex items-center">
                <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="mr-4 p-2.5 bg-white border border-gray-200 rounded-full text-gray-500 hover:text-green-700 hover:bg-green-50 hover:border-green-200 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-2xl sm:text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Recibir Orden de Compra #{{ str_pad($purchaseOrder->id, 6, '0', STR_PAD_LEFT) }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Ingresa la mercancía a tu inventario y adjunta las facturas del proveedor.</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            <form action="{{ route('purchase-orders.receive', $purchaseOrder) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-8 mb-6 relative">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-500 to-green-400"></div>
                    
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Datos de Ingreso
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Sucursal o Bodega de destino -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Sucursal / Bodega Destino <span class="text-red-500">*</span></label>
                            <p class="text-xs text-gray-500 mb-2">Por defecto, la mercancía debe ingresar a la Bodega Central antes de ser traspasada.</p>
                            <select name="branch_id" required class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm cursor-pointer p-3 bg-gray-50">
                                <option value="" disabled>Selecciona una sucursal o bodega...</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $branch->is_warehouse ? 'selected' : '' }}>
                                        {{ $branch->name }} {{ $branch->is_warehouse ? '⭐ (Recomendado)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <hr class="border-gray-100">

                        <!-- Archivos de la Factura -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 mb-4">Factura del Proveedor (Opcional pero recomendado)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition text-center">
                                    <label class="cursor-pointer block">
                                        <svg class="w-8 h-8 mx-auto text-red-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <span class="block text-sm font-bold text-gray-700">Subir Factura PDF</span>
                                        <input type="file" name="invoice_pdf" accept="application/pdf" class="mt-3 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                    </label>
                                </div>
                                <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition text-center">
                                    <label class="cursor-pointer block">
                                        <svg class="w-8 h-8 mx-auto text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                        <span class="block text-sm font-bold text-gray-700">Subir Factura XML</span>
                                        <input type="file" name="invoice_xml" accept="text/xml,application/xml" class="mt-3 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200 p-6 mb-6">
                    <h3 class="text-sm font-bold text-gray-800 mb-4">Resumen de Mercancía a Ingresar</h3>
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="p-3 font-bold text-gray-600">Producto</th>
                                    <th class="p-3 font-bold text-gray-600 text-center">Cantidad a Ingresar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($purchaseOrder->items as $item)
                                <tr>
                                    <td class="p-3 font-medium text-gray-800">{{ $item->product->name }}</td>
                                    <td class="p-3 text-center">
                                        <span class="inline-block bg-green-100 text-green-800 font-bold px-3 py-1 rounded-lg">+{{ $item->quantity }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-8 border-t pt-6">
                    <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="px-6 py-3 bg-white border border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition">Cancelar</a>
                    <button type="submit" class="px-8 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition shadow-md focus:ring-2 focus:ring-green-500 focus:ring-offset-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Confirmar Recepción e Ingresar al Inventario
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
