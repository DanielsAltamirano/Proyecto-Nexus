<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Catálogo de Servicios
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Administra los servicios médicos y sus precios.
                    </p>
                </div>
                <div class="mt-5 sm:mt-0 flex flex-col sm:flex-row gap-3 w-full sm:w-auto justify-end" x-data>
                    <button @click="$dispatch('open-create-modal')" class="w-full sm:w-auto group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-green-700 rounded-xl hover:bg-green-800 hover:-translate-y-1 hover:shadow-lg hover:shadow-green-500/30 whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Nuevo Servicio</span>
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="servicesManager()" @open-create-modal.window="openCreateModal()">
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
            
            @if($errors->any())
                <div class="mb-6 flex items-center p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="font-medium">
                        <ul class="list-disc pl-5 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif



            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl transition-all duration-300 hover:shadow-md">
                <div class="p-0 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Servicio</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Precio</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($services as $service)
                            <tr class="hover:bg-gray-50/80 transition-colors duration-150 group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        {{ $service->id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-xl bg-green-50 text-green-600 border border-green-100 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900 group-hover:text-green-600 transition-colors">{{ $service->name }}</div>
                                            @if(isset($service->description))
                                                <div class="text-xs text-gray-500 truncate max-w-xs">{{ $service->description }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-bold bg-green-50 text-green-700 border border-green-200">
                                        ${{ number_format($service->price, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEditModal({{ $service }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors tooltip" title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button @click="openDeleteModal({{ $service }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors tooltip" title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h10"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        </div>
                                        <p class="text-lg font-medium text-gray-900">No hay servicios registrados</p>
                                        <p class="text-sm text-gray-500 mt-1">Comienza agregando tu primer servicio médico.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Create/Edit Modal -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showModal" x-transition.scale class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form :action="isEditing ? '/services/' + form.id : '{{ route('services.store') }}'" method="POST">
                        @csrf
                        <template x-if="isEditing">
                            @method('PUT')
                        </template>
                        
                        <div class="bg-white px-6 pt-6 pb-6">
                            <h3 class="text-2xl font-black text-gray-900 mb-6" x-text="isEditing ? '{{ auth()->user()->hasRole('Doctor') ? 'Solicitar Cambio de Servicio' : 'Editar Servicio' }}' : 'Nuevo Servicio'"></h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Servicio</label>
                                    <input type="text" name="name" x-model="form.name" required class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-colors bg-gray-50 p-3">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Precio ($)</label>
                                    <input type="number" step="0.01" name="price" x-model="form.price" required class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-colors bg-gray-50 p-3">
                                </div>
                                
                                <template x-if="isEditing && {{ auth()->user()->hasRole('Doctor') ? 'true' : 'false' }}">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Motivo de la actualización</label>
                                        <textarea name="reason" required rows="2" class="w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-colors bg-gray-50 p-3" placeholder="Explica por qué solicitas este cambio..."></textarea>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-sm transition-colors" x-text="isEditing ? '{{ auth()->user()->hasRole('Doctor') ? 'Enviar Solicitud' : 'Guardar Cambios' }}' : 'Crear Servicio'"></button>
                            <button type="button" @click="showModal = false" class="bg-white hover:bg-gray-100 text-gray-700 font-bold py-2.5 px-6 rounded-xl border border-gray-300 transition-colors">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showDeleteModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showDeleteModal" x-transition.scale class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form :action="'/services/' + form.id" method="POST">
                        @csrf
                        @method('DELETE')
                        
                        <div class="bg-white px-6 pt-6 pb-6 text-center">
                            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                                <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 mb-2">{{ auth()->user()->hasRole('Doctor') ? '¿Solicitar eliminar servicio?' : '¿Eliminar servicio?' }}</h3>
                            <p class="text-gray-500 mb-4">¿Estás seguro de que deseas {{ auth()->user()->hasRole('Doctor') ? 'solicitar la eliminación de' : 'eliminar' }} <span class="font-bold text-gray-800" x-text="form.name"></span>?</p>
                            
                            @if(auth()->user()->hasRole('Doctor'))
                            <div class="text-left mt-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Motivo de la eliminación</label>
                                <textarea name="reason" required rows="2" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring focus:ring-red-200 transition-colors bg-gray-50 p-3" placeholder="Explica por qué este servicio ya no debe existir..."></textarea>
                            </div>
                            @else
                                <p class="text-red-500 font-bold text-sm">Esta acción no se puede deshacer.</p>
                            @endif
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-sm transition-colors">{{ auth()->user()->hasRole('Doctor') ? 'Enviar Solicitud' : 'Eliminar Definitivamente' }}</button>
                            <button type="button" @click="showDeleteModal = false" class="bg-white hover:bg-gray-100 text-gray-700 font-bold py-2.5 px-6 rounded-xl border border-gray-300 transition-colors">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('servicesManager', () => ({
                showModal: false,
                showDeleteModal: false,
                isEditing: false,
                form: {
                    id: '',
                    name: '',
                    price: '',
                },
                
                openCreateModal() {
                    this.isEditing = false;
                    this.form = { id: '', name: '', price: '' };
                    this.showModal = true;
                },
                
                openEditModal(service) {
                    this.isEditing = true;
                    this.form = { ...service };
                    this.showModal = true;
                },
                
                openDeleteModal(service) {
                    this.form = { ...service };
                    this.showDeleteModal = true;
                }
            }));
        });
    </script>
</x-app-layout>
