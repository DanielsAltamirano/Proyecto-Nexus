<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 py-2 px-6 transition-all duration-300">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Punto de Venta
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Bienvenido de nuevo, {{ Auth::user()->name }}. Resumen general y terminal de ventas.
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <style>
        /* Ocultar flechas / spinners en inputs numéricos */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>

    <div class="py-4" x-data="posSystem" {{-- @scanner-result.window="if($event.detail.target === 'posSearch'){ handleScan($event.detail.value); } else if($event.detail.target === 'clientSearch'){ clientBarcode = $event.detail.value; searchClient(); }" --}}>
        {{-- El padding-left sigue al header: max-w-7xl centrado con px-4 sm:px-6 lg:px-8 --}}
        {{-- En pantallas > 1280px (max-w-7xl = 80rem), el header deja ~(viewport-80rem)/2 de margen izq. --}}
        <div class="w-full pl-4 sm:pl-6 lg:pl-8 xl:pl-[max(2rem,calc((100vw-80rem)/2+2rem))] pr-0">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 lg:hidden">
                <!-- Ventas de Tu Turno / Corte de Caja -->
                <button type="button" @click="handleSessionClick" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all border-l-4 border-l-blue-500 text-left group w-full relative overflow-hidden">
                    <div class="flex items-center justify-between relative z-10 mb-2">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase group-hover:text-blue-600 transition-colors" x-text="hasOpenSession ? `Avance Meta Diaria (${weeklyProgress.shift})` : 'Turno Cerrado'"></p>
                            <div class="flex items-baseline gap-2 mt-1" x-show="hasOpenSession">
                                <h3 class="text-2xl font-black text-gray-800" x-text="weeklyProgress.percentage + '%'"></h3>
                                <!--Linea para mostrar el avance de ventas con la cifra en mxn-->
                                <!--<span class="text-sm font-bold text-gray-400" x-show="weeklyProgress.target > 0" x-text="formatMoney(weeklyProgress.sales) + ' / ' + formatMoney(weeklyProgress.target)"></span>-->
                            </div>
                            <h3 class="text-2xl font-black text-gray-800" x-show="!hasOpenSession">Abrir Caja</h3>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    
                    <!-- Progress Bar (Only visible if session is open and target > 0) -->
                    <div class="w-full bg-gray-100 rounded-full h-2 mt-3 overflow-hidden relative z-10" x-show="hasOpenSession && weeklyProgress.target > 0">
                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-1000 ease-out" :style="`width: ${weeklyProgress.percentage}%`"></div>
                    </div>
                </button>

                <!-- Historial de Ventas / Reimpresión -->
                <a href="{{ route('sales.index') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all border-l-4 border-l-green-600 cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <div>
                            @if(auth()->user()->hasRole(['admin', 'superadmin']))
                                <p class="text-xs font-bold text-gray-500 uppercase group-hover:text-green-600 transition-colors">Historial Completo</p>
                                <h3 class="text-2xl font-black text-gray-800">Detalles de Venta</h3>
                            @else
                                <p class="text-xs font-bold text-gray-500 uppercase group-hover:text-green-600 transition-colors">Ventas del Día</p>
                                <h3 class="text-2xl font-black text-gray-800">Reimpresión de Tickets</h3>
                            @endif
                        </div>
                        <div class="p-3 bg-green-50 rounded-xl text-green-700 group-hover:bg-green-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                    </div>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

                <!-- Lado Izquierdo: Buscador y Catálogo -->
                <div class="lg:col-span-5 flex flex-col space-y-4">
                    
                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-xl shadow-sm mb-2" role="alert">
                            <p class="font-bold">Error en la operación</p>
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <!-- Buscador y Escáner -->
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
                        <div class="relative flex-grow flex items-center bg-gray-50 rounded-xl border border-gray-200 focus-within:border-green-500 focus-within:ring-1 focus-within:ring-green-500 transition-all overflow-hidden">
                            <div class="pl-4 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" x-model="searchQuery" @keydown.enter.prevent="handleScan(searchQuery)" class="w-full border-none focus:ring-0 p-4 bg-transparent text-gray-900 text-lg font-medium placeholder-gray-400" placeholder="Buscar medicamento por nombre o código de barras (SKU)...">
                            
                            {{-- Botón de escáner de cámara comentado para optimización
                            <button type="button" @click="$dispatch('open-scanner-possearch')" class="px-6 py-4 bg-green-50 text-green-700 hover:bg-green-100 font-bold border-l border-green-100 transition-colors flex items-center justify-center group" title="Escanear Código">
                                <svg class="w-6 h-6 transform group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="currentColor">
                                    <rect x="3" y="5" width="2" height="14"></rect>
                                    <rect x="7" y="5" width="1" height="14"></rect>
                                    <rect x="10" y="5" width="3" height="14"></rect>
                                    <rect x="15" y="5" width="1" height="14"></rect>
                                    <rect x="18" y="5" width="2" height="14"></rect>
                                    <rect x="22" y="5" width="1" height="14"></rect>
                                </svg>
                            </button>
                            --}}
                        </div>
                    </div>

                    @if($isGenericoPromo || $isRecetaPromo || $is4x3Promo || $isVolumenPromo)
                    @php
                        $dias = ['Sunday' => 'Domingo', 'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado'];
                        $diaActual = $dias[now()->format('l')];
                        $activePromosCount = 0;
                        if($isGenericoPromo) $activePromosCount++;
                        if($isRecetaPromo) $activePromosCount++;
                        if($is4x3Promo) $activePromosCount++;
                        if($isVolumenPromo) $activePromosCount++;
                    @endphp
                    <div class="bg-gradient-to-r from-cyan-500 to-teal-400 rounded-xl p-3 flex items-center gap-3 shadow-sm mb-2">
                        <div class="w-9 h-9 bg-white/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-xl">🌟</span>
                        </div>
                        <div class="flex-1">
                            @if($activePromosCount > 1)
                            <p class="text-white font-black text-sm leading-tight">¡Múltiples Promos Activas!</p>
                            <p class="text-cyan-50 text-xs font-medium">Hay {{ $activePromosCount }} promociones activas hoy {{ $diaActual }}. Aprovecha los descuentos y precios especiales.</p>
                            @else
                                @if($isGenericoPromo)
                                <p class="text-white font-black text-sm leading-tight">¡{{ $diaActual }} de Promo de Genéricos!</p>
                                <p class="text-cyan-50 text-xs font-medium">Todos los medicamentos genéricos (GEN) tienen <strong class="text-white">{{ $genericoDiscount }}% de descuento</strong> hoy.</p>
                                @elseif($isRecetaPromo)
                                <p class="text-white font-black text-sm leading-tight">¡{{ $diaActual }} de Receta Completa!</p>
                                <p class="text-cyan-50 text-xs font-medium">Aplica descuento manual del <strong class="text-white">{{ $recetaDiscount }}%</strong> en medicamentos genéricos.</p>
                                @elseif($is4x3Promo)
                                <p class="text-white font-black text-sm leading-tight">¡{{ $diaActual }} de Promo 4x3!</p>
                                <p class="text-cyan-50 text-xs font-medium">En la compra de 4 productos seleccionados, pagas solo 3.</p>
                                @elseif($isVolumenPromo)
                                <p class="text-white font-black text-sm leading-tight">¡{{ $diaActual }} de Promo x Volumen!</p>
                                <p class="text-cyan-50 text-xs font-medium">Precios especiales por volumen activos en productos seleccionados.</p>
                                @endif
                            @endif
                        </div>
                        <a href="{{ route('promos.index') }}" class="flex-shrink-0 text-white/80 hover:text-white text-xs underline font-bold">Ver promos</a>
                    </div>
                    @endif

                    <!-- Grid de Productos Rápidos -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex-grow flex flex-col h-[550px]">
                        <div class="p-4 border-b border-gray-50 bg-gray-50 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-gray-700">Catálogo Rápido</h3>
                                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">Ordenado por popularidad</span>
                            </div>
                            <span class="text-xs font-bold text-green-700 bg-green-50 px-3 py-1.5 rounded-full border border-green-100 shadow-inner" 
                                  x-text="searchQuery.trim() ? (filteredProducts.length + ' resultado' + (filteredProducts.length === 1 ? '' : 's')) : (filteredProducts.length + ' más populares')"></span>
                        </div>
                        <div class="p-4 overflow-y-auto flex-grow grid grid-cols-2 sm:grid-cols-3 gap-3.5 content-start bg-gray-50/30">
                            <template x-for="product in filteredProducts" :key="product.id">
                                <button @click="addToCart(product)"
                                    :title="product.name + (product.sku ? ' (SKU: ' + product.sku + ')' : '')"
                                    :class="isProduct4x3Active(product) 
                                        ? 'flex flex-col justify-between items-center p-2.5 sm:p-3 bg-white border-2 border-yellow-400 rounded-xl hover:border-yellow-500 hover:shadow-lg hover:bg-yellow-50/50 transition-all text-center group relative overflow-hidden min-h-[150px] h-[155px]' 
                                        : (isProductVolumenActive(product) 
                                            ? 'flex flex-col justify-between items-center p-2.5 sm:p-3 bg-white border-2 border-cyan-400 rounded-xl hover:border-cyan-500 hover:shadow-lg hover:bg-cyan-50/50 transition-all text-center group relative overflow-hidden min-h-[150px] h-[155px]' 
                                            : ((isGenericoPromo && product.type === 'GEN' && !isProductExcluded(product)) 
                                                ? 'flex flex-col justify-between items-center p-2.5 sm:p-3 bg-white border-2 border-amber-300 rounded-xl hover:border-amber-500 hover:shadow-lg hover:bg-amber-50/50 transition-all text-center group relative overflow-hidden min-h-[150px] h-[155px]' 
                                                : 'flex flex-col justify-between items-center p-2.5 sm:p-3 bg-white border border-gray-200 rounded-xl hover:border-green-500 hover:shadow-lg hover:bg-green-50 transition-all text-center group relative overflow-hidden min-h-[150px] h-[155px]'))">

                                    {{-- Badge de stock o servicio --}}
                                    <div class="absolute top-2 right-2 px-1.5 py-0.5 bg-gray-100 text-gray-500 text-[10px] rounded font-bold border border-gray-200 group-hover:bg-green-100 group-hover:text-green-700 transition-colors z-10" x-text="product.is_service ? 'Servicio' : 'Stock: ' + getTotalStock(product)"></div>

                                    {{-- Badge de promo 4x3 --}}
                                    <template x-if="isProduct4x3Active(product)">
                                        <div class="absolute top-2 left-2 px-1.5 py-0.5 bg-yellow-400 text-yellow-900 text-[10px] rounded font-black shadow-sm z-10">4x3</div>
                                    </template>

                                    {{-- Badge de promo volumen --}}
                                    <template x-if="isProductVolumenActive(product)">
                                        <div class="absolute top-2 left-2 px-1.5 py-0.5 bg-cyan-500 text-white text-[10px] rounded font-black shadow-sm z-10" x-text="'Vol (' + (product.promo_volumen_min_qty || 2) + '+)'"></div>
                                    </template>

                                    {{-- Badge de promo genéricos (solo si no está en 4x3 ni volumen hoy) --}}
                                    <template x-if="isGenericoPromo && product.type === 'GEN' && !isProductExcluded(product)">
                                        <div class="absolute top-2 left-2 px-1.5 py-0.5 bg-amber-400 text-white text-[10px] rounded font-black shadow-sm z-10" x-text="'-' + genericoDiscount + '%'"></div>
                                    </template>

                                    {{-- Icono superior estético y proporcionado --}}
                                    <div class="text-gray-300 group-hover:text-green-500 transition-colors mt-1 flex-shrink-0">
                                        <template x-if="product.is_service">
                                            <svg class="w-6 h-6 sm:w-7 sm:h-7 transform group-hover:scale-110 transition-transform text-pink-300 group-hover:text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        </template>
                                        <template x-if="!product.is_service">
                                            <svg class="w-6 h-6 sm:w-7 sm:h-7 transform group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        </template>
                                    </div>

                                    {{-- Nombre del medicamento (diseño con altura adecuada para evitar solapamientos) --}}
                                    <div class="my-auto w-full px-1 flex items-center justify-center min-h-[38px]">
                                        <span class="text-[11px] sm:text-xs font-black text-gray-800 line-clamp-2 leading-tight tracking-tight break-words text-center" x-text="product.name"></span>
                                    </div>

                                    {{-- Sección de precios anclada al fondo --}}
                                    <div class="mt-auto w-full flex flex-col items-center justify-end min-h-[26px]">
                                        {{-- Precio para Promo Volumen --}}
                                        <template x-if="isProductVolumenActive(product)">
                                            <div class="flex flex-col items-center">
                                                <span class="text-[9px] text-gray-400 font-semibold leading-none" x-text="formatMoney(getPrice(product))"></span>
                                                <span class="text-[11px] font-black text-cyan-700 bg-cyan-50 px-1.5 py-0.5 rounded border border-cyan-200 mt-0.5 leading-none" x-text="formatMoney(product.promo_volumen_price) + ' (' + (product.promo_volumen_min_qty || 2) + '+)'"></span>
                                            </div>
                                        </template>

                                        {{-- Precio normal o 4x3 --}}
                                        <template x-if="isProduct4x3Active(product) || (!isProductExcluded(product) && !(isGenericoPromo && product.type === 'GEN'))">
                                            <span class="text-xs font-bold text-green-700 bg-green-50 px-2 py-0.5 rounded leading-none" x-text="formatMoney(getPrice(product))"></span>
                                        </template>

                                        {{-- Precio con descuento de Genéricos (cuando aplica) --}}
                                        <template x-if="isGenericoPromo && product.type === 'GEN' && !isProductExcluded(product)">
                                            <div class="flex flex-col items-center">
                                                <span class="text-[9px] font-bold text-gray-400 line-through leading-none" x-text="formatMoney(getPrice(product))"></span>
                                                <span class="text-xs font-black text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 mt-0.5 leading-none" x-text="formatMoney(getDiscountedPrice(product))"></span>
                                            </div>
                                        </template>
                                    </div>

                                </button>
                            </template>
                            
                            <template x-if="filteredProducts.length === 0">
                                <div class="col-span-full py-16 flex flex-col items-center justify-center text-gray-400 bg-white rounded-xl border border-dashed border-gray-300">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-xl font-bold text-gray-500">Sin resultados</p>
                                    <p class="text-sm mt-1">No hay productos que coincidan con la búsqueda.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Lado Derecho: Ticket de Venta -->
                <div class="lg:col-span-4 bg-white rounded-l-2xl shadow-xl border-t-8 border-t-green-700 flex flex-col overflow-hidden h-[600px] lg:h-[750px] sticky top-24">
                    <!-- Ticket Header & Tabs -->
                    <div class="bg-gray-50 text-gray-800 border-b border-gray-100 z-10 flex flex-col">
                        <div class="p-5 flex items-center justify-between pb-3">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-green-100 text-green-700 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                                <h3 class="font-black text-lg tracking-tight">TICKET DE VENTA</h3>
                            </div>
                            <span class="text-green-700 font-bold text-xs bg-green-50 border border-green-200 px-3 py-1 rounded-full shadow-sm" x-text="cart.length + ' articulos'"></span>
                        </div>
                        
                        <!-- Tabs Multiticket -->
                        <div class="px-3 pb-0 flex overflow-x-auto gap-2" style="scrollbar-width: none;">
                            <template x-for="(ticket, index) in tickets" :key="ticket.id">
                                <div class="group flex items-center px-4 py-2 rounded-t-lg transition-colors border-b-2 whitespace-nowrap cursor-pointer relative flex-shrink-0"
                                     :class="activeTicketId === ticket.id ? 'bg-white text-green-700 border-green-500 shadow-[0_-4px_6px_-2px_rgba(0,0,0,0.05)]' : 'bg-gray-100 text-gray-500 border-transparent hover:bg-gray-200'"
                                     @click="loadTicket(ticket.id)">
                                    <span class="font-bold text-xs mr-2" x-text="ticket.name"></span>
                                    <span x-show="ticket.cart.length > 0" class="bg-green-100 text-green-800 font-bold rounded-full px-1.5 py-0.5 text-[10px] mr-2" x-text="ticket.cart.length"></span>
                                    
                                    <button type="button" @click.stop="removeTicket(ticket.id)" class="text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full p-0.5 transition-colors opacity-0 group-hover:opacity-100" :class="{'opacity-100': tickets.length === 1 && ticket.cart.length > 0}" title="Cancelar Ticket">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </template>
                            <button type="button" x-show="tickets.length < 4" @click="addTicket(); loadTicket(tickets[tickets.length - 1].id)" class="px-3 py-2 rounded-t-lg bg-gray-200 text-gray-600 hover:bg-gray-300 transition-colors flex flex-shrink-0 items-center font-bold text-xs border-b-2 border-transparent" title="Nuevo Ticket">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Branch Selector -->
                    @if(auth()->user()->hasAnyRole(['superadmin', 'admin', 'compras']))
                        <div class="px-5 py-3 bg-orange-50 border-b border-orange-100 text-sm">
                            <label class="block font-bold text-orange-800 mb-1.5 text-xs uppercase tracking-wider">Sucursal (Venta Externa)</label>
                            <select x-model="branchId" class="w-full rounded-lg border-orange-200 focus:ring-orange-500 focus:border-orange-500 py-2 text-sm bg-white shadow-sm font-medium">
                                <option value="">Seleccione desde dónde cobrar...</option>
                                @foreach(\App\Models\Branch::where('is_warehouse', false)->get() as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif


                    <!-- Cart Items -->
                    <div class="flex-grow overflow-y-auto p-4 space-y-3 bg-white">
                        <template x-for="(item, index) in cart" :key="(item.is_service ? 's' : 'p') + '-' + item.id">
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 shadow-sm relative group hover:border-green-300 transition-colors">
                                <button @click="removeFromCart(index)" class="absolute -top-2 -right-2 bg-red-100 text-red-600 rounded-full p-1.5 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-500 hover:text-white" title="Eliminar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                
                                <div class="mb-3 pr-2">
                                    <h4 class="font-bold text-gray-900 text-sm leading-snug" x-text="item.name"></h4>
                                    <p class="text-[11px] text-gray-500 font-mono mt-1" x-text="'SKU: ' + (item.sku || 'N/A')"></p>
                                    <template x-if="isRecetaPromo && !item.is_service && item.type === 'GEN' && !isProductExcluded(item)">
                                        <div class="mt-2 flex items-center">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" :checked="item.applyRecetaCompleta" @change="toggleReceta(index)">
                                                <span class="ml-2 text-xs font-bold text-blue-800">Aplicar Receta Completa</span>
                                            </label>
                                        </div>
                                    </template>
                                    <template x-if="isRecetaPromo && !item.is_service && item.type === 'GEN' && isProductExcluded(item)">
                                        <div class="mt-1 flex items-center">
                                            <span class="text-[10px] text-gray-400 font-medium italic">Excluido de Receta (Promo activa)</span>
                                        </div>
                                    </template>
                                </div>
                                
                                <div class="flex items-center justify-between mt-2 pt-3 border-t border-gray-200/60">
                                    <div class="flex items-center bg-white rounded-lg p-1 border border-gray-200 shadow-sm">
                                        <button @click="decrement(index)" class="w-8 h-8 flex items-center justify-center rounded text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                        </button>
                                        <input type="text" x-model.number="item.quantity" class="w-10 text-center text-sm font-bold bg-transparent border-none focus:ring-0 p-0 text-gray-900" readonly>
                                        <button @click="increment(index)" class="w-8 h-8 flex items-center justify-center rounded text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                    </div>
                                    <div class="text-right">
                                        <!-- Precio con Promo 4x3 -->
                                        <template x-if="isProduct4x3Active(item)">
                                            <div>
                                                <div class="text-xs text-gray-500 font-medium" x-text="formatMoney(getPrice(item)) + ' / u'"></div>
                                                <div class="text-xs font-black text-yellow-700 flex items-center justify-end gap-1" x-show="item.quantity >= 4">
                                                    <span class="bg-yellow-100 text-yellow-800 text-[9px] font-black px-1.5 py-0.5 rounded" x-text="'¡4x3! (' + Math.floor(item.quantity / 4) + ' gratis)'"></span>
                                                </div>
                                                <div class="font-black text-green-700 text-base" x-text="formatMoney(getLineTotal(item))"></div>
                                            </div>
                                        </template>

                                        <!-- Precio con Promo Volumen -->
                                        <template x-if="isProductVolumenActive(item)">
                                            <div>
                                                <template x-if="item.quantity >= (item.promo_volumen_min_qty || 2)">
                                                    <div>
                                                        <div class="text-xs text-gray-400 line-through font-medium" x-text="formatMoney(getPrice(item)) + ' / u'"></div>
                                                        <div class="text-xs font-black text-cyan-600 flex items-center justify-end gap-1">
                                                            <span class="bg-cyan-100 text-cyan-800 text-[9px] font-black px-1.5 py-0.5 rounded">¡Precio Volumen!</span>
                                                            <span x-text="formatMoney(item.promo_volumen_price) + ' / u'"></span>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="item.quantity < (item.promo_volumen_min_qty || 2)">
                                                    <div>
                                                        <div class="text-xs text-gray-500 font-medium" x-text="formatMoney(getPrice(item)) + ' / u'"></div>
                                                        <span class="text-[9px] text-gray-400 block" x-text="'Volumen desde ' + (item.promo_volumen_min_qty || 2) + ' pzas'"></span>
                                                    </div>
                                                </template>
                                                <div class="font-black text-green-700 text-base" x-text="formatMoney(getLineTotal(item))"></div>
                                            </div>
                                        </template>

                                        <!-- Sin Promo 4x3 ni Volumen: Descuento activo (Genéricos o Receta) -->
                                        <template x-if="!isProductExcluded(item) && item.originalPrice">
                                            <div>
                                                <div class="text-xs text-gray-400 line-through font-medium" x-text="formatMoney(item.originalPrice) + ' / u'"></div>
                                                <div class="text-xs font-black text-amber-600 flex items-center justify-end gap-1">
                                                    <span class="bg-amber-100 text-amber-700 text-[9px] font-black px-1 py-0.5 rounded" x-text="'-' + item.discount_pct + '%'"></span>
                                                    <span x-text="formatMoney(item.price) + ' / u'"></span>
                                                </div>
                                                <div class="font-black text-green-700 text-base" x-text="formatMoney(getLineTotal(item))"></div>
                                            </div>
                                        </template>

                                        <!-- Sin Promo 4x3 ni Volumen: Precio regular -->
                                        <template x-if="!isProductExcluded(item) && !item.originalPrice">
                                            <div>
                                                <div class="text-xs text-gray-500 font-medium" x-text="formatMoney(item.price) + ' / u'"></div>
                                                <div class="font-black text-green-700 text-base" x-text="formatMoney(getLineTotal(item))"></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="cart.length === 0">
                            <div class="h-full flex flex-col items-center justify-center text-gray-400 p-8 text-center space-y-4 bg-gray-50/50 rounded-2xl border-2 border-dashed border-gray-200 m-2">
                                <div class="bg-white p-4 rounded-full shadow-sm border border-gray-100">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-500 mb-1">Ticket Vacío</p>
                                    <p class="text-xs text-gray-400">Escanea o busca un producto para comenzar.</p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Checkout Section -->
                    <div class="bg-gray-900 p-6 z-10 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
                        <div class="flex justify-between items-end mb-5">
                            <span class="text-gray-400 font-bold uppercase text-xs tracking-widest">Total a Pagar</span>
                            <span class="text-4xl font-black text-green-400 tracking-tight" x-text="formatMoney(cartTotal)"></span>
                        </div>
                        
                        <form id="posForm" method="POST" action="{{ route('sales.store') }}">
                            @csrf
                            <input type="hidden" name="cart" id="cartInput">
                            <input type="hidden" name="payment_method" id="paymentMethodInput">
                            <input type="hidden" name="amount_paid" id="amountPaidInput">
                            <input type="hidden" name="change_amount" id="changeAmountInput">
                            <input type="hidden" name="client_barcode" id="clientBarcodeInput">
                            <input type="hidden" name="mixed_cash" id="mixedCashInput" value="0">
                            <input type="hidden" name="mixed_card" id="mixedCardInput" value="0">
                            <input type="hidden" name="mixed_transfer" id="mixedTransferInput" value="0">
                            <input type="hidden" name="has_receta_completa" id="hasRecetaInput" value="0">
                            <input type="hidden" name="cedula_medica" :value="cedulaMedica">
                            <input type="hidden" name="ui_ticket_id" :value="activeTicketId">
                            @if(auth()->user()->hasAnyRole(['superadmin', 'admin', 'compras']))
                                <input type="hidden" name="branch_id" :value="branchId">
                            @endif

                            <!-- Cédula Médica obligatoria para Medicamentos Controlados -->
                            <div x-show="cart.some(item => item.is_controlled)" x-cloak class="mb-4 bg-red-950/60 border border-red-500/40 rounded-xl p-3">
                                <label class="block text-xs font-bold text-red-300 uppercase tracking-wider mb-1">
                                    Cédula Profesional Médica (Obligatorio - Controlado)
                                </label>
                                <input type="text" x-model="cedulaMedica" placeholder="Ej. CED-7894561"
                                    class="w-full rounded-lg bg-gray-800 border-red-500/50 text-white text-sm px-3 py-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                            
                            <button type="button" @click="triggerCheckout" 
                                class="w-full bg-green-500 hover:bg-green-400 text-gray-900 font-black py-4 px-6 rounded-xl shadow-[0_0_20px_rgba(34,197,94,0.3)] transition-all transform active:scale-95 flex items-center justify-center text-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none"
                                :disabled="cart.length === 0 || ({{ auth()->user()->hasAnyRole(['superadmin', 'admin', 'compras']) ? 'true' : 'false' }} && !branchId)">
                                <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                COBRAR
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Barra de Acciones Lateral (Solo Desktop) -->
                <div class="hidden lg:flex lg:col-span-3 flex-col space-y-3 sticky top-24 h-fit pr-0">
                    
                    <!-- Ventas de Tu Turno / Corte de Caja -->
                    <button type="button" @click="handleSessionClick"
                        class="bg-white py-6 px-3 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all border-l-4 border-l-blue-500 text-left group w-full relative overflow-hidden">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-start justify-between">
                                <p class="text-[10px] font-bold text-gray-500 uppercase leading-tight group-hover:text-blue-600 transition-colors"
                                    x-text="hasOpenSession ? `Avance Meta Diaria (${weeklyProgress.shift})` : 'Turno Cerrado'"></p>
                                <div class="p-2 bg-blue-50 rounded-lg text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                            <div x-show="hasOpenSession">
                                <h3 class="text-3xl font-black text-gray-800" x-text="weeklyProgress.percentage + '%'"></h3>
                                <p class="text-[9px] text-blue-400 font-bold uppercase tracking-wider mt-1 group-hover:text-blue-600 transition-colors">Clic para cerrar turno &rsaquo;</p>
                            </div>
                            <h3 class="text-xl font-black text-gray-800" x-show="!hasOpenSession">Abrir Caja</h3>
                        </div>
                        <!-- Progress Bar -->
                        <div class="absolute bottom-0 left-0 w-full bg-gray-100 h-1.5" x-show="hasOpenSession && weeklyProgress.target > 0">
                            <div class="bg-blue-500 h-full transition-all duration-1000 ease-out" :style="`width: ${weeklyProgress.percentage}%`"></div>
                        </div>
                    </button>

                    <!-- Historial / Reimpresión -->
                    <a href="{{ route('sales.index') }}"
                        class="bg-white py-6 px-3 rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all border-l-4 border-l-green-600 cursor-pointer group">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-start justify-between">
                                @if(auth()->user()->hasRole(['admin', 'superadmin']))
                                    <p class="text-[10px] font-bold text-gray-500 uppercase leading-tight group-hover:text-green-600 transition-colors">Historial Completo</p>
                                @else
                                    <p class="text-[10px] font-bold text-gray-500 uppercase leading-tight group-hover:text-green-600 transition-colors">Ventas del Día</p>
                                @endif
                                <div class="p-2 bg-green-50 rounded-lg text-green-700 group-hover:bg-green-600 group-hover:text-white transition-colors flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                            </div>
                            @if(auth()->user()->hasRole(['admin', 'superadmin']))
                                <h3 class="text-xl font-black text-gray-800">Detalles de Venta</h3>
                            @else
                                <h3 class="text-xl font-black text-gray-800">Reimpresión de Tickets</h3>
                            @endif
                        </div>
                    </a>

                    <!-- Gastos del Día -->
                    <button type="button" @click="openExpensesModal()"
                        x-show="hasOpenSession"
                        class="bg-white py-6 px-3 rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all border-l-4 border-l-orange-500 cursor-pointer group w-full text-left relative overflow-hidden">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-start justify-between">
                                <p class="text-[10px] font-bold text-gray-500 uppercase leading-tight group-hover:text-orange-600 transition-colors">Operativos del Día</p>
                                <div class="p-2 bg-orange-50 rounded-lg text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-black text-gray-800">Gastos</h3>
                            <p class="text-[9px] text-orange-400 font-bold uppercase tracking-wider group-hover:text-orange-600 transition-colors" x-show="sessionExpensesTotal > 0" x-text="'Total: ' + formatMoney(sessionExpensesTotal)"></p>
                        </div>
                    </button>

                </div>
            </div>

            <!-- Custom Notification (Centered Modal Style) -->
            <div x-show="notification.show" 
                 style="display: none;"
                 class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none"
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 scale-95" 
                 x-transition:enter-end="opacity-100 scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 scale-100" 
                 x-transition:leave-end="opacity-0 scale-95">
                 
                <div class="pointer-events-auto bg-white max-w-sm w-full rounded-[2rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] border border-gray-100 p-8 flex flex-col items-center text-center relative overflow-hidden">
                    
                    <!-- Decorative background blob -->
                    <div class="absolute -top-24 -right-24 w-48 h-48 rounded-full opacity-10 pointer-events-none"
                         :class="{
                             'bg-red-500': notification.type === 'error',
                             'bg-green-500': notification.type === 'success',
                             'bg-blue-500': notification.type === 'info'
                         }"></div>

                    <!-- Icon -->
                    <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-full relative z-10" 
                         :class="{
                             'bg-red-50 text-red-500 border-4 border-red-100': notification.type === 'error',
                             'bg-green-50 text-green-500 border-4 border-green-100': notification.type === 'success',
                             'bg-blue-50 text-blue-500 border-4 border-blue-100': notification.type === 'info'
                         }">
                        <!-- Error Icon -->
                        <svg x-show="notification.type === 'error'" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <!-- Success Icon -->
                        <svg x-show="notification.type === 'success'" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <!-- Info Icon -->
                        <svg x-show="notification.type === 'info'" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>

                    <h3 class="text-2xl font-black text-gray-900 mb-3 relative z-10 tracking-tight" x-text="notification.type === 'error' ? '¡Atención!' : (notification.type === 'success' ? '¡Excelente!' : 'Aviso')"></h3>
                    <p class="text-base text-gray-500 font-medium leading-relaxed relative z-10" x-text="notification.message"></p>

                    <button @click="notification.show = false" class="mt-8 w-full inline-flex justify-center rounded-2xl border border-gray-200 px-6 py-3.5 bg-gray-50 text-base font-bold text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 active:scale-95 relative z-10">
                        Entendido
                    </button>
                </div>
            </div>

        </div><!-- end max-w-7xl -->

    <!-- Modal de Pago -->
    <div x-show="showPaymentModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showPaymentModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" @click="showPaymentModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="showPaymentModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                                Finalizar Venta
                            </h3>
                            <div class="mt-4">
                                <p class="text-gray-500 font-bold mb-2">Total a Pagar:</p>
                                <div class="text-4xl font-black text-green-600 mb-6" x-text="formatMoney(cartTotal)"></div>
                                
                                <div x-show="!clientData" class="bg-blue-50 border border-blue-100 p-4 rounded-xl mb-2 flex items-center gap-4">
                                    <div class="bg-blue-100 p-2 rounded-full text-blue-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-bold text-blue-900 mb-1">Tarjeta de Puntos (Opcional)</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" x-model="clientBarcode" @keydown.enter.prevent="searchClient()" :disabled="isSearchingClient" class="w-full bg-white border border-blue-200 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 text-sm shadow-sm" placeholder="Escanea o escribe código">
                                            {{-- Botón de escáner de cámara comentado para optimización
                                            <button type="button" @click="$dispatch('open-scanner-clientsearch')" class="px-3 py-2 bg-blue-100 text-blue-700 hover:bg-blue-200 font-bold rounded-lg transition-colors flex-shrink-0" title="Escanear con Cámara">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                                    <rect x="3" y="5" width="2" height="14"></rect>
                                                    <rect x="7" y="5" width="1" height="14"></rect>
                                                    <rect x="10" y="5" width="3" height="14"></rect>
                                                    <rect x="15" y="5" width="1" height="14"></rect>
                                                    <rect x="18" y="5" width="2" height="14"></rect>
                                                    <rect x="22" y="5" width="1" height="14"></rect>
                                                </svg>
                                            </button>
                                            --}}
                                        </div>
                                    </div>
                                </div>
                                <div x-show="errorClient" class="text-red-500 text-sm mb-4 font-medium" x-text="errorClient"></div>

                                <!-- Vista previa de la tarjeta -->
                                <div x-show="clientData" style="display:none;" class="mb-6 relative">
                                    <div class="absolute -top-3 -right-3 z-20">
                                        <button type="button" @click="clientData = null; clientBarcode = ''; errorClient = null;" class="bg-red-500 text-white rounded-full p-1.5 hover:bg-red-600 shadow-lg transform hover:scale-105 transition-transform" title="Quitar tarjeta">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div class="w-full bg-gradient-to-tr from-purple-950 via-purple-900 to-indigo-950 rounded-2xl shadow-xl relative overflow-hidden text-white flex flex-col p-4 aspect-[2.2]">
                                        <div class="absolute -right-8 -top-8 w-24 h-24 bg-purple-500 rounded-full mix-blend-overlay filter blur-xl opacity-40 pointer-events-none"></div>
                                        <div class="absolute -left-8 -bottom-8 w-24 h-24 bg-violet-500 rounded-full mix-blend-overlay filter blur-xl opacity-40 pointer-events-none"></div>

                                        <!-- Top: Name & Logo -->
                                        <div class="flex justify-between items-start relative z-10 w-full">
                                            <div class="overflow-hidden pr-2">
                                                <p class="font-bold text-sm tracking-widest uppercase truncate" x-text="clientData?.name"></p>
                                            </div>
                                            <img src="{{ asset('img/nexusbanner.jpeg') }}" alt="Nexus ERP" class="h-4 object-contain rounded opacity-95 shrink-0">
                                        </div>
                                        
                                        <!-- Bottom: Puntos -->
                                        <div class="mt-auto relative z-10">
                                            <p class="text-[9px] text-purple-200 uppercase tracking-widest mb-0.5 opacity-80">Puntos Acumulados</p>
                                            <p class="font-black text-xl text-yellow-400 leading-none truncate" x-text="clientData?.points"></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <label class="block text-sm font-bold text-gray-700 mb-2">Método de Pago</label>
                                <select x-model="paymentMethod" class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-green-500 focus:border-green-500 block p-3 mb-4 font-medium shadow-sm">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta (Débito/Crédito)</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="mixto">Mixto (Efectivo/Tarjeta/Transferencia)</option>
                                </select>
                                
                                <div x-show="paymentMethod === 'mixto'" x-transition class="mb-4">
                                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-4 space-y-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Monto con Tarjeta</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 font-bold">$</span>
                                                </div>
                                                <input type="number" x-model.number="mixedCardAmount" @wheel="$event.target.blur()" class="bg-white border border-gray-300 text-gray-900 rounded-lg focus:ring-green-500 focus:border-green-500 block w-full pl-8 p-2.5 shadow-sm" placeholder="0.00" step="0.01">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Monto con Transferencia</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 font-bold">$</span>
                                                </div>
                                                <input type="number" x-model.number="mixedTransferAmount" @wheel="$event.target.blur()" class="bg-white border border-gray-300 text-gray-900 rounded-lg focus:ring-green-500 focus:border-green-500 block w-full pl-8 p-2.5 shadow-sm" placeholder="0.00" step="0.01">
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center bg-green-50 p-3 rounded-lg border border-green-100">
                                            <span class="text-sm font-bold text-green-800">Restante (Efectivo):</span>
                                            <span class="text-lg font-black text-green-700" x-text="formatMoney(mixedCashAmount)"></span>
                                        </div>
                                    </div>
                                    
                                    <template x-if="mixedCashAmount > 0">
                                        <div class="mt-4">
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Efectivo Recibido (Para calcular cambio)</label>
                                            <div class="relative mb-4">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-lg font-bold">$</span>
                                                </div>
                                                <input type="number" x-model.number="amountPaid" @wheel="$event.target.blur()" class="bg-white border border-gray-300 text-gray-900 text-2xl rounded-lg focus:ring-green-500 focus:border-green-500 block w-full pl-8 p-3 font-bold shadow-sm" placeholder="0.00" step="0.01">
                                            </div>
                                            
                                            <div class="bg-gray-100 p-4 rounded-xl flex justify-between items-center border border-gray-200">
                                                <span class="text-gray-600 font-bold uppercase tracking-wide text-sm">Su Cambio:</span>
                                                <span class="text-3xl font-black text-gray-900" :class="{'text-red-500': amountPaid > 0 && amountPaid < mixedCashAmount}" x-text="amountPaid ? formatMoney(mixedChangeAmount) : '$0.00'"></span>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="mixedCashAmount === 0 && (parseFloat(mixedCardAmount) > 0 || parseFloat(mixedTransferAmount) > 0)">
                                        <div class="p-3 mt-4 bg-emerald-50 text-emerald-800 text-sm font-bold rounded-xl border border-emerald-200 text-center flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Total cubierto sin restante en efectivo.
                                        </div>
                                    </template>
                                </div>
                                
                                <div x-show="paymentMethod === 'efectivo'" x-transition>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Monto Entregado (Efectivo)</label>
                                    <div class="relative mb-4">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-lg font-bold">$</span>
                                        </div>
                                        <input type="number" x-model.number="amountPaid" @wheel="$event.target.blur()" class="bg-white border border-gray-300 text-gray-900 text-2xl rounded-lg focus:ring-green-500 focus:border-green-500 block w-full pl-8 p-3 font-bold shadow-sm" placeholder="0.00" step="0.01">
                                    </div>
                                    
                                    <div class="bg-gray-100 p-4 rounded-xl flex justify-between items-center border border-gray-200">
                                        <span class="text-gray-600 font-bold uppercase tracking-wide text-sm">Su Cambio:</span>
                                        <span class="text-3xl font-black text-gray-900" :class="{'text-red-500': amountPaid > 0 && amountPaid < cartTotal}" x-text="amountPaid ? formatMoney(changeAmount) : '$0.00'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="button" @click="submitSale" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-green-600 text-base font-bold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors" :disabled="(paymentMethod === 'efectivo' && (amountPaid === '' || amountPaid < cartTotal)) || (paymentMethod === 'mixto' && mixedCashAmount > 0 && (amountPaid === '' || amountPaid < mixedCashAmount)) || isSubmittingSale">
                        <span x-show="!isSubmittingSale">Confirmar Pago</span>
                        <span x-show="isSubmittingSale">Procesando...</span>
                    </button>
                    <button type="button" @click="showPaymentModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- PASO PREVIO: Confirmación de Pago a Doctor -->
    <div x-show="showDoctorPaymentStep" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-doctor-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDoctorPaymentStep" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showDoctorPaymentStep"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100">
                <div class="bg-pink-50 border-b border-pink-100 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-pink-100 text-pink-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900" id="modal-doctor-title">Pago al Doctor</h3>
                            <p class="text-xs text-pink-600 font-medium">Servicios del turno</p>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-5 space-y-4">
                    <p class="text-sm text-gray-600">Por favor, confirma el pago de los servicios del doctor. Esto se descontará de la caja antes de ingresar el corte físico.</p>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                        <template x-for="svc in breakdownSummary.services_list" :key="svc.name">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100 text-sm">
                                <span class="text-gray-700">
                                    <span class="font-bold text-gray-900" x-text="svc.quantity + 'x '"></span>
                                    <span x-text="svc.name"></span>
                                </span>
                                <span class="font-bold text-gray-900" x-text="formatMoney(svc.amount)"></span>
                            </div>
                        </template>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-pink-50 rounded-xl border border-pink-200 mt-4">
                        <span class="font-bold text-pink-800 uppercase tracking-wide text-sm">Total a Pagar</span>
                        <span class="font-black text-pink-700 text-2xl" x-text="formatMoney(breakdownSummary.services_total)"></span>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="button" @click="confirmDoctorPayment" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-transparent shadow-sm px-6 py-3 bg-pink-600 text-base font-bold text-white hover:bg-pink-700 sm:ml-3 sm:w-auto sm:text-sm transition-colors disabled:opacity-50" :disabled="isConfirmingDoctor">
                        <svg x-show="!isConfirmingDoctor" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span x-show="!isConfirmingDoctor">Confirmar Pago</span>
                        <span x-show="isConfirmingDoctor">Procesando...</span>
                    </button>
                    <button type="button" @click="showDoctorPaymentStep = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors" :disabled="isConfirmingDoctor">
                        Cancelar Cierre
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- PASO 1: Modal de Corte Ciego (Conteo de Denominaciones) -->
    <div x-show="showBlindClose" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-blind-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showBlindClose" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showBlindClose"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">

                <!-- Header -->
                <div class="bg-gradient-to-r from-green-800 to-blue-700 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/15 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-lg" id="modal-blind-title">Corte de Caja</h3>
                            <p class="text-green-200 text-xs">Cuenta el efectivo sin ver el saldo del sistema.</p>
                        </div>
                        <div class="ml-auto text-right">
                            <p class="text-xs text-green-200 uppercase tracking-wider">Total Contado</p>
                            <p class="text-2xl font-black text-white" x-text="formatMoney(blindTotal)"></p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-5 max-h-[65vh] overflow-y-auto space-y-5">

                    <!-- Billetes -->
                    <div>
                        <p class="text-xs font-black text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Billetes
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <template x-for="denom in billDenominations" :key="denom.value">
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex flex-col items-center gap-2">
                                    <!-- Denominación + subtotal -->
                                    <div class="flex items-center justify-between w-full">
                                        <span class="text-xs font-black text-amber-700 uppercase tracking-wider">$<span x-text="denom.value % 1 === 0 ? denom.value : denom.value.toFixed(2)"></span></span>
                                        <span class="text-[10px] text-amber-600 font-bold" x-text="'= ' + formatMoney(denom.value * denom.qty)"></span>
                                    </div>
                                    <!-- Stepper -->
                                    <div class="flex items-center gap-1.5 w-full">
                                        <button type="button" @click="denom.qty = Math.max(0, denom.qty - 1)" class="w-10 h-10 sm:w-8 sm:h-8 flex-shrink-0 flex items-center justify-center rounded-lg bg-amber-200 text-amber-800 hover:bg-amber-300 text-lg font-black border border-amber-300 transition-colors select-none active:scale-95" tabindex="-1">−</button>
                                        <input type="number" min="0" x-model.number="denom.qty" @input="denom.qty = Math.max(0, parseInt($event.target.value) || 0)" class="flex-1 min-w-0 text-center text-base font-black border border-amber-300 rounded-lg bg-white focus:ring-2 focus:ring-amber-400 focus:border-amber-400 py-2 sm:py-1 px-1 transition-all" placeholder="0" @wheel.prevent>
                                        <button type="button" @click="denom.qty = denom.qty + 1" class="w-10 h-10 sm:w-8 sm:h-8 flex-shrink-0 flex items-center justify-center rounded-lg bg-amber-200 text-amber-800 hover:bg-amber-300 text-lg font-black border border-amber-300 transition-colors select-none active:scale-95" tabindex="-1">+</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Monedas -->
                    <div>
                        <p class="text-xs font-black text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"></path></svg>
                            Monedas
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <template x-for="denom in coinDenominations" :key="denom.value">
                                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2">
                                    <!-- Denominación + subtotal -->
                                    <div class="flex items-center justify-between w-full">
                                        <span class="text-xs font-black text-gray-600 uppercase tracking-wider">$<span x-text="denom.value % 1 === 0 ? denom.value : denom.value.toFixed(2)"></span></span>
                                        <span class="text-[10px] text-gray-500 font-bold" x-text="'= ' + formatMoney(denom.value * denom.qty)"></span>
                                    </div>
                                    <!-- Stepper -->
                                    <div class="flex items-center gap-1.5 w-full">
                                        <button type="button" @click="denom.qty = Math.max(0, denom.qty - 1)" class="w-10 h-10 sm:w-8 sm:h-8 flex-shrink-0 flex items-center justify-center rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 text-lg font-black border border-gray-300 transition-colors select-none active:scale-95" tabindex="-1">−</button>
                                        <input type="number" min="0" x-model.number="denom.qty" @input="denom.qty = Math.max(0, parseInt($event.target.value) || 0)" class="flex-1 min-w-0 text-center text-base font-black border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-gray-400 focus:border-gray-400 py-2 sm:py-1 px-1 transition-all" placeholder="0" @wheel.prevent>
                                        <button type="button" @click="denom.qty = denom.qty + 1" class="w-10 h-10 sm:w-8 sm:h-8 flex-shrink-0 flex items-center justify-center rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 text-lg font-black border border-gray-300 transition-colors select-none active:scale-95" tabindex="-1">+</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border-t border-blue-100 px-6 py-4 space-y-3">
                    <!-- Warning banner -->
                    <div class="flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-xl p-3">
                        <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <p class="text-xs font-bold text-amber-700">Verifica que la cifra del conteo sea correcta. Al pasar a la siguiente pantalla <span class="underline">no podrás volver</span> al conteo de denominaciones.</p>
                    </div>
                    <div class="flex justify-between gap-3">
                        <button type="button" @click="showBlindClose = false; resetDenominations()" class="inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="button" @click="proceedToBreakdown()" class="inline-flex items-center justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-blue-700 text-sm font-bold text-white hover:bg-blue-800 transition-colors gap-2">
                            Siguiente
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PASO 2: Modal de Desglose y Corte de Caja -->
    <div x-show="showSalesBreakdown" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showSalesBreakdown" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showSalesBreakdown"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">Corte de Caja del Turno</h3>
                            <div class="mt-4 space-y-4">
                                <div class="text-xs text-gray-500 font-bold uppercase tracking-wider">Desglose de Ventas</div>
                                
                                {{-- Total Ventas primero --}}
                                <div class="flex justify-between items-center p-3 bg-green-50 rounded-xl border border-green-200">
                                    <span class="font-bold text-green-800 uppercase tracking-wide">Total Ventas</span>
                                    <span class="font-black text-green-700 text-xl" x-text="formatMoney(sessionData.total_sales)"></span>
                                </div>

                                {{-- Métodos de pago después --}}
                                <div class="space-y-2 mt-2">
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                        <span class="font-bold text-gray-600">Efectivo</span>
                                        <span class="font-black text-gray-900" x-text="formatMoney(sessionData.cash_sales)"></span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                        <span class="font-bold text-gray-600">Tarjeta</span>
                                        <span class="font-black text-gray-900" x-text="formatMoney(sessionData.card_sales)"></span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                        <span class="font-bold text-gray-600">Transferencia</span>
                                        <span class="font-black text-gray-900" x-text="formatMoney(sessionData.transfer_sales)"></span>
                                    </div>
                                </div>

                                <!-- Servicios del Doctor (Total colapsable con clic) -->
                                <template x-if="breakdownSummary.services_list && breakdownSummary.services_list.length > 0">
                                    <div class="mt-4 border-t border-pink-100 pt-4">
                                        <button type="button"
                                                @click="showServicesDetail = !showServicesDetail"
                                                class="w-full flex justify-between items-center p-3 bg-pink-50 hover:bg-pink-100/80 rounded-xl border border-pink-200 transition-colors cursor-pointer text-left group">
                                            <div class="flex items-center gap-2.5">
                                                <div class="p-1.5 rounded-lg bg-pink-100 text-pink-600 group-hover:scale-105 transition-transform">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                </div>
                                                <div>
                                                    <span class="font-bold text-pink-800 text-sm block">Total Servicios</span>
                                                    <span class="text-[10px] text-pink-600 font-medium flex items-center gap-1">
                                                        <span x-text="showServicesDetail ? 'Ocultar desglose' : 'Clic para ver desglose'"></span>
                                                        <svg class="w-3 h-3 transition-transform duration-200" :class="showServicesDetail ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="font-black text-pink-700 text-base" x-text="'- ' + formatMoney(breakdownSummary.services_total)"></span>
                                        </button>

                                        <div x-show="showServicesDetail" x-transition class="mt-2 space-y-2 bg-pink-50/40 rounded-xl p-3 border border-pink-100 max-h-48 overflow-y-auto">
                                            <template x-for="svc in breakdownSummary.services_list" :key="svc.name">
                                                <div class="flex justify-between items-center text-xs border-b border-pink-100/70 pb-2 last:border-0 last:pb-0">
                                                    <span class="text-gray-700 font-medium">
                                                        <span class="font-bold text-pink-900" x-text="svc.quantity + 'x '"></span>
                                                        <span x-text="svc.name"></span>
                                                    </span>
                                                    <span class="font-bold text-pink-800" x-text="'- ' + formatMoney(svc.amount)"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Gastos Operativos del Día (Total colapsable con clic) -->
                                <template x-if="breakdownSummary.expenses_list && breakdownSummary.expenses_list.length > 0">
                                    <div class="border-t border-orange-100 pt-4">
                                        <button type="button"
                                                @click="showExpensesDetail = !showExpensesDetail"
                                                class="w-full flex justify-between items-center p-3 bg-orange-50 hover:bg-orange-100/80 rounded-xl border border-orange-200 transition-colors cursor-pointer text-left group">
                                            <div class="flex items-center gap-2.5">
                                                <div class="p-1.5 rounded-lg bg-orange-100 text-orange-600 group-hover:scale-105 transition-transform">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                </div>
                                                <div>
                                                    <span class="font-bold text-orange-800 text-sm block">Total Gastos (se descuentan)</span>
                                                    <span class="text-[10px] text-orange-600 font-medium flex items-center gap-1">
                                                        <span x-text="showExpensesDetail ? 'Ocultar desglose' : 'Clic para ver desglose'"></span>
                                                        <svg class="w-3 h-3 transition-transform duration-200" :class="showExpensesDetail ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="font-black text-orange-700 text-base" x-text="'- ' + formatMoney(breakdownSummary.expenses_total)"></span>
                                        </button>

                                        <div x-show="showExpensesDetail" x-transition class="mt-2 space-y-1.5 bg-orange-50/40 rounded-xl p-3 border border-orange-100 max-h-48 overflow-y-auto">
                                            <template x-for="exp in breakdownSummary.expenses_list" :key="exp.id">
                                                <div class="flex justify-between items-center text-xs border-b border-orange-100/70 pb-2 last:border-0 last:pb-0 text-gray-700">
                                                    <span class="font-medium truncate pr-2" x-text="exp.concept"></span>
                                                    <span class="font-bold text-red-600 shrink-0" x-text="'- ' + formatMoney(exp.amount)"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                
                                {{-- Cierre de Caja Físico --}}
                                <div class="mt-4 border-t border-gray-100 pt-4">
                                    <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-2">Cierre de Caja Físico</div>
                                    
                                    {{-- 1. Fondo Inicial --}}
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-200 mb-2.5">
                                        <span class="font-bold text-gray-600">Fondo Inicial</span>
                                        <span class="font-black text-gray-900" x-text="formatMoney(sessionData.starting_cash)"></span>
                                    </div>
                                    
                                    {{-- 2. Debería Haber (Arriba de efectivo contado y debajo de fondo inicial) --}}
                                    <div class="flex justify-between items-center p-3 bg-blue-50/70 rounded-lg border border-blue-200 mb-2.5">
                                        <div>
                                            <p class="font-bold text-blue-900 text-sm">Debería Haber</p>
                                            <p class="text-[10px] text-blue-600 mt-0.5">Fondo + Efectivo - Gastos - Servicios</p>
                                            <p class="text-[10px] text-amber-600 font-bold mt-0.5" x-show="doctorPendingAmount > 0" x-text="'Pago a doctor pendiente (sin efectivo en caja): ' + formatMoney(doctorPendingAmount)"></p>
                                        </div>
                                        <span class="font-black text-blue-900 text-lg" x-text="formatMoney(computedExpectedCash)"></span>
                                    </div>

                                    {{-- 3. Efectivo Físico Contado --}}
                                    <div class="flex justify-between items-center p-3 bg-indigo-50/60 rounded-lg border border-indigo-200 mb-2.5">
                                        <div>
                                            <p class="font-bold text-indigo-900 text-sm">Efectivo Físico Contado</p>
                                            <p class="text-[10px] text-indigo-500 mt-0.5">Registrado en el conteo de denominaciones</p>
                                        </div>
                                        <span class="font-black text-indigo-900 text-lg" x-text="formatMoney(actualCash)"></span>
                                    </div>
                                    
                                    {{-- 4. Diferencia --}}
                                    <div class="bg-gray-100 p-4 rounded-xl flex justify-between items-center border border-gray-200 mb-2.5" x-show="actualCash !== ''">
                                        <div>
                                            <span class="text-gray-700 font-bold uppercase tracking-wide text-xs">Diferencia:</span>
                                            <p class="text-[10px] text-gray-400 mt-0.5">Contado vs. Debería haber</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xl font-black block"
                                                :class="computedDifference < 0 ? 'text-red-600' : (computedDifference > 0 ? 'text-amber-600' : 'text-green-600')"
                                                x-text="formatMoney(computedDifference)">
                                            </span>
                                            <span class="text-[11px] font-bold"
                                                :class="computedDifference < 0 ? 'text-red-500' : (computedDifference > 0 ? 'text-amber-500' : 'text-green-600')"
                                                x-text="computedDifference < 0 ? 'Faltante' : (computedDifference > 0 ? 'Sobrante' : 'Cuadrado ($0)')">
                                            </span>
                                        </div>
                                    </div>

                                    {{-- 5. Dinero en la Lapicera (Debería haber menos Fondo Inicial) --}}
                                    <div class="p-3.5 bg-emerald-50 rounded-xl border border-emerald-200 flex justify-between items-center">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-extrabold text-emerald-900 text-sm">Dinero en la Lapicera</p>
                                                <p class="text-[10px] text-emerald-600 font-medium">
                                                    ("Debería haber" - "Fondo inicial")<template x-if="computedDifference > 0"><span class="font-bold text-emerald-700"> + sobrante</span></template>
                                                </p>
                                            </div>
                                        </div>
                                        <span class="text-lg font-black text-emerald-800" x-text="formatMoney(computedPencilCaseMoney)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end border-t border-gray-200">
                    <button type="button" @click="showConfirmClose = true" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-red-600 text-base font-bold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm transition-colors disabled:opacity-50" :disabled="isClosing">
                        <span x-show="!isClosing">Cerrar Turno</span>
                        <span x-show="isClosing">Cerrando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Cierre de Caja -->
    <div x-show="showConfirmClose" style="display: none;" class="fixed inset-0 z-[70] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showConfirmClose" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showConfirmClose"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">Confirmar Cierre de Turno</h3>
                            <div class="mt-2 text-sm text-gray-500 mb-4">
                                ¿Estás completamente seguro que deseas cerrar este turno de caja? Esta acción no se puede deshacer.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="button" @click="closeSession" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-red-600 text-base font-bold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors disabled:opacity-50" :disabled="isClosing">
                        <span x-show="!isClosing">Sí, Cerrar Turno</span>
                        <span x-show="isClosing">Procesando...</span>
                    </button>
                    <button type="button" @click="showConfirmClose = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Apertura de Caja -->
    <div x-show="showOpenSessionModal" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showOpenSessionModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showOpenSessionModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">Apertura de Turno</h3>
                            <div class="mt-2 text-sm text-gray-500 mb-4">
                                Para comenzar a cobrar en esta sucursal, debes ingresar el dinero base (fondo) que hay en caja.
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Fondo de Caja (Monto Inicial)</label>
                                <div class="relative mb-4">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-lg font-bold">$</span>
                                    </div>
                                    <input type="number" x-model.number="startingCash" @wheel="$event.target.blur()" @keydown.enter.prevent="openSession()" class="bg-white border border-gray-300 text-gray-900 text-2xl rounded-lg focus:ring-green-500 focus:border-green-500 block w-full pl-8 p-3 font-bold shadow-sm" placeholder="0.00" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="button" @click="openSession" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-green-600 text-base font-bold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors disabled:opacity-50" :disabled="startingCash === '' || isOpening">
                        <span x-show="!isOpening">Abrir Turno</span>
                        <span x-show="isOpening">Abriendo...</span>
                    </button>
                    <!-- Botón Cancelar (Disponible para todos) -->
                    <button type="button" @click="showOpenSessionModal = false; if(isMultiBranch) branchId = '';" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Gastos Operativos -->
    <div x-show="showExpensesModal" style="display: none;" class="fixed inset-0 z-[65] overflow-y-auto" aria-labelledby="gastos-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showExpensesModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" @click="showExpensesModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showExpensesModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">

                <!-- Header -->
                <div class="bg-orange-50 border-b border-orange-100 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-orange-100 text-orange-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900" id="gastos-modal-title">Gastos Operativos del Día</h3>
                            <p class="text-xs text-orange-600 font-medium">Se descontarán del corte de caja</p>
                        </div>
                    </div>
                    <button @click="showExpensesModal = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Formulario de Registro -->
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Concepto de Gasto</label>
                        <select x-model="expenseForm.expense_id" class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-colors bg-gray-50 p-3 text-sm">
                            <option value="">-- Selecciona un concepto --</option>
                            <template x-for="exp in availableExpenses" :key="exp.id">
                                <option :value="exp.id" x-text="exp.name"></option>
                            </template>
                        </select>
                        <p x-show="availableExpenses.length === 0" class="text-xs text-orange-500 mt-1">⚠️ No hay conceptos configurados. <a href="/expenses" target="_blank" class="underline font-bold">Ir a Catálogo</a></p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Monto Gastado ($)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold">$</span>
                            </div>
                            <input type="number" x-model="expenseForm.amount" @wheel="$event.target.blur()" step="0.01" min="0.01" placeholder="0.00"
                                class="w-full pl-8 rounded-xl border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-colors bg-gray-50 p-3 text-xl font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Notas (Opcional)</label>
                        <input type="text" x-model="expenseForm.notes" placeholder="Descripción breve..." class="w-full rounded-xl border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-colors bg-gray-50 p-3 text-sm">
                    </div>

                    <div x-show="expenseError" class="text-red-600 text-sm font-medium bg-red-50 rounded-xl p-3 border border-red-200" x-text="expenseError"></div>

                    <button type="button" @click="registerExpense()" :disabled="isRegisteringExpense || !expenseForm.expense_id || !expenseForm.amount"
                        class="w-full bg-orange-600 hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-3 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <svg x-show="!isRegisteringExpense" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span x-show="!isRegisteringExpense">Registrar Gasto</span>
                        <span x-show="isRegisteringExpense">Registrando...</span>
                    </button>
                </div>

                <!-- Log de Gastos del Turno -->
                <div class="border-t border-gray-100 px-6 py-4 bg-gray-50">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Gastos de Este Turno</p>
                        <span class="text-xs font-black text-orange-600" x-show="sessionExpensesTotal > 0" x-text="'Total: ' + formatMoney(sessionExpensesTotal)"></span>
                    </div>
                    <p x-show="expensesLog.length === 0" class="text-sm text-gray-400 text-center py-4">Ningún gasto registrado aún.</p>
                    <div class="space-y-2 max-h-44 overflow-y-auto">
                        <template x-for="log in expensesLog" :key="log.id">
                            <div class="flex items-center justify-between p-2.5 bg-white rounded-xl border border-gray-200 text-sm">
                                <div>
                                    <span class="font-bold text-gray-800" x-text="log.concept"></span>
                                    <span class="text-gray-400 text-xs ml-1" x-show="log.created_at" x-text="'• ' + log.created_at"></span>
                                    <p class="text-xs text-gray-500 mt-0.5" x-show="log.notes" x-text="log.notes"></p>
                                </div>
                                <span class="font-black text-orange-600 text-sm flex-shrink-0 ml-2" x-text="formatMoney(log.amount)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Script de Alpine para la Lógica del Punto de Venta -->

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        // Evitar que la rueda del mouse altere los montos en inputs numéricos
        document.addEventListener('wheel', function(e) {
            if (document.activeElement && document.activeElement.type === 'number') {
                document.activeElement.blur();
            }
        }, { passive: true });

        document.addEventListener('alpine:init', () => {
            Alpine.data('posSystem', () => ({
                products: [...@json($products).map(p => ({...p, is_service: false})), ...@json($services ?? []).map(s => ({...s, is_service: true}))],
                cart: [],
                searchQuery: '',
                branchId: '{{ auth()->user()->hasAnyRole(['superadmin', 'admin', 'compras']) ? '' : auth()->user()->branch_id }}',
                paymentMethod: 'efectivo',
                amountPaid: '',
                mixedCardAmount: '',
                mixedTransferAmount: '',
                showPaymentModal: false,
                clientBarcode: '',
                clientData: null,
                isSearchingClient: false,
                errorClient: null,

                // Multiticket state
                tickets: [],
                activeTicketId: null,
                isSubmittingSale: false,

                // Día y Turno actuales para verificación de promociones
                todayName: '{{ $todayName }}',
                currentShift: '{{ $shift }}',

                // Promo de Genéricos
                isGenericoPromo: {{ $isGenericoPromo ? 'true' : 'false' }},
                genericoDiscount: {{ $genericoDiscount }},
                
                // Promo Receta Completa
                isRecetaPromo: {{ $isRecetaPromo ? 'true' : 'false' }},
                recetaDiscount: {{ $recetaDiscount ?? 20 }},
                
                // Promo 4x3
                is4x3Promo: {{ $is4x3Promo ? 'true' : 'false' }},

                // Promo Volumen
                isVolumenPromo: {{ $isVolumenPromo ? 'true' : 'false' }},
                
                // Nuevas variables de Caja
                hasOpenSession: false,
                sessionData: { starting_cash: 0, cash_sales: 0, card_sales: 0, transfer_sales: 0, total_sales: 0 },
                showOpenSessionModal: false,
                showSalesBreakdown: false,
                weeklyProgress: { target: 0, sales: 0, shift: 'Matutino', percentage: 0 },
                startingCash: '',
                actualCash: '',
                isOpening: false,
                isClosing: false,
                showConfirmClose: false,
                preventAutoReopen: false,
                showBlindClose: false,
                showServicesDetail: false,
                showExpensesDetail: false,

                // === Gastos Operativos ===
                showExpensesModal: false,
                availableExpenses: [],
                expensesLog: [],
                sessionExpensesTotal: 0,
                expenseForm: { expense_id: '', amount: '', notes: '' },
                isRegisteringExpense: false,
                expenseError: null,

                // === Corte Ciego - Resumen ===
                breakdownSummary: {
                    services_total: 0, services_list: [],
                    expenses_total: 0, expenses_list: [],
                    doctor_paid: false, doctor_payment_amount: 0
                },
                isConfirmingDoctor: false,
                showDoctorPaymentStep: false,
                billDenominations: [
                    { value: 1000, qty: 0 },
                    { value: 500,  qty: 0 },
                    { value: 200,  qty: 0 },
                    { value: 100,  qty: 0 },
                    { value: 50,   qty: 0 },
                    { value: 20,   qty: 0 },
                ],
                coinDenominations: [
                    { value: 20,   qty: 0 },
                    { value: 10,   qty: 0 },
                    { value: 5,    qty: 0 },
                    { value: 2,    qty: 0 },
                    { value: 1,    qty: 0 },
                    { value: 0.50, qty: 0 },
                ],
                notification: { show: false, message: '', type: 'error' },
                cedulaMedica: '',
                
                isMultiBranch: {{ auth()->user()->hasAnyRole(['superadmin', 'admin', 'compras']) ? 'true' : 'false' }},
                
                showNotification(message, type = 'error') {
                    this.notification.message = message;
                    this.notification.type = type;
                    this.notification.show = true;
                    setTimeout(() => { this.notification.show = false; }, 4000);
                },
                
                init() {
                    this.loadTickets();
                    this.$watch('cart', () => this.syncAndSave(), { deep: true });
                    this.$watch('clientBarcode', () => this.syncAndSave());
                    this.$watch('clientData', () => this.syncAndSave());

                    if(this.branchId) {
                        this.checkSession();
                    }
                    this.$watch('branchId', (val) => {
                        this.cart = [];
                        if(val) this.checkSession();
                        else this.hasOpenSession = false;
                    });
                },
                
                loadTickets() {
                    let saved = localStorage.getItem('pos_tickets');
                    let ticketsToLoad = saved ? JSON.parse(saved) : [];
                    
                    const idToClear = '{{ session("clear_ui_ticket_id", "") }}';
                    if (idToClear) {
                        ticketsToLoad = ticketsToLoad.filter(t => t.id != idToClear);
                        localStorage.setItem('pos_tickets', JSON.stringify(ticketsToLoad));
                    }

                    if (ticketsToLoad.length > 0) {
                        this.tickets = ticketsToLoad;
                    } else {
                        this.addTicket();
                        return;
                    }
                    
                    this.loadTicket(this.tickets[0].id);
                },

                addTicket() {
                    const id = Date.now().toString();
                    this.tickets.push({
                        id: id,
                        name: 'Ticket ' + (this.tickets.length + 1),
                        cart: [],
                        clientBarcode: '',
                        clientData: null
                    });
                    if (!this.activeTicketId) {
                        this.loadTicket(id);
                    }
                },

                loadTicket(id) {
                    if (this.activeTicketId) {
                        this.syncAndSave(); // save current before switching
                    }
                    const t = this.tickets.find(x => x.id === id);
                    if (t) {
                        this.activeTicketId = t.id;
                        this.cart = JSON.parse(JSON.stringify(t.cart));
                        this.clientBarcode = t.clientBarcode;
                        this.clientData = t.clientData;
                    }
                },

                syncAndSave() {
                    if (!this.activeTicketId) return;
                    const idx = this.tickets.findIndex(x => x.id === this.activeTicketId);
                    if (idx !== -1) {
                        this.tickets[idx].cart = JSON.parse(JSON.stringify(this.cart));
                        this.tickets[idx].clientBarcode = this.clientBarcode;
                        this.tickets[idx].clientData = this.clientData;
                        localStorage.setItem('pos_tickets', JSON.stringify(this.tickets));
                    }
                },

                removeTicket(id) {
                    this.tickets = this.tickets.filter(x => x.id !== id);
                    if (this.tickets.length === 0) {
                        this.addTicket();
                    }
                    if (this.activeTicketId === id) {
                        this.activeTicketId = null; // force reload next
                        this.loadTicket(this.tickets[this.tickets.length - 1].id);
                    } else {
                        // We must save the updated tickets array if we delete a non-active ticket
                        localStorage.setItem('pos_tickets', JSON.stringify(this.tickets));
                    }
                },

                checkGoalCelebration() {
                    if (this.weeklyProgress && this.weeklyProgress.percentage >= 100 && this.weeklyProgress.target > 0) {
                        const today = new Date().toISOString().split('T')[0];
                        const cacheKey = `goal_celebrated_${this.branchId}_${this.weeklyProgress.shift}_${today}`;
                        if (!localStorage.getItem(cacheKey)) {
                            localStorage.setItem(cacheKey, 'true');
                            this.triggerConfetti();
                        }
                    }
                },
                
                triggerConfetti() {
                    if (typeof confetti !== 'function') return;

                    // Celebration sound via Web Audio API
                    try {
                        var ac = new (window.AudioContext || window.webkitAudioContext)();
                        [[523.25,0],[659.25,0.1],[783.99,0.2],[1046.5,0.3]].forEach(function(n) {
                            var o = ac.createOscillator(), g = ac.createGain();
                            o.type = 'triangle'; o.frequency.value = n[0];
                            g.gain.setValueAtTime(0, ac.currentTime + n[1]);
                            g.gain.linearRampToValueAtTime(0.18, ac.currentTime + n[1] + 0.04);
                            g.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + n[1] + 0.5);
                            o.connect(g); g.connect(ac.destination);
                            o.start(ac.currentTime + n[1]); o.stop(ac.currentTime + n[1] + 0.5);
                        });
                    } catch(e) {}
                    
                    var duration = 3000;
                    var animationEnd = Date.now() + duration;
                    var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };

                    function randomInRange(min, max) {
                        return Math.random() * (max - min) + min;
                    }

                    var interval = setInterval(function() {
                        var timeLeft = animationEnd - Date.now();

                        if (timeLeft <= 0) {
                            return clearInterval(interval);
                        }

                        var particleCount = 50 * (timeLeft / duration);
                        confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                        confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
                    }, 250);
                },
                
                async checkSession() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const res = await fetch('{{ route('cash_register.check') }}', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ branch_id: this.branchId })
                        });
                        const data = await res.json();
                        this.hasOpenSession = data.has_open_session;
                        if (data.weekly_progress) {
                            this.weeklyProgress = data.weekly_progress;
                            this.checkGoalCelebration();
                        }
                        if(this.hasOpenSession) {
                            this.sessionData = data.session;
                            this.sessionExpensesTotal = data.session.expenses_total || 0;
                            this.showOpenSessionModal = false;
                        } else {
                            this.sessionData = { starting_cash: 0, cash_sales: 0, card_sales: 0, transfer_sales: 0, total_sales: 0 };
                            this.sessionExpensesTotal = 0;
                            if(this.branchId && !this.preventAutoReopen) {
                                this.showOpenSessionModal = true;
                            }
                        }
                    } catch(e) { console.error('Error checking session', e); }
                },
                
                async openSession() {
                    if(this.isOpening) return;
                    if(!this.startingCash && this.startingCash !== 0) return;
                    this.isOpening = true;
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const res = await fetch('{{ route('cash_register.open') }}', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ branch_id: this.branchId, starting_cash: this.startingCash })
                        });
                        const data = await res.json();
                        if(data.success) {
                            if (data.weekly_progress) {
                                this.weeklyProgress = data.weekly_progress;
                            }
                            this.showOpenSessionModal = false;
                            this.startingCash = '';
                            this.showNotification('Turno abierto correctamente', 'success');
                            await this.checkSession();
                        } else {
                            this.showNotification(data.message || 'Error al abrir caja');
                        }
                    } catch(e) { 
                        console.error(e); 
                        this.showNotification('Ocurrió un error al procesar la solicitud.');
                    }
                    this.isOpening = false;
                },
                
                async closeSession() {
                    if(!this.actualCash && this.actualCash !== 0) return;
                    this.isClosing = true;
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const res = await fetch('{{ route('cash_register.close') }}', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ session_id: this.sessionData.id, actual_cash: this.actualCash })
                        });
                        const data = await res.json();
                        if(data.success) {
                            this.showConfirmClose = false;
                            this.showSalesBreakdown = false;
                            this.actualCash = '';
                            this.showNotification('Turno cerrado correctamente', 'success');
                            window.openCenteredPopup(data.redirect_url, 'Imprimir Corte', 450, 720);
                            
                            this.preventAutoReopen = true;
                            
                            await this.checkSession();
                        } else {
                            this.showNotification(data.message || 'Error al cerrar caja');
                        }
                    } catch(e) { 
                        console.error(e); 
                        this.showNotification('Ocurrió un error al procesar la solicitud.');
                    }
                    this.isClosing = false;
                },
                
                async handleSessionClick() {
                    if(this.hasOpenSession) {
                        // Cargar resumen de servicios y gastos PRIMERO
                        if (this.sessionData && this.sessionData.id) {
                            try {
                                const r = await fetch(`/cash-register/session-summary?session_id=${this.sessionData.id}`, {
                                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                                });
                                this.breakdownSummary = await r.json();
                            } catch(e) {}
                        }
                        
                        // Si hay servicios de doctor y no se ha pagado, mostrar confirmación
                        if (this.breakdownSummary.services_total > 0 && !this.breakdownSummary.doctor_paid) {
                            this.showDoctorPaymentStep = true;
                        } else {
                            this.proceedToBlindCut();
                        }
                    } else if(this.branchId || !this.isMultiBranch) {
                        this.preventAutoReopen = false;
                        this.showOpenSessionModal = true;
                    } else {
                        this.showNotification('Selecciona una sucursal primero.', 'info');
                    }
                },

                proceedToBlindCut() {
                    this.showDoctorPaymentStep = false;
                    this.resetDenominations();
                    this.showBlindClose = true;
                },
                
                get blindTotal() {
                    const bills = this.billDenominations.reduce((sum, d) => sum + (d.value * d.qty), 0);
                    const coins = this.coinDenominations.reduce((sum, d) => sum + (d.value * d.qty), 0);
                    return parseFloat((bills + coins).toFixed(2));
                },

                get doctorDueAmount() {
                    return parseFloat(this.breakdownSummary.doctor_paid 
                        ? (this.breakdownSummary.doctor_payment_amount || this.breakdownSummary.services_total || 0) 
                        : (this.breakdownSummary.services_total || 0));
                },

                get availableCash() {
                    const available = parseFloat(this.sessionData.starting_cash || 0)
                        + parseFloat(this.sessionData.cash_sales || 0)
                        - parseFloat(this.breakdownSummary.expenses_total || 0);
                    return Math.max(available, 0);
                },

                // Parte del pago al doctor que realmente sale de la caja (tope: efectivo disponible).
                get doctorCashPayment() {
                    return Math.min(this.doctorDueAmount, this.availableCash);
                },

                // Excedente del pago al doctor que no se cubre con efectivo (pendiente, fuera del cierre).
                get doctorPendingAmount() {
                    return parseFloat(Math.max(this.doctorDueAmount - this.doctorCashPayment, 0).toFixed(2));
                },

                get computedExpectedCash() {
                    return parseFloat((this.availableCash - this.doctorCashPayment).toFixed(2));
                },

                get computedDifference() {
                    return parseFloat(((parseFloat(this.actualCash || 0)) - Math.max(this.computedExpectedCash, 0)).toFixed(2));
                },

                get computedPencilCaseMoney() {
                    const startCash = parseFloat(this.sessionData.starting_cash || 0);
                    const base = this.computedExpectedCash - startCash;
                    const sobrante = this.computedDifference > 0 ? this.computedDifference : 0;
                    return Math.max(parseFloat((base + sobrante).toFixed(2)), 0);
                },

                resetDenominations() {
                    this.billDenominations.forEach(d => d.qty = 0);
                    this.coinDenominations.forEach(d => d.qty = 0);
                },

                proceedToBreakdown() {
                    // Trasladar el total contado al campo actualCash del paso 2
                    this.actualCash = this.blindTotal;
                    this.showBlindClose = false;
                    this.showServicesDetail = false;
                    this.showExpensesDetail = false;
                    this.showSalesBreakdown = true;

                    // El resumen de servicios y gastos ya se cargó en handleSessionClick
                    if (this.sessionData && this.sessionData.id) {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                        // Audit log (silencioso)
                        fetch('{{ route('cash_register.log_audit') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({ session_id: this.sessionData.id, actual_cash: this.blindTotal })
                        }).catch(() => {});
                    }
                },

                async openExpensesModal() {
                    this.showExpensesModal = true;
                    this.expenseForm = { expense_id: '', amount: '', notes: '' };
                    this.expenseError = null;
                    if (this.availableExpenses.length === 0) {
                        try {
                            const r = await fetch('/expenses/api-list', { headers: { 'Accept': 'application/json' } });
                            this.availableExpenses = await r.json();
                        } catch(e) {}
                    }
                    // Recargar log de gastos de la sesión actual
                    if (this.sessionData && this.sessionData.id) {
                        try {
                            const r = await fetch(`/cash-register/session-summary?session_id=${this.sessionData.id}`, { headers: { 'Accept': 'application/json' } });
                            const d = await r.json();
                            this.expensesLog = d.expenses_list || [];
                            this.sessionExpensesTotal = d.expenses_total || 0;
                            this.breakdownSummary.services_total = d.services_total || 0;
                        } catch(e) {}
                    }
                },

                async registerExpense() {
                    if (!this.expenseForm.expense_id || !this.expenseForm.amount) {
                        this.expenseError = 'Selecciona un concepto e ingresa el monto.';
                        return;
                    }

                    // === NUEVA VALIDACIÓN: límite de gasto ===
                    const cashSales = parseFloat(this.sessionData.cash_sales || 0);
                    const startingCash = parseFloat(this.sessionData.starting_cash || 0);
                    const alreadySpent = parseFloat(this.sessionExpensesTotal || 0);
                    const servicesReserve = parseFloat(this.breakdownSummary.services_total || 0);
                    
                    const availableCash = startingCash + cashSales - alreadySpent - servicesReserve;
                    const newExpenseAmount = parseFloat(this.expenseForm.amount || 0);

                    if (newExpenseAmount > availableCash) {
                        this.expenseError = 'El monto del gasto supera el efectivo disponible en caja en este momento. Ajusta el monto o cancela la operación.';
                        return;
                    }
                    // === FIN VALIDACIÓN ===

                    this.isRegisteringExpense = true;
                    this.expenseError = null;
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const branchId = this.branchId || '{{ auth()->user()->branch_id }}';
                        const r = await fetch('{{ route('cash_register.daily_expense') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({ expense_id: this.expenseForm.expense_id, amount: this.expenseForm.amount, notes: this.expenseForm.notes, branch_id: branchId })
                        });
                        const data = await r.json();
                        if (data.success) {
                            this.expensesLog.unshift(data.expense);
                            this.sessionExpensesTotal = data.total_expenses;
                            this.expenseForm = { expense_id: '', amount: '', notes: '' };
                        } else {
                            this.expenseError = data.message || 'Error al registrar el gasto.';
                        }
                    } catch(e) {
                        this.expenseError = 'Ocurrió un error al registrar el gasto.';
                    }
                    this.isRegisteringExpense = false;
                },

                async confirmDoctorPayment() {
                    this.isConfirmingDoctor = true;
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const r = await fetch('{{ route('cash_register.confirm_doctor_payment') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({ session_id: this.sessionData.id, amount: this.breakdownSummary.services_total })
                        });
                        const data = await r.json();
                        if (data.success) {
                            this.breakdownSummary.doctor_paid = true;
                            this.breakdownSummary.doctor_payment_amount = data.doctor_payment_amount;
                            // Si estamos en el paso previo al corte ciego, continuar
                            if (this.showDoctorPaymentStep) {
                                this.proceedToBlindCut();
                            }
                        } else {
                            this.showNotification(data.message || 'Error al confirmar pago.');
                        }
                    } catch(e) {
                        this.showNotification('Ocurrió un error al confirmar el pago al doctor.');
                    }
                    this.isConfirmingDoctor = false;
                },

                get filteredProducts() {
                    const lower = this.searchQuery.trim().toLowerCase();
                    
                    // En reposo (sin búsqueda): mostrar los Top 40 productos más populares con stock
                    if (lower === '') {
                        return this.products.filter(p => {
                            if (p.is_service) return true;
                            if (p.total_stock !== undefined && (!this.isMultiBranch || !this.branchId)) return p.total_stock > 0;
                            return this.getTotalStock(p) > 0;
                        }).slice(0, 40);
                    }
                    
                    // Al buscar o escanear: buscar en TODO el catálogo (1669 productos) sin paginación
                    return this.products.filter(p => {
                        const hasStock = p.is_service || (p.total_stock !== undefined && (!this.isMultiBranch || !this.branchId) ? p.total_stock > 0 : this.getTotalStock(p) > 0);
                        if (!hasStock) return false;
                        return (p.name && p.name.toLowerCase().includes(lower)) || 
                               (p.sku && p.sku.toLowerCase().includes(lower));
                    }).slice(0, 80); // Límite amplio para no saturar el DOM ante búsquedas de una sola letra
                },
                
                getPrice(product) {
                    if (product.is_service) return parseFloat(product.price);
                    if (product.base_price !== undefined && (!this.isMultiBranch || !this.branchId)) return product.base_price;
                    if (!product.batches || product.batches.length === 0) return 0;
                    const validBatches = this.branchId ? product.batches.filter(b => b.branch_id == this.branchId) : product.batches;
                    if (validBatches.length === 0) return 0;
                    return parseFloat(validBatches[0].selling_price);
                },

                isProduct4x3Active(product) {
                    if (!product || !product.is_promo_4x3) return false;
                    if (product.is_promo_4x3_active !== undefined && (!this.isMultiBranch || !this.branchId)) {
                        return product.is_promo_4x3_active;
                    }
                    const days = product.promo_4x3_days || [];
                    const shifts = product.promo_4x3_shifts || [];
                    const branches = product.promo_4x3_branches || [];

                    if (days.length === 0 || shifts.length === 0 || branches.length === 0) return false;
                    if (!days.includes(this.todayName) || !shifts.includes(this.currentShift)) return false;

                    if (branches.includes('all')) return true;
                    if (this.branchId && branches.includes(String(this.branchId))) return true;
                    if (!this.branchId && !this.isMultiBranch) return true;

                    return false;
                },

                isProductVolumenActive(product) {
                    if (!product || !product.is_promo_volumen) return false;
                    if (product.is_promo_volumen_active !== undefined && (!this.isMultiBranch || !this.branchId)) {
                        return product.is_promo_volumen_active;
                    }
                    const days = product.promo_volumen_days || [];
                    const shifts = product.promo_volumen_shifts || [];
                    const branches = product.promo_volumen_branches || [];

                    if (days.length === 0 || shifts.length === 0 || branches.length === 0) return false;
                    if (!days.includes(this.todayName) || !shifts.includes(this.currentShift)) return false;

                    if (branches.includes('all')) return true;
                    if (this.branchId && branches.includes(String(this.branchId))) return true;
                    if (!this.branchId && !this.isMultiBranch) return true;

                    return false;
                },

                isProductExcluded(product) {
                    return this.isProduct4x3Active(product) || this.isProductVolumenActive(product);
                },

                getDiscountedPrice(product) {
                    const base = this.getPrice(product);
                    if (product.is_service || product.type !== 'GEN') return base;
                    if (this.isProductExcluded(product)) return base;
                    
                    let bestDiscount = 0;
                    if (this.isGenericoPromo) {
                        bestDiscount = this.genericoDiscount;
                    }
                    if (this.isRecetaPromo && product.applyRecetaCompleta) {
                        bestDiscount = Math.max(bestDiscount, this.recetaDiscount);
                    }
                    
                    if (bestDiscount > 0) {
                        return parseFloat((base * (1 - bestDiscount / 100)).toFixed(2));
                    }
                    return base;
                },
                
                getTotalStock(product) {
                    if (product.is_service) return '∞';
                    if (product.total_stock !== undefined && (!this.isMultiBranch || !this.branchId)) return product.total_stock;
                    if(!product.batches || product.batches.length === 0) return 0;
                    if (this.isMultiBranch && !this.branchId) return 0;
                    return product.batches.reduce((sum, b) => {
                        if (this.branchId && b.branch_id != this.branchId) return sum;
                        return sum + parseInt(b.stock);
                    }, 0);
                },
                
                formatMoney(amount) {
                    return '$' + parseFloat(amount).toFixed(2);
                },
                
                addToCart(product) {
                    if(!this.hasOpenSession) {
                        this.showNotification('Debes abrir un turno de caja antes de agregar productos.', 'info');
                        this.handleSessionClick();
                        return;
                    }
                    const existing = this.cart.find(item => item.id === product.id && item.is_service === product.is_service);
                    const maxStock = product.is_service ? Infinity : this.getTotalStock(product);
                    
                    if (existing) {
                        if (existing.quantity < maxStock) {
                            existing.quantity++;
                            this.playBeep();
                        } else {
                            this.showNotification('Stock máximo alcanzado para este producto.', 'error');
                        }
                    } else {
                        if (maxStock > 0 || product.is_service) {
                            const basePrice  = this.getPrice(product);
                            let finalPrice = basePrice;
                            let discountPct = 0;
                            const isExcluded = this.isProductExcluded(product);
                            
                            // Initialize with applyRecetaCompleta = false
                            // cartKey is unique per item type: 'p-{id}' for products, 's-{id}' for services
                            // This prevents Alpine.js x-for key collisions when a product and a service share the same DB id
                            let newItem = {
                                ...product,
                                cartKey: (product.is_service ? 's' : 'p') + '-' + product.id,
                                applyRecetaCompleta: false,
                                quantity: 1,
                                maxStock: maxStock
                            };
                            
                            if (!product.is_service && product.type === 'GEN' && !isExcluded) {
                                if (this.isGenericoPromo) {
                                    discountPct = this.genericoDiscount;
                                }
                                if (discountPct > 0) {
                                    finalPrice = parseFloat((basePrice * (1 - discountPct / 100)).toFixed(2));
                                }
                            }

                            // Si tiene 4x3 o Volumen activo hoy, su precio unitario es basePrice (la promo aplica por su propia regla)
                            if (isExcluded) {
                                finalPrice = basePrice;
                                discountPct = 0;
                            }

                            newItem.price = finalPrice;
                            newItem.originalPrice = (finalPrice < basePrice) ? basePrice : null;
                            newItem.discount_pct = (finalPrice < basePrice) ? discountPct : null;

                            this.cart.push(newItem);
                            this.playBeep();
                        } else {
                            this.showNotification('Producto sin stock en esta sucursal.', 'error');
                        }
                    }
                    this.searchQuery = '';
                },
                
                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },
                
                increment(index) {
                    if (this.cart[index].quantity < this.cart[index].maxStock) {
                        this.cart[index].quantity++;
                    }
                },
                
                decrement(index) {
                    if (this.cart[index].quantity > 1) {
                        this.cart[index].quantity--;
                    } else {
                        this.removeFromCart(index);
                    }
                },
                
                toggleReceta(index) {
                    let item = this.cart[index];
                    if (this.isProductExcluded(item)) {
                        item.applyRecetaCompleta = false;
                        return;
                    }

                    item.applyRecetaCompleta = !item.applyRecetaCompleta;
                    
                    const basePrice = this.getPrice(item);
                    let finalPrice = this.getDiscountedPrice(item);
                    let hasPromo = finalPrice < basePrice;
                    
                    item.price = finalPrice;
                    item.originalPrice = hasPromo ? basePrice : null;
                    
                    if (hasPromo) {
                        let bestDiscount = 0;
                        if (this.isGenericoPromo) bestDiscount = this.genericoDiscount;
                        if (this.isRecetaPromo && item.applyRecetaCompleta) bestDiscount = Math.max(bestDiscount, this.recetaDiscount);
                        item.discount_pct = bestDiscount;
                    } else {
                        item.discount_pct = null;
                    }
                },
                
                getLineTotal(item) {
                    const basePrice = this.getPrice(item);
                    if (this.isProduct4x3Active(item)) {
                         const freeQty = Math.floor(item.quantity / 4);
                         const paidQty = item.quantity - freeQty;
                         return basePrice * paidQty;
                    }
                    if (this.isProductVolumenActive(item)) {
                         const minQty = parseInt(item.promo_volumen_min_qty) || 2;
                         if (item.quantity >= minQty && item.promo_volumen_price) {
                             return parseFloat(item.promo_volumen_price) * item.quantity;
                         }
                         return basePrice * item.quantity;
                    }
                    return item.price * item.quantity;
                },
                
                get cartTotal() {
                    return this.cart.reduce((sum, item) => {
                        const lineTotal = this.getLineTotal(item);
                        item.line_total = lineTotal; // Save for backend
                        return sum + lineTotal;
                    }, 0);
                },
                
                get mixedCashAmount() {
                    let rem = this.cartTotal - (parseFloat(this.mixedCardAmount) || 0) - (parseFloat(this.mixedTransferAmount) || 0);
                    return rem > 0 ? parseFloat(rem.toFixed(2)) : 0;
                },
                
                get changeAmount() {
                    if (this.paymentMethod !== 'efectivo' || !this.amountPaid) return 0;
                    const change = parseFloat(this.amountPaid) - this.cartTotal;
                    return change > 0 ? change : 0;
                },
                
                get mixedChangeAmount() {
                    const change = parseFloat(this.amountPaid || 0) - this.mixedCashAmount;
                    return change > 0 ? parseFloat(change.toFixed(2)) : 0;
                },
                
                triggerCheckout() {
                    if (this.cart.length === 0) return;
                    if(!this.hasOpenSession) {
                        this.showNotification('Debes abrir un turno de caja antes de cobrar.', 'error');
                        return;
                    }
                    this.amountPaid = '';
                    this.mixedCardAmount = '';
                    this.mixedTransferAmount = '';
                    this.showPaymentModal = true;
                },
                
                async submitSale() {
                    if (this.cart.length === 0 || this.isSubmittingSale) return;
                    
                    if (this.paymentMethod === 'efectivo' && (this.amountPaid < this.cartTotal)) {
                        this.showNotification('El monto pagado es menor al total.', 'error');
                        return;
                    }
                    
                    if (this.paymentMethod === 'mixto' && this.mixedCashAmount > 0 && (this.amountPaid < this.mixedCashAmount)) {
                        this.showNotification('El efectivo recibido es menor al monto restante en efectivo.', 'error');
                        return;
                    }

                    let hasReceta = false;
                    if (this.isRecetaPromo) {
                        hasReceta = this.cart.some(item => item.applyRecetaCompleta === true);
                    }

                    let amountPaidVal = this.amountPaid || 0;
                    let changeAmountVal = this.changeAmount;
                    let mixedCashVal = 0;
                    let mixedCardVal = 0;
                    let mixedTransferVal = 0;

                    if (this.paymentMethod === 'mixto') {
                        mixedCashVal = this.mixedCashAmount;
                        mixedCardVal = parseFloat(this.mixedCardAmount) || 0;
                        mixedTransferVal = parseFloat(this.mixedTransferAmount) || 0;
                        amountPaidVal = this.mixedCashAmount > 0 ? (this.amountPaid || 0) : 0;
                        changeAmountVal = this.mixedCashAmount > 0 ? this.mixedChangeAmount : 0;
                    }

                    const payload = {
                        cart: JSON.stringify(this.cart),
                        payment_method: this.paymentMethod,
                        amount_paid: amountPaidVal,
                        change_amount: changeAmountVal,
                        client_barcode: this.clientBarcode ? this.clientBarcode : null,
                        mixed_cash: mixedCashVal,
                        mixed_card: mixedCardVal,
                        mixed_transfer: mixedTransferVal,
                        has_receta_completa: hasReceta ? '1' : '0',
                        cedula_medica: this.cedulaMedica ? this.cedulaMedica.trim() : null,
                        branch_id: this.branchId || null
                    };

                    this.isSubmittingSale = true;

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const response = await fetch('{{ route('sales.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            const soldItems = JSON.parse(JSON.stringify(this.cart));

                            // Cerrar modal de pago y limpiar campos
                            this.showPaymentModal = false;
                            this.amountPaid = '';
                            this.mixedCardAmount = '';
                            this.mixedTransferAmount = '';
                            this.clientBarcode = '';
                            this.clientData = null;

                            // Abrir ventana emergente del ticket
                            if (data.ticket_url) {
                                window.openCenteredPopup(data.ticket_url, "TicketWindow", 450, 720);
                            }

                            // Descontar inventario local en memoria de los productos
                            soldItems.forEach(item => {
                                if (item.is_service) return;
                                const p = this.products.find(prod => !prod.is_service && prod.id === item.id);
                                if (p) {
                                    if (p.total_stock !== undefined) {
                                        p.total_stock = Math.max(0, p.total_stock - item.quantity);
                                    }
                                    if (p.batches && Array.isArray(p.batches)) {
                                        let remaining = item.quantity;
                                        for (let b of p.batches) {
                                            if (remaining <= 0) break;
                                            const take = Math.min(b.stock, remaining);
                                            b.stock -= take;
                                            remaining -= take;
                                        }
                                    }
                                }
                            });

                            // Eliminar el ticket cobrado del multiticket
                            const currentTicketId = this.activeTicketId;
                            this.removeTicket(currentTicketId);

                            // Notificación de éxito
                            this.showNotification('Venta ' + (data.ticket_id || '') + ' registrada exitosamente.', 'success');

                            // Actualizar balance de corte y metas en segundo plano
                            await this.checkSession();
                        } else {
                            this.showNotification(data.message || 'Error al procesar la venta.', 'error');
                        }
                    } catch (err) {
                        console.error('Error al procesar la venta:', err);
                        this.showNotification('Ocurrió un error al procesar el cobro. Revisa tu conexión.', 'error');
                    } finally {
                        this.isSubmittingSale = false;
                    }
                },
                
                handleScan(sku) {
                    if(!this.hasOpenSession) {
                        this.showNotification('Abre un turno de caja antes de escanear productos.', 'info');
                        return;
                    }
                    this.searchQuery = sku;
                    const match = this.products.find(p => p.sku === sku);
                    if (match) {
                        this.addToCart(match);
                    } else {
                        this.showNotification('No se encontró el producto con SKU: ' + sku, 'error');
                    }
                },
                
                searchClient() {
                    if(!this.clientBarcode) return;
                    this.isSearchingClient = true;
                    this.errorClient = null;
                    fetch(`/users/by-barcode/${this.clientBarcode}`)
                        .then(res => {
                            if (!res.ok) throw new Error('No encontrado');
                            return res.json();
                        })
                        .then(data => {
                            this.clientData = data;
                            this.clientBarcode = data.barcode;
                        })
                        .catch(err => {
                            this.errorClient = 'No se encontró un cliente con este código.';
                            this.clientBarcode = '';
                        })
                        .finally(() => {
                            this.isSearchingClient = false;
                        });
                },
                
                playBeep() {
                    if(!window.globalAudioCtx) {
                        window.globalAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    }
                    if (window.globalAudioCtx.state === 'suspended') {
                        window.globalAudioCtx.resume();
                    }
                    const osc = window.globalAudioCtx.createOscillator();
                    const gain = window.globalAudioCtx.createGain();
                    osc.connect(gain);
                    gain.connect(window.globalAudioCtx.destination);
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(800, window.globalAudioCtx.currentTime);
                    gain.gain.setValueAtTime(0.1, window.globalAudioCtx.currentTime);
                    osc.start();
                    gain.gain.exponentialRampToValueAtTime(0.00001, window.globalAudioCtx.currentTime + 0.1);
                    osc.stop(window.globalAudioCtx.currentTime + 0.1);
                }
            }));
        });
    </script>

    {{-- Modales de escáner de cámara comentados para optimización
    <x-barcode-scanner-modal targetModel="posSearch" />
    <x-barcode-scanner-modal targetModel="clientSearch" />
    --}}
    
    @if(session('ticket_id'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            window.openCenteredPopup("{{ route('sales.ticket', session('ticket_id')) }}", "TicketWindow", 450, 720);
        });
    </script>
    @endif
</x-app-layout>