<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon / Isotipo -->
        <link rel="icon" type="image/png" href="{{ asset('img/isotipo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('img/isotipo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script>
            window.openCenteredPopup = function(url, title = 'Ticket', w = 450, h = 720) {
                const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
                const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;
                const width = window.innerWidth ? window.innerWidth : (document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width);
                const height = window.innerHeight ? window.innerHeight : (document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height);
                
                const left = Math.max(0, Math.round(((width / 2) - (w / 2)) + (dualScreenLeft || 0)));
                const top = Math.max(0, Math.round(((height / 2) - (h / 2)) + (dualScreenTop || 0)));
                
                const popup = window.open(url, title, `scrollbars=yes,width=${w},height=${h},top=${top},left=${left},resizable=yes,toolbar=no,menubar=no,location=no,status=no`);
                if (popup && popup.focus) popup.focus();
                return popup;
            };
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- SCRIPT GLOBAL PARA OCULTAR ALERTAS AUTOMÁTICAMENTE -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Seleccionamos todos los elementos que tengan el atributo role="alert"
                const alerts = document.querySelectorAll('[role="alert"]');
                
                if (alerts.length > 0) {
                    // Esperamos 4 segundos (4000 milisegundos) antes de empezar a ocultar
                    setTimeout(() => {
                        alerts.forEach(alert => {
                            // 1. Añadimos clases de Tailwind para un desvanecimiento suave hacia arriba
                            alert.classList.add('transition-all', 'duration-500', 'ease-in-out', 'opacity-0', '-translate-y-4');
                            
                            // 2. Esperamos medio segundo a que termine la animación y eliminamos el HTML del DOM
                            setTimeout(() => {
                                alert.remove();
                            }, 500);
                        });
                    }, 4000); // <- Puedes cambiar este 4000 por 3000 si quieres que dure solo 3 segundos
                }
            });
        </script>

        <x-confirm-delete-modal />
    </body>
</html>
