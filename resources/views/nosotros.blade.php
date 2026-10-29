<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nosotros - Farmacias CAMUR</title>
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

    <main class="flex-grow py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
            
            <h1 class="text-4xl sm:text-5xl font-extrabold text-green-800 tracking-tight mb-8">
                Acerca de <span class="text-blue-600">Nosotros</span>
            </h1>
            
            <div class="bg-white p-8 sm:p-12 rounded-2xl shadow-sm border border-gray-100 text-left">
                <p class="text-lg text-gray-600 font-light mb-6 leading-relaxed">
                    En <strong class="text-green-700">Farmacias CAMUR</strong>, nuestro compromiso es cuidar de ti y de tu familia. Somos una empresa dedicada a proveer medicamentos, productos de salud e higiene de la más alta calidad, siempre a precios justos y accesibles.
                </p>
                <p class="text-lg text-gray-600 font-light mb-6 leading-relaxed">
                    Nuestra misión no se detiene en surtir una receta; buscamos ofrecer un servicio integral que incluye atención médica oportuna a través de nuestros profesionales calificados. Creemos que la salud es lo más importante y por ello trabajamos todos los días para estar siempre cerca de ti cuando más lo necesitas.
                </p>
                <p class="text-lg text-gray-600 font-light leading-relaxed">
                    Gracias por confiar tu bienestar en nuestras manos. Seguimos innovando y mejorando para brindarte la mejor experiencia y calidez humana en cada una de tus visitas.
                </p>
            </div>

            <div class="mt-12 inline-flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ url('/') }}" class="bg-green-800 text-white px-8 py-3 rounded-lg font-semibold text-sm hover:bg-green-900 transition-colors shadow-sm w-full sm:w-auto">
                    &larr; Volver al Inicio
                </a>
            </div>

        </div>
    </main>

    <!-- Footer Compartido -->
    @include('layouts.footer')

</body>
</html>
