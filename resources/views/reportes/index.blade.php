<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-gray-100 p-3 px-5 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-green-50 rounded-full opacity-70 blur-xl transition-transform duration-700 group-hover:scale-150"></div>
            
            <div class="relative z-10 flex items-center">
                <div>
                    <h2 class="font-extrabold text-xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight flex items-center gap-2 leading-tight">
                        <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Reportes Gerenciales
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Consulta de ventas, análisis de caducidades y valorización de inventarios.
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Pestanas --}}
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div class="flex gap-2 border-b border-gray-200 mb-6">
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'ventas']) }}"
               class="px-5 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 transition-all duration-150
               {{ ($cadTab ?? 'ventas') === 'ventas' ? 'border-green-600 text-green-700 bg-white' : 'border-transparent text-gray-500 hover:text-green-600 hover:border-green-200' }}">
                📊 Ventas
            </a>
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'caducidades']) }}"
               class="px-5 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 transition-all duration-150
               {{ ($cadTab ?? 'ventas') === 'caducidades' ? 'border-red-600 text-red-700 bg-white' : 'border-transparent text-gray-500 hover:text-red-600 hover:border-red-200' }}">
                ⚠️ Caducidades
            </a>
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'inventario']) }}"
               class="px-5 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 transition-all duration-150
               {{ ($cadTab ?? 'ventas') === 'inventario' ? 'border-blue-600 text-blue-700 bg-white' : 'border-transparent text-gray-500 hover:text-blue-600 hover:border-blue-200' }}">
                📦 Inventario
            </a>
        </div>

        {{-- ===================== PESTAÑA VENTAS ===================== --}}
        @if(($cadTab ?? 'ventas') === 'ventas')

        {{-- Filtros Ventas --}}
        <form id="formVentas" method="GET" action="{{ route('reportes.index') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
            <input type="hidden" name="tab" value="ventas">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Desde</label>
                    <input type="text" name="fecha_desde" value="{{ $fechaDesde }}"
                        class="datepicker border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-300 focus:border-green-400 outline-none">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Hasta</label>
                    <input type="text" name="fecha_hasta" value="{{ $fechaHasta }}"
                        class="datepicker border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-300 focus:border-green-400 outline-none">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Sucursal</label>
                    <select name="sucursal_id" onchange="document.getElementById('formVentas').submit()" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-300 outline-none">
                        <option value="all" {{ $sucursalId === 'all' ? 'selected' : '' }}>Todas las sucursales</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $sucursalId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <a href="{{ route('reportes.exportar-ventas', ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta, 'sucursal_id' => $sucursalId]) }}"
                   class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all duration-150 shadow-sm mt-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar Excel
                </a>
            </div>
        </form>

        {{-- KPIs --}}
        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-4 gap-4 mb-6">
            @php
                $kpis = [
                    ['label'=>'Total Vendido',      'value'=>'$'.number_format($totalVentas,2),       'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color'=>'green'],
                    ['label'=>'Ticket Promedio',    'value'=>'$'.number_format($ticketPromedio,2),     'icon'=>'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z', 'color'=>'blue'],
                    ['label'=>'Gastos Operativos',  'value'=>'$'.number_format($totalGastos,2),        'icon'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color'=>'orange'],
                    ['label'=>'Ganancia Neta',      'value'=>'$'.number_format($gananciaNeta,2),       'icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'color'=>($gananciaNeta>=0?'emerald':'red')],
                    ['label'=>'Transacciones',      'value'=>number_format($totalTx),                  'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color'=>'purple'],
                    ['label'=>'Efectivo',           'value'=>'$'.number_format($totalEfectivo,2),      'icon'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color'=>'teal'],
                    ['label'=>'Tarjeta',            'value'=>'$'.number_format($totalTarjeta,2),       'icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'color'=>'indigo'],
                    ['label'=>'Transferencia',      'value'=>'$'.number_format($totalTransfer,2),      'icon'=>'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'color'=>'pink'],
                ];
                $colorMap = ['green'=>'bg-green-50 text-green-600 border-green-100','blue'=>'bg-blue-50 text-blue-600 border-blue-100','orange'=>'bg-orange-50 text-orange-600 border-orange-100','emerald'=>'bg-emerald-50 text-emerald-600 border-emerald-100','red'=>'bg-red-50 text-red-600 border-red-100','purple'=>'bg-purple-50 text-purple-600 border-purple-100','teal'=>'bg-teal-50 text-teal-600 border-teal-100','indigo'=>'bg-indigo-50 text-indigo-600 border-indigo-100','pink'=>'bg-pink-50 text-pink-600 border-pink-100'];
            @endphp
            @foreach($kpis as $kpi)
            <div class="bg-white rounded-2xl border {{ $colorMap[$kpi['color']] }} border p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $colorMap[$kpi['color']] }} flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide leading-tight">{{ $kpi['label'] }}</p>
                    <p class="text-lg font-bold text-gray-800 leading-tight">{{ $kpi['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Graficas --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
            {{-- Ventas por dia --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4">Ventas por día</h3>
                <div class="relative w-full h-[280px]">
                    <canvas id="chartVentasDia"></canvas>
                </div>
            </div>
            {{-- Metodo de pago --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4">Métodos de pago</h3>
                <div class="relative w-full h-[280px] flex justify-center">
                    <canvas id="chartMetodo"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
            {{-- Ventas por sucursal --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4">Ventas por sucursal</h3>
                <div class="relative w-full h-[280px]">
                    <canvas id="chartSucursal"></canvas>
                </div>
            </div>
            {{-- Top productos --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-1">Top 10 productos más vendidos</h3>
                <p class="text-xs text-gray-400 mb-3">Por monto total</p>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($topProductos as $i => $tp)
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-green-100 text-green-700 text-xs font-bold flex items-center justify-center flex-shrink-0">{{ $i+1 }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-800 truncate">{{ optional($tp->product)->name ?? 'Producto eliminado' }}</p>
                            <p class="text-xs text-gray-400">{{ number_format($tp->total_qty) }} uds — ${{ number_format($tp->total_monto,2) }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400">Sin datos para este período.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Ranking cajeros --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Ranking de cajeros</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-gray-400 uppercase border-b border-gray-100">
                            <th class="pb-2 text-left">#</th>
                            <th class="pb-2 text-left">Cajero</th>
                            <th class="pb-2 text-right">Turnos</th>
                            <th class="pb-2 text-right">Total Vendido</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rankingCajeros as $i => $cajero)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="py-2 text-gray-400 font-medium">{{ $i+1 }}</td>
                            <td class="py-2 font-semibold text-gray-800">{{ $cajero['cajero'] }}</td>
                            <td class="py-2 text-right text-gray-500">{{ $cajero['turnos'] }}</td>
                            <td class="py-2 text-right font-bold text-green-700">${{ number_format($cajero['total'],2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-4 text-center text-gray-400 text-xs">Sin datos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabla detallada turnos --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-8">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Detalle por turno</h3>
            <div class="overflow-x-auto overflow-y-auto max-h-[400px]">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-white z-10 shadow-sm">
                        <tr class="text-xs text-gray-400 uppercase border-b border-gray-100">
                            <th class="pb-2 text-left">Sucursal</th>
                            <th class="pb-2 text-left">Fecha</th>
                            <th class="pb-2 text-left">Cajero</th>
                            <th class="pb-2 text-right">Ticket Prom.</th>
                            <th class="pb-2 text-right">Efectivo</th>
                            <th class="pb-2 text-right">Tarjeta</th>
                            <th class="pb-2 text-right">Transf.</th>
                            <th class="pb-2 text-right">Total</th>
                            <th class="pb-2 text-right">Gastos</th>
                            <th class="pb-2 text-right">Neta</th>
                            <th class="pb-2 text-right">Tx</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tablaVentas as $row)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="py-2 font-medium text-gray-800">{{ $row['sucursal'] }}</td>
                            <td class="py-2 text-gray-500">{{ $row['fecha'] }}</td>
                            <td class="py-2 text-gray-600">{{ $row['cajero'] }}</td>
                            <td class="py-2 text-right text-gray-600">${{ number_format($row['ticket_prom'],2) }}</td>
                            <td class="py-2 text-right text-gray-600">${{ number_format($row['efectivo'],2) }}</td>
                            <td class="py-2 text-right text-gray-600">${{ number_format($row['tarjeta'],2) }}</td>
                            <td class="py-2 text-right text-gray-600">${{ number_format($row['transferencia'],2) }}</td>
                            <td class="py-2 text-right font-bold text-green-700">${{ number_format($row['total'],2) }}</td>
                            <td class="py-2 text-right text-orange-600">${{ number_format($row['gastos'],2) }}</td>
                            <td class="py-2 text-right font-bold {{ $row['neta'] >= 0 ? 'text-emerald-700' : 'text-red-600' }}">${{ number_format($row['neta'],2) }}</td>
                            <td class="py-2 text-right text-gray-400">{{ $row['transacciones'] }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="11" class="py-6 text-center text-gray-400 text-xs">No hay turnos cerrados en este período con los filtros seleccionados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @elseif(($cadTab ?? 'ventas') === 'caducidades')
        {{-- ===================== PESTAÑA CADUCIDADES ===================== --}}

        {{-- Filtros caducidades --}}
        <form id="formCaducidades" method="GET" action="{{ route('reportes.index') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
            <input type="hidden" name="tab" value="caducidades">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Sucursal</label>
                    <select name="cad_sucursal_id" onchange="document.getElementById('formCaducidades').submit()" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-300 outline-none">
                        <option value="all" {{ $cadSucursalId === 'all' ? 'selected' : '' }}>Todas las sucursales</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $cadSucursalId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Mostrar</label>
                    <select name="cad_dias" onchange="document.getElementById('formCaducidades').submit()" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-300 outline-none">
                        <option value="vencidos" {{ $cadDias === 'vencidos' ? 'selected' : '' }}>⛔ Ya vencidos</option>
                        <option value="30" {{ $cadDias === '30' ? 'selected' : '' }}>⚠️ Próximos 30 días</option>
                        <option value="60" {{ $cadDias === '60' ? 'selected' : '' }}>🟡 Próximos 60 días</option>
                        <option value="90" {{ $cadDias === '90' ? 'selected' : '' }}>🟢 Próximos 90 días</option>
                    </select>
                </div>
                <a href="{{ route('reportes.exportar-caducidades', ['cad_sucursal_id' => $cadSucursalId, 'cad_dias' => $cadDias]) }}"
                   class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all duration-150 shadow-sm mt-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar Excel
                </a>
            </div>
        </form>

        {{-- KPIs caducidades --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-red-50 border border-red-100 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <div><p class="text-xs font-semibold text-red-400 uppercase tracking-wide">Vencidos</p><p class="text-2xl font-bold text-red-700">{{ $cadVencidos }}</p></div>
            </div>
            <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div><p class="text-xs font-semibold text-orange-400 uppercase tracking-wide">Críticos (&le;30d)</p><p class="text-2xl font-bold text-orange-700">{{ $cadCriticos }}</p></div>
            </div>
            <div class="bg-yellow-50 border border-yellow-100 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-xs font-semibold text-yellow-500 uppercase tracking-wide">Próximos (&le;60d)</p><p class="text-2xl font-bold text-yellow-700">{{ $cadProximos }}</p></div>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-xs font-semibold text-green-400 uppercase tracking-wide">Advertencia (&le;90d)</p><p class="text-2xl font-bold text-green-700">{{ $cadAdvertencia }}</p></div>
            </div>
        </div>

        {{-- Grafica caducidades por estado --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4">Distribución por estado</h3>
                <div class="relative w-full h-[280px] flex justify-center">
                    <canvas id="chartCadEstado"></canvas>
                </div>
            </div>
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4">Productos por sucursal</h3>
                <div class="relative w-full h-[280px]">
                    <canvas id="chartCadSucursal"></canvas>
                </div>
            </div>
        </div>

        {{-- Tabla caducidades --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-8">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Detalle de productos</h3>
            <div class="overflow-x-auto overflow-y-auto max-h-[400px]">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-white z-10 shadow-sm">
                        <tr class="text-xs text-gray-400 uppercase border-b border-gray-100">
                            <th class="pb-2 text-left">Producto</th>
                            <th class="pb-2 text-left">SKU</th>
                            <th class="pb-2 text-left">No. Lote</th>
                            <th class="pb-2 text-left">Sucursal</th>
                            <th class="pb-2 text-left">Rack/Nivel</th>
                            <th class="pb-2 text-right">Stock</th>
                            <th class="pb-2 text-right">Fecha Cad.</th>
                            <th class="pb-2 text-right">Días</th>
                            <th class="pb-2 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tablaCaducidades as $row)
                        @php
                            $badgeClass = match($row['estado']) {
                                'VENCIDO'     => 'bg-red-100 text-red-700',
                                'CRITICO'     => 'bg-orange-100 text-orange-700',
                                'PROXIMO'     => 'bg-yellow-100 text-yellow-700',
                                default       => 'bg-green-100 text-green-700',
                            };
                        @endphp
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="py-2 font-medium text-gray-800">{{ $row['producto'] }}</td>
                            <td class="py-2 text-gray-500 font-mono text-xs">{{ $row['sku'] }}</td>
                            <td class="py-2 text-gray-500 font-mono text-xs">{{ $row['lote'] }}</td>
                            <td class="py-2 text-gray-600">{{ $row['sucursal'] }}</td>
                            <td class="py-2 text-gray-400 text-xs">{{ $row['rack'] ?: '-' }}</td>
                            <td class="py-2 text-right font-semibold text-gray-700">{{ $row['stock'] }}</td>
                            <td class="py-2 text-right text-gray-600">{{ $row['fecha_cad'] }}</td>
                            <td class="py-2 text-right font-bold {{ $row['dias_restantes'] < 0 ? 'text-red-600' : 'text-gray-700' }}">{{ $row['dias_restantes'] }}</td>
                            <td class="py-2 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $badgeClass }}">{{ $row['estado'] }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="py-6 text-center text-gray-400 text-xs">No hay lotes que coincidan con el filtro seleccionado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @elseif(($cadTab ?? 'ventas') === 'inventario')
        {{-- ===================== PESTAÑA INVENTARIO ===================== --}}
        
        {{-- Filtros inventario --}}
        <form id="formInventario" method="GET" action="{{ route('reportes.index') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
            <input type="hidden" name="tab" value="inventario">
            <div class="flex flex-wrap gap-4 items-center justify-between w-full">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Sucursal / Bodega</label>
                    <select name="inv_sucursal_id" onchange="document.getElementById('formInventario').submit()" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
                        <option value="all" {{ $invSucursalId === 'all' ? 'selected' : '' }}>Todas</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $invSucursalId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <a href="{{ route('reportes.exportar-inventario', ['inv_sucursal_id' => $invSucursalId]) }}"
                   class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all duration-150 shadow-sm mt-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar Excel
                </a>
            </div>
        </form>

        {{-- KPIs Inventario --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div><p class="text-xs font-semibold text-blue-500 uppercase tracking-wide">Total Piezas</p><p class="text-xl font-bold text-blue-800">{{ number_format($invTotalPiezas) }}</p></div>
            </div>
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><p class="text-xs font-semibold text-indigo-500 uppercase tracking-wide">Valor Inversión</p><p class="text-xl font-bold text-indigo-800">${{ number_format($invInversion, 2) }}</p></div>
            </div>
            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div><p class="text-xs font-semibold text-emerald-500 uppercase tracking-wide">Valor Público</p><p class="text-xl font-bold text-emerald-800">${{ number_format($invValorPublico, 2) }}</p></div>
            </div>
            <div class="bg-purple-50 border border-purple-100 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div><p class="text-xs font-semibold text-purple-500 uppercase tracking-wide">SKUs Únicos</p><p class="text-xl font-bold text-purple-800">{{ number_format($invSkusUnicos) }}</p></div>
            </div>
        </div>

        {{-- Grafica y Tabla Bajo Stock --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col">
                <h3 class="text-sm font-bold text-gray-700 mb-4 text-center md:text-left">Valor de inversión por Sucursal</h3>
                <div class="relative w-full flex-1 flex justify-center items-center min-h-[250px]">
                    <canvas id="chartInvSucursal"></canvas>
                </div>
            </div>
            <div class="md:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-1">Alertas: Bajo Stock</h3>
                <p class="text-xs text-gray-400 mb-4">Lotes con 5 unidades o menos.</p>
                <div class="overflow-x-auto overflow-y-auto max-h-[440px]">
                    <table class="w-full text-sm text-left">
                        <thead class="sticky top-0 bg-white z-10 shadow-sm text-xs text-gray-400 uppercase">
                            <tr>
                                <th class="pb-2">Producto</th>
                                <th class="pb-2">SKU</th>
                                <th class="pb-2">Sucursal</th>
                                <th class="pb-2 text-right">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invBajoStock as $b)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                <td class="py-2 font-medium text-gray-800">{{ optional($b->product)->name ?? '-' }}</td>
                                <td class="py-2 text-gray-500 font-mono text-xs">{{ optional($b->product)->sku ?? '-' }}</td>
                                <td class="py-2 text-gray-600">{{ optional($b->branch)->name ?? '-' }}</td>
                                <td class="py-2 text-right font-bold text-red-600">{{ $b->stock }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-6 text-center text-gray-400 text-xs">No hay alertas de stock bajo.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @endif
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inicializar Flatpickr para forzar formato Dia/Mes/Año
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d", // Formato enviado al servidor
            altInput: true,
            altFormat: "d/m/Y", // Formato visual (Dia/Mes/Año)
            locale: "es", // Español
            onChange: function(selectedDates, dateStr, instance) {
                document.getElementById('formVentas').submit();
            }
        });

        const tab = @json($cadTab ?? 'ventas');

        if (tab === 'ventas') {
            // --- Ventas por dia ---
            const diasLabels = @json($ventasPorDia->pluck('fecha'));
            const diasData   = @json($ventasPorDia->pluck('total'));
            new Chart(document.getElementById('chartVentasDia'), {
                type: 'line',
                data: { labels: diasLabels, datasets: [{ label: 'Ventas ($)', data: diasData, borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,0.08)', tension: 0.4, fill: true, pointBackgroundColor: '#16a34a', pointRadius: 4 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } } } }
            });

            // --- Metodo de pago (con porcentajes) ---
            const metodoPago = @json($metodoPago);
            const totalMetodo = Object.values(metodoPago).reduce((a, b) => a + b, 0);
            new Chart(document.getElementById('chartMetodo'), {
                type: 'doughnut',
                data: { labels: Object.keys(metodoPago), datasets: [{ data: Object.values(metodoPago), backgroundColor: ['#16a34a', '#2563eb', '#9333ea'], borderWidth: 2, borderColor: '#fff' }] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 11 } } },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    const val = ctx.parsed;
                                    const pct = totalMetodo > 0 ? ((val / totalMetodo) * 100).toFixed(1) : 0;
                                    return ` ${ctx.label}: $${val.toLocaleString('es-MX', {minimumFractionDigits:2})} (${pct}%)`;
                                }
                            }
                        },
                        datalabels: false
                    },
                    // Plugin inline para mostrar % directamente en el grafico
                    animation: { animateRotate: true }
                },
                plugins: [{
                    id: 'percentLabels',
                    afterDatasetDraw(chart) {
                        const { ctx, data } = chart;
                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                        if (total === 0) return;
                        chart.getDatasetMeta(0).data.forEach((arc, i) => {
                            const val = data.datasets[0].data[i];
                            const pct = ((val / total) * 100).toFixed(1);
                            if (pct < 3) return;
                            const { x, y } = arc.tooltipPosition();
                            ctx.save();
                            ctx.font = 'bold 11px sans-serif';
                            ctx.fillStyle = '#fff';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(pct + '%', x, y);
                            ctx.restore();
                        });
                    }
                }]
            });

            // --- Ventas por sucursal ---
            const sucursalLabels = @json($ventasPorSucursal->pluck('nombre'));
            const sucursalData   = @json($ventasPorSucursal->pluck('total'));
            new Chart(document.getElementById('chartSucursal'), {
                type: 'bar',
                data: { labels: sucursalLabels, datasets: [{ label: 'Total ($)', data: sucursalData, backgroundColor: 'rgba(22,163,74,0.7)', borderRadius: 8, borderSkipped: false }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } } } }
            });
        } else if (tab === 'caducidades') {
            // --- Caducidades por estado ---
            new Chart(document.getElementById('chartCadEstado'), {
                type: 'doughnut',
                data: {
                    labels: ['Vencidos', 'Críticos', 'Próximos', 'Advertencia'],
                    datasets: [{ data: [@json($cadVencidos), @json($cadCriticos), @json($cadProximos), @json($cadAdvertencia)], backgroundColor: ['#dc2626','#ea580c','#ca8a04','#16a34a'], borderWidth: 2, borderColor: '#fff' }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
            });

            // --- Caducidades por sucursal ---
            const cadBySuc = {};
            @foreach($tablaCaducidades as $row)
                cadBySuc[@json($row['sucursal'])] = (cadBySuc[@json($row['sucursal'])] || 0) + 1;
            @endforeach
            new Chart(document.getElementById('chartCadSucursal'), {
                type: 'bar',
                data: { labels: Object.keys(cadBySuc), datasets: [{ label: 'Lotes', data: Object.values(cadBySuc), backgroundColor: 'rgba(220,38,38,0.65)', borderRadius: 8 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
            });
        } else if (tab === 'inventario') {
            const invLabels = @json($invPorSucursal->pluck('nombre'));
            const invData = @json($invPorSucursal->pluck('valor'));
            new Chart(document.getElementById('chartInvSucursal'), {
                type: 'doughnut',
                data: { 
                    labels: invLabels, 
                    datasets: [{ 
                        data: invData, 
                        backgroundColor: ['#2563eb', '#16a34a', '#d97706', '#9333ea', '#e11d48', '#0891b2', '#0f766e'], 
                        borderWidth: 2, borderColor: '#fff' 
                    }] 
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: { 
                        legend: { position: 'bottom', labels: { font: { size: 11 } } } 
                    } 
                }
            });
        }
    });
    </script>
</x-app-layout>
