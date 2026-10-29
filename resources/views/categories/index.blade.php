<x-app-layout>
    <x-slot name="header">
        <div x-data class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Categorías de Productos
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Clasifica tus medicamentos para un inventario más ordenado.
                    </p>
                </div>
                <div class="mt-5 sm:mt-0">
                    <button @click="$dispatch('open-create-category')" class="btn-group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-green-700 rounded-xl hover:bg-green-800 hover:-translate-y-1 hover:shadow-lg hover:shadow-green-500/30">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Nueva Categoría</span>
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        createCatModal: false,
        editCatModal: false,
        catEditUrl: '',
        catEditName: '',
        catEditDesc: ''
    }" @open-create-category.window="createCatModal = true">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 flex items-center p-4 text-green-800 bg-green-50 border border-green-200 rounded-xl shadow-sm animate-pulse" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @elseif(session('error'))
                <div class="mb-6 flex items-center p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm animate-pulse" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl transition-all duration-300 hover:shadow-md">
                <div class="p-0 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($categories as $category)
                                <tr class="hover:bg-gray-50/80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center text-green-700">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $category->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                        {{ $category->description ?? 'Sin descripción disponible' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="
                                            catEditUrl = '{{ route('categories.update', $category) }}';
                                            catEditName = '{{ addslashes($category->name) }}';
                                            catEditDesc = '{{ addslashes($category->description ?? '') }}';
                                            editCatModal = true;
                                        " class="text-blue-500 hover:text-blue-700 mx-3 transition-colors focus:outline-none">Editar</button>
                                        
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-400 hover:text-red-600 transition-colors" @click.prevent="$dispatch('open-confirm-delete', { message: '¿Eliminar esta categoría?', form: $el.closest('form') })">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-24 text-center">
                                        <div class="flex flex-col items-center justify-center transform transition-all duration-500 hover:scale-105">
                                            <div class="relative p-5 bg-green-50 rounded-full border border-green-100 mb-5 shadow-inner">
                                                <div class="absolute inset-0 bg-green-700 rounded-full opacity-10 animate-ping"></div>
                                                <svg class="w-12 h-12 text-green-700 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">No hay categorías</h3>
                                            <p class="text-gray-500 text-sm mb-8 max-w-sm leading-relaxed">Crea grupos para organizar mejor tus productos médicos.</p>
                                            <button @click="createCatModal = true" class="inline-flex items-center text-white font-semibold bg-green-700 hover:bg-green-800 px-6 py-3 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5">
                                                + Crear mi primera categoría
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- CREATE CATEGORY MODAL -->
            <div x-show="createCatModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak style="display: none;">
                <!-- Backdrop -->
                <div x-show="createCatModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="createCatModal = false"></div>
                
                <!-- Modal content -->
                <div x-show="createCatModal" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden border border-gray-100">
                    
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-800 to-green-500"></div>
                    
                    <div class="flex justify-between items-center p-6 border-b border-gray-50">
                        <h3 class="text-xl font-bold text-gray-900">Nueva Categoría</h3>
                        <button @click="createCatModal = false" class="text-gray-400 hover:text-red-500 transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('categories.store') }}">
                        @csrf
                        <div class="p-6 space-y-5">
                            <div class="space-y-1">
                                <label for="create_name" class="block text-sm font-bold text-gray-700">Nombre de la Categoría <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="create_name" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white" required placeholder="Ej. Material de Curación">
                            </div>
                            <div class="space-y-1">
                                <label for="create_description" class="block text-sm font-bold text-gray-700">Descripción (Opcional)</label>
                                <textarea name="description" id="create_description" rows="3" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white resize-none" placeholder="Breve detalle..."></textarea>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="createCatModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-100 transition-all focus:outline-none">Cancelar</button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-green-700 rounded-xl hover:bg-green-800 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">Guardar Categoría</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- EDIT CATEGORY MODAL -->
            <div x-show="editCatModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak style="display: none;">
                <!-- Backdrop -->
                <div x-show="editCatModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="editCatModal = false"></div>
                
                <!-- Modal content -->
                <div x-show="editCatModal" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden border border-gray-100">
                    
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-800 to-green-500"></div>
                    
                    <div class="flex justify-between items-center p-6 border-b border-gray-50">
                        <h3 class="text-xl font-bold text-gray-900">Editar Categoría</h3>
                        <button @click="editCatModal = false" class="text-gray-400 hover:text-red-500 transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form method="POST" :action="catEditUrl">
                        @csrf
                        @method('PUT')
                        <div class="p-6 space-y-5">
                            <div class="space-y-1">
                                <label for="edit_name" class="block text-sm font-bold text-gray-700">Nombre de la Categoría <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="edit_name" x-model="catEditName" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white" required>
                            </div>
                            <div class="space-y-1">
                                <label for="edit_description" class="block text-sm font-bold text-gray-700">Descripción (Opcional)</label>
                                <textarea name="description" id="edit_description" x-model="catEditDesc" rows="3" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white resize-none"></textarea>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="editCatModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-100 transition-all focus:outline-none">Cancelar</button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-green-700 rounded-xl hover:bg-green-800 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">Actualizar Categoría</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>