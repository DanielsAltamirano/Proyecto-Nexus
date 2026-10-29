<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - Nexus ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">
    <div class="flex min-h-screen">
        
        <!-- Left Pane: Branding -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-purple-900 via-purple-800 to-violet-600 items-center justify-center">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMSI+PC9yZWN0Pgo8cGF0aCBkPSJNMCAwTDggOFpNOCAwTDAgOFoiIHN0cm9rZT0iI2ZmZiIgc3Ryb2tlLW9wYWNpdHk9IjAuMSIgc3Ryb2tlLXdpZHRoPSIxIj48L3BhdGg+Cjwvc3ZnPg==')]"></div>
            
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-purple-500 rounded-full mix-blend-screen filter blur-[80px] opacity-40"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-violet-400 rounded-full mix-blend-screen filter blur-[80px] opacity-40"></div>
            
            <div class="relative z-10 text-center px-12">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white/10 backdrop-blur-md rounded-3xl mb-8 shadow-2xl border border-white/20">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h2 class="text-4xl font-extrabold text-white mb-4 tracking-tight drop-shadow-md">Bienvenido a Nexus ERP</h2>
                <p class="text-purple-50 text-lg font-medium max-w-md mx-auto leading-relaxed drop-shadow">
                    El sistema inteligente para la gestión integral y el control de inventario de tu farmacia.
                </p>
            </div>
        </div>

        <!-- Right Pane: Login Form -->
        <div class="w-full lg:w-1/2 relative flex flex-col items-center justify-center p-6 pt-24 pb-24 sm:p-12 sm:py-24">
            
            <!-- Back Button -->
            <a href="{{ url('/') }}" class="absolute top-6 left-6 sm:top-8 sm:left-8 inline-flex items-center text-sm font-bold text-gray-500 hover:text-purple-600 transition-colors duration-300 z-20 group">
                <div class="p-2 bg-white rounded-full shadow-sm border border-gray-100 mr-2 group-hover:shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </div>
                Volver al Inicio
            </a>

            <div class="w-full max-w-md bg-white p-6 sm:p-12 rounded-[2rem] shadow-2xl border border-gray-100 relative overflow-hidden group">
                
                <!-- Subtle background glow -->
                <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-purple-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150 pointer-events-none"></div>

                <!-- Logo -->
                <div class="flex justify-center mb-8 relative z-10">
                    <img src="{{ asset('img/nexusbanner.jpeg') }}" alt="Nexus ERP" class="h-16 w-auto rounded-lg mix-blend-multiply drop-shadow-sm transition-transform duration-500 hover:scale-105">
                </div>

                <div class="text-center mb-10 relative z-10">
                    <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight">Iniciar Sesión</h3>
                    <p class="text-sm text-gray-500 mt-2">Ingresa tus credenciales para acceder al panel.</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4 relative z-10" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6 relative z-10">
                    @csrf

                    <!-- Email Address -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-sm font-bold text-gray-700">Correo Electrónico</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                class="pl-11 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-purple-600 focus:border-purple-600 block w-full p-3.5 transition-all hover:bg-white focus:bg-white shadow-sm"
                                placeholder="tu@correo.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm font-medium" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-1.5" x-data="{ show: false }">
                        <div class="flex justify-between items-center">
                            <label for="password" class="block text-sm font-bold text-gray-700">Contraseña</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-purple-600 hover:text-purple-800 transition-colors">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                                class="pl-11 pr-11 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-purple-600 focus:border-purple-600 block w-full p-3.5 transition-all hover:bg-white focus:bg-white shadow-sm"
                                placeholder="••••••••">
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center">
                                <button type="button" @click="show = !show" class="text-gray-400 hover:text-purple-600 focus:outline-none transition-colors" title="Mostrar/Ocultar contraseña">
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

                    <!-- Remember Me -->
                    <div class="flex items-center pt-2">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 focus:ring-2 transition-colors cursor-pointer">
                        <label for="remember_me" class="ml-2 text-sm font-medium text-gray-600 cursor-pointer select-none">Mantener sesión iniciada</label>
                    </div>

                    <!-- Submit Button & Register Link -->
                    <div class="pt-4 space-y-4">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-4 text-sm font-bold text-white transition-all duration-300 bg-gradient-to-r from-purple-700 to-purple-600 rounded-xl hover:from-purple-800 hover:to-purple-700 shadow-md hover:shadow-lg hover:shadow-purple-500/30 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2">
                            Ingresar al Sistema
                            <svg class="w-5 h-5 ml-2 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>

                        @if (Route::has('register'))
                            <div class="relative flex items-center py-2">
                                <div class="flex-grow border-t border-gray-200"></div>
                                <span class="flex-shrink-0 mx-4 text-gray-400 text-sm font-medium">O</span>
                                <div class="flex-grow border-t border-gray-200"></div>
                            </div>

                            <a href="{{ route('register') }}" class="w-full inline-flex justify-center items-center px-6 py-4 text-sm font-bold text-gray-700 transition-all duration-300 bg-white border-2 border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2">
                                Crear una cuenta nueva
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="absolute bottom-6 text-center w-full lg:w-1/2 right-0">
                <p class="text-xs text-gray-400 font-medium">&copy; {{ date('Y') }} Nexus ERP. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</body>
</html>
