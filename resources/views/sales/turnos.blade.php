<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-blue-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>

            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-blue-900 to-green-600 tracking-tight">
                        Turnos de Caja
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Historial de turnos, cortes de caja y monitoreo de actividad.
                    </p>
                </div>
                {{-- Badge de advertencia si hay sesiones sospechosas en este mes --}}
                @php
                    $suspiciousCount = $sessions->getCollection()->filter(fn($s) =>
                        $s->audits->where('event', 'breakdown_opened')->count() >= 2
                    )->count();
                @endphp
                @if($suspiciousCount > 0)
                <div class="flex items-center gap-2 bg-red-50 border border-red-200 rounded-xl px-4 py-2.5 shadow-sm">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <p class="text-xs font-black text-red-700 uppercase tracking-wide">Actividad Sospechosa</p>
                        <p class="text-xs text-red-600">{{ $suspiciousCount }} turno(s) con comportamiento anómalo este periodo.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </x-slot>

    @php
        $monthLabels = [
            '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr', '05' => 'May', '06' => 'Jun',
            '07' => 'Jul', '08' => 'Ago', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
        ];
    @endphp

    <div class="py-8 overflow-x-hidden">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filtros --}}
            <form method="GET" action="{{ route('turnos.index') }}" class="mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 items-end transition-all">
                @if($user->hasRole(['superadmin', 'admin']))
                <div class="flex-1 w-full md:w-auto">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Sucursal</label>
                    <select name="branch_id" onchange="this.form.submit()" class="block w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 bg-gray-50 text-gray-700 py-2.5 cursor-pointer transition-colors">
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
                    <select name="month" onchange="this.form.submit()" class="block w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 bg-gray-50 text-gray-700 py-2.5 cursor-pointer transition-colors">
                        <option value="all">Cualquier fecha</option>
                        @foreach($monthsList as $m)
                            @php $parts = explode('-', $m); $label = $monthLabels[$parts[1]] . ' ' . $parts[0]; @endphp
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            {{-- Tabla de Turnos --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Usuario / Sucursal</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Apertura</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Cierre</th>
                                <th class="px-5 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Fondo</th>
                                <th class="px-5 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Ventas</th>
                                <th class="px-5 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Diferencia</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($sessions as $session)
                            @php
                                $breakdownAttempts = $session->audits->where('event', 'breakdown_opened')->count();
                                $isSuspicious = $breakdownAttempts >= 2;
                            @endphp
                            <tr class="{{ $isSuspicious ? 'bg-red-50/60' : 'hover:bg-gray-50' }} transition-colors">
                                {{-- Usuario / Sucursal --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if($isSuspicious)
                                        <div x-data="{ open: false }" class="relative">
                                            <button @click="open = true" class="flex-shrink-0 w-7 h-7 bg-red-100 rounded-full flex items-center justify-center border border-red-300 hover:bg-red-200 transition-colors" title="Ver detalles de actividad sospechosa">
                                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            </button>

                                            {{-- Modal Centrado (Soluciona el problema de recortes y responsividad) --}}
                                            <div x-show="open" style="display:none;" 
                                                 class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                                                 x-transition:enter="transition ease-out duration-300"
                                                 x-transition:enter-start="opacity-0"
                                                 x-transition:enter-end="opacity-100"
                                                 x-transition:leave="transition ease-in duration-200"
                                                 x-transition:leave-start="opacity-100"
                                                 x-transition:leave-end="opacity-0">
                                                
                                                <div @click.away="open = false" 
                                                     class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md border border-red-100 relative whitespace-normal"
                                                     x-transition:enter="transition ease-out duration-300"
                                                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                                                    
                                                    {{-- Botón de cerrar --}}
                                                    <button @click="open = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-full p-1.5 transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>

                                                    <p class="text-sm font-black text-red-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                                                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                        </span>
                                                        Actividad Sospechosa
                                                    </p>
                                                    <p class="text-sm text-gray-700 mb-3 leading-relaxed break-words">
                                                        <span class="font-bold text-red-600">{{ $session->user?->name ?? 'Usuario no disponible' }}</span> llegó a la pantalla de desglose de cierre
                                                        <span class="font-black text-red-700 bg-red-50 px-1.5 py-0.5 rounded">{{ $breakdownAttempts }} veces</span> para este turno sin completar el corte.
                                                    </p>
                                                    <p class="text-xs text-gray-500 mb-4 break-words">Esto puede indicar un intento de cuadrar el efectivo a prueba y error antes de cerrar el turno.</p>
                                                    
                                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                                        <p class="text-xs font-bold text-gray-500 uppercase mb-3 tracking-wider">Detalle de Intentos:</p>
                                                        <div class="space-y-2.5 max-h-[40vh] overflow-y-auto pr-2">
                                                            @foreach($session->audits->where('event', 'breakdown_opened') as $audit)
                                                            <div class="flex justify-between items-center text-sm p-2.5 rounded-lg border border-gray-50 bg-gray-50/50 hover:bg-white transition-colors">
                                                                <div class="text-gray-600 font-medium">
                                                                    Intento {{ $loop->iteration }}
                                                                    <span class="font-mono text-gray-400 text-xs ml-1.5 bg-white px-1.5 py-0.5 rounded shadow-sm">({{ $audit->created_at->format('H:i') }})</span>
                                                                </div>
                                                                <div class="text-right">
                                                                    @if(!is_null($audit->difference))
                                                                        @if($audit->difference == 0)
                                                                            <span class="inline-flex items-center gap-1 font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-md text-xs">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                                Cuadrado ($0)
                                                                            </span>
                                                                        @elseif($audit->difference > 0)
                                                                            <span class="inline-flex items-center gap-1 font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md text-xs">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                                                                Sobrante (+${{ number_format($audit->difference, 2) }})
                                                                            </span>
                                                                        @else
                                                                            <span class="inline-flex items-center gap-1 font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-md text-xs">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                                                Faltante (${{ number_format(abs($audit->difference), 2) }})
                                                                            </span>
                                                                        @endif
                                                                    @else
                                                                        <span class="text-gray-400 text-xs font-medium">—</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 flex items-center gap-1.5 flex-wrap">
                                                {{ $session->user?->name ?? 'Usuario no disponible' }}
                                                @if($session->user?->trashed())
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200" title="Usuario dado de baja">
                                                    Inactivo
                                                </span>
                                                @endif
                                                @if($isSuspicious)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-black bg-red-100 text-red-700 border border-red-200">
                                                    ⚠ {{ $breakdownAttempts }}x
                                                </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $session->branch->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                {{-- Apertura --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ $session->start_time->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $session->start_time->format('H:i') }}</div>
                                </td>
                                {{-- Cierre --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if($session->end_time)
                                        <div class="text-sm text-gray-900 font-medium">{{ $session->end_time->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $session->end_time->format('H:i') }}</div>
                                    @else
                                        <span class="text-xs text-amber-600 font-bold">Abierto</span>
                                    @endif
                                </td>
                                {{-- Fondo --}}
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <span class="text-sm font-bold text-gray-700">${{ number_format($session->starting_cash, 2) }}</span>
                                </td>
                                {{-- Ventas --}}
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    @php
                                        $displayTotalSales = $session->status !== 'closed' ? $session->sales->sum('total_price') : ($session->total_sales ?? 0);
                                    @endphp
                                    <span class="text-sm font-black text-green-700">${{ number_format($displayTotalSales, 2) }}</span>
                                </td>
                                {{-- Diferencia --}}
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    @if($session->status === 'closed')
                                        @php $diff = $session->difference ?? 0; @endphp
                                        <span class="text-sm font-black {{ $diff < 0 ? 'text-red-600' : ($diff > 0 ? 'text-amber-600' : 'text-green-700') }}">
                                            {{ $diff == 0 ? 'Exacto' : ($diff > 0 ? '+' : '') . '$' . number_format(abs($diff), 2) }}
                                        </span>
                                        @if($diff != 0)
                                        <div class="text-xs {{ $diff < 0 ? 'text-red-400' : 'text-amber-400' }}">
                                            {{ $diff < 0 ? 'Faltante' : 'Sobrante' }}
                                        </div>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                {{-- Estado --}}
                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    @if($session->status === 'closed')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">✓ Cerrado</span>
                                    @if(str_starts_with($session->notes ?? '', 'CIERRE MANUAL'))
                                         <div class="mt-1">
                                             <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black bg-red-100 text-red-700 border border-red-200 uppercase tracking-wide cursor-help" title="{{ $session->notes }}">Cierre manual</span>
                                         </div>
                                         @endif
                                     @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">● Abierto</span>
                                    @endif
                                </td>
                                {{-- Acciones --}}
                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Ojito: Modal desglose del turno --}}
                                        <div x-data="{ open: false }">
                                            <button @click="open = true" 
                                                    type="button"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 rounded-lg text-xs font-bold transition-colors cursor-pointer"
                                                    title="Ver desglose del turno">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>

                                            {{-- ====== MODAL DE AUDITORÍA DEL TURNO ====== --}}
                                            @php
                                                $sExpenses     = $session->dailyExpenses ?? collect();
                                                $sExpTotal     = $sExpenses->sum('amount');
                                                $sDocPayment   = $session->doctor_payment_amount ?? 0;
                                                $sDocPending   = 0;
                                                $sDiff         = $session->difference;
                                                if ($session->status !== 'closed') {
                                                    $cashSales     = $session->sales->sum('cash_payment');
                                                    $cardSales     = $session->sales->sum('card_payment');
                                                    $transferSales = $session->sales->sum('transfer_payment');
                                                    $totalSales    = $session->sales->sum('total_price');
                                                } else {
                                                    $cashSales     = $session->cash_sales ?? 0;
                                                    $cardSales     = $session->card_sales ?? 0;
                                                    $transferSales = $session->transfer_sales ?? 0;
                                                    $totalSales    = $session->total_sales ?? 0;
                                                }
                                                $startCash     = $session->starting_cash ?? 0;
                                                $expectedCash  = $session->expected_cash;
                                                $actualCash    = $session->actual_cash;

                                                // Servicios agrupados desde las ventas del turno
                                                $sessionServices = $session->sales
                                                    ->whereNotNull('service_id')
                                                    ->groupBy('service_id')
                                                    ->map(fn($group) => [
                                                        'name'     => $group->first()->service?->name ?? 'Servicio',
                                                        'quantity' => $group->sum('quantity'),
                                                        'amount'   => $group->sum('total_price'),
                                                    ])->values();
                                                $servicesTotal = $sessionServices->sum('amount');
                                            @endphp
                                            <div x-show="open" style="display:none;"
                                                 class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-gray-900/70 backdrop-blur-sm text-left whitespace-normal"
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0"
                                                 x-transition:enter-end="opacity-100"
                                                 x-transition:leave="transition ease-in duration-150"
                                                 x-transition:leave-start="opacity-100"
                                                 x-transition:leave-end="opacity-0">

                                                <div @click.away="open = false"
                                                     class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-gray-100 overflow-hidden text-left"
                                                     x-transition:enter="transition ease-out duration-200"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100">

                                                    {{-- Header --}}
                                                    <div class="px-6 pt-5 pb-4 border-b border-gray-100 flex items-center justify-between">
                                                        <div class="flex items-center gap-3">
                                                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                                            </div>
                                                            <div>
                                                                <h3 class="text-base font-bold text-gray-900">Desglose del Turno</h3>
                                                                <p class="text-xs text-gray-500">{{ $session->user?->name ?? 'Usuario no disponible' }} @if($session->user?->trashed())<span class="text-amber-600 font-semibold">(Inactivo)</span> @endif&middot; {{ $session->start_time->format('d/m/y H:i') }}</p>
                                                            </div>
                                                        </div>
                                                        <button @click="open = false" type="button" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-full p-1.5 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>

                                                    {{-- Body --}}
                                                    <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">

                                                        {{-- Desglose de Ventas --}}
                                                        {{-- Desglose de Ventas --}}
                                                        <div>
                                                            <div class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-2">Desglose de Ventas</div>
                                                            
                                                            {{-- Total Ventas primero --}}
                                                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-xl border border-green-200 mb-2">
                                                                <span class="font-bold text-green-800 uppercase tracking-wide">Total Ventas</span>
                                                                <span class="font-black text-green-700 text-lg">${{ number_format($totalSales, 2) }}</span>
                                                            </div>

                                                            {{-- Métodos de pago después --}}
                                                            <div class="space-y-2">
                                                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                                                    <span class="font-bold text-gray-600">Efectivo</span>
                                                                    <span class="font-black text-gray-900">${{ number_format($cashSales, 2) }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                                                    <span class="font-bold text-gray-600">Tarjeta</span>
                                                                    <span class="font-black text-gray-900">${{ number_format($cardSales, 2) }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                                                    <span class="font-bold text-gray-600">Transferencia</span>
                                                                    <span class="font-black text-gray-900">${{ number_format($transferSales, 2) }}</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Servicios del Doctor y Pago al Doctor --}}
                                                        @if($sessionServices->isNotEmpty() || $session->doctor_paid_at || $sDocPayment > 0)
                                                        <div class="border-t border-pink-100 pt-4" x-data="{ showSvcDetail: false }">
                                                            @if($sessionServices->isNotEmpty())
                                                            <button type="button"
                                                                    @click="showSvcDetail = !showSvcDetail"
                                                                    class="w-full flex justify-between items-center p-3 bg-pink-50 hover:bg-pink-100/80 rounded-xl border border-pink-200 transition-colors cursor-pointer text-left group mb-2">
                                                                <div class="flex items-center gap-2.5">
                                                                    <div class="p-1.5 rounded-lg bg-pink-100 text-pink-600 group-hover:scale-105 transition-transform">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                                                    </div>
                                                                    <div>
                                                                        <span class="font-bold text-pink-800 text-sm block">Total Servicios</span>
                                                                        <span class="text-[10px] text-pink-600 font-medium flex items-center gap-1">
                                                                            <span x-text="showSvcDetail ? 'Ocultar desglose' : 'Clic para ver desglose'"></span>
                                                                            <svg class="w-3 h-3 transition-transform duration-200" :class="showSvcDetail ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <span class="font-black text-pink-700 text-base">-${{ number_format($servicesTotal, 2) }}</span>
                                                            </button>

                                                            <div x-show="showSvcDetail" x-transition class="space-y-1.5 bg-pink-50/40 rounded-xl p-3 border border-pink-100 mb-2 max-h-48 overflow-y-auto">
                                                                @foreach($sessionServices as $svc)
                                                                <div class="flex justify-between items-center text-xs border-b border-pink-100/70 pb-1.5 last:border-0 last:pb-0 text-gray-700">
                                                                    <span>
                                                                        <span class="font-bold text-pink-900">{{ $svc['quantity'] }}x </span>
                                                                        {{ $svc['name'] }}
                                                                    </span>
                                                                    <span class="font-bold text-pink-800">-${{ number_format($svc['amount'], 2) }}</span>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                            @endif

                                                            @if($session->doctor_paid_at || $sDocPayment > 0)
                                                            @php
                                                                $sAvailableCash = max($startCash + $cashSales - $sExpTotal, 0);
                                                                $sDocCashPaid   = min($sDocPayment, $sAvailableCash);
                                                                $sDocPending    = max($sDocPayment - $sDocCashPaid, 0);
                                                            @endphp
                                                            <div class="flex justify-between items-center p-3 {{ $sDocPending > 0 ? 'bg-amber-50 border-amber-200' : 'bg-green-50 border-green-200' }} rounded-xl border">
                                                                <div>
                                                                    <div class="flex items-center gap-2 {{ $sDocPending > 0 ? 'text-amber-800' : 'text-green-800' }} font-bold text-sm">
                                                                        <svg class="w-4 h-4 {{ $sDocPending > 0 ? 'text-amber-600' : 'text-green-600' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                                        Pago al Doctor ({{ $sDocPending > 0 ? 'incompleto' : 'descontado' }})
                                                                    </div>
                                                                    @if($session->doctor_paid_at)
                                                                    <div class="text-[11px] {{ $sDocPending > 0 ? 'text-amber-600' : 'text-green-600' }} font-medium mt-0.5">
                                                                        Confirmado {{ $session->doctor_paid_at->format('d/m/y H:i') }}
                                                                    </div>
                                                                    @endif
                                                                    @if($sDocPending > 0)
                                                                    <div class="text-[11px] text-amber-700 font-bold mt-1">
                                                                        No hubo efectivo suficiente en caja: de ${{ number_format($sDocPayment, 2) }} solo se pudo pagar ${{ number_format($sDocCashPaid, 2) }}.
                                                                        Pendiente: ${{ number_format($sDocPending, 2) }}
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                                <span class="font-black {{ $sDocPending > 0 ? 'text-amber-700' : 'text-green-700' }} text-base">- ${{ number_format($sDocCashPaid, 2) }}</span>
                                                            </div>
                                                            @elseif($sessionServices->isNotEmpty())
                                                            <div class="flex justify-between items-center p-2.5 bg-amber-50 rounded-xl border border-amber-200 text-xs">
                                                                <span class="font-bold text-amber-800 flex items-center gap-1.5">
                                                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                                    Pago al Doctor
                                                                </span>
                                                                <span class="font-semibold text-amber-700">Pendiente / No registrado</span>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        @endif

                                                        {{-- Gastos Operativos --}}
                                                        @if($sExpenses->isNotEmpty())
                                                        <div class="border-t border-orange-100 pt-4" x-data="{ showExpDetail: false }">
                                                            <button type="button"
                                                                    @click="showExpDetail = !showExpDetail"
                                                                    class="w-full flex justify-between items-center p-3 bg-orange-50 hover:bg-orange-100/80 rounded-xl border border-orange-200 transition-colors cursor-pointer text-left group mb-2">
                                                                <div class="flex items-center gap-2.5">
                                                                    <div class="p-1.5 rounded-lg bg-orange-100 text-orange-600 group-hover:scale-105 transition-transform">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                                    </div>
                                                                    <div>
                                                                        <span class="font-bold text-orange-800 text-sm block">Total Gastos (descontados)</span>
                                                                        <span class="text-[10px] text-orange-600 font-medium flex items-center gap-1">
                                                                            <span x-text="showExpDetail ? 'Ocultar desglose' : 'Clic para ver desglose'"></span>
                                                                            <svg class="w-3 h-3 transition-transform duration-200" :class="showExpDetail ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <span class="font-black text-orange-700 text-base">- ${{ number_format($sExpTotal, 2) }}</span>
                                                            </button>

                                                            <div x-show="showExpDetail" x-transition class="space-y-1.5 bg-orange-50/40 rounded-xl p-3 border border-orange-100 mb-2 max-h-48 overflow-y-auto">
                                                                @foreach($sExpenses as $exp)
                                                                <div class="flex justify-between items-center text-xs border-b border-orange-100/70 pb-1.5 last:border-0 last:pb-0 text-gray-700">
                                                                    <span class="truncate pr-2">{{ $exp->expense?->name ?? $exp->notes ?? 'Gasto' }}</span>
                                                                    <span class="font-bold text-red-600 shrink-0">- ${{ number_format($exp->amount, 2) }}</span>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        @endif

                                                        {{-- Cierre Físico --}}
                                                        @if($session->status === 'closed')
                                                        <div class="border-t border-gray-100 pt-4">
                                                            <div class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-2">Cierre Físico de Caja</div>
                                                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-200 mb-2">
                                                                <span class="font-bold text-gray-600">Fondo Inicial</span>
                                                                <span class="font-black text-gray-900">${{ number_format($startCash, 2) }}</span>
                                                            </div>
                                                            @if($sExpTotal > 0 || $sDocPayment > 0)
                                                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg border border-red-200 mb-2">
                                                                <span class="font-bold text-red-700">Deducciones (Gastos y Doc)</span>
                                                                <span class="font-black text-red-700">- ${{ number_format($sExpTotal + min($sDocPayment, max($startCash + $cashSales - $sExpTotal, 0)), 2) }}</span>
                                                            </div>
                                                            @if(($sDocPending ?? 0) > 0)
                                                            <div class="flex justify-between items-center p-3 bg-amber-50 rounded-lg border border-amber-200 mb-2">
                                                                <span class="font-bold text-amber-800 text-sm">Doctor pendiente (pago incompleto)</span>
                                                                <span class="font-black text-amber-700">${{ number_format($sDocPending, 2) }}</span>
                                                            </div>
                                                            @endif
                                                            @endif
                                                            @if(!is_null($expectedCash))
                                                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-200 mb-2">
                                                                <span class="font-bold text-blue-800">Debería haber (calculado)</span>
                                                                <span class="font-black text-blue-900">${{ number_format($expectedCash, 2) }}</span>
                                                            </div>
                                                            @endif
                                                            @if(!is_null($actualCash))
                                                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-200 mb-2">
                                                                <span class="font-bold text-gray-600">Cajero contó</span>
                                                                <span class="font-black text-gray-900">${{ number_format($actualCash, 2) }}</span>
                                                            </div>
                                                            @if(!is_null($sDiff))
                                                            <div class="flex justify-between items-center p-4 rounded-xl border @if($sDiff < 0) bg-red-50 border-red-200 @elseif($sDiff > 0) bg-amber-50 border-amber-200 @else bg-green-50 border-green-200 @endif">
                                                                <span class="font-bold @if($sDiff < 0) text-red-800 @elseif($sDiff > 0) text-amber-800 @else text-green-800 @endif uppercase tracking-wide text-sm">
                                                                    @if($sDiff == 0) ✓ Cuadrado
                                                                    @elseif($sDiff > 0) Sobrante
                                                                    @else Faltante
                                                                    @endif
                                                                </span>
                                                                <span class="font-black text-xl @if($sDiff < 0) text-red-600 @elseif($sDiff > 0) text-amber-600 @else text-green-600 @endif">
                                                                    {{ $sDiff > 0 ? '+' : '' }}${{ number_format($sDiff, 2) }}
                                                                </span>
                                                            </div>
                                                            @endif
                                                            @endif

                                                            {{-- Dinero en la Lapicera (Debería haber menos Fondo Inicial + Sobrante) --}}
                                                            @if(!is_null($expectedCash))
                                                            @php
                                                                $sobranteTurno = ($sDiff ?? 0) > 0 ? $sDiff : 0;
                                                                $dineroLapiceraTurno = max(($expectedCash - $startCash) + $sobranteTurno, 0);
                                                            @endphp
                                                            <div class="mt-2.5 p-3.5 bg-emerald-50 rounded-xl border border-emerald-200 flex justify-between items-center">
                                                                <div class="flex items-center gap-2.5">
                                                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                        </svg>
                                                                    </div>
                                                                    <div>
                                                                        <p class="font-extrabold text-emerald-900 text-sm">Dinero en la Lapicera</p>
                                                                        <p class="text-[10px] text-emerald-600 font-medium">
                                                                            ("Debería haber" - "Fondo inicial")@if($sobranteTurno > 0)<span class="font-bold text-emerald-700"> + sobrante</span>@endif
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <span class="text-lg font-black text-emerald-800">${{ number_format($dineroLapiceraTurno, 2) }}</span>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        @endif

                                                    </div>

                                                    {{-- Footer --}}
                                                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                                                        @if($session->status === 'closed')
                                                        <button onclick="window.openCenteredPopup('{{ route('cash_register.ticket', $session->id) }}', 'Corte', 450, 720)" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-xl text-sm font-bold transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                            Imprimir Ticket
                                                        </button>
                                                        @endif
                                                        <button @click="open = false" type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-bold transition-colors">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Imprimir (solo turnos cerrados) --}}
                                        @if($session->status === 'closed')
                                        <button onclick="window.openCenteredPopup('{{ route('cash_register.ticket', $session->id) }}', 'Reimprimir Corte', 450, 720)"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-lg text-xs font-bold transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            Imprimir
                                        </button>
                                        @endif
                                    {{-- Cierre manual (solo superadmin, solo turnos abiertos) --}}
                                         @if($session->status === 'open' && $user->hasRole('superadmin'))
                                         <div x-data="{
                                                 open: false, notes: '', loading: false, error: '',
                                                 async submit() {
                                                     this.loading = true; this.error = '';
                                                     try {
                                                         const res = await fetch('{{ route('cash_register.manual_close', $session->id) }}', {
                                                             method: 'POST',
                                                             headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                                             body: JSON.stringify({ notes: this.notes })
                                                         });
                                                         const data = await res.json().catch(() => ({}));
                                                         if (res.ok && data.success) { window.location.reload(); return; }
                                                         this.error = data.message || 'No se pudo cerrar el turno.';
                                                     } catch (e) {
                                                         this.error = 'Error de conexion. Intenta de nuevo.';
                                                     }
                                                     this.loading = false;
                                                 }
                                             }">
                                             <button @click="open = true" type="button"
                                                     class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white border border-red-700 rounded-lg text-xs font-bold transition-colors cursor-pointer"
                                                     title="Cierre manual del turno">
                                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                 Cerrar turno
                                             </button>

                                             <div x-show="open" style="display:none;"
                                                  class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-gray-900/70 backdrop-blur-sm text-left whitespace-normal"
                                                  x-transition:enter="transition ease-out duration-200"
                                                  x-transition:enter-start="opacity-0"
                                                  x-transition:enter-end="opacity-100">
                                                 <div @click.away="if (!loading) open = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-gray-100 overflow-hidden">
                                                     <div class="px-6 pt-5 pb-4 border-b border-gray-100 flex items-center gap-3">
                                                         <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                                             <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                         </div>
                                                         <div>
                                                             <h3 class="text-base font-bold text-gray-900">Cierre manual de turno</h3>
                                                             <p class="text-xs text-gray-500">{{ $session->user?->name ?? 'Usuario no disponible' }} &middot; {{ $session->branch->name ?? 'N/A' }} &middot; {{ $session->start_time->format('d/m/y H:i') }}</p>
                                                         </div>
                                                     </div>
                                                     <div class="px-6 py-4 space-y-3">
                                                         <p class="text-sm text-gray-600">
                                                             El turno se cerrar&aacute; con el efectivo que el sistema calcula en caja, <strong>sin conteo de billetes</strong> y <strong>sin confirmar el pago al doctor</strong>. Quedar&aacute; marcado como <strong>cierre manual</strong>. Esta acci&oacute;n no se puede deshacer.
                                                         </p>
                                                         <div>
                                                             <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Motivo (opcional)</label>
                                                             <textarea x-model="notes" rows="3" maxlength="500" placeholder="Ej. El cajero olvid&oacute; cerrar el turno"
                                                                       class="w-full rounded-xl border-gray-200 text-sm focus:border-red-400 focus:ring-red-400"></textarea>
                                                         </div>
                                                         <p x-show="error" x-text="error" class="text-xs font-bold text-red-600"></p>
                                                     </div>
                                                     <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                                                         <button @click="open = false" :disabled="loading" type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-bold transition-colors disabled:opacity-50">Cancelar</button>
                                                         <button @click="submit()" :disabled="loading" type="button" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold transition-colors disabled:opacity-50">
                                                             <span x-show="!loading">Cerrar turno</span>
                                                             <span x-show="loading">Cerrando...</span>
                                                         </button>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                         @endif
                                     </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-gray-400 bg-gray-50/50">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        <p class="text-xl font-bold text-gray-500">Sin turnos en este período.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($sessions->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $sessions->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
