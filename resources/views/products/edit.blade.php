<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl"></div>
            
            <div class="relative z-10 flex items-center">
                <a href="{{ route('products.index') }}" class="mr-4 p-2.5 bg-white border border-gray-200 rounded-full text-gray-500 hover:text-green-700 hover:bg-green-50 hover:border-green-200 transition-all duration-200 shadow-sm hover:shadow">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-2xl sm:text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Editar: {{ $product->name }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Modifica los datos del producto en el catálogo.
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="flex items-center p-4 text-green-800 bg-green-50 border border-green-200 rounded-xl shadow-sm" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="flex items-center p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9a1 1 0 012 0v4a1 1 0 01-2 0V9zm1-5a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- ===================== BLOQUE 1: DATOS DEL PRODUCTO ===================== --}}
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl relative">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-800 to-green-500"></div>
                
                <div class="p-8 text-gray-900">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Información del Producto
                    </h3>

                    <form method="POST" action="{{ route('products.update', $product) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            
                            {{-- SKU --}}
                            <div class="space-y-1" x-data="{ sku: '{{ old('sku', $product->sku) }}' }" @scanner-result.window="if($event.detail.target === 'sku'){ sku = $event.detail.value; }">
                                <label for="sku" class="block text-sm font-bold text-gray-700">Código de Barras / SKU <span class="text-red-500">*</span></label>
                                <div class="relative flex items-center bg-gray-50 border border-gray-200 rounded-xl focus-within:border-green-600 focus-within:ring-1 focus-within:ring-green-600 transition-colors overflow-hidden hover:bg-white">
                                    <div class="pl-3 pr-2 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    </div>
                                    <input type="text" name="sku" id="sku" x-model="sku"
                                        class="w-full border-none focus:ring-0 p-3 bg-transparent text-gray-900 text-sm" 
                                        required placeholder="Ej. 750123456789">
                                    <button type="button" @click="$dispatch('open-scanner-sku')" class="p-3 bg-white text-gray-600 hover:bg-green-100 hover:text-green-700 font-bold border-l border-gray-200 transition-colors flex items-center justify-center group" title="Escanear Código">
                                        <svg class="w-6 h-6 transform group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="currentColor">
                                            <rect x="3" y="5" width="2" height="14"></rect>
                                            <rect x="7" y="5" width="1" height="14"></rect>
                                            <rect x="10" y="5" width="3" height="14"></rect>
                                            <rect x="15" y="5" width="1" height="14"></rect>
                                            <rect x="18" y="5" width="2" height="14"></rect>
                                            <rect x="22" y="5" width="1" height="14"></rect>
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                            </div>

                            {{-- Nombre Comercial --}}
                            <div class="space-y-1">
                                <label for="name" class="block text-sm font-bold text-gray-700">Nombre / Descripción <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
                                        class="pl-10 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-colors hover:bg-white" 
                                        required placeholder="Ej. Paracetamol 500mg o Galletas Oreo">
                                </div>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            {{-- Categoría --}}
                            <div class="space-y-1">
                                <label for="category_id" class="block text-sm font-bold text-gray-700">Categoría <span class="text-red-500">*</span></label>
                                <div class="flex items-center gap-2">
                                    <select name="category_id" id="category_id" required
                                        class="flex-1 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block p-3 transition-colors hover:bg-white cursor-pointer">
                                        <option value="" disabled>Seleccione una categoría...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="openCategoryModal()" class="px-3 py-3 bg-gray-100 hover:bg-green-50 text-gray-700 hover:text-green-700 font-bold rounded-xl border border-gray-200 hover:border-green-300 transition-colors whitespace-nowrap flex items-center justify-center shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>

                            {{-- Proveedor --}}
                            <div class="space-y-1">
                                <label for="provider_id" class="block text-sm font-bold text-gray-700">Proveedor Principal <span class="text-red-500">*</span></label>
                                <div class="flex items-center gap-2">
                                    <select name="provider_id" id="provider_id" required
                                        class="flex-1 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block p-3 transition-colors hover:bg-white cursor-pointer">
                                        <option value="" disabled>Seleccione un proveedor...</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ old('provider_id', $product->provider_id) == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="openProviderModal()" class="px-3 py-3 bg-gray-100 hover:bg-green-50 text-gray-700 hover:text-green-700 font-bold rounded-xl border border-gray-200 hover:border-green-300 transition-colors whitespace-nowrap flex items-center justify-center shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('provider_id')" class="mt-2" />
                            </div>

                            {{-- ===== CAMPOS FARMACÉUTICOS / CLASIFICACIÓN ===== --}}
                            <div class="md:col-span-2 space-y-4 pt-4 border-t border-gray-100" x-data="{ type: '{{ old('type', $product->type ?? 'OTR') }}' }">
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-3">Clasificación del Producto <span class="text-red-500">*</span></label>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="type" value="OTR" x-model="type" class="hidden peer">
                                            <div class="px-4 py-3 rounded-xl border-2 text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 border-gray-200 text-gray-500 hover:border-blue-300">
                                                <span class="block font-bold">Producto General</span>
                                                <span class="text-xs">Abarrotes, material, etc (OTR)</span>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="type" value="GEN" x-model="type" class="hidden peer">
                                            <div class="px-4 py-3 rounded-xl border-2 text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 border-gray-200 text-gray-500 hover:border-green-300">
                                                <span class="block font-bold">Medicamento Genérico</span>
                                                <span class="text-xs">Fármacos genéricos (GEN)</span>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="type" value="PAT" x-model="type" class="hidden peer">
                                            <div class="px-4 py-3 rounded-xl border-2 text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:text-purple-700 border-gray-200 text-gray-500 hover:border-purple-300">
                                                <span class="block font-bold">Medicamento Patente</span>
                                                <span class="text-xs">Fármacos de patente (PAT)</span>
                                            </div>
                                        </label>
                                    </div>
                                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                                </div>

                                {{-- Campos de Medicamento (ocultos si es OTR) --}}
                                <div x-show="type !== 'OTR'" style="display: none;" x-transition.opacity.duration.300ms class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 pt-2 bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                                    
                                    {{-- Sustancia Activa --}}
                                    <div class="space-y-1">
                                        <label for="active_ingredient" class="block text-sm font-bold text-gray-700">Sustancia Activa (Opcional)</label>
                                        <input type="text" name="active_ingredient" id="active_ingredient" value="{{ old('active_ingredient', $product->active_ingredient) }}" 
                                            class="bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-colors hover:bg-gray-50" 
                                            placeholder="Ej. Paracetamol, Ibuprofeno">
                                        <x-input-error :messages="$errors->get('active_ingredient')" class="mt-2" />
                                    </div>

                                    {{-- Presentación --}}
                                    <div class="space-y-1">
                                        <label for="presentation" class="block text-sm font-bold text-gray-700">Presentación / Vía de Admin. (Opcional)</label>
                                        <input type="text" name="presentation" id="presentation" value="{{ old('presentation', $product->presentation) }}" 
                                            class="bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-colors hover:bg-gray-50" 
                                            placeholder="Ej. Caja 20 tabletas 500mg, Vía oral">
                                        <x-input-error :messages="$errors->get('presentation')" class="mt-2" />
                                    </div>

                                    {{-- Opciones Normativas --}}
                                    <div class="md:col-span-2 flex flex-col sm:flex-row gap-6 p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                                        <div class="flex items-center">
                                            <input id="prescription_required" name="prescription_required" type="checkbox" value="1" {{ old('prescription_required', $product->prescription_required) ? 'checked' : '' }} class="w-5 h-5 text-green-600 bg-gray-50 border-gray-300 rounded focus:ring-green-500 focus:ring-2 cursor-pointer">
                                            <label for="prescription_required" class="ml-2 text-sm font-bold text-gray-700 cursor-pointer">
                                                Requiere Receta Médica
                                                <span class="block text-xs font-normal text-gray-500">Antibióticos o tratamientos especiales.</span>
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="is_controlled" name="is_controlled" type="checkbox" value="1" {{ old('is_controlled', $product->is_controlled) ? 'checked' : '' }} class="w-5 h-5 text-red-600 bg-gray-50 border-gray-300 rounded focus:ring-red-500 focus:ring-2 cursor-pointer">
                                            <label for="is_controlled" class="ml-2 text-sm font-bold text-gray-700 cursor-pointer">
                                                Medicamento Controlado
                                                <span class="block text-xs font-normal text-gray-500">Estupefacientes o psicotrópicos (Grupos I, II, III).</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Mensaje si es Producto General --}}
                                <div x-show="type === 'OTR'" style="display: none;" x-transition.opacity.duration.300ms class="pt-2">
                                    <div class="flex items-center gap-3 text-sm text-blue-800 bg-blue-50 border border-blue-200 rounded-xl p-4">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold">Producto No Farmacéutico</p>
                                            <p class="text-blue-600/80 mt-0.5">La sustancia activa, presentación y requerimientos de receta no aplican y serán omitidos al guardar.</p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        {{-- Descripción --}}
                        <div class="mt-6 space-y-1">
                            <label for="description" class="block text-sm font-bold text-gray-700">Descripción / Notas adicionales</label>
                            <textarea name="description" id="description" rows="3" 
                                class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-colors hover:bg-white resize-none" 
                                placeholder="Indicaciones, contraindicaciones o notas del fabricante (Opcional)">{{ old('description', $product->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        {{-- Botones Submit --}}
                        <div class="flex flex-col sm:flex-row items-center justify-end mt-8 pt-6 border-t border-gray-100 gap-3">
                            <a href="{{ route('products.index') }}" class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:text-gray-800 transition-colors text-center focus:outline-none focus:ring-2 focus:ring-gray-200">
                                Cancelar
                            </a>
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3 text-sm font-bold text-white transition-all duration-300 ease-in-out bg-green-700 rounded-xl hover:bg-green-800 shadow-md hover:shadow-lg hover:shadow-green-500/30 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL CATEGORIA -->
    <div id="categoryModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" onclick="closeCategoryModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border-t-4 border-t-green-600">
                <div class="flex justify-between items-center mb-5 pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-900">Nueva Categoría</h3>
                    <button type="button" onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-500"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <div id="categoryModalAlert" class="hidden mb-4 p-3 rounded-lg text-sm font-medium"></div>
                <form id="ajaxCategoryForm">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" id="ajax_cat_name" required class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200">Cancelar</button>
                        <button type="submit" id="btnSaveCategory" class="px-6 py-2 bg-green-700 text-white font-bold rounded-xl hover:bg-green-800">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL PROVEEDOR -->
    <div id="providerModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" onclick="closeProviderModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border-t-4 border-t-green-600">
                <div class="flex justify-between items-center mb-5 pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-900">Nuevo Proveedor</h3>
                    <button type="button" onclick="closeProviderModal()" class="text-gray-400 hover:text-gray-500"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <div id="providerModalAlert" class="hidden mb-4 p-3 rounded-lg text-sm font-medium"></div>
                <form id="ajaxProviderForm">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre de la Empresa <span class="text-red-500">*</span></label>
                        <input type="text" id="ajax_prov_name" required class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Teléfono</label>
                        <input type="text" id="ajax_prov_phone" class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Dirección</label>
                        <input type="text" id="ajax_prov_address" class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeProviderModal()" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200">Cancelar</button>
                        <button type="submit" id="btnSaveProvider" class="px-6 py-2 bg-green-700 text-white font-bold rounded-xl hover:bg-green-800">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Funciones para Categoría
        const catModal = document.getElementById('categoryModal');
        const catForm = document.getElementById('ajaxCategoryForm');
        const catAlert = document.getElementById('categoryModalAlert');
        const btnCat = document.getElementById('btnSaveCategory');

        function openCategoryModal() {
            catModal.classList.remove('hidden');
            catForm.reset();
            catAlert.classList.add('hidden');
        }

        function closeCategoryModal() {
            catModal.classList.add('hidden');
        }

        catForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            btnCat.disabled = true;
            btnCat.innerHTML = 'Guardando...';
            catAlert.classList.add('hidden');

            try {
                const response = await fetch("{{ route('categories.storeApi') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ name: document.getElementById('ajax_cat_name').value })
                });
                const data = await response.json();

                if (response.ok) {
                    const select = document.getElementById('category_id');
                    const newOption = new Option(data.category.name, data.category.id, true, true);
                    select.append(newOption);
                    closeCategoryModal();
                } else {
                    catAlert.classList.remove('hidden');
                    catAlert.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'border');
                    catAlert.innerHTML = data.message || 'Error al guardar.';
                }
            } catch (error) {
                catAlert.classList.remove('hidden');
                catAlert.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'border');
                catAlert.innerHTML = 'Error de conexión.';
            } finally {
                btnCat.disabled = false;
                btnCat.innerHTML = 'Guardar';
            }
        });

        // Funciones para Proveedor
        const provModal = document.getElementById('providerModal');
        const provForm = document.getElementById('ajaxProviderForm');
        const provAlert = document.getElementById('providerModalAlert');
        const btnProv = document.getElementById('btnSaveProvider');

        function openProviderModal() {
            provModal.classList.remove('hidden');
            provForm.reset();
            provAlert.classList.add('hidden');
        }

        function closeProviderModal() {
            provModal.classList.add('hidden');
        }

        provForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            btnProv.disabled = true;
            btnProv.innerHTML = 'Guardando...';
            provAlert.classList.add('hidden');

            try {
                const response = await fetch("{{ route('providers.storeApi') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        name: document.getElementById('ajax_prov_name').value,
                        phone: document.getElementById('ajax_prov_phone').value,
                        address: document.getElementById('ajax_prov_address').value
                    })
                });
                const data = await response.json();

                if (response.ok) {
                    const select = document.getElementById('provider_id');
                    const newOption = new Option(data.provider.name, data.provider.id, true, true);
                    select.append(newOption);
                    closeProviderModal();
                } else {
                    provAlert.classList.remove('hidden');
                    provAlert.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'border');
                    provAlert.innerHTML = data.message || 'Error al guardar.';
                }
            } catch (error) {
                provAlert.classList.remove('hidden');
                provAlert.classList.add('bg-red-50', 'text-red-700', 'border-red-200', 'border');
                provAlert.innerHTML = 'Error de conexión.';
            } finally {
                btnProv.disabled = false;
                btnProv.innerHTML = 'Guardar';
            }
        }
    </script>
    
    <!-- Modal del Lector de Código de Barras -->
    <x-barcode-scanner-modal targetModel="sku" />
</x-app-layout>
