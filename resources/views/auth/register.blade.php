<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Cuenta - Farmacias Camur</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">
    <div class="flex min-h-screen">
        
        <!-- Left Pane: Branding -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-green-900 via-green-800 to-green-600 items-center justify-center">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMSI+PC9yZWN0Pgo8cGF0aCBkPSJNMCAwTDggOFpNOCAwTDAgOFoiIHN0cm9rZT0iI2ZmZiIgc3Ryb2tlLW9wYWNpdHk9IjAuMSIgc3Ryb2tlLXdpZHRoPSIxIj48L3BhdGg+Cjwvc3ZnPg==')]"></div>
            
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-green-500 rounded-full mix-blend-screen filter blur-[80px] opacity-40"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-emerald-400 rounded-full mix-blend-screen filter blur-[80px] opacity-40"></div>
            
            <div class="relative z-10 text-center px-12">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white/10 backdrop-blur-md rounded-3xl mb-8 shadow-2xl border border-white/20">
                    <!-- User Add Icon -->
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <h2 class="text-4xl font-extrabold text-white mb-4 tracking-tight drop-shadow-md">Únete a Nexus ERP</h2>
                <p class="text-green-50 text-lg font-medium max-w-md mx-auto leading-relaxed drop-shadow">
                    Regístrate para comenzar a gestionar tu farmacia con el mejor sistema inteligente.
                </p>
            </div>
        </div>

        <!-- Right Pane: Register Form -->
        <div class="w-full lg:w-1/2 relative flex flex-col items-center justify-center p-6 pt-24 pb-24 sm:p-12 sm:py-24">
            
            <!-- Back Button -->
            <a href="{{ url('/') }}" class="absolute top-6 left-6 sm:top-8 sm:left-8 inline-flex items-center text-sm font-bold text-gray-500 hover:text-green-600 transition-colors duration-300 z-20 group">
                <div class="p-2 bg-white rounded-full shadow-sm border border-gray-100 mr-2 group-hover:shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </div>
                Volver al Inicio
            </a>

            <div class="w-full max-w-md bg-white p-6 sm:p-12 rounded-[2rem] shadow-2xl border border-gray-100 relative overflow-hidden group">
                
                <!-- Subtle background glow -->
                <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150 pointer-events-none"></div>

                <!-- Logo -->
                <div class="flex justify-center mb-8 relative z-10">
                    <img src="{{ asset('img/camur_banner.png') }}" alt="Farmacias Camur" class="h-16 w-auto drop-shadow-sm transition-transform duration-500 hover:scale-105">
                </div>

                <div class="text-center mb-10 relative z-10">
                    <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight">Crear Cuenta</h3>
                    <p class="text-sm text-gray-500 mt-2">Ingresa tus datos para registrarte en el sistema.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5 relative z-10">
                    @csrf

                    <!-- Name -->
                    <div class="space-y-1.5">
                        <label for="name" class="block text-sm font-bold text-gray-700">Nombre Completo</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                                class="pl-11 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3.5 transition-all hover:bg-white focus:bg-white shadow-sm"
                                placeholder="Tu nombre">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm font-medium" />
                    </div>

                    <!-- Email Address -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-sm font-bold text-gray-700">Correo Electrónico</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                class="pl-11 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3.5 transition-all hover:bg-white focus:bg-white shadow-sm"
                                placeholder="tu@correo.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm font-medium" />
                    </div>

                    <!-- Phone Number -->
                    <div class="space-y-1.5">
                        <label for="phone" class="block text-sm font-bold text-gray-700">Teléfono (10 dígitos)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required pattern="[0-9]{10}" maxlength="10" title="Debe contener exactamente 10 números" autocomplete="tel"
                                class="pl-11 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3.5 transition-all hover:bg-white focus:bg-white shadow-sm"
                                placeholder="1234567890">
                        </div>
                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-red-500 text-sm font-medium" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-1.5" x-data="{ show: false }">
                        <label for="password" class="block text-sm font-bold text-gray-700">Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                                class="pl-11 pr-11 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3.5 transition-all hover:bg-white focus:bg-white shadow-sm"
                                placeholder="••••••••">
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center">
                                <button type="button" @click="show = !show" class="text-gray-400 hover:text-green-600 focus:outline-none transition-colors" title="Mostrar/Ocultar contraseña">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg x-show="show" class="w-5 h-5" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm font-medium" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-1.5" x-data="{ show: false }">
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700">Confirmar Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                                class="pl-11 pr-11 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3.5 transition-all hover:bg-white focus:bg-white shadow-sm"
                                placeholder="••••••••">
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center">
                                <button type="button" @click="show = !show" class="text-gray-400 hover:text-green-600 focus:outline-none transition-colors" title="Mostrar/Ocultar contraseña">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg x-show="show" class="w-5 h-5" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-sm font-medium" />
                    </div>

                    <!-- Submit Button & Links -->
                    <div class="pt-4 space-y-4">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-4 text-sm font-bold text-white transition-all duration-300 bg-gradient-to-r from-green-700 to-green-600 rounded-xl hover:from-green-800 hover:to-green-700 shadow-md hover:shadow-lg hover:shadow-green-500/30 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">
                            Crear mi Cuenta
                            <svg class="w-5 h-5 ml-2 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                        
                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-green-700 transition-colors">
                                ¿Ya tienes una cuenta? Inicia sesión
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="absolute bottom-6 text-center w-full lg:w-1/2 right-0">
                <p class="text-xs text-gray-400 font-medium">&copy; {{ date('Y') }} Farmacias Camur. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</body>
</html>
