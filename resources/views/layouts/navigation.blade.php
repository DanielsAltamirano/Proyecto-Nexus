<nav x-data="{ open: false }" class="bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm sticky top-0 z-50 transition-all duration-300">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">

            {{-- LOGO - Izquierda --}}
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="transition transform hover:scale-105 duration-300">
                        <img src="{{ asset('img/nexusbanner.jpeg') }}" alt="Nexus ERP" class="h-12 w-auto rounded-lg mix-blend-multiply drop-shadow-sm">
                    </a>
                </div>

                {{-- LINKS CENTRO --}}
                <div class="hidden space-x-1 xl:space-x-2 lg:ms-4 xl:ms-10 lg:flex items-center">
                    <x-nav-link :href="url('/')" :active="request()->is('/')">
                        {{ __('Inicio') }}
                    </x-nav-link>
                    @if(!auth()->check() || auth()->user()->hasRole(['cliente', 'admin', 'superadmin']) || auth()->user()->roles->isEmpty())
                    <x-nav-link :href="route('nosotros')" :active="request()->routeIs('nosotros')">
                        {{ __('Nosotros') }}
                    </x-nav-link>
                    <x-nav-link :href="route('sucursales')" :active="request()->routeIs('sucursales')">
                        {{ __('Sucursales') }}
                    </x-nav-link>
                    <x-nav-link :href="route('vacancies.public.index')" :active="request()->routeIs('vacancies.public.index')">
                        {{ __('Vacantes') }}
                    </x-nav-link>
                    @endif
                    {{-- 
                    <x-nav-link :href="route('tienda')" :active="request()->routeIs('tienda')">
                        {{ __('Tienda') }}
                    </x-nav-link> 
                    --}}

                    @auth
                    @if(auth()->user()->hasRole(['admin', 'superadmin', 'Personal de Mostrador', 'compras']))
                        <div class="hidden lg:flex lg:items-center">
                            <x-dropdown align="right" width="w-64">
                                <x-slot name="trigger">
                                    <button class="{{ request()->routeIs('pos') || request()->routeIs('sales.*') || request()->routeIs('metas.*') || request()->routeIs('promos.*') ? 'inline-flex items-center px-3 py-2 rounded-xl bg-green-50 text-green-800 font-bold shadow-sm border border-green-100 transition-all duration-200' : 'inline-flex items-center px-3 py-2 rounded-xl text-gray-500 font-medium border border-transparent hover:text-green-700 hover:bg-gray-50 hover:shadow-sm transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none' }}">
                                        <div>Ventas</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="p-2 space-y-1">
                                        {{-- Punto de Venta: todos los roles autorizados --}}
                                        <a href="{{ route('pos') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-green-50 group">
                                            <div class="flex-shrink-0 bg-green-100 text-green-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-green-700 transition-colors">Punto de Venta</p>
                                                <p class="text-xs text-gray-500 mt-1">Caja y cobro de productos</p>
                                            </div>
                                        </a>

                                        {{-- Ventas e Historial: solo admin y superadmin --}}
                                        @if(auth()->user()->hasRole(['admin', 'superadmin']))
                                        <a href="{{ route('sales.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-blue-50 group">
                                            <div class="flex-shrink-0 bg-blue-100 text-blue-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors">Historial de Ventas</p>
                                                <p class="text-xs text-gray-500 mt-1">Registro de transacciones</p>
                                            </div>
                                        </a>

                                        <a href="{{ route('metas.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-purple-50 group">
                                            <div class="flex-shrink-0 bg-purple-100 text-purple-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-purple-700 transition-colors">Metas de Venta</p>
                                                <p class="text-xs text-gray-500 mt-1">Objetivos por sucursal y turno</p>
                                            </div>
                                        </a>

                                        <a href="{{ route('turnos.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-indigo-50 group">
                                            <div class="flex-shrink-0 bg-indigo-100 text-indigo-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">Turnos</p>
                                                <p class="text-xs text-gray-500 mt-1">Historial de cortes y auditoría</p>
                                            </div>
                                        </a>

                                        <a href="{{ route('promos.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-orange-50 group">
                                            <div class="flex-shrink-0 bg-orange-100 text-orange-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z"/></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-orange-700 transition-colors">Promos</p>
                                                <p class="text-xs text-gray-500 mt-1">Gestión de promociones activas</p>
                                            </div>
                                        </a>
                                        @endif
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif



                    @if(auth()->user()->hasRole(['admin', 'superadmin', 'Personal de Mostrador', 'compras', 'Doctor']))
                        @if(auth()->user()->hasRole(['admin', 'superadmin', 'compras', 'Doctor']))
                        <div class="hidden lg:flex lg:items-center">
                            <x-dropdown align="right" width="w-64">
                                <x-slot name="trigger">
                                    <button class="{{ request()->routeIs('providers.*') || request()->routeIs('products.*') || request()->routeIs('categories.*') || request()->routeIs('services.*') || request()->routeIs('expenses.*') ? 'inline-flex items-center px-3 py-2 rounded-xl bg-green-50 text-green-800 font-bold shadow-sm border border-green-100 transition-all duration-200' : 'inline-flex items-center px-3 py-2 rounded-xl text-gray-500 font-medium border border-transparent hover:text-green-700 hover:bg-gray-50 hover:shadow-sm transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none' }}">
                                        <div>Catálogo</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="p-2 space-y-1">
                                        @if(auth()->user()->hasRole(['admin', 'superadmin', 'compras']))
                                        <a href="{{ route('providers.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-orange-50 group">
                                            <div class="flex-shrink-0 bg-orange-100 text-orange-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-orange-700 transition-colors">Proveedores</p>
                                                <p class="text-xs text-gray-500 mt-1">Directorio de proveedores</p>
                                            </div>
                                        </a>

                                        <a href="{{ route('categories.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-blue-50 group">
                                            <div class="flex-shrink-0 bg-blue-100 text-blue-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors">Categorías</p>
                                                <p class="text-xs text-gray-500 mt-1">Clasificación de productos</p>
                                            </div>
                                        </a>

                                        <a href="{{ route('products.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-purple-50 group">
                                            <div class="flex-shrink-0 bg-purple-100 text-purple-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-purple-700 transition-colors">Productos</p>
                                                <p class="text-xs text-gray-500 mt-1">Catálogo de productos</p>
                                            </div>
                                        </a>
                                        @endif

                                        {{-- Servicios --}}
                                        @if(auth()->user()->hasRole(['admin', 'superadmin', 'Doctor']))
                                        <a href="{{ route('services.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-pink-50 group">
                                            <div class="flex-shrink-0 bg-pink-100 text-pink-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-pink-700 transition-colors">Servicios</p>
                                                <p class="text-xs text-gray-500 mt-1">Catálogo de servicios y precios</p>
                                            </div>
                                        </a>
                                        @endif

                                        {{-- Gastos Operativos --}}
                                        @if(auth()->user()->hasRole(['admin', 'superadmin']))
                                        <a href="{{ route('expenses.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-orange-50 group">
                                            <div class="flex-shrink-0 bg-orange-100 text-orange-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-orange-700 transition-colors">Gastos Operativos</p>
                                                <p class="text-xs text-gray-500 mt-1">Conceptos de gastos del día</p>
                                            </div>
                                        </a>
                                        @endif
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                        @endif

                        @if(auth()->user()->hasRole(['admin', 'superadmin', 'Personal de Mostrador', 'compras']))
                        <div class="hidden lg:flex lg:items-center">
                            <x-dropdown align="right" width="w-64">
                                <x-slot name="trigger">
                                    <button class="{{ request()->routeIs('batches.*') || request()->routeIs('transfers.*') || request()->routeIs('purchase-orders.*') ? 'inline-flex items-center px-3 py-2 rounded-xl bg-green-50 text-green-800 font-bold shadow-sm border border-green-100 transition-all duration-200' : 'inline-flex items-center px-3 py-2 rounded-xl text-gray-500 font-medium border border-transparent hover:text-green-700 hover:bg-gray-50 hover:shadow-sm transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none' }}">
                                        <div>Inventario</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="p-2 space-y-1">
                                        <a href="{{ route('batches.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-teal-50 group">
                                            <div class="flex-shrink-0 bg-teal-100 text-teal-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-teal-700 transition-colors">Existencias</p>
                                                <p class="text-xs text-gray-500 mt-1">Inventario actual</p>
                                            </div>
                                        </a>

                                        <a href="{{ route('transfers.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-yellow-50 group">
                                            <div class="flex-shrink-0 bg-yellow-100 text-yellow-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-yellow-700 transition-colors">Traspasos</p>
                                                <p class="text-xs text-gray-500 mt-1">Movimiento entre sedes</p>
                                            </div>
                                        </a>

                                        <a href="{{ route('purchase-orders.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-indigo-50 group">
                                            <div class="flex-shrink-0 bg-indigo-100 text-indigo-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">Compras</p>
                                                <p class="text-xs text-gray-500 mt-1">Órdenes a proveedores</p>
                                            </div>
                                        </a>

                                        @if(auth()->user()->hasRole(['admin', 'superadmin']))
                                        <a href="{{ route('batches.deletions') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-red-50 group">
                                            <div class="flex-shrink-0 bg-red-100 text-red-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-red-700 transition-colors">Bajas</p>
                                                <p class="text-xs text-gray-500 mt-1">Historial de eliminaciones</p>
                                            </div>
                                        </a>
                                        @endif
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                        @endif
                    @endif

                    @if(auth()->user()->hasRole(['admin', 'superadmin', 'compras', 'Personal de Mostrador', 'Doctor']))
                        <div class="hidden lg:flex lg:items-center">
                            <x-dropdown align="right" width="w-64">
                                <x-slot name="trigger">
                                    <button class="{{ request()->routeIs('users.*') || request()->routeIs('product_requests.*') || request()->routeIs('reportes.*') ? 'inline-flex items-center px-3 py-2 rounded-xl bg-green-50 text-green-800 font-bold shadow-sm border border-green-100 transition-all duration-200' : 'inline-flex items-center px-3 py-2 rounded-xl text-gray-500 font-medium border border-transparent hover:text-green-700 hover:bg-gray-50 hover:shadow-sm transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none' }}">
                                        <div>Ajustes</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="p-2 space-y-1">
                                        @if(auth()->user()->hasRole(['admin', 'superadmin']))
                                        <a href="{{ route('users.clients') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-blue-50 group">
                                            <div class="flex-shrink-0 bg-blue-100 text-blue-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors">Clientes</p>
                                                <p class="text-xs text-gray-500 mt-1">Ver a la comunidad</p>
                                            </div>
                                        </a>

                                        <a href="{{ route('users.staff') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-green-50 group">
                                            <div class="flex-shrink-0 bg-green-100 text-green-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-green-700 transition-colors">Personal y Sucursales</p>
                                                <p class="text-xs text-gray-500 mt-1">Gestión de personal y sucursales</p>
                                            </div>
                                        </a>
                                        @endif

                                        @if(auth()->user()->hasRole(['superadmin', 'admin', 'compras', 'Personal de Mostrador', 'Doctor']))
                                        <a href="{{ route('product_requests.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-orange-50 group">
                                            <div class="flex-shrink-0 bg-orange-100 text-orange-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-orange-700 transition-colors">Solicitudes</p>
                                                <p class="text-xs text-gray-500 mt-1">Bandeja de pedidos</p>
                                            </div>
                                        </a>
                                        @endif

                                        @if(auth()->user()->hasRole(['admin', 'superadmin']))
                                        <a href="{{ route('reportes.index') }}" class="flex items-start p-3 rounded-xl transition-all duration-200 hover:bg-emerald-50 group">
                                            <div class="flex-shrink-0 bg-emerald-100 text-emerald-600 rounded-lg p-2 group-hover:scale-110 transition-transform duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-emerald-700 transition-colors">Reportes</p>
                                                <p class="text-xs text-gray-500 mt-1">Análisis y estadísticas</p>
                                            </div>
                                        </a>
                                        @endif
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif
                    @endauth
                </div>
            </div>

            {{-- DERECHA - Usuario autenticado: dropdown de perfil | Visitante: login/register --}}
            <div class="hidden lg:flex lg:items-center lg:ms-2 xl:ms-6 shrink-0">
                @auth
                    {{-- Dropdown de usuario autenticado (estilo original) --}}
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-green-700 hover:bg-green-50 focus:outline-none transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="hidden xl:block">{{ Auth::user()->name }}</div>
                                <div class="block xl:hidden">{{ explode(' ', Auth::user()->name)[0] }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Mi Perfil') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="text-red-600 hover:bg-red-50">
                                    {{ __('Cerrar Sesión') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    {{-- Botones para visitantes --}}
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-green-700 hover:bg-green-50 focus:outline-none transition ease-in-out duration-150">
                            Iniciar sesión
                        </a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="ms-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-md text-white bg-green-700 hover:bg-green-800 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                            Registrarse
                        </a>
                    @endif
                @endauth
            </div>

            {{-- HAMBURGUESA - Mobile --}}
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MENÚ MOBILE --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden border-t border-gray-200 max-h-[calc(100vh-5rem)] overflow-y-auto bg-white">

        {{-- Links directos (Inicio, Nosotros, Sucursales, Vacantes) --}}
        <div class="px-3 pt-3 pb-2 space-y-1">
            <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->is('/') ? 'bg-green-50 text-green-800 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-green-700' }}">
                <span class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center {{ request()->is('/') ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </span>
                Inicio
            </a>
            @if(!auth()->check() || auth()->user()->hasRole(['cliente', 'admin', 'superadmin']) || auth()->user()->roles->isEmpty())
            <a href="{{ route('nosotros') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('nosotros') ? 'bg-green-50 text-green-800 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-green-700' }}">
                <span class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('nosotros') ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                Nosotros
            </a>
            <a href="{{ route('sucursales') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('sucursales') ? 'bg-green-50 text-green-800 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-green-700' }}">
                <span class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('sucursales') ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
                Sucursales
            </a>
            <a href="{{ route('vacancies.public.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('vacancies.public.index') ? 'bg-green-50 text-green-800 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-green-700' }}">
                <span class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('vacancies.public.index') ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                Vacantes
            </a>
            @endif
        </div>

        @auth
        {{-- Secciones con acordeones colapsables --}}
        <div class="px-3 pb-3 space-y-2 border-t border-gray-100">

            {{-- ACORDEÓN VENTAS --}}
            @if(auth()->user()->hasRole(['admin', 'superadmin', 'Personal de Mostrador', 'compras', 'Doctor']))
            <div x-data="{ openSection: false }" class="mt-2">
                <button @click="openSection = !openSection"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('pos') || request()->routeIs('sales.*') || request()->routeIs('metas.*') || request()->routeIs('promos.*') || request()->routeIs('turnos.*') ? 'bg-green-50' : 'hover:bg-gray-50' }}">
                    <div class="flex items-center gap-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <span class="text-sm font-semibold {{ request()->routeIs('pos') || request()->routeIs('sales.*') || request()->routeIs('metas.*') || request()->routeIs('promos.*') || request()->routeIs('turnos.*') ? 'text-green-800' : 'text-gray-700' }}">Ventas</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': openSection }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openSection" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 ml-4 pl-3 border-l-2 border-green-100 space-y-0.5" style="display:none;">
                    <a href="{{ route('pos') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('pos') ? 'text-green-700 font-semibold bg-green-50' : 'text-gray-600 hover:text-green-700 hover:bg-gray-50' }}">Punto de Venta</a>
                    @if(auth()->user()->hasRole(['admin', 'superadmin']))
                    <a href="{{ route('sales.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('sales.*') ? 'text-green-700 font-semibold bg-green-50' : 'text-gray-600 hover:text-green-700 hover:bg-gray-50' }}">Historial de Ventas</a>
                    <a href="{{ route('metas.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('metas.*') ? 'text-green-700 font-semibold bg-green-50' : 'text-gray-600 hover:text-green-700 hover:bg-gray-50' }}">Metas de Venta</a>
                    <a href="{{ route('turnos.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('turnos.*') ? 'text-green-700 font-semibold bg-green-50' : 'text-gray-600 hover:text-green-700 hover:bg-gray-50' }}">Turnos</a>
                    <a href="{{ route('promos.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('promos.*') ? 'text-green-700 font-semibold bg-green-50' : 'text-gray-600 hover:text-green-700 hover:bg-gray-50' }}">Promos</a>
                    @endif
                </div>
            </div>
            @endif

            {{-- ACORDEÓN CATÁLOGO --}}
            @if(auth()->user()->hasRole(['admin', 'superadmin', 'compras', 'Doctor']))
            <div x-data="{ openSection: false }" class="">
                <button @click="openSection = !openSection"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('providers.*') || request()->routeIs('products.*') || request()->routeIs('categories.*') || request()->routeIs('services.*') || request()->routeIs('expenses.*') ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                    <div class="flex items-center gap-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                        <span class="text-sm font-semibold {{ request()->routeIs('providers.*') || request()->routeIs('products.*') || request()->routeIs('categories.*') || request()->routeIs('services.*') || request()->routeIs('expenses.*') ? 'text-blue-800' : 'text-gray-700' }}">Catálogo</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': openSection }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openSection" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 ml-4 pl-3 border-l-2 border-blue-100 space-y-0.5" style="display:none;">
                    @if(auth()->user()->hasRole(['admin', 'superadmin', 'compras']))
                    <a href="{{ route('providers.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('providers.*') ? 'text-blue-700 font-semibold bg-blue-50' : 'text-gray-600 hover:text-blue-700 hover:bg-gray-50' }}">Proveedores</a>
                    <a href="{{ route('categories.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('categories.*') ? 'text-blue-700 font-semibold bg-blue-50' : 'text-gray-600 hover:text-blue-700 hover:bg-gray-50' }}">Categorías</a>
                    <a href="{{ route('products.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('products.*') ? 'text-blue-700 font-semibold bg-blue-50' : 'text-gray-600 hover:text-blue-700 hover:bg-gray-50' }}">Productos</a>
                    @endif
                    @if(auth()->user()->hasRole(['admin', 'superadmin', 'Doctor']))
                    <a href="{{ route('services.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('services.*') ? 'text-blue-700 font-semibold bg-blue-50' : 'text-gray-600 hover:text-blue-700 hover:bg-gray-50' }}">Servicios</a>
                    @endif
                    @if(auth()->user()->hasRole(['admin', 'superadmin']))
                    <a href="{{ route('expenses.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('expenses.*') ? 'text-blue-700 font-semibold bg-blue-50' : 'text-gray-600 hover:text-blue-700 hover:bg-gray-50' }}">Gastos Operativos</a>
                    @endif
                </div>
            </div>
            @endif

            {{-- ACORDEÓN INVENTARIO --}}
            @if(auth()->user()->hasRole(['admin', 'superadmin', 'Personal de Mostrador', 'compras']))
            <div x-data="{ openSection: false }" class="">
                <button @click="openSection = !openSection"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('batches.*') || request()->routeIs('transfers.*') || request()->routeIs('purchase-orders.*') ? 'bg-teal-50' : 'hover:bg-gray-50' }}">
                    <div class="flex items-center gap-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </span>
                        <span class="text-sm font-semibold {{ request()->routeIs('batches.*') || request()->routeIs('transfers.*') || request()->routeIs('purchase-orders.*') ? 'text-teal-800' : 'text-gray-700' }}">Inventario</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': openSection }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openSection" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 ml-4 pl-3 border-l-2 border-teal-100 space-y-0.5" style="display:none;">
                    <a href="{{ route('batches.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('batches.index') ? 'text-teal-700 font-semibold bg-teal-50' : 'text-gray-600 hover:text-teal-700 hover:bg-gray-50' }}">Existencias</a>
                    <a href="{{ route('transfers.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('transfers.*') ? 'text-teal-700 font-semibold bg-teal-50' : 'text-gray-600 hover:text-teal-700 hover:bg-gray-50' }}">Traspasos</a>
                    <a href="{{ route('purchase-orders.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('purchase-orders.*') ? 'text-teal-700 font-semibold bg-teal-50' : 'text-gray-600 hover:text-teal-700 hover:bg-gray-50' }}">Compras</a>
                    @if(auth()->user()->hasRole(['admin', 'superadmin']))
                    <a href="{{ route('batches.deletions') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('batches.deletions') ? 'text-teal-700 font-semibold bg-teal-50' : 'text-gray-600 hover:text-teal-700 hover:bg-gray-50' }}">Bajas (Eliminaciones)</a>
                    @endif
                </div>
            </div>
            @endif

            {{-- ACORDEÓN AJUSTES --}}
            @if(auth()->user()->hasRole(['admin', 'superadmin', 'compras', 'Personal de Mostrador', 'Doctor']))
            <div x-data="{ openSection: false }" class="">
                <button @click="openSection = !openSection"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('users.*') || request()->routeIs('product_requests.*') || request()->routeIs('reportes.*') ? 'bg-purple-50' : 'hover:bg-gray-50' }}">
                    <div class="flex items-center gap-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <span class="text-sm font-semibold {{ request()->routeIs('users.*') || request()->routeIs('product_requests.*') || request()->routeIs('reportes.*') ? 'text-purple-800' : 'text-gray-700' }}">Ajustes</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': openSection }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openSection" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 ml-4 pl-3 border-l-2 border-purple-100 space-y-0.5" style="display:none;">
                    @if(auth()->user()->hasRole(['admin', 'superadmin']))
                    <a href="{{ route('users.clients') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('users.clients') ? 'text-purple-700 font-semibold bg-purple-50' : 'text-gray-600 hover:text-purple-700 hover:bg-gray-50' }}">Clientes</a>
                    <a href="{{ route('users.staff') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('users.staff') ? 'text-purple-700 font-semibold bg-purple-50' : 'text-gray-600 hover:text-purple-700 hover:bg-gray-50' }}">Personal y Sucursales</a>
                    @endif
                    @if(auth()->user()->hasRole(['superadmin', 'admin', 'compras', 'Personal de Mostrador', 'Doctor']))
                    <a href="{{ route('product_requests.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('product_requests.*') ? 'text-purple-700 font-semibold bg-purple-50' : 'text-gray-600 hover:text-purple-700 hover:bg-gray-50' }}">Solicitudes</a>
                    @endif
                    @if(auth()->user()->hasRole(['admin', 'superadmin']))
                    <a href="{{ route('reportes.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-150 {{ request()->routeIs('reportes.*') ? 'text-purple-700 font-semibold bg-purple-50' : 'text-gray-600 hover:text-purple-700 hover:bg-gray-50' }}">Reportes</a>
                    @endif
                </div>
            </div>
            @endif

        </div>
        @else
            <div class="px-3 pt-2 pb-3 space-y-1">
                <a href="{{ url('/#servicios') }}" class="block ps-3 pe-4 py-2 text-base font-medium text-gray-600 hover:text-green-700 hover:bg-gray-50">Servicios</a>
                <a href="{{ url('/#contacto') }}" class="block ps-3 pe-4 py-2 text-base font-medium text-gray-600 hover:text-green-700 hover:bg-gray-50">Contacto</a>
            </div>
        @endauth


        <div class="pt-4 pb-1 border-t border-gray-200 bg-gray-50">
            @auth
                <div class="px-4 flex items-center">
                    <div class="flex-shrink-0 bg-green-100 p-2 rounded-full">
                        <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ms-3">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">{{ __('Mi Perfil') }}</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="text-red-600">
                            {{ __('Cerrar Sesión') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="px-4 py-2 space-y-2">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="block text-center px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-md hover:bg-gray-100 transition">Iniciar sesión</a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="block text-center px-4 py-2 text-sm font-semibold text-white bg-green-700 rounded-md hover:bg-green-800 transition">Registrarse</a>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>