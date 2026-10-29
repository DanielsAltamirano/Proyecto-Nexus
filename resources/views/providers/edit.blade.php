<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Proveedor: ') . $provider->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('providers.update', $provider) }}">
                        @csrf
                        @method('PUT') <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre de la Empresa</label>
                                <input type="text" name="name" id="name" 
                                    value="{{ old('name', $provider->name) }}" 
                                    class="mt-1 block w-full border-gray-300 focus:border-pharmacy focus:ring-pharmacy rounded-md shadow-sm" required>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono de Contacto (Opcional)</label>
                                <input type="text" name="phone" id="phone" 
                                    value="{{ old('phone', $provider->phone) }}" 
                                    class="mt-1 block w-full border-gray-300 focus:border-pharmacy focus:ring-pharmacy rounded-md shadow-sm">
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="address" class="block text-sm font-medium text-gray-700">Dirección Física (Opcional)</label>
                            <textarea name="address" id="address" rows="3" 
                                class="mt-1 block w-full border-gray-300 focus:border-pharmacy focus:ring-pharmacy rounded-md shadow-sm">{{ old('address', $provider->address) }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('providers.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-pharmacy hover:bg-green-800 text-white font-bold py-2 px-4 rounded shadow transition">
                                Actualizar Proveedor
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>