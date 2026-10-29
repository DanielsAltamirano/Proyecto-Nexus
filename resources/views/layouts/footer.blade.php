<footer id="contacto" class="bg-white border-t border-gray-200 pt-10 pb-6 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row justify-between items-start gap-8 lg:gap-12 mb-8">
            
            <!-- Brand Info -->
            <div class="lg:w-1/3 shrink-0">
                <img src="{{ asset('img/camur_banner.png') }}" alt="Farmacias Camur" class="h-10 w-auto mb-4">
                <p class="text-gray-500 font-light text-sm leading-relaxed mb-4 max-w-sm">
                    Cuidando tu salud y la de tu familia en todo momento con profesionales altamente capacitados y empatía genuina.
                </p>
                <div class="flex space-x-3">
                    <!-- Social Icons (smaller) -->
                    <a href="#" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-green-50 hover:text-green-600 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-green-50 hover:text-green-600 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.20 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-green-50 hover:text-green-600 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Links (horizontal) -->
            <div class="lg:w-2/3 flex flex-col md:flex-row justify-between gap-8 lg:gap-12 w-full">
                <!-- Navigation -->
                <div class="md:w-1/4">
                    <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-4">Navegación</h4>
                    <ul class="flex flex-col space-y-2 text-sm">
                        <li><a href="{{ url('/') }}" class="text-gray-500 hover:text-green-600 transition-colors">Inicio</a></li>
                        <li><a href="{{ url('/#servicios') }}" class="text-gray-500 hover:text-green-600 transition-colors">Servicios</a></li>
                        <li><a href="{{ url('/#contacto') }}" class="text-gray-500 hover:text-green-600 transition-colors">Contacto</a></li>
                        @if (Route::has('login'))
                            <li><a href="{{ route('login') }}" class="text-gray-500 hover:text-green-600 transition-colors">Acceso al Sistema</a></li>
                        @endif
                    </ul>
                </div>

                <!-- Contact & Schedule -->
                <div class="md:w-3/4">
                    <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-4">Sucursales y Contacto</h4>
                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-8">
                        <ul class="space-y-3 text-sm flex-1">
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-gray-500 font-light">Av. Principal 1234<br>Ciudad, Estado 12345</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span class="text-gray-500 font-light">+52 123 456 7890</span>
                            </li>
                        </ul>
                        <ul class="space-y-3 text-sm flex-1">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span class="text-gray-500 font-light">contacto@farmaciascamur.mx</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-gray-500 font-light">Lunes a Domingo<br>08:00 AM - 10:00 PM</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-gray-400 text-xs font-light">
                &copy; {{ date('Y') }} Farmacias CAMUR. Todos los derechos reservados.
            </p>
            <div class="flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-green-600 text-xs font-light transition-colors">Aviso de Privacidad</a>
                <a href="#" class="text-gray-400 hover:text-green-600 text-xs font-light transition-colors">Términos y Condiciones</a>
            </div>
        </div>
    </div>
</footer>
