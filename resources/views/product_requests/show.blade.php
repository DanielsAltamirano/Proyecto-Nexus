<x-app-layout>
    <div class="min-h-screen bg-gray-50/50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumb & Header -->
            <div class="mb-8">
                <nav class="flex text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('product_requests.index') }}" class="inline-flex items-center hover:text-orange-600 transition-colors">
                                Solicitudes a Compras
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                <span class="text-gray-900 font-semibold">Solicitud #REQ-{{ str_pad($productRequest->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                
                <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                            Detalle de Solicitud
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Revisión de requerimiento para la sucursal <strong class="text-gray-900">{{ $productRequest->branch->name }}</strong>
                        </p>
                    </div>
                    
                    <div>
                        @if($productRequest->status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-800 shadow-sm border border-yellow-200">
                                <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>
                                Pendiente de revisión
                            </span>
                        @elseif($productRequest->status === 'ordered_to_provider')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-800 shadow-sm border border-blue-200">
                                <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                                Orden de Compra #{{ $productRequest->purchaseOrder->id ?? '' }}
                            </span>
                        @elseif($productRequest->status === 'fulfilled')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800 shadow-sm border border-green-200">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                                Surtido
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Columna Izquierda: Detalles del pedido -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Tarjeta Principal -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-900">Información del Requerimiento</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 font-medium mb-1">Producto Solicitado</p>
                                <p class="text-xl font-bold text-gray-900">{{ $productRequest->product->name }}</p>
                                <p class="text-sm text-gray-400 mt-1">SKU: {{ $productRequest->product->sku ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium mb-1">Cantidad Requerida</p>
                                <p class="text-2xl font-black text-orange-600">{{ $productRequest->quantity }} <span class="text-base font-medium text-gray-500">unidades</span></p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500 font-medium mb-1">Justificación / Notas</p>
                                <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700 italic border border-gray-100">
                                    "{{ $productRequest->notes ?? 'Sin notas adicionales.' }}"
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trazabilidad (SAP Style) -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Historial de Trazabilidad</h3>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Log de Auditoría</span>
                        </div>
                        <div class="p-6">
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    @foreach($productRequest->histories as $idx => $history)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white {{ $idx === 0 ? 'bg-gray-400' : 'bg-orange-500' }}">
                                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                        <div>
                                                            <p class="text-sm text-gray-500">
                                                                <span class="font-bold text-gray-900">{{ $history->action }}</span>
                                                                por <span class="font-medium text-gray-900">{{ $history->user?->name ?? 'N/A' }}</span>
                                                            </p>
                                                            @if($history->details)
                                                                <p class="mt-1 text-sm text-gray-600">{{ $history->details }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="whitespace-nowrap text-right text-xs text-gray-500 font-mono">
                                                            {{ $history->created_at->format('d M H:i') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Acciones y Alertas -->
                <div class="space-y-6">
                    
                    @if($productRequest->status === 'pending')
                        <!-- Alerta Bodega -->
                        @if($bodega)
                            <div class="bg-white rounded-3xl shadow-sm border {{ $bodegaStock >= $productRequest->quantity ? 'border-green-200' : 'border-gray-100' }} overflow-hidden">
                                <div class="px-6 py-5 {{ $bodegaStock >= $productRequest->quantity ? 'bg-green-50/50' : 'bg-gray-50/50' }} border-b {{ $bodegaStock >= $productRequest->quantity ? 'border-green-100' : 'border-gray-50' }}">
                                    <h3 class="text-lg font-bold {{ $bodegaStock >= $productRequest->quantity ? 'text-green-900' : 'text-gray-900' }}">Análisis de Almacén</h3>
                                </div>
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="text-sm text-gray-500">Stock en Bodega Central:</div>
                                        <div class="text-2xl font-black {{ $bodegaStock >= $productRequest->quantity ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $bodegaStock }}
                                        </div>
                                    </div>

                                    @if($bodegaStock >= $productRequest->quantity)
                                        <div class="bg-green-50 text-green-800 text-sm p-4 rounded-xl mb-6 border border-green-100">
                                            <strong>¡Hay stock suficiente!</strong> Puedes surtir esta solicitud directamente desde la bodega sin necesidad de realizar una compra.
                                        </div>
                                        
                                        <form action="{{ route('product_requests.approveFromBodega', $productRequest) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full flex justify-center items-center px-4 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition-colors shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Surtir desde Bodega
                                            </button>
                                        </form>
                                    @else
                                        <div class="bg-red-50 text-red-800 text-sm p-4 rounded-xl mb-6 border border-red-100">
                                            <strong>Stock insuficiente en bodega.</strong> Es necesario generar una orden de compra a proveedor para surtir a la sucursal.
                                        </div>
                                        
                                        <a href="{{ route('product_requests.createPurchaseOrder', $productRequest) }}" class="w-full flex justify-center items-center px-4 py-3 bg-orange-600 text-white font-bold rounded-xl hover:bg-orange-700 transition-colors shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            Generar Orden de Compra
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-50 text-yellow-800 p-4 rounded-xl border border-yellow-200">
                                No se ha configurado la Sucursal "Bodega". Asigna el atributo is_warehouse a una sucursal para usarla como Almacén Central.
                            </div>
                        @endif
                    @else
                        <!-- Ya procesado -->
                        <div class="bg-gray-50 border border-gray-200 rounded-3xl p-6 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Solicitud Procesada</h3>
                            <p class="text-sm text-gray-500">Esta solicitud ya no requiere acciones adicionales.</p>
                            
                            @if($productRequest->transfer_id)
                                <div class="mt-4 inline-flex items-center text-sm font-semibold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg">
                                    Ver Traspaso #{{ $productRequest->transfer_id }}
                                </div>
                            @elseif($productRequest->purchase_order_id)
                                <a href="{{ route('purchase-orders.show', $productRequest->purchase_order_id) }}" class="mt-4 inline-flex items-center text-sm font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                                    Ver Orden de Compra #{{ $productRequest->purchase_order_id }}
                                </a>
                            @endif
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
