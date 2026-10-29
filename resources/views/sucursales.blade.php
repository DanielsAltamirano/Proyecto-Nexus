<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sucursales - Nexus ERP</title>
    <!-- Favicon / Isotipo -->
    <link rel="icon" type="image/png" href="{{ asset('img/isotipo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/isotipo.png') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased selection:bg-purple-500 selection:text-white flex flex-col min-h-screen">

    {{-- Navbar compartida --}}
    @include('layouts.navigation')

    <main class="flex-grow py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto text-center">
            
            <h1 class="text-4xl sm:text-5xl font-extrabold text-purple-800 tracking-tight mb-4">
                Nuestras <span class="text-violet-600">Sucursales</span>
            </h1>
            <p class="text-lg text-gray-600 font-light mb-12">Encuentra la sucursal Nexus más cercana a ti.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 text-left">
                
                @php
                    $branches = $branches ?? \App\Models\Branch::where('name', 'not like', '%Bodega%')->get();
                @endphp
                @forelse ($branches as $branch)
                <!-- Tarjeta de Sucursal -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow flex flex-col h-full">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-violet-50 text-violet-600 rounded-lg flex items-center justify-center shrink-0">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-purple-800">{{ $branch->name }}</h3>
                    </div>
                    <div class="flex-grow">
                        <p class="text-gray-600 text-sm mb-2 leading-relaxed">
                            {{ $branch->address ?? 'Dirección no especificada' }}
                        </p>
                        @if($branch->phone)
                        <p class="text-gray-600 text-sm mb-4 leading-relaxed font-medium">
                            📞 {{ $branch->phone }}
                        </p>
                        @endif
                    </div>
                    @php
                        $mapsLink = $branch->maps_url 
                            ? $branch->maps_url 
                            : 'https://www.google.com/maps/search/?api=1&query=' . urlencode('Nexus ' . $branch->name . ' ' . $branch->address);
                    @endphp
                    <a href="{{ $mapsLink }}" target="_blank" class="text-violet-600 hover:text-violet-800 font-semibold text-sm inline-flex items-center gap-1 transition-colors mt-auto">
                        Ver en el mapa &rarr;
                    </a>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">Próximamente estaremos más cerca de ti. Aún no hay sucursales registradas.</p>
                </div>
                @endforelse

            </div>

            <div class="mt-12 inline-flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ url('/') }}" class="bg-purple-800 text-white px-8 py-3 rounded-lg font-semibold text-sm hover:bg-purple-900 transition-colors shadow-sm w-full sm:w-auto">
                    &larr; Volver al Inicio
                </a>
            </div>

        </div>
    </main>

    <!-- Footer Compartido -->
    @include('layouts.footer')

</body>
</html>
