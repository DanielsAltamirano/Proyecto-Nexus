<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tienda en Línea - Farmacias CAMUR</title>
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
<body class="bg-gray-50 font-sans text-gray-900 antialiased selection:bg-green-500 selection:text-white flex flex-col min-h-screen">

    {{-- Navbar compartida --}}
    @include('layouts.navigation')

    <main class="flex-grow flex items-center justify-center py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl w-full text-center">
            
            <!-- Icono estático -->
            <div class="mb-8 inline-flex items-center justify-center w-20 h-20 rounded-xl bg-blue-50 text-blue-600 shadow-sm border border-blue-100">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>

            <!-- Contenido -->
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-green-800 tracking-tight mb-5">
                Nuestra <span class="text-blue-600">Tienda en Línea</span>
            </h1>
            
            <p class="text-lg sm:text-xl text-gray-600 font-normal mb-10 max-w-2xl mx-auto leading-relaxed">
                Estamos trabajando intensamente para llevar la mejor selección de medicamentos y productos de salud directamente hasta tu puerta. ¡Espérala muy pronto!
            </p>

            <div class="inline-flex flex-col sm:flex-row gap-3 justify-center items-center">
                <a href="{{ url('/') }}" class="bg-green-800 text-white px-6 py-3 rounded-lg font-semibold text-sm hover:bg-green-900 transition-colors shadow-sm w-full sm:w-auto">
                    &larr; Volver al Inicio
                </a>
            </div>

        </div>
    </main>

    <!-- Footer Compartido -->
    @include('layouts.footer')

</body>
</html>
