<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nexus ERP - Tu salud, es primero</title>
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

        /* Arco invertido: pseudo-elemento que forma la curva morada en la parte inferior */
        .arc-section {
            position: relative;
            background: #6d28d9; /* purple-700 / violet-700 */
            padding-top: 60px;
            padding-bottom: 0;
            overflow: visible;
        }
        .arc-section::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: -5%;
            width: 110%;
            height: 120px;
            background: #6d28d9;
            border-radius: 0 0 50% 50%;
            z-index: 1;
        }

        /* Círculo del video */
        .video-circle-wrapper {
            position: relative;
            z-index: 10;
        }
        .video-circle {
            width: 320px;
            height: 320px;
            border-radius: 50%;
            overflow: hidden;
            border: 8px solid #ffffff;
            box-shadow: 0 30px 80px rgba(0,0,0,0.25), 0 0 0 4px rgba(255,255,255,0.3);
            background: #000;
        }
        @media (min-width: 640px) {
            .video-circle {
                width: 420px;
                height: 420px;
            }
        }
        @media (min-width: 1024px) {
            .video-circle {
                width: 500px;
                height: 500px;
            }
        }


        /* Animación para el título */
        @keyframes titleShine {
            0% { background-position: 200% center; }
            100% { background-position: -200% center; }
        }
        @keyframes floatTitle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .animated-title {
            background: linear-gradient(to right, #ffffff 20%, #ddd6fe 40%, #ddd6fe 60%, #ffffff 80%);
            background-size: 200% auto;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: titleShine 5s linear infinite, floatTitle 4s ease-in-out infinite;
            display: inline-block;
            padding-bottom: 0.2em; /* Evitar que se corte el borde de la tipografía */
        }
    </style>
</head>
<body class="bg-white font-sans text-gray-900 antialiased selection:bg-purple-500 selection:text-white flex flex-col min-h-screen overflow-x-hidden">

    {{-- Navbar compartida --}}
    @include('layouts.navigation')

    <div class="w-full overflow-x-hidden">
        <!-- ============================================================ -->
        <!-- SECCIÓN VIDEO: Arco invertido morado + círculo blanco        -->
        <!-- ============================================================ -->
        <section class="arc-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center pb-32 pt-6">
                <!-- Título (siempre arriba) -->
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight animated-title mb-10">
                    <a href="{{ route('nosotros') }}" class="block w-full">
                        ¡Conoce quiénes somos !
                    </a>
                </h2>

                <!-- Contenedor relativo que alojará video y burbujas -->
                <div class="relative flex flex-col items-center w-full max-w-5xl">
                    
                    <!-- Burbuja para móvil (flujo normal antes del video, oculta en desktop) -->
                    <div class="md:hidden mt-2 mb-10 mx-auto relative max-w-[90%] sm:max-w-md text-left z-20">
                        <div class="bg-white text-gray-800 px-4 py-3 rounded-2xl rounded-tl-none shadow-2xl text-sm sm:text-base relative inline-block animate-bounce" style="animation: floatTitle 4s ease-in-out infinite reverse; animation-delay: 1s;">
                            <!-- Cola de la burbuja -->
                            <div class="absolute top-0 -left-3 w-4 h-4 bg-white" style="clip-path: polygon(100% 0, 0 0, 100% 100%);"></div>
                            <p class="leading-relaxed text-gray-700">
                                No solo surtimos recetas, <strong class="text-purple-700">cuidamos de tu familia</strong> con atención médica rápida y medicamentos de la más alta calidad a precios justos. ¡Siempre cerca de ti cuando más lo necesitas! 💊✨
                            </p>
                            <div class="flex justify-end items-center mt-1.5 gap-1.5 text-gray-400">
                                <span class="text-[11px] font-medium">10:00 a.m.</span>
                                <a href="#" class="text-purple-500 hover:text-purple-600 transform hover:scale-110 transition-all duration-300" title="Contactar por WhatsApp">
                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Círculo con el video -->
                    <div class="video-circle-wrapper relative mx-auto">
                        <div class="video-circle relative z-10">
                            <video
                                src="{{ asset('img/pildora.mp4') }}"
                                autoplay
                                loop
                                muted
                                playsinline
                                style="width:100%; height:100%; object-fit:cover;"
                            ></video>
                        </div>
                        <!-- Anillo decorativo externo -->
                        <div class="absolute inset-0 rounded-full z-0" style="box-shadow: 0 0 0 16px rgba(255,255,255,0.08); border-radius: 50%;"></div>
                        
                        <!-- Burbuja para Desktop (Posicionada al lado del video) -->
                        <div class="hidden md:block absolute top-1/4 left-[96%] lg:left-[102%] w-72 lg:w-[22rem] text-left z-30">
                            <div class="bg-white text-gray-800 px-5 py-4 rounded-3xl rounded-tl-none shadow-2xl text-sm lg:text-base animate-bounce relative" style="animation: floatTitle 4s ease-in-out infinite reverse; animation-delay: 1s;">
                                <!-- Cola de la burbuja apuntando al video -->
                                <div class="absolute top-0 -left-4 w-5 h-5 bg-white" style="clip-path: polygon(100% 0, 0 0, 100% 100%);"></div>
                                <p class="leading-relaxed text-gray-700">
                                    No solo surtimos recetas, <strong class="text-purple-700">cuidamos de tu familia</strong> con atención médica rápida y medicamentos de la más alta calidad a precios justos. ¡Siempre cerca de ti cuando más lo necesitas! 💊✨
                                </p>
                                <div class="flex justify-end items-center mt-2 gap-1.5 text-gray-400">
                                    <span class="text-[11px] font-medium">10:00 a.m.</span>
                                    <a href="#" class="text-purple-500 hover:text-purple-600 transform hover:scale-110 transition-all duration-300" title="Contactar por WhatsApp">
                                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- Separador visual que conecta el arco con el carrusel -->
        <div style="height: 80px; background: white;"></div>

        <!-- ============================================================ -->
        <!-- HERO CAROUSEL                                                -->
        <!-- ============================================================ -->
        <main class="relative overflow-hidden flex-grow bg-white border-t border-gray-100"
              x-data="{ 
                  activeSlide: 0, 
                  interval: null,
                  slides: [
                      {
                          title1: 'Tu salud,',
                          title2: 'nuestra prioridad.',
                          desc: 'Acceso rápido a medicamentos de calidad, atención médica oportuna y el trato personalizado que mereces en cada visita.',
                          image: 'https://images.unsplash.com/photo-1581594693702-fbdc51b2763b?auto=format&fit=crop&w=800&q=80',
                          badge: 'Atención Médica'
                      },
                      {
                          title1: 'Medicamentos',
                          title2: 'siempre disponibles.',
                          desc: 'Catálogo amplio de genéricos y patentes. Encuentra lo que necesitas sin esperas, con precios accesibles para toda la familia.',
                          image: 'https://images.unsplash.com/photo-1631549916768-4119b2e5f926?auto=format&fit=crop&w=800&q=80',
                          badge: 'Farmacia Completa'
                      },
                      {
                          title1: 'Compromiso y',
                          title2: 'calidad humana.',
                          desc: 'Un equipo de profesionales capacitados, instalaciones modernas y la calidez de quienes conocen a su comunidad.',
                          image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSYN2buQNbJHknWWGTx_QcGESjrRKV7l3r4wCcLQQWp5UGvxRyblg6CPWI&s=10',
                          badge: 'Equipo Experto'
                      }
                  ],
                  nextSlide() {
                      this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                  }
              }" 
              x-init="interval = setInterval(() => nextSlide(), 6000)">
              
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20" style="min-height: 500px;">
                
                <template x-for="(slide, index) in slides" :key="index">
                    <div x-show="activeSlide === index" 
                         x-transition:enter="transition-all duration-700 ease-in-out"
                         x-transition:enter-start="opacity-0 translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition-all duration-700 ease-in-out absolute top-0 left-0 right-0 mt-12 md:mt-20"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="w-full flex flex-col md:flex-row items-center justify-between gap-10">
                        
                        <!-- Texto del slide -->
                        <div class="md:w-1/2 text-center md:text-left z-10 w-full">
                            <!-- Badge de categoría -->
                            <div class="inline-flex items-center gap-2 bg-violet-50 border border-violet-200 text-violet-700 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-md mb-4">
                                <span class="w-1.5 h-1.5 bg-violet-500 rounded-full inline-block"></span>
                                <span x-text="slide.badge"></span>
                            </div>

                            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight leading-tight mb-5">
                                <span class="text-purple-800" x-text="slide.title1"></span><br>
                                <span class="text-violet-600" x-text="slide.title2"></span>
                            </h1>
                            <p class="mt-2 text-base md:text-lg text-gray-600 mb-8 max-w-lg mx-auto md:mx-0 leading-relaxed" x-text="slide.desc"></p>
                            
                            <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                                <a href="#servicios" class="bg-purple-800 text-white px-6 py-3 rounded-lg font-semibold text-sm hover:bg-purple-900 transition-colors shadow-sm">
                                    Explorar Servicios
                                </a>
                                <a href="#contacto" class="bg-white text-violet-700 px-6 py-3 rounded-lg font-semibold text-sm border border-violet-200 hover:border-violet-400 hover:bg-violet-50 transition-colors">
                                    Nuestra Ubicación
                                </a>
                            </div>
                        </div>

                        <!-- Imagen del slide con marco propio -->
                        <div class="md:w-1/2 mt-8 md:mt-0 relative w-full flex justify-center">
                            
                            <!-- Marco decorativo -->
                            <div class="slide-image-frame relative max-w-md w-full">
                                <img :src="slide.image" :alt="slide.title1"
                                     class="relative z-10 rounded-xl shadow-md w-full object-cover"
                                     style="height: 380px;">
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Controles del carrusel -->
                <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2 z-20">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="activeSlide = index; clearInterval(interval); interval = setInterval(() => nextSlide(), 6000)" 
                                :class="activeSlide === index ? 'w-6 bg-purple-800' : 'w-2 bg-gray-300 hover:bg-gray-400'" 
                                class="h-2 rounded-full transition-all duration-300 focus:outline-none"></button>
                    </template>
                </div>
            </div>
        </main>

        <!-- FEATURES SECTION -->
        <section id="servicios" class="bg-gray-50 py-16 border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-extrabold text-purple-800 tracking-tight">Lo que nos hace diferentes</h2>
                    <p class="mt-2 text-gray-600 font-normal text-lg">Servicios diseñados para ti y tu familia</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    
                    <div class="group p-8 bg-white rounded-xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-gray-200 cursor-default">
                        <div class="mx-auto bg-violet-50 text-violet-600 group-hover:bg-violet-600 group-hover:text-white rounded-lg flex items-center justify-center mb-5 transition-colors duration-300 transform group-hover:scale-110" style="width: 56px; height: 56px;">
                            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-purple-800 mb-2 group-hover:text-violet-700 transition-colors duration-300">Surtido Completo</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Catálogo extenso de medicamentos de patente y genéricos para todas tus necesidades.</p>
                    </div>

                    <div class="group p-8 bg-white rounded-xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-gray-200 cursor-default">
                        <div class="mx-auto bg-violet-50 text-violet-600 group-hover:bg-violet-600 group-hover:text-white rounded-lg flex items-center justify-center mb-5 transition-colors duration-300 transform group-hover:scale-110" style="width: 56px; height: 56px;">
                            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-purple-800 mb-2 group-hover:text-violet-700 transition-colors duration-300">Médicos Expertos</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Profesionales calificados listos para darte un diagnóstico certero y el mejor trato humano.</p>
                    </div>

                    <div class="group p-8 bg-white rounded-xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-gray-200 cursor-default">
                        <div class="mx-auto bg-violet-50 text-violet-600 group-hover:bg-violet-600 group-hover:text-white rounded-lg flex items-center justify-center mb-5 transition-colors duration-300 transform group-hover:scale-110" style="width: 56px; height: 56px;">
                            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-purple-800 mb-2 group-hover:text-violet-700 transition-colors duration-300">Cuidado Inteligente</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Instalaciones modernas, seguimiento de pacientes y la seguridad de estar en buenas manos.</p>
                    </div>

                </div>
            </div>
        </section>
    </div>

    <section class="py-16 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-extrabold text-purple-800 tracking-tight mb-4">¿No sabes cuál es tu sucursal más cercana?</h2>
            <p class="mt-2 text-gray-600 font-normal text-lg mb-8 max-w-2xl mx-auto">¡No te preocupes! Explora nuestro mapa interactivo y encuentra la sucursal Nexus más próxima a tu ubicación.</p>
            <a href="{{ route('sucursales') }}" class="inline-flex items-center justify-center bg-purple-700 text-white px-8 py-3 rounded-lg font-semibold text-sm hover:bg-purple-800 transition-colors shadow-sm">
                Ver todas las sucursales &rarr;
            </a>
        </div>
    </section>
    <!-- Footer Compartido -->
    @include('layouts.footer')

</body>
</html>
