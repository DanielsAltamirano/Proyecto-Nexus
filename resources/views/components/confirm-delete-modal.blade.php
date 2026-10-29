<div x-data="{ show: false, message: '', formElement: null }"
     @open-confirm-delete.window="show = true; message = $event.detail.message; formElement = $event.detail.form"
     x-show="show"
     class="fixed inset-0 z-[100] flex items-center justify-center"
     x-cloak
     style="display: none;">
    
    <!-- Backdrop -->
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="show = false"></div>
    
    <!-- Modal -->
    <div x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-gray-100 p-6 text-center transform transition-all">
        
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
            <svg class="h-10 w-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        
        <h3 class="text-xl font-extrabold text-gray-900 mb-2">Confirmar Acción</h3>
        <p class="text-sm text-gray-500 mb-8 px-4" x-text="message"></p>
        
        <div class="flex justify-center gap-3">
            <button type="button" @click="show = false" class="px-6 py-2.5 text-sm font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors focus:outline-none">
                Cancelar
            </button>
            <button type="button" @click="formElement.submit()" class="px-6 py-2.5 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Sí, Confirmar
            </button>
        </div>
    </div>
</div>
