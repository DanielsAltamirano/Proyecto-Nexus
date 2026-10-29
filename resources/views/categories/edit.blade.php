<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl"></div>
            
            <div class="relative z-10 flex items-center">
                <a href="{{ route('products.index') . '#categories' }}" class="mr-4 p-2.5 bg-white border border-gray-200 rounded-full text-gray-500 hover:text-green-700 hover:bg-green-50 hover:border-green-200 transition-all duration-200 shadow-sm hover:shadow">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-2xl sm:text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Editar Categoría: {{ $category->name }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Actualiza la información de este grupo de medicamentos.
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl relative transition-all duration-300 hover:shadow-md">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-800 to-green-500"></div>
                
                <div class="p-8 text-gray-900">
                    <form method="POST" action="{{ route('categories.update', $category) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <div class="space-y-1">
                                <label for="name" class="block text-sm font-bold text-gray-700">Nombre de la Categoría <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" 
                                        class="pl-10 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3.5 transition-all hover:bg-white" 
                                        required>
                                </div>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="space-y-1">
                                <label for="description" class="block text-sm font-bold text-gray-700">Descripción (Opcional)</label>
                                <textarea name="description" id="description" rows="4" 
                                    class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3.5 transition-all hover:bg-white resize-none" 
                                    placeholder="Detalles sobre este grupo...">{{ old('description', $category->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-end mt-10 pt-6 border-t border-gray-100 gap-3">
                            <a href="{{ route('products.index') . '#categories' }}" class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:text-gray-800 transition-all text-center">
                                Cancelar
                            </a>
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-10 py-3 text-sm font-bold text-white transition-all duration-300 bg-green-700 rounded-xl hover:bg-green-800 shadow-md hover:shadow-lg hover:shadow-green-500/30 hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Actualizar Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>