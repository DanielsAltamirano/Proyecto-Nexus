<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="font-extrabold text-3xl text-gray-900 tracking-tight">
                            Traspaso #TR-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}
                        </h2>
                        @if($transfer->status === 'en_transito')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">En Tránsito</span>
                        @elseif($transfer->status === 'en_proceso')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-purple-100 text-purple-800 border border-purple-200">En Proceso</span>
                        @elseif($transfer->status === 'empaquetado')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-indigo-100 text-indigo-800 border border-indigo-200">Empaquetado</span>
                        @elseif($transfer->status === 'received')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200">Recibido (Completo)</span>
                        @elseif($transfer->status === 'partial')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">Parcial</span>
                        @elseif($transfer->status === 'rejected')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-red-100 text-red-800 border border-red-200">Rechazado</span>
                        @elseif($transfer->status === 'cancelled')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-gray-100 text-gray-800 border border-gray-200">Cancelado</span>
                        @elseif($transfer->status === 'pending')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">Solicitud de Traspaso</span>
                        @elseif($transfer->status === 'pending_override')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-orange-100 text-orange-800 border border-orange-200">Petición Autorización Lote</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500">
                        Generado el {{ $transfer->created_at->format('d M Y, h:i A') }} por <span class="font-bold">{{ $transfer->creator?->name ?? 'Sistema' }}</span>
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('transfers.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
                        &larr; Volver al historial
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="flex items-center p-4 text-green-800 bg-green-50 border border-green-200 rounded-xl shadow-sm" role="alert">
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="flex items-center p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm" role="alert">
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Tarjetas de Información de Origen y Destino -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Origen -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-start space-x-4">
                    <div class="p-3 bg-gray-50 rounded-xl text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wide">Origen (Enviado por)</p>
                        <h4 class="text-xl font-extrabold text-gray-900 mt-1">{{ $transfer->fromBranch?->name ?? 'N/A' }}</h4>
                        <p class="text-sm text-gray-500 mt-1">Salida de Inventario</p>
                    </div>
                </div>

                <!-- Destino -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-green-100 border-l-4 border-l-green-500 flex items-start space-x-4">
                    <div class="p-3 bg-green-50 rounded-xl text-green-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-green-500 uppercase tracking-wide">Destino (A recibir por)</p>
                        <h4 class="text-xl font-extrabold text-gray-900 mt-1">{{ $transfer->toBranch?->name ?? 'N/A' }}</h4>
                        <p class="text-sm text-gray-500 mt-1">
                            @if($transfer->received_by)
                                Recibido por: <span class="font-bold">{{ $transfer->receiver?->name ?? 'Sistema' }}</span> el {{ \Carbon\Carbon::parse($transfer->received_at)->format('d M Y') }}
                            @else
                                A la espera de recepción
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notas Generales -->
            @if($transfer->notes)
                <div class="bg-yellow-50/50 p-6 rounded-2xl border border-yellow-100">
                    <h4 class="text-sm font-bold text-yellow-800 uppercase tracking-wide mb-2">Notas del Traspaso</h4>
                    <p class="text-yellow-900 whitespace-pre-wrap">{{ $transfer->notes }}</p>
                </div>
            @endif

            <!-- Formulario de Recepción / Detalles -->
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl">
                <form action="{{ route('transfers.update', $transfer) }}" method="POST" id="receiveForm" x-data>
                    @csrf
                    @method('PUT')
                    <div class="p-0 text-gray-900 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Lote / Caducidad</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Medicamento</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Cantidad</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-green-600 uppercase bg-green-50/50">Recibido</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($transfer->items as $index => $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item->batch)
                                                <div class="text-sm font-bold font-mono text-gray-900">{{ $item->batch->batch_number }}</div>
                                                <div class="text-xs text-gray-500 mt-1">Exp: {{ $item->batch->expiration_date ? \Carbon\Carbon::parse($item->batch->expiration_date)->format('M Y') : 'N/A' }}</div>
                                            @else
                                                <div class="text-sm font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded inline-block">Automático (FIFO)</div>
                                                <div class="text-xs text-gray-500 mt-1">Lote se asignará al aprobar</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $prod = $item->product ?? ($item->batch ? $item->batch->product : null);
                                            @endphp
                                            <div class="text-sm font-bold text-gray-900">{{ $prod ? $prod->name : 'N/A' }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5">SKU: {{ $prod ? $prod->sku : '' }} | {{ $prod ? $prod->type : '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if(in_array($transfer->status, ['pending', 'pending_override', 'en_proceso', 'empaquetado']))
                                                <div class="text-xs text-gray-500 uppercase tracking-wide mb-1 font-bold">
                                                    {{ in_array($transfer->status, ['en_proceso', 'empaquetado']) ? 'A Enviar' : 'Solicitado' }}
                                                </div>
                                                
                                                @if((auth()->user()->hasRole(['superadmin', 'admin', 'compras']) || auth()->user()->branch_id == $transfer->from_branch_id) && $transfer->status === 'pending')
                                                    <!-- Input to edit approved amount -->
                                                    <input type="hidden" name="approved_items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <button type="button" onclick="let v=this.parentElement.querySelector('input[type=number]'); v.value=Math.max(0,parseInt(v.value||0)-1);" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 text-lg font-black border border-blue-200 transition-colors select-none" tabindex="-1">−</button>
                                                        <input type="number" name="approved_items[{{ $item->id }}][quantity_approved]" value="{{ $item->quantity_requested }}" max="{{ $item->quantity_requested }}" min="0" required class="w-16 text-center rounded-lg border-blue-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 font-bold text-blue-900 bg-white" @wheel.prevent>
                                                        <button type="button" onclick="let v=this.parentElement.querySelector('input[type=number]'); v.value=Math.min({{ $item->quantity_requested }},parseInt(v.value||0)+1);" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 text-lg font-black border border-blue-200 transition-colors select-none" tabindex="-1">+</button>
                                                    </div>
                                                    <div class="text-[10px] text-gray-400 mt-1">Puedes modificar</div>
                                                @else
                                                    @php
                                                        // Si la solicitud ya fue aprobada, el stock reservado está en quantity_sent
                                                        $approvedAlready = in_array($transfer->status, ['en_proceso', 'empaquetado']) && $transfer->logs->contains('action', 'Solicitud Aprobada (En Proceso)');
                                                        $qtyShown = $approvedAlready ? $item->quantity_sent : $item->quantity_requested;
                                                    @endphp
                                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 font-bold rounded-lg text-sm">{{ $qtyShown }}</span>
                                                @endif
                                            @else
                                                @if($item->quantity_requested > 0 && $item->quantity_requested != $item->quantity_sent)
                                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wide mb-0.5 line-through" title="Cantidad originalmente solicitada">Sol: {{ $item->quantity_requested }}</div>
                                                @endif
                                                <div class="text-xs text-gray-500 uppercase tracking-wide mb-1 font-bold">Enviado</div>
                                                <span class="px-3 py-1 bg-gray-100 text-gray-800 font-bold rounded-lg text-sm">{{ $item->quantity_sent }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center bg-green-50/20">
                                            @if($transfer->status === 'en_transito')
                                                <!-- Solo permitir editar si la sucursal del usuario es el destino o si es admin/superadmin -->
                                                @if((auth()->user()->hasRole(['superadmin', 'admin']) || auth()->user()->branch_id == $transfer->to_branch_id) && !auth()->user()->hasRole('compras'))
                                                    <input type="hidden" name="received_items[{{ $index }}][id]" value="{{ $item->id }}">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <button type="button" onclick="let v=this.parentElement.querySelector('input[type=number]'); v.value=Math.max(0,parseInt(v.value||0)-1);" class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-100 text-green-700 hover:bg-green-200 text-lg font-black border border-green-200 transition-colors select-none" tabindex="-1">−</button>
                                                        <input type="number" name="received_items[{{ $index }}][quantity_received]" value="{{ $item->quantity_sent }}" max="{{ $item->quantity_sent }}" min="0" required class="w-16 text-center rounded-lg border-green-200 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 font-bold text-green-900 bg-white" @wheel.prevent>
                                                        <button type="button" onclick="let v=this.parentElement.querySelector('input[type=number]'); v.value=Math.min({{ $item->quantity_sent }},parseInt(v.value||0)+1);" class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-100 text-green-700 hover:bg-green-200 text-lg font-black border border-green-200 transition-colors select-none" tabindex="-1">+</button>
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-400 italic">Pendiente de recepción</span>
                                                @endif
                                            @elseif(in_array($transfer->status, ['pending', 'pending_override', 'en_proceso', 'empaquetado']))
                                                <span class="text-sm text-gray-400 italic">No disponible aún</span>
                                            @else
                                                <span class="px-3 py-1 {{ $item->quantity_received == $item->quantity_sent ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} font-bold rounded-lg border {{ $item->quantity_received == $item->quantity_sent ? 'border-green-200' : 'border-yellow-200' }}">
                                                    {{ $item->quantity_received ?? 0 }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($transfer->status === 'en_proceso' || $transfer->status === 'empaquetado')
                        <div class="p-6 bg-purple-50 border-t border-purple-100">
                            @if(auth()->user()->hasRole(['superadmin', 'admin', 'compras']) || auth()->user()->branch_id == $transfer->from_branch_id)
                                <div class="mb-4 pb-4 border-b border-purple-200">
                                    <h4 class="text-sm font-bold text-purple-900 mb-3">Acciones de Origen (Proceso de Envío)</h4>
                                    <div class="flex flex-wrap gap-3">
                                        @if($transfer->status === 'en_proceso')
                                            <button type="button" class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm text-sm" @click.prevent="
                                                let f = $el.closest('form');
                                                let i = f.querySelector('input[name=action]');
                                                if(!i) { i = document.createElement('input'); i.type = 'hidden'; i.name = 'action'; f.appendChild(i); }
                                                i.value = 'mark_empaquetado';
                                                f.submit();
                                            ">
                                                Marcar como Empaquetado
                                            </button>
                                        @endif

                                        @if($transfer->status === 'empaquetado')
                                            <button type="button" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-sm text-sm" @click.prevent="
                                                let f = $el.closest('form');
                                                let i = f.querySelector('input[name=action]');
                                                if(!i) { i = document.createElement('input'); i.type = 'hidden'; i.name = 'action'; f.appendChild(i); }
                                                i.value = 'mark_en_transito';
                                                $dispatch('open-confirm-delete', { message: '¿Enviar mercancía en este momento? El inventario se descontará automáticamente de tu sucursal.', form: f })
                                            ">
                                                Enviar (En Tránsito)
                                            </button>
                                        @endif

                                        <button type="button" class="px-6 py-2.5 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition-colors shadow-sm text-sm ml-auto" @click.prevent="
                                            let f = $el.closest('form');
                                            let i = f.querySelector('input[name=action]');
                                            if(!i) { i = document.createElement('input'); i.type = 'hidden'; i.name = 'action'; f.appendChild(i); }
                                            i.value = 'cancel';
                                            $dispatch('open-confirm-delete', { message: '¿Estás seguro de cancelar este traspaso?', form: f })
                                        ">
                                            Cancelar Traspaso
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @elseif($transfer->status === 'en_transito')
                        <div class="p-6 bg-gray-50 border-t border-gray-100">
                            
                            <!-- Acciones para Destino (Recibir / Rechazar) -->
                            @if((auth()->user()->hasRole(['superadmin', 'admin']) || auth()->user()->branch_id == $transfer->to_branch_id) && !auth()->user()->hasRole('compras'))
                                <div>
                                    <h4 class="text-sm font-bold text-green-800 mb-3">Acciones de Destino (Recepción)</h4>
                                    
                                    <div class="mb-4">
                                        <label for="notes" class="block text-sm font-bold text-gray-700 mb-2">Notas de Recepción / Faltantes (Opcional)</label>
                                        <textarea name="notes" id="notes" rows="2" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Ej. Caja dañada, faltó 1 unidad..."></textarea>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-3">
                                        <button type="button" class="px-6 py-2.5 bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-800 font-bold rounded-xl border border-red-200 transition-colors shadow-sm flex items-center" @click.prevent="
                                            let f = $el.closest('form');
                                            let i = f.querySelector('input[name=action]');
                                            if(!i) { i = document.createElement('input'); i.type = 'hidden'; i.name = 'action'; f.appendChild(i); }
                                            i.value = 'reject';
                                            $dispatch('open-confirm-delete', { message: '¿Estás seguro de RECHAZAR todo el traspaso? El stock será devuelto a la sucursal de origen.', form: f })
                                        ">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Rechazar Todo
                                        </button>
                                        
                                        <button type="button" class="px-8 py-2.5 bg-green-700 text-white font-bold rounded-xl hover:bg-green-800 transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-0.5 ml-auto flex items-center" @click.prevent="
                                            let f = $el.closest('form');
                                            let i = f.querySelector('input[name=action]');
                                            if(!i) { i = document.createElement('input'); i.type = 'hidden'; i.name = 'action'; f.appendChild(i); }
                                            i.value = 'receive';
                                            $dispatch('open-confirm-delete', { message: '¿Estás seguro de RECIBIR las cantidades indicadas? Se ingresarán a tu inventario y las faltantes se regresarán al origen.', form: f })
                                        ">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Confirmar Recepción
                                        </button>
                                    </div>
                                </div>
                            @endif

                        </div>
                    @elseif($transfer->status === 'pending')
                        <div class="p-6 bg-blue-50 border-t border-blue-100">
                            @if(auth()->user()->hasRole(['superadmin', 'admin', 'compras']) || auth()->user()->branch_id == $transfer->from_branch_id)
                                <div>
                                    <h4 class="text-sm font-bold text-blue-900 mb-3">Responder a la Solicitud</h4>
                                    <p class="text-sm text-blue-700 mb-4">Esta sucursal te está solicitando estos productos. Puedes modificar la cantidad a enviar si no tienes suficiente stock o no deseas mandar todo. Si apruebas, el sistema enviará los lotes automáticamente usando la regla FIFO.</p>
                                    
                                    <div class="mb-4">
                                        <label for="send_notes" class="block text-sm font-bold text-blue-900 mb-2">Notas de Envío / Motivo de cambio de cantidad (Opcional)</label>
                                        <textarea name="send_notes" id="send_notes" rows="2" class="block w-full rounded-xl border-blue-200 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Ej. Solo envío 5 piezas porque el resto está reservado..."></textarea>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-3">
                                        <button type="button" class="px-6 py-2.5 bg-red-100 text-red-700 hover:bg-red-200 font-bold rounded-xl border border-red-200 transition-colors shadow-sm flex items-center" @click.prevent="
                                            let f = $el.closest('form');
                                            let i = f.querySelector('input[name=action]');
                                            if(!i) { i = document.createElement('input'); i.type = 'hidden'; i.name = 'action'; f.appendChild(i); }
                                            i.value = 'reject_request';
                                            $dispatch('open-confirm-delete', { message: '¿Rechazar esta solicitud?', form: f })
                                        ">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Rechazar
                                        </button>
                                        
                                        <button type="button" class="px-8 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-md transform hover:-translate-y-0.5 ml-auto flex items-center" @click.prevent="
                                            let f = $el.closest('form');
                                            let i = f.querySelector('input[name=action]');
                                            if(!i) { i = document.createElement('input'); i.type = 'hidden'; i.name = 'action'; f.appendChild(i); }
                                            i.value = 'approve_request';
                                            $dispatch('open-confirm-delete', { message: '¿Aprobar y Enviar? El stock se deducirá automáticamente.', form: f })
                                        ">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Aprobar Solicitud (En Proceso)
                                        </button>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-blue-800 font-bold">Esperando respuesta de la sucursal de origen.</p>
                            @endif
                        </div>
                    @elseif($transfer->status === 'pending_override')
                        <div class="p-6 bg-orange-50 border-t border-orange-100">
                            @if(auth()->user()->hasRole(['superadmin', 'admin', 'compras']))
                                <div>
                                    <h4 class="text-sm font-bold text-orange-900 mb-3">Autorizar Envío de Lote Específico</h4>
                                    <p class="text-sm text-orange-700 mb-4">Un empleado está solicitando enviar un lote de forma manual. Autoriza la salida de inventario o recházala.</p>
                                    
                                    <div class="flex flex-wrap gap-3">
                                        <button type="button" class="px-6 py-2.5 bg-red-100 text-red-700 hover:bg-red-200 font-bold rounded-xl border border-red-200 transition-colors shadow-sm flex items-center" @click.prevent="
                                            let f = $el.closest('form');
                                            let i = f.querySelector('input[name=action]');
                                            if(!i) { i = document.createElement('input'); i.type = 'hidden'; i.name = 'action'; f.appendChild(i); }
                                            i.value = 'reject_request';
                                            $dispatch('open-confirm-delete', { message: '¿Rechazar esta petición?', form: f })
                                        ">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Rechazar
                                        </button>
                                        
                                        <button type="button" class="px-8 py-2.5 bg-orange-600 text-white font-bold rounded-xl hover:bg-orange-700 transition-colors shadow-md transform hover:-translate-y-0.5 ml-auto flex items-center" @click.prevent="
                                            let f = $el.closest('form');
                                            let i = f.querySelector('input[name=action]');
                                            if(!i) { i = document.createElement('input'); i.type = 'hidden'; i.name = 'action'; f.appendChild(i); }
                                            i.value = 'approve_override';
                                            $dispatch('open-confirm-delete', { message: '¿Autorizar y Enviar? El stock se deducirá del lote indicado.', form: f })
                                        ">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Autorizar (En Proceso)
                                        </button>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-orange-800 font-bold">Esperando autorización de un administrador.</p>
                            @endif
                        </div>
                    @endif
                </form>
            </div>

            <!-- Historial de Movimientos / Bitácora -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mt-6">
                <h3 class="text-lg font-extrabold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Historial del Traspaso
                </h3>
                
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @forelse($transfer->logs()->orderBy('created_at', 'asc')->get() as $log)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center ring-8 ring-white">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">{{ $log->action }} <span class="font-bold text-gray-900">{{ $log->user?->name ?? 'Sistema' }}</span></p>
                                                @if($log->notes)
                                                    <p class="mt-1 text-sm text-gray-600">{{ $log->notes }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $log->created_at }}">{{ $log->created_at->format('d M h:i A') }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <p class="text-sm text-gray-500 italic">No hay registros de historial.</p>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Botón Imprimir Guía (Solo visible para el origen o admins una vez empaquetado) -->
            @if(in_array($transfer->status, ['empaquetado', 'en_transito', 'completed']) && (auth()->user()->hasRole(['superadmin', 'admin', 'compras']) || auth()->user()->branch_id == $transfer->from_branch_id))
                <div class="mt-8 flex justify-center pb-8">
                    <a href="{{ route('transfers.print', $transfer) }}" target="_blank" class="px-8 py-3 bg-gray-800 text-white font-bold rounded-xl hover:bg-gray-900 transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Imprimir Guía de Envío
                    </a>
                </div>
            @endif
            
        </div>
    </div>
</x-app-layout>
