<div class="p-6">

    <!-- Título de la sección -->
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">
        Gestor de Zapatos
    </h1>

    <!-- Botón de Agregar Producto -->
    <button wire:click="showModal" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-6 rounded-lg shadow-md mb-4 transition duration-200">
        Agregar Producto
    </button>


<!-- Agregar botón para descargar PDF
<button wire:click="downloadPdf" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg shadow-md mb-4">
    Descargar PDF
</button> -->

<!-- Agregar botón para descargar PDF -->
<button wire:click="downloadPdf" style="background-color: #9B1C31 !important;" class="hover:bg-red-900 text-white py-2 px-6 rounded-lg shadow-md mb-4">
    Descargar PDF
</button>




    <!-- Modal de agregar/editar producto -->
    @if($modalVisible)
        <div class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-50">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-white rounded-lg shadow-xl p-8 w-1/3 space-y-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                        {{ $productoId ? 'Actualizar Producto' : 'Agregar Producto' }}
                    </h2>
                    <form wire:submit.prevent="{{ $productoId ? 'update' : 'create' }}">
                        <!-- Nombre -->
                        <div class="mb-6">
                            <label for="nombre" class="block text-gray-700 text-sm font-medium mb-2">Nombre:</label>
                            <input type="text" id="nombre" wire:model="nombre" 
                                   class="border rounded-lg w-full py-3 px-4 text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 mt-1">
                            @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Imagen -->
                        <div class="mb-6">
                            <label for="imagen" class="block text-gray-700 text-sm font-medium mb-2">Imagen:</label>
                            <input type="file" id="imagen" wire:model="imagen" 
                                   class="border rounded-lg w-full py-3 px-4 text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 mt-1">
                            @error('imagen') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-6 rounded-lg shadow-md transition duration-200">
                                {{ $productoId ? 'Actualizar' : 'Guardar' }}
                            </button>
                            <button type="button" wire:click="closeModal" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-6 rounded-lg shadow-md transition duration-200">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de confirmación de eliminación -->
    @if($confirmDelete)
        <div class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-50">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-white rounded-lg shadow-xl p-8 w-1/3 space-y-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">¿Estás seguro de eliminar este producto?</h2>
                    <div class="flex justify-end gap-4">
                        <button wire:click="delete" class="bg-red-600 hover:bg-red-700 text-white py-2 px-6 rounded-lg shadow-md transition duration-200">
                            Sí, eliminar
                        </button>
                        <button wire:click="cancelDelete" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-6 rounded-lg shadow-md transition duration-200">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabla de productos -->
    <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
        <table class="table-auto w-full border-collapse text-sm text-gray-700">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border-b px-4 py-2">ID</th>
                    <th class="border-b px-4 py-2">Nombre</th>
                    <th class="border-b px-4 py-2">Imagen</th>
                    <th class="border-b px-4 py-2">Creado</th>
                    <th class="border-b px-4 py-2">Actualizado</th>
                    <th class="border-b px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $producto)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="border-b px-4 py-2">{{ $producto->id }}</td>
                        <td class="border-b px-4 py-2">{{ $producto->nombre }}</td>
                        <td class="border-b px-4 py-2">
                            <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto" class="h-16 rounded-md">
                        </td>
                        <td class="border-b px-4 py-2">{{ $producto->created_at }}</td>
                        <td class="border-b px-4 py-2">{{ $producto->update_at }}</td>
                        <!-- Alineación de los botones -->
                        <td class="border-b px-4 py-2 text-center">
                            <div class="flex justify-center gap-4">
                                <!-- Botón de Editar -->
                                <button wire:click="edit({{ $producto->id }})" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-6 rounded-lg shadow-md">
                                    Editar
                                </button>
                                <!-- Botón de Eliminar -->
                                <button wire:click="confirmDelete({{ $producto->id }})" class="bg-red-600 hover:bg-red-700 text-white py-2 px-6 rounded-lg shadow-md">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
