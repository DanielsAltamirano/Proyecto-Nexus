<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-indigo-50 rounded-full opacity-70 blur-2xl"></div>
            
            <div class="relative z-10 flex items-center">
                <a href="{{ route('purchase-orders.index') }}" class="mr-4 p-2.5 bg-white border border-gray-200 rounded-full text-gray-500 hover:text-indigo-700 hover:bg-indigo-50 hover:border-indigo-200 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-2xl sm:text-3xl text-transparent bg-clip-text bg-gradient-to-r from-indigo-900 to-indigo-600 tracking-tight">
                        Orden de Compra #{{ str_pad($purchaseOrder->id, 6, '0', STR_PAD_LEFT) }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Generada el {{ $purchaseOrder->created_at->format('d/m/Y') }} por {{ $purchaseOrder->user?->name ?? 'N/A' }}</p>
                </div>
            </div>
            
            <div class="relative z-10 flex space-x-2">
                <a href="{{ route('purchase-orders.pdf', $purchaseOrder) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 hover:bg-gray-50 hover:text-indigo-600 focus:outline-none transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    PDF
                </a>
                <a href="{{ route('purchase-orders.xml', $purchaseOrder) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 hover:bg-gray-50 hover:text-green-600 focus:outline-none transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    XML
                </a>
                
                @if($purchaseOrder->status === 'pending')
                    <a href="{{ route('purchase-orders.receiveForm', $purchaseOrder) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors shadow-sm ml-2">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Recibir Mercancía
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="flex items-center p-4 text-green-800 bg-green-50 border border-green-200 rounded-xl shadow-sm" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Información del Proveedor -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm col-span-1 md:col-span-2 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-indigo-500"></div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Datos del Proveedor</h3>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-indigo-50 p-3 rounded-xl">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-lg font-bold text-gray-900">{{ $purchaseOrder->provider->name }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $purchaseOrder->provider->address ?? 'Sin dirección' }}</p>
                            <p class="text-sm text-gray-500">{{ $purchaseOrder->provider->phone ?? 'Sin teléfono' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Estado -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-center items-center text-center">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Estado Actual</h3>
                    @if($purchaseOrder->status === 'pending')
                        <span class="px-4 py-2 rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200 font-bold flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Pendiente de Recepción
                        </span>
                    @elseif($purchaseOrder->status === 'received')
                        <span class="px-4 py-2 rounded-full bg-green-100 text-green-800 border border-green-200 font-bold flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Completada / Recibida
                        </span>
                    @endif
                    <p class="text-xs text-gray-500 mt-3">Monto Total: <strong class="text-indigo-600 text-lg">${{ number_format($purchaseOrder->total_amount, 2) }}</strong></p>
                </div>
            </div>

            @if($purchaseOrder->status === 'received' && ($purchaseOrder->provider_invoice_pdf || $purchaseOrder->provider_invoice_xml))
            <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100 shadow-sm flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-blue-900 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Archivos de Factura del Proveedor
                    </h3>
                    <p class="text-xs text-blue-700 mt-1">Archivos adjuntados al momento de recibir la mercancía.</p>
                </div>
                <div class="flex gap-2">
                    @if($purchaseOrder->provider_invoice_pdf)
                        <a href="{{ Storage::url($purchaseOrder->provider_invoice_pdf) }}" target="_blank" class="px-4 py-2 bg-white text-blue-700 border border-blue-200 rounded-lg text-sm font-bold hover:bg-blue-100 transition shadow-sm">Ver Factura PDF</a>
                    @endif
                    @if($purchaseOrder->provider_invoice_xml)
                        <a href="{{ Storage::url($purchaseOrder->provider_invoice_xml) }}" target="_blank" class="px-4 py-2 bg-white text-blue-700 border border-blue-200 rounded-lg text-sm font-bold hover:bg-blue-100 transition shadow-sm">Ver Factura XML</a>
                    @endif
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Artículos Solicitados
                    </h3>
                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">SKU</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Cantidad</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($purchaseOrder->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">{{ $item->product->sku }}</td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $item->product->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 border-t border-gray-200">
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-right text-sm font-extrabold text-gray-700 uppercase">Total:</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-lg font-extrabold text-indigo-700">${{ number_format($purchaseOrder->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($purchaseOrder->notes)
                        <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <h4 class="text-sm font-bold text-gray-700 mb-2">Notas Adicionales</h4>
                            <p class="text-sm text-gray-600">{{ $purchaseOrder->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
