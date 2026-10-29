<x-app-layout>
    <div class="min-h-screen bg-gray-50/50 py-8" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8" x-show="show" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;">
            
            @if(auth()->user()->hasRole(['superadmin', 'admin', 'compras', 'Personal de Mostrador']))
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-amber-500 tracking-tight">
                        Solicitudes a Compras
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 font-medium">Bandeja de entrada de peticiones de sucursales</p>
                </div>
            </div>

            <!-- Tabla de Solicitudes -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider"># Folio</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha / Sucursal</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Producto y Cantidad</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($requests as $req)
                                <tr class="hover:bg-orange-50/30 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono text-sm font-bold text-gray-900">#REQ-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $req->branch->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $req->created_at->format('d/m/Y h:i A') }}</div>
                                        <div class="text-xs text-gray-400">Por: {{ $req->user?->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $req->product->name }}</div>
                                        <div class="text-xs font-medium text-orange-600 bg-orange-100 inline-block px-2 py-0.5 rounded-full mt-1">
                                            Req: {{ $req->quantity }} unid.
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($req->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pendiente de revisión
                                            </span>
                                        @elseif($req->status === 'ordered_to_provider')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Orden de Compra enviada
                                            </span>
                                        @elseif($req->status === 'fulfilled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Surtido
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('product_requests.show', $req) }}" class="inline-flex items-center text-orange-600 hover:text-orange-900 bg-orange-50 hover:bg-orange-100 px-3 py-1.5 rounded-lg transition-colors font-semibold">
                                            Revisar Solicitud
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="relative p-4 bg-orange-50 rounded-full mb-4 animate-pulse">
                                                <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900">Bandeja al día</h3>
                                            <p class="text-gray-500 text-sm mt-1">No hay solicitudes de producto pendientes.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($requests->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
            @endif

            <!-- Título de Sección -->
            <div class="flex justify-between items-end mt-16 mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-500 tracking-tight">
                        Autorizaciones
                    </h2>
                    <p class="mt-2 text-sm text-gray-500 font-medium">Gestión de ajustes de inventario y catálogo de servicios</p>
                </div>
            </div>

            @php
                $allRequests = collect();
                foreach($batchEditRequests as $b) {
                    $b->req_type = 'batch';
                    $allRequests->push($b);
                }
                foreach($serviceEditRequests as $s) {
                    $s->req_type = 'service';
                    $allRequests->push($s);
                }
                $allRequests = $allRequests->sortByDesc('created_at');
            @endphp

            <!-- Tabla Unificada -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-12">
                <div class="overflow-x-auto">
                    <table class="w-full text-left divide-y divide-gray-100 table-auto">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th scope="col" class="px-4 lg:px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Fecha / Solicitante</th>
                                <th scope="col" class="px-4 lg:px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tipo / Referencia</th>
                                <th scope="col" class="px-4 lg:px-5 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Detalles del Cambio</th>
                                <th scope="col" class="px-4 lg:px-5 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Estado</th>
                                <th scope="col" class="px-4 lg:px-5 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($allRequests as $req)
                                @if($req->req_type === 'batch')
                                <tr class="hover:bg-blue-50/30 transition-colors group">
                                    <td class="px-4 lg:px-5 py-3.5 whitespace-nowrap align-top sm:align-middle">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                                                #AUTH-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>
                                        <div class="text-sm font-bold text-gray-900 leading-snug">{{ $req->user?->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>{{ $req->created_at->format('d/m/Y h:i A') }}</span>
                                        </div>
                                        <div class="text-xs text-gray-400 font-medium mt-0.5 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <span>{{ $req->batch->branch->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 lg:px-5 py-3.5 align-top sm:align-middle min-w-[200px]">
                                        <div class="mb-1">
                                            @if($req->type === 'delete')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                                    Inventario (Baja)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">
                                                    Inventario (Edición)
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-sm font-bold text-gray-900 leading-snug break-words">{{ $req->batch->product->name }}</div>
                                        <div class="text-xs text-gray-500 mt-1">Lote: <span class="font-mono font-semibold text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200">{{ $req->batch->batch_number }}</span></div>
                                    </td>
                                    <td class="px-4 lg:px-5 py-3.5 align-top sm:align-middle">
                                        @if($req->type === 'delete')
                                            <div class="text-xs font-bold text-red-600 bg-red-50 inline-block px-2 py-0.5 rounded border border-red-200 mb-1">
                                                Solicitud de Eliminación Permanente
                                            </div>
                                            <div class="text-xs text-gray-600 italic max-w-sm" title="{{ $req->reason }}">
                                                <span class="font-semibold not-italic text-gray-700">Motivo:</span> "{{ $req->reason }}"
                                            </div>
                                        @else
                                            <div class="flex flex-col gap-1.5 text-xs">
                                                @if(!is_null($req->requested_stock))
                                                    <div class="inline-flex items-center gap-1.5 whitespace-nowrap text-gray-600">
                                                        <span class="font-medium text-gray-500 w-16">Stock:</span>
                                                        <span class="line-through text-gray-400">{{ $req->old_stock ?? $req->batch->stock }}</span>
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                        <span class="font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">{{ $req->requested_stock }}</span>
                                                    </div>
                                                @endif
                                                @if(!is_null($req->requested_selling_price))
                                                    <div class="inline-flex items-center gap-1.5 whitespace-nowrap text-gray-600">
                                                        <span class="font-medium text-gray-500 w-16">Precio:</span>
                                                        <span class="line-through text-gray-400">${{ number_format($req->old_selling_price ?? $req->batch->selling_price, 2) }}</span>
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                        <span class="font-bold text-green-700 bg-green-50 px-1.5 py-0.5 rounded border border-green-100">${{ number_format($req->requested_selling_price, 2) }}</span>
                                                    </div>
                                                @endif
                                                @if(!is_null($req->requested_expiration_date))
                                                    <div class="inline-flex items-center gap-1.5 whitespace-nowrap text-gray-600">
                                                        <span class="font-medium text-gray-500 w-16">Caducidad:</span>
                                                        <span class="line-through text-gray-400">{{ $req->old_expiration_date ? \Carbon\Carbon::parse($req->old_expiration_date)->format('d/m/Y') : 'N/A' }}</span>
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                        <span class="font-bold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100">{{ \Carbon\Carbon::parse($req->requested_expiration_date)->format('d/m/Y') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-5 py-3.5 whitespace-nowrap text-center align-top sm:align-middle">
                                        @if($req->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5 animate-pulse"></span>
                                                Pendiente
                                            </span>
                                        @elseif($req->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                                Aprobado
                                            </span>
                                        @elseif($req->status === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                                Rechazado
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-5 py-3.5 whitespace-nowrap text-right text-sm font-medium align-top sm:align-middle">
                                        <a href="{{ route('batches.requests.show', $req) }}" class="inline-flex items-center text-xs sm:text-sm text-blue-700 hover:text-white bg-blue-50 hover:bg-blue-600 px-3.5 py-2 rounded-xl transition-all duration-150 font-bold border border-blue-200 hover:border-blue-600 shadow-sm hover:shadow-md hover:shadow-blue-500/20 group/btn">
                                            <span>Revisar Expediente</span>
                                            <svg class="w-4 h-4 ml-1.5 transform group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                                @else
                                <tr class="hover:bg-orange-50/30 transition-colors group">
                                    <td class="px-4 lg:px-5 py-3.5 whitespace-nowrap align-top sm:align-middle">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <span class="font-mono text-xs font-bold text-orange-700 bg-orange-50 px-2 py-0.5 rounded-md border border-orange-100">
                                                #AUTH-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>
                                        <div class="text-sm font-bold text-gray-900 leading-snug">{{ $req->user?->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>{{ $req->created_at->format('d/m/Y h:i A') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 lg:px-5 py-3.5 align-top sm:align-middle min-w-[200px]">
                                        <div class="mb-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-orange-100 text-orange-800 border border-orange-200">
                                                Servicio ({{ $req->type === 'update' ? 'Edición' : 'Eliminación' }})
                                            </span>
                                        </div>
                                        <div class="text-sm font-bold text-gray-900 leading-snug break-words">{{ $req->service->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 lg:px-5 py-3.5 align-top sm:align-middle">
                                        @if($req->type === 'update')
                                            <div class="flex flex-col gap-1.5 text-xs">
                                                @if(isset($req->requested_changes['name']))
                                                    <div class="flex items-center gap-1.5 text-gray-600">
                                                        <span class="font-medium text-gray-500 w-16">Nombre:</span>
                                                        <span class="line-through text-gray-400">{{ $req->service->name }}</span>
                                                        <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                        <span class="font-bold text-orange-700 bg-orange-50 px-1.5 py-0.5 rounded border border-orange-100">{{ $req->requested_changes['name'] ?? 'N/A' }}</span>
                                                    </div>
                                                @endif
                                                @if(isset($req->requested_changes['price']))
                                                    <div class="flex items-center gap-1.5 whitespace-nowrap text-gray-600">
                                                        <span class="font-medium text-gray-500 w-16">Precio:</span>
                                                        <span class="line-through text-gray-400">${{ number_format($req->service->price ?? 0, 2) }}</span>
                                                        <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                        <span class="font-bold text-green-700 bg-green-50 px-1.5 py-0.5 rounded border border-green-100">${{ number_format($req->requested_changes['price'] ?? 0, 2) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        @if($req->reason)
                                            <div class="text-xs text-gray-600 italic max-w-sm mt-1" title="{{ $req->reason }}">
                                                <span class="font-semibold not-italic text-gray-700">Motivo:</span> "{{ $req->reason }}"
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-5 py-3.5 whitespace-nowrap text-center align-top sm:align-middle">
                                        @if($req->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5 animate-pulse"></span>
                                                Pendiente
                                            </span>
                                        @elseif($req->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                                Aprobado
                                            </span>
                                        @elseif($req->status === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                                Rechazado
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-5 py-3.5 whitespace-nowrap text-right text-sm font-medium align-top sm:align-middle">
                                        @if($req->status === 'pending' && auth()->user()->hasRole(['superadmin', 'admin']))
                                            <div class="flex justify-end gap-1.5">
                                                <form action="{{ route('services.approve_edit', $req->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-green-50 text-green-700 hover:bg-green-600 hover:text-white border border-green-200 hover:border-green-600 font-bold py-1.5 px-3 rounded-lg text-xs transition-colors shadow-sm">Aprobar</button>
                                                </form>
                                                <form action="{{ route('services.reject_edit', $req->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-red-50 text-red-700 hover:bg-red-600 hover:text-white border border-red-200 hover:border-red-600 font-bold py-1.5 px-3 rounded-lg text-xs transition-colors shadow-sm">Rechazar</button>
                                                </form>
                                            </div>
                                        @elseif($req->status === 'pending')
                                            <span class="text-gray-400 text-xs italic">En revisión...</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="relative p-4 bg-blue-50 rounded-full mb-4 animate-pulse">
                                                <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900">Todo en orden</h3>
                                            <p class="text-gray-500 text-sm mt-1">No hay peticiones de autorización pendientes.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($batchEditRequests->hasPages() || $serviceEditRequests->hasPages())
                    <div class="px-4 sm:px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex flex-col gap-3">
                        @if($batchEditRequests->hasPages())
                            <div class="overflow-x-auto">
                                <span class="text-xs text-gray-500 font-bold mb-1.5 block">Paginación Inventario:</span> 
                                {{ $batchEditRequests->appends(['page' => request()->page])->links() }}
                            </div>
                        @endif
                        @if($serviceEditRequests->hasPages())
                            <div class="overflow-x-auto">
                                <span class="text-xs text-gray-500 font-bold mb-1.5 block">Paginación Servicios:</span> 
                                {{ $serviceEditRequests->appends(['page' => request()->page])->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
