<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Directorio de Proveedores
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Administra la agenda de empresas farmacéuticas y laboratorios.
                    </p>
                </div>
                <div class="mt-5 sm:mt-0">
                    <a href="{{ route('providers.create') }}" class="btn-group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-green-700 rounded-xl hover:bg-green-800 hover:-translate-y-1 hover:shadow-lg hover:shadow-green-500/30">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Nuevo Proveedor</span>
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        editProviderModal: false,
        providerEditUrl: '',
        providerEditName: '',
        providerEditPhone: '',
        providerEditAddress: ''
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-pharmacy text-green-700 p-4 mb-4 rounded" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre / Empresa</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($providers as $provider)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $provider->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $provider->phone ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $provider->address ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button type="button" @click="
                                            providerEditUrl = '{{ route('providers.update', $provider) }}';
                                            providerEditName = '{{ addslashes($provider->name) }}';
                                            providerEditPhone = '{{ addslashes($provider->phone ?? '') }}';
                                            providerEditAddress = '{{ addslashes($provider->address ?? '') }}';
                                            editProviderModal = true;
                                        " class="text-blue-600 hover:text-blue-900 mr-4 font-medium focus:outline-none">Editar</button>
                                        <form action="{{ route('providers.destroy', $provider) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 font-medium" @click.prevent="$dispatch('open-confirm-delete', { message: '¿Eliminar este proveedor?', form: $el.closest('form') })">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                        No hay proveedores registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>

            <!-- EDIT PROVIDER MODAL -->
            <div x-show="editProviderModal" class="fixed inset-0 z-[100] flex items-center justify-center" x-cloak style="display: none;">
                <!-- Backdrop -->
                <div x-show="editProviderModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="editProviderModal = false"></div>
                
                <!-- Modal content -->
                <div x-show="editProviderModal" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden border border-gray-100">
                    
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-800 to-green-500"></div>
                    
                    <div class="flex justify-between items-center p-6 border-b border-gray-50">
                        <h3 class="text-xl font-bold text-gray-900">Editar Proveedor</h3>
                        <button @click="editProviderModal = false" class="text-gray-400 hover:text-red-500 transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form method="POST" :action="providerEditUrl">
                        @csrf
                        @method('PUT')
                        <div class="p-6 space-y-5">
                            <div class="space-y-1">
                                <label for="edit_name" class="block text-sm font-bold text-gray-700">Nombre / Empresa <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="edit_name" x-model="providerEditName" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white" required>
                            </div>
                            <div class="space-y-1">
                                <label for="edit_phone" class="block text-sm font-bold text-gray-700">Teléfono</label>
                                <input type="text" name="phone" id="edit_phone" x-model="providerEditPhone" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white">
                            </div>
                            <div class="space-y-1">
                                <label for="edit_address" class="block text-sm font-bold text-gray-700">Dirección</label>
                                <textarea name="address" id="edit_address" x-model="providerEditAddress" rows="2" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white resize-none"></textarea>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="editProviderModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-100 transition-all focus:outline-none">Cancelar</button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-green-700 rounded-xl hover:bg-green-800 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">Actualizar Proveedor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>