<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-blue-50 rounded-full opacity-70 blur-2xl"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-blue-900 to-blue-600 tracking-tight">
                        Expediente de Autorización
                    </h2>
                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Registro y trazabilidad completa del cambio de inventario
                    </p>
                </div>
                <div>
                    @if(auth()->user()->hasRole(['superadmin', 'admin']))
                        <a href="{{ route('product_requests.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                            &larr; Volver a la Bandeja
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                            &larr; Volver al Panel
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 text-green-800 bg-green-50 border border-green-200 rounded-xl font-bold shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl font-bold shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <!-- Encabezado del Expediente -->
                <div class="border-b border-gray-100 bg-gray-50/50 p-6 sm:px-8 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900">Folio: #AUTH-{{ str_pad($batchEditRequest->id, 5, '0', STR_PAD_LEFT) }}</h3>
                        <p class="text-gray-500 mt-1 font-medium">{{ ucfirst($batchEditRequest->created_at->locale('es')->translatedFormat('l, d \d\e F \d\e Y - h:i A')) }}</p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        @if($batchEditRequest->status === 'pending')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-black bg-yellow-100 text-yellow-800 shadow-sm border border-yellow-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                PENDIENTE DE RESOLUCIÓN
                            </span>
                        @elseif($batchEditRequest->status === 'approved')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-black bg-green-100 text-green-800 shadow-sm border border-green-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                APROBADO Y APLICADO
                            </span>
                        @elseif($batchEditRequest->status === 'rejected')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-black bg-red-100 text-red-800 shadow-sm border border-red-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                RECHAZADO
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <!-- Panel Izquierdo: Contexto -->
                        <div class="space-y-6">
                            <div class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100">
                                <h4 class="text-xs font-black text-blue-800 uppercase tracking-wider mb-4">Información del Lote</h4>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm text-gray-500 font-bold">Producto</p>
                                        <p class="text-lg font-black text-gray-900">{{ $batchEditRequest->batch->product->name }}</p>
                                    </div>
                                    <div class="flex justify-between">
                                        <div>
                                            <p class="text-sm text-gray-500 font-bold">Lote</p>
                                            <p class="text-base font-mono font-bold text-gray-800">{{ $batchEditRequest->batch->batch_number }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 font-bold">Sucursal</p>
                                            <p class="text-base font-bold text-gray-800">{{ $batchEditRequest->batch->branch->name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                <h4 class="text-xs font-black text-gray-500 uppercase tracking-wider mb-4">Datos del Solicitante</h4>
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 font-bold text-xl mr-4">
                                        {{ substr($batchEditRequest->user?->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-base font-bold text-gray-900">{{ $batchEditRequest->user?->name ?? 'Usuario no disponible' }}</p>
                                        <p class="text-sm text-gray-500 font-medium">{{ $batchEditRequest->user?->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <p class="text-sm text-gray-500 font-bold mb-1">Motivo / Justificación:</p>
                                    <p class="text-base text-gray-800 italic bg-white p-3 rounded-xl border border-gray-100 shadow-inner">
                                        "{{ $batchEditRequest->reason }}"
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Derecho: Cambios y Resolución -->
                        <div class="space-y-6">
                            <div class="bg-white p-5 rounded-2xl border-2 border-dashed border-gray-200">
                                <h4 class="text-xs font-black text-gray-500 uppercase tracking-wider mb-4 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                    {{ $batchEditRequest->type === 'delete' ? 'Acción Solicitada' : 'Cambios Solicitados' }}
                                </h4>
                                
                                @if($batchEditRequest->type === 'delete')
                                    <div class="mb-4 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </div>
                                        <h5 class="text-lg font-black text-red-600 uppercase">Eliminación de Existencia</h5>
                                        <p class="text-sm text-gray-500 mt-2">Esta solicitud borrará la existencia del producto del sistema de forma permanente y lo registrará en el historial de eliminaciones.</p>
                                    </div>
                                @else
                                    @if(!is_null($batchEditRequest->requested_stock))
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-500 font-bold mb-1">Stock (Unidades)</p>
                                        <div class="flex items-center text-lg">
                                            <span class="line-through text-gray-400 font-mono">{{ $batchEditRequest->old_stock ?? $batchEditRequest->batch->stock }}</span>
                                            <svg class="w-5 h-5 mx-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            <span class="font-black text-blue-600 font-mono text-2xl bg-blue-50 px-3 py-1 rounded-lg">{{ $batchEditRequest->requested_stock }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if(!is_null($batchEditRequest->requested_selling_price))
                                    <div>
                                        <p class="text-sm text-gray-500 font-bold mb-1">Precio de Venta</p>
                                        <div class="flex items-center text-lg">
                                            <span class="line-through text-gray-400 font-mono">${{ number_format($batchEditRequest->old_selling_price ?? $batchEditRequest->batch->selling_price, 2) }}</span>
                                            <svg class="w-5 h-5 mx-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            <span class="font-black text-green-600 font-mono text-2xl bg-green-50 px-3 py-1 rounded-lg">${{ number_format($batchEditRequest->requested_selling_price, 2) }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if(!is_null($batchEditRequest->requested_expiration_date))
                                    <div>
                                        <p class="text-sm text-gray-500 font-bold mb-1">Fecha de Caducidad</p>
                                        <div class="flex items-center text-lg">
                                            <span class="line-through text-gray-400 font-mono">{{ $batchEditRequest->old_expiration_date ? \Carbon\Carbon::parse($batchEditRequest->old_expiration_date)->format('d/m/Y') : 'N/A' }}</span>
                                            <svg class="w-5 h-5 mx-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            <span class="font-black text-purple-600 font-mono text-2xl bg-purple-50 px-3 py-1 rounded-lg">{{ \Carbon\Carbon::parse($batchEditRequest->requested_expiration_date)->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                @endif
                                @endif
                            </div>

                            @if($batchEditRequest->status !== 'pending')
                                <div class="bg-gray-800 text-white p-5 rounded-2xl shadow-inner">
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-wider mb-4">Resolución</h4>
                                    <div class="flex items-center mb-3">
                                        <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center text-gray-300 font-bold mr-3">
                                            {{ substr($batchEditRequest->resolver?->name ?? 'S', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-white">Por: {{ $batchEditRequest->resolver?->name ?? 'Sistema' }}</p>
                                            <p class="text-xs text-gray-400">{{ $batchEditRequest->updated_at->format('d/m/Y h:i A') }}</p>
                                        </div>
                                    </div>
                                    @if($batchEditRequest->resolution_notes)
                                        <div class="mt-3 bg-gray-900 p-3 rounded-xl border border-gray-700">
                                            <p class="text-xs text-gray-400 font-bold mb-1">Notas del Autorizador:</p>
                                            <p class="text-sm text-gray-200">"{{ $batchEditRequest->resolution_notes }}"</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer Acciones -->
                @if($batchEditRequest->status === 'pending' && (auth()->user()->hasRole(['superadmin', 'admin']) || auth()->id() === $batchEditRequest->user_id))
                    <div class="bg-gray-50 border-t border-gray-200 p-6 sm:px-8 flex flex-col sm:flex-row justify-end gap-4" x-data>
                        @if(auth()->id() === $batchEditRequest->user_id)
                            <form action="{{ route('batches.cancel_request', $batchEditRequest) }}" method="POST" class="w-full sm:w-auto flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-white border-2 border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-colors font-black text-lg shadow-sm flex justify-center items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Cancelar Solicitud
                                </button>
                            </form>
                        @endif

                        @if(auth()->user()->hasRole(['superadmin', 'admin']))
                        <form id="reject-form-{{ $batchEditRequest->id }}" action="{{ route('batches.reject_edit', $batchEditRequest) }}" method="POST" class="w-full sm:w-auto flex">
                            @csrf
                            <input type="hidden" id="reject-note-{{ $batchEditRequest->id }}" name="resolution_notes" value="">
                            <button type="button" @click="$dispatch('open-prompt-modal', { 
                                title: 'Rechazar Autorización', 
                                description: 'Por favor, añade una nota explicando el rechazo (opcional).', 
                                action: 'reject',
                                id: {{ $batchEditRequest->id }}
                            })" class="w-full sm:w-auto px-8 py-3 bg-white border-2 border-red-200 text-red-600 rounded-xl hover:bg-red-50 hover:border-red-300 transition-colors font-black text-lg shadow-sm flex justify-center items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Rechazar
                            </button>
                        </form>

                        <form id="approve-form-{{ $batchEditRequest->id }}" action="{{ route('batches.approve_edit', $batchEditRequest) }}" method="POST" class="w-full sm:w-auto flex">
                            @csrf
                            <input type="hidden" id="approve-note-{{ $batchEditRequest->id }}" name="resolution_notes" value="">
                            <button type="button" @click="$dispatch('open-prompt-modal', { 
                                title: 'Aprobar Autorización', 
                                description: 'Puedes agregar una nota de aprobación que quedará en el expediente (opcional).', 
                                action: 'approve',
                                id: {{ $batchEditRequest->id }}
                            })" class="w-full sm:w-auto px-8 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors font-black text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex justify-center items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                {{ $batchEditRequest->type === 'delete' ? 'Aprobar y Eliminar' : 'Aprobar y Aplicar Cambios' }}
                            </button>
                        </form>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Modal Prompt Genérico (Para las notas) -->
    <div x-data="{ show: false, title: '', description: '', inputVal: '', action: '', targetId: '' }"
         @open-prompt-modal.window="show = true; title = $event.detail.title; description = $event.detail.description; action = $event.detail.action; targetId = $event.detail.id; inputVal = ''; setTimeout(() => $refs.promptInput.focus(), 100);"
         x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true" @click="show = false">
                <div class="absolute inset-0 bg-gray-900 opacity-75 backdrop-blur-sm"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="show" x-transition.scale class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <div class="bg-white px-8 pt-8 pb-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-extrabold text-gray-900" x-text="title"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 font-medium" x-text="description"></p>
                            </div>
                            <div class="mt-4">
                                <textarea x-ref="promptInput" x-model="inputVal" rows="3" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Escribe aquí..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-8 py-5 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="button" @click="window.processPrompt(inputVal, action, targetId); show = false" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Confirmar
                    </button>
                    <button type="button" @click="show = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.processPrompt = function(val, action, id) {
            if (action === 'reject') {
                document.getElementById('reject-note-' + id).value = val;
                document.getElementById('reject-form-' + id).submit();
            } else if (action === 'approve') {
                document.getElementById('approve-note-' + id).value = val;
                document.getElementById('approve-form-' + id).submit();
            }
        };
    </script>
</x-app-layout>
