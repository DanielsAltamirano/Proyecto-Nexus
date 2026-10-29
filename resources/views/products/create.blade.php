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
                        Registrar Nuevo Medicamento
                    </h2>
                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Añade un nuevo producto al catálogo general.
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl relative">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-800 to-green-500"></div>
                
                <div class="p-8 text-gray-900">
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            
                            <!-- SKU -->
                            <div class="space-y-1" x-data="{ sku: '{{ old('sku') }}' }" @scanner-result.window="if($event.detail.target === 'sku'){ sku = $event.detail.value; }">
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

                            <!-- Tipo de Medicamento (Nuevo Campo) -->
                            <div class="space-y-1">
                                <label for="type" class="block text-sm font-bold text-gray-700">Tipo de Medicamento <span class="text-red-500">*</span></label>
                                <select name="type" id="type" required 
                                    class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-colors hover:bg-white cursor-pointer">
                                    <option value="GEN" {{ old('type') == 'GEN' ? 'selected' : '' }}>Genérico (GEN)</option>
                                    <option value="PAT" {{ old('type') == 'PAT' ? 'selected' : '' }}>Patente (PAT)</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <!-- Nombre Comercial -->
                            <div class="space-y-1 md:col-span-2">
                                <label for="name" class="block text-sm font-bold text-gray-700">Nombre Comercial / Compuesto <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                        class="pl-10 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-colors hover:bg-white" 
                                        required placeholder="Ej. Paracetamol 500mg">
                                </div>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Sustancia Activa -->
                            <div class="space-y-1">
                                <label for="active_ingredient" class="block text-sm font-bold text-gray-700">Sustancia Activa (Opcional)</label>
                                <input type="text" name="active_ingredient" id="active_ingredient" value="{{ old('active_ingredient') }}" 
                                    class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-colors hover:bg-white" 
                                    placeholder="Ej. Paracetamol, Ibuprofeno">
                                <x-input-error :messages="$errors->get('active_ingredient')" class="mt-2" />
                            </div>

                            <!-- Presentación -->
                            <div class="space-y-1">
                                <label for="presentation" class="block text-sm font-bold text-gray-700">Presentación y Vía de Administración (Opcional)</label>
                                <input type="text" name="presentation" id="presentation" value="{{ old('presentation') }}" 
                                    class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-colors hover:bg-white" 
                                    placeholder="Ej. Caja con 20 tabletas 500mg, Vía oral">
                                <x-input-error :messages="$errors->get('presentation')" class="mt-2" />
                            </div>

                            <!-- Opciones Normativas -->
                            <div class="md:col-span-2 flex flex-col sm:flex-row gap-6 mt-2 mb-2 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="flex items-center">
                                    <input id="prescription_required" name="prescription_required" type="checkbox" value="1" {{ old('prescription_required') ? 'checked' : '' }} class="w-5 h-5 text-green-600 bg-white border-gray-300 rounded focus:ring-green-500 focus:ring-2 cursor-pointer">
                                    <label for="prescription_required" class="ml-2 text-sm font-bold text-gray-700 cursor-pointer">
                                        Requiere Receta Médica
                                        <span class="block text-xs font-normal text-gray-500">Antibióticos o tratamientos especiales.</span>
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="is_controlled" name="is_controlled" type="checkbox" value="1" {{ old('is_controlled') ? 'checked' : '' }} class="w-5 h-5 text-red-600 bg-white border-gray-300 rounded focus:ring-red-500 focus:ring-2 cursor-pointer">
                                    <label for="is_controlled" class="ml-2 text-sm font-bold text-gray-700 cursor-pointer">
                                        Medicamento Controlado
                                        <span class="block text-xs font-normal text-gray-500">Estupefacientes o psicotrópicos (Grupos I, II, III).</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Categoría -->
                            <div class="space-y-1">
                                <label for="category_id" class="block text-sm font-bold text-gray-700">Categoría <span class="text-red-500">*</span></label>
                                <div class="flex items-center gap-2">
                                    <select name="category_id" id="category_id" required
                                        class="flex-1 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block p-3 transition-colors hover:bg-white cursor-pointer">
                                        <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Seleccione una categoría...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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

                            <!-- Proveedor -->
                            <div class="space-y-1">
                                <label for="provider_id" class="block text-sm font-bold text-gray-700">Proveedor Principal <span class="text-red-500">*</span></label>
                                <div class="flex items-center gap-2">
                                    <select name="provider_id" id="provider_id" required
                                        class="flex-1 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block p-3 transition-colors hover:bg-white cursor-pointer">
                                        <option value="" disabled {{ old('provider_id') ? '' : 'selected' }}>Seleccione un proveedor...</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ old('provider_id') == $provider->id ? 'selected' : '' }}>
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
                        </div>

                        <!-- Descripción -->
                        <div class="mt-6 space-y-1">
                            <label for="description" class="block text-sm font-bold text-gray-700">Descripción / Notas adicionales</label>
                            <textarea name="description" id="description" rows="3" 
                                class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-colors hover:bg-white resize-none" 
                                placeholder="Indicaciones, contraindicaciones o notas del fabricante (Opcional)">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>


                        <!-- Botones Submit -->
                        <div class="flex flex-col sm:flex-row items-center justify-end mt-8 pt-6 border-t border-gray-100 gap-3">
                            <a href="{{ route('products.index') }}" class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:text-gray-800 transition-colors text-center focus:outline-none focus:ring-2 focus:ring-gray-200">
                                Cancelar
                            </a>
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3 text-sm font-bold text-white transition-all duration-300 ease-in-out bg-green-700 rounded-xl hover:bg-green-800 shadow-md hover:shadow-lg hover:shadow-green-500/30 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Guardar Medicamento
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
        });
    </script>
    
    <!-- Modal del Lector de Código de Barras -->
    <x-barcode-scanner-modal targetModel="sku" />
</x-app-layout>