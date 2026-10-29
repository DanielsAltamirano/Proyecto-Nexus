<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-green-50 rounded-full opacity-70 blur-2xl transition-transform duration-700 group-hover:scale-150"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h2 class="font-extrabold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Personal y Sucursales
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Administra el personal y las sucursales del sistema.
                    </p>
                </div>
                <div class="mt-5 sm:mt-0 flex flex-col sm:flex-row gap-3">
                    @if(auth()->user()->hasRole(['admin', 'superadmin']))
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-create-staff'))" class="btn-group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-blue-600 rounded-xl hover:bg-blue-700 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-500/30">
                            <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            <span>Registrar Personal</span>
                        </button>
                    @endif
                    @if(auth()->user()->hasRole('superadmin'))
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-create-branch'))" class="btn-group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-300 ease-in-out bg-green-700 rounded-xl hover:bg-green-800 hover:-translate-y-1 hover:shadow-lg hover:shadow-green-500/30">
                            <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Registrar Sucursal</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        createBranchModal: false,
        createStaffModal: false,
        editBranchModal: false,
        branchEditUrl: '',
        branchEditName: '',
        branchEditAddress: '',
        branchEditPhone: '',
        branchEditMapsUrl: ''
    }"
    @open-create-branch.window="createBranchModal = true"
    @open-create-staff.window="createStaffModal = true">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 flex items-center p-4 text-green-800 bg-green-50 border border-green-200 rounded-xl shadow-sm animate-pulse" role="alert">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 flex items-center p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm animate-pulse" role="alert">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-sm" role="alert">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-bold">Por favor corrige los siguientes errores:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm ml-8">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl transition-all duration-300 hover:shadow-md">
                <div class="p-0 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Contacto</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sucursal y Rol</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-50/80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center text-green-700 font-bold">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900 flex items-center">
                                                    {{ $user->name }}
                                                    @if(auth()->id() === $user->id)
                                                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Tú</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->phone ?? 'Sin teléfono' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(auth()->id() === $user->id)
                                            <span class="text-sm text-gray-500 block mb-1">
                                                Sucursal: <span class="font-bold">{{ $user->branch ? $user->branch->name : 'Central / Global' }}</span>
                                            </span>
                                            <span class="text-sm text-gray-500 block">Rol: {{ ucfirst($user->roles->first()?->name ?? 'Sin rol') }}</span>
                                        @else
                                            <form action="{{ route('users.updateStaff', $user) }}" method="POST" class="flex flex-col sm:flex-row items-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                <select name="branch_id" class="text-sm border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm w-full sm:w-auto" onchange="this.form.submit()">
                                                    <option value="">Central / Global</option>
                                                    @foreach($branches as $branch)
                                                        <option value="{{ $branch->id }}" {{ $user->branch_id == $branch->id ? 'selected' : '' }}>
                                                            {{ $branch->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <select name="role" class="text-sm border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm w-full sm:w-auto" onchange="this.form.submit()">
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                            {{ ucfirst($role->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="text-red-500 hover:text-red-700 hover:underline transition-colors ml-3" @click.prevent="$dispatch('open-confirm-delete', { message: '¿Estás seguro de dar de baja a este usuario? Perderá acceso al sistema.', form: $el.closest('form') })">
                                                    Dar de Baja
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 italic text-xs">No puedes darte de baja</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        No hay personal registrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- SECCIÓN DE SUCURSALES -->
            <div class="mt-12 mb-6 flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h3 class="font-extrabold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-green-900 to-green-600 tracking-tight">
                        Sucursales
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Consulta y administra las sucursales físicas.
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl transition-all duration-300 hover:shadow-md">
                <div class="p-0 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dirección</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Teléfono</th>
                                @if(auth()->user()->hasRole('superadmin'))
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($branches as $branch)
                                <tr class="hover:bg-gray-50/80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 bg-green-100 p-2 rounded-lg text-green-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $branch->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                        {{ $branch->address ?? 'No registrada' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $branch->phone ?? 'N/A' }}
                                    </td>
                                    @if(auth()->user()->hasRole('superadmin'))
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="
                                                branchEditUrl = '{{ route('branches.update', $branch) }}';
                                                branchEditName = '{{ addslashes($branch->name) }}';
                                                branchEditAddress = '{{ addslashes($branch->address ?? '') }}';
                                                branchEditPhone = '{{ addslashes($branch->phone ?? '') }}';
                                                branchEditMapsUrl = '{{ addslashes($branch->maps_url ?? '') }}';
                                                editBranchModal = true;
                                            " class="text-blue-500 hover:text-blue-700 mx-3 transition-colors focus:outline-none">Editar</button>
                                            
                                            <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="text-red-400 hover:text-red-600 transition-colors" @click.prevent="$dispatch('open-confirm-delete', { message: '¿Eliminar esta sucursal?', form: $el.closest('form') })">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->hasRole('superadmin') ? '4' : '3' }}" class="px-6 py-12 text-center text-gray-500">
                                        No hay sucursales registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>

        <!-- CREATE BRANCH MODAL -->
        @if(auth()->user()->hasRole('superadmin'))
        <div x-show="createBranchModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak style="display: none;">
            <div x-show="createBranchModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="createBranchModal = false"></div>
            
            <div x-show="createBranchModal" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden border border-gray-100">
                
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-800 to-green-500"></div>
                
                <div class="flex justify-between items-center p-6 border-b border-gray-50">
                    <h3 class="text-xl font-bold text-gray-900">Registrar Sucursal</h3>
                    <button @click="createBranchModal = false" class="text-gray-400 hover:text-red-500 transition-colors focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('branches.store') }}">
                    @csrf
                    <div class="p-6 space-y-5">
                        <div class="space-y-1">
                            <label for="create_name" class="block text-sm font-bold text-gray-700">Nombre de la Sucursal <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="create_name" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white" required placeholder="Ej. Sucursal Centro">
                        </div>
                        <div class="space-y-1">
                            <label for="create_address" class="block text-sm font-bold text-gray-700">Dirección</label>
                            <input type="text" name="address" id="create_address" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white" placeholder="Av. Principal #123">
                        </div>
                        <div class="space-y-1">
                            <label for="create_phone" class="block text-sm font-bold text-gray-700">Teléfono</label>
                            <input type="text" name="phone" id="create_phone" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white" placeholder="10 dígitos">
                        </div>
                        <div class="space-y-1">
                            <label for="create_maps_url" class="block text-sm font-bold text-gray-700">Enlace Google Maps (Opcional)</label>
                            <input type="url" name="maps_url" id="create_maps_url" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white" placeholder="https://maps.app.goo.gl/...">
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="createBranchModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-100 transition-all focus:outline-none">Cancelar</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-green-700 rounded-xl hover:bg-green-800 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">Guardar Sucursal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT BRANCH MODAL -->
        <div x-show="editBranchModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak style="display: none;">
            <div x-show="editBranchModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="editBranchModal = false"></div>
            
            <div x-show="editBranchModal" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden border border-gray-100">
                
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-800 to-green-500"></div>
                
                <div class="flex justify-between items-center p-6 border-b border-gray-50">
                    <h3 class="text-xl font-bold text-gray-900">Editar Sucursal</h3>
                    <button @click="editBranchModal = false" class="text-gray-400 hover:text-red-500 transition-colors focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form method="POST" :action="branchEditUrl">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-5">
                        <div class="space-y-1">
                            <label for="edit_name" class="block text-sm font-bold text-gray-700">Nombre de la Sucursal <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="edit_name" x-model="branchEditName" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white" required>
                        </div>
                        <div class="space-y-1">
                            <label for="edit_address" class="block text-sm font-bold text-gray-700">Dirección</label>
                            <input type="text" name="address" id="edit_address" x-model="branchEditAddress" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white">
                        </div>
                        <div class="space-y-1">
                            <label for="edit_phone" class="block text-sm font-bold text-gray-700">Teléfono</label>
                            <input type="text" name="phone" id="edit_phone" x-model="branchEditPhone" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white">
                        </div>
                        <div class="space-y-1">
                            <label for="edit_maps_url" class="block text-sm font-bold text-gray-700">Enlace Google Maps (Opcional)</label>
                            <input type="url" name="maps_url" id="edit_maps_url" x-model="branchEditMapsUrl" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-600 focus:border-green-600 block w-full p-3 transition-all hover:bg-white" placeholder="https://maps.app.goo.gl/...">
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="editBranchModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-100 transition-all focus:outline-none">Cancelar</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-green-700 rounded-xl hover:bg-green-800 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">Actualizar Sucursal</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- CREATE STAFF MODAL -->
        @if(auth()->user()->hasRole(['admin', 'superadmin']))
        <div x-show="createStaffModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak style="display: none;">
            <div x-show="createStaffModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="createStaffModal = false"></div>
            
            <div x-show="createStaffModal" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden border border-gray-100 max-h-[90vh] overflow-y-auto">
                
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-800 to-blue-500"></div>
                
                <div class="flex justify-between items-center p-6 border-b border-gray-50">
                    <h3 class="text-xl font-bold text-gray-900">Registrar Personal</h3>
                    <button @click="createStaffModal = false" class="text-gray-400 hover:text-red-500 transition-colors focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('users.staff.store') }}">
                    @csrf
                    <div class="p-6 space-y-5">
                        <div class="space-y-1">
                            <label for="staff_name" class="block text-sm font-bold text-gray-700">Nombre Completo <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="staff_name" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-600 focus:border-blue-600 block w-full p-3 transition-all hover:bg-white" required>
                        </div>
                        <div class="space-y-1">
                            <label for="staff_email" class="block text-sm font-bold text-gray-700">Correo Electrónico <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="staff_email" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-600 focus:border-blue-600 block w-full p-3 transition-all hover:bg-white" required>
                        </div>
                        <div class="space-y-1">
                            <label for="staff_phone" class="block text-sm font-bold text-gray-700">Teléfono</label>
                            <input type="tel" name="phone" id="staff_phone" pattern="[0-9]{10}" maxlength="10" title="Debe contener exactamente 10 números" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-600 focus:border-blue-600 block w-full p-3 transition-all hover:bg-white" placeholder="10 dígitos">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1" x-data="{ show: false }">
                                <label for="staff_password" class="block text-sm font-bold text-gray-700">Contraseña <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="password" id="staff_password" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-600 focus:border-blue-600 block w-full p-3 pr-10 transition-all hover:bg-white" required autocomplete="new-password">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <button type="button" @click="show = !show" class="text-gray-400 hover:text-blue-600 focus:outline-none transition-colors" title="Mostrar/Ocultar contraseña">
                                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            <svg x-show="show" class="w-5 h-5" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-1" x-data="{ show: false }">
                                <label for="staff_password_confirmation" class="block text-sm font-bold text-gray-700">Confirmar <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="password_confirmation" id="staff_password_confirmation" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-600 focus:border-blue-600 block w-full p-3 pr-10 transition-all hover:bg-white" required autocomplete="new-password">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <button type="button" @click="show = !show" class="text-gray-400 hover:text-blue-600 focus:outline-none transition-colors" title="Mostrar/Ocultar contraseña">
                                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            <svg x-show="show" class="w-5 h-5" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="staff_role" class="block text-sm font-bold text-gray-700">Rol <span class="text-red-500">*</span></label>
                            <select name="role" id="staff_role" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-600 focus:border-blue-600 block w-full p-3 transition-all hover:bg-white" required>
                                <option value="">Selecciona un rol</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label for="staff_branch" class="block text-sm font-bold text-gray-700">Sucursal Asignada</label>
                            <select name="branch_id" id="staff_branch" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-600 focus:border-blue-600 block w-full p-3 transition-all hover:bg-white">
                                <option value="">Central / Global</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="createStaffModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-100 transition-all focus:outline-none">Cancelar</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
