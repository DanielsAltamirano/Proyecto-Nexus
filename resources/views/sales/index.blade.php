<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>

            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Historial de Ventas
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Registro de todos los tickets generados en el punto de venta.
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('pos') }}" class="group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-green-700 rounded-xl hover:bg-green-800 hover:-translate-y-1 hover:shadow-lg hover:shadow-green-500/30 whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nueva Venta
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 p-4 mb-6 rounded-xl shadow-sm flex items-center gap-3" role="alert">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="font-bold">{{ session('success') }}</p>
                </div>
            @endif

            @php
                $monthLabels = [
                    '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr', '05' => 'May', '06' => 'Jun',
                    '07' => 'Jul', '08' => 'Ago', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
                ];
            @endphp

            <!-- Filters -->
            @unless(auth()->user()->hasRole('Personal de Mostrador'))
            <form method="GET" action="{{ route('sales.index') }}" class="mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 items-end transition-all">
                @if($user->hasRole(['superadmin', 'admin']))
                <div class="flex-1 w-full md:w-auto">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Sucursal</label>
                    <select name="branch_id" onchange="this.form.submit()" class="block w-full border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 bg-gray-50 text-gray-700 py-2.5 cursor-pointer transition-colors">
                        <option value="">Todas las Sucursales</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="flex-1 w-full md:w-auto">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Mes</label>
                    <select name="month" onchange="this.form.submit()" class="block w-full border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 bg-gray-50 text-gray-700 py-2.5 cursor-pointer transition-colors">
                        <option value="all">Cualquier fecha</option>
                        @foreach($monthsList as $m)
                            @php
                                $parts = explode('-', $m);
                                $label = $monthLabels[$parts[1]] . ' ' . $parts[0];
                            @endphp
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            @endunless

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-48">Ticket / Fecha</th>
                                @if(auth()->user()->hasRole(['superadmin', 'admin']))
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sucursal</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Atendió</th>
                                @endif
                                <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-24">Artículos</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-36">Método de Pago</th>
                                <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider w-28">Total</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-28">Promo</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-24">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100" x-data="{ openTicket: null }">
                            @forelse ($tickets as $receiptId => $items)
                                @php
                                    $firstItem = $items->first();
                                    $totalTicket = $items->sum('total_price');
                                    $qtyItems = $items->sum('quantity');
                                    $recetaDiscount = \Illuminate\Support\Facades\Cache::get('promo_receta_completa_discount', 20);
                                    $genericoDiscount = \Illuminate\Support\Facades\Cache::get('promo_generico_discount', 25);
                                    
                                    // Receta Completa: el porcentaje de descuento coincide con el configurado para Receta
                                    $hasReceta = $items->filter(fn($i) => $i->discount_pct == $recetaDiscount)->count() > 0;
                                    
                                    // Promo Genéricos: el porcentaje de descuento coincide con el configurado para Genéricos
                                    $hasPromoGenerico = !$hasReceta && $items->filter(fn($i) => $i->discount_pct == $genericoDiscount)->count() > 0;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    {{-- Ticket / Fecha --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="text-sm font-black text-gray-900">{{ $receiptId }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $firstItem->created_at->format('d M Y, H:i') }}</div>
                                    </td>
                                    {{-- Sucursal / Atendió (solo admins) --}}
                                    @if(auth()->user()->hasRole(['superadmin', 'admin']))
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                        {{ $firstItem->batch->branch->name ?? $firstItem->cashRegisterSession->branch->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-bold flex items-center gap-1.5">
                                            {{ $firstItem->user?->name ?? 'N/A' }}
                                            @if($firstItem->user?->trashed())
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Inactivo</span>
                                            @endif
                                        </div>
                                    </td>
                                    @endif
                                    {{-- Artículos --}}
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <span class="bg-gray-100 px-2.5 py-1 rounded-full text-xs font-bold border border-gray-200 text-gray-600">{{ $qtyItems }} und.</span>
                                    </td>
                                    {{-- Método de Pago --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($firstItem->payment_method === 'efectivo')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">💵 Efectivo</span>
                                        @elseif($firstItem->payment_method === 'tarjeta')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">💳 Tarjeta</span>
                                        @elseif($firstItem->payment_method === 'transferencia')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">📱 Transferencia</span>
                                        @elseif($firstItem->payment_method === 'mixto')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">🔄 Mixto</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-200">{{ strtoupper($firstItem->payment_method) }}</span>
                                        @endif
                                    </td>
                                    {{-- Total --}}
                                    <td class="px-5 py-4 whitespace-nowrap text-right">
                                        <div class="text-base font-black text-green-600">${{ number_format($totalTicket, 2) }}</div>
                                    </td>
                                    {{-- Promo badge --}}
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        @if($hasReceta)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-[11px] font-bold border border-blue-300" title="Venta con Receta Completa">
                                                🧾 Receta Completa
                                            </span>
                                        @elseif($hasPromoGenerico)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold border border-amber-300" title="Promo de Genéricos aplicada">
                                                🏷️ Promo Genéricos
                                            </span>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </td>
                                    {{-- Acciones --}}
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                        <!-- Botón Ver Detalles (Admins y Compras) -->
                                        @if(auth()->user()->hasRole(['superadmin', 'admin', 'compras']))
                                        <button @click="openTicket === '{{ $receiptId }}' ? openTicket = null : openTicket = '{{ $receiptId }}'" class="inline-flex items-center p-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors" title="Ver Artículos">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                        @endif
                                        <!-- Botón Imprimir -->
                                        @if(str_starts_with($receiptId, 'TK-'))
                                        <button onclick="window.openCenteredPopup('{{ route('sales.ticket', $receiptId) }}', 'Imprimir Ticket', 450, 720)" class="inline-flex items-center p-2 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-lg transition-colors" title="Imprimir Ticket">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </button>
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                                <!-- Fila Expandible para detalles -->
                                <tr x-show="openTicket === '{{ $receiptId }}'" x-transition class="bg-gray-50 border-b border-gray-100" style="display: none;">
                                    <td colspan="{{ auth()->user()->hasRole(['superadmin', 'admin']) ? 8 : 6 }}" class="px-6 py-6">
                                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-inner">
                                            <h4 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Desglose de Artículos</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                @foreach($items as $item)
                                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                                    <div>
                                                        <p class="font-bold text-sm text-gray-900">{{ $item->product ? $item->product->name : ($item->service ? $item->service->name : 'Desconocido') }}</p>
                                                        @if($item->batch)
                                                            <p class="text-xs text-gray-500 font-mono">Lote: {{ $item->batch->batch_number }}</p>
                                                        @elseif($item->service)
                                                            <p class="text-xs text-pink-500 font-bold uppercase tracking-wider">Servicio</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-xs text-gray-500">{{ $item->quantity }} x ${{ number_format($item->total_price / $item->quantity, 2) }}</p>
                                                        <p class="font-bold text-green-700">${{ number_format($item->total_price, 2) }}</p>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            
                                            @if($firstItem->payment_method === 'efectivo' && $firstItem->amount_paid)
                                            <div class="mt-6 pt-4 border-t border-gray-100 flex gap-6 text-sm">
                                                <div>
                                                    <span class="text-gray-500 font-bold">Pagado:</span>
                                                    <span class="font-black text-gray-900">${{ number_format($firstItem->amount_paid, 2) }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500 font-bold">Cambio:</span>
                                                    <span class="font-black text-gray-900">${{ number_format($firstItem->change_amount, 2) }}</span>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center text-gray-400 bg-gray-50/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            <p class="text-xl font-bold text-gray-500">No hay ventas registradas.</p>
                                            <p class="text-sm mt-1">Realiza una venta en el Punto de Venta para verla aquí.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Script para Auto-Abrir Ticket después de una venta -->
    @if(session('ticket_id'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                window.openCenteredPopup('{{ route("sales.ticket", session("ticket_id")) }}', 'Imprimir Ticket', 450, 720);
            }, 500);
        });
    </script>
    @endif
</x-app-layout>