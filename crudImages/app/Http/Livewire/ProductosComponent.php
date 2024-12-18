<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Intervention\Image\Facades\Image;


class ProductosComponent extends Component
{
    use WithFileUploads;

    public $nombre;
    public $imagen;
    public $productoId;
    public $modalVisible = false;
    public $confirmDelete = false; // Nueva variable para el modal de confirmación

    public function render()
    {
        $productos = DB::table('productos')->get();

        return view('livewire.productos-component', [
            'productos' => $productos,
        ]);
    }
    

    // Mostrar el modal para agregar un producto
    public function showModal()
    {
        $this->resetFields(); // Resetea los campos del modal
        $this->modalVisible = true;
    }

    // Mostrar el modal para editar un producto
    public function edit($productoId)
    {
        $producto = DB::table('productos')->where('id', $productoId)->first();
        $this->productoId = $producto->id;
        $this->nombre = $producto->nombre;
        $this->imagen = $producto->imagen; // Mantener la imagen actual

        $this->modalVisible = true; // Abrir el modal para editar
    }

    // Mostrar el modal de confirmación de eliminación
    public function confirmDelete($productoId)
    {
        $this->productoId = $productoId;
        $this->confirmDelete = true; // Mostrar el modal de confirmación
    }

    // Eliminar un producto
    public function delete()
    {
        $producto = DB::table('productos')->where('id', $this->productoId)->first();
        
        if ($producto) {
            // Asegúrate de que la ruta de la imagen sea correcta
            $imagenPath = 'public/ImagenesLocales/' . $producto->imagen;
            
            // Verificar si la imagen existe y eliminarla
            if (Storage::exists($imagenPath)) {
                Storage::delete($imagenPath); // Eliminar la imagen
            }
    
            // Eliminar el producto de la base de datos
            DB::table('productos')->where('id', $this->productoId)->delete();
        }
    
        // Cerrar modal de confirmación
        $this->confirmDelete = false;
        // Recargar los productos
        $this->emit('refreshComponent');
    }
    
    // Cancelar la eliminación
    public function cancelDelete()
    {
        $this->confirmDelete = false; // Cerrar el modal sin eliminar
    }

    // Cerrar el modal de agregar/editar producto
    public function closeModal()
    {
        $this->resetFields();
        $this->modalVisible = false;
    }

    // Restablecer los campos
    public function resetFields()
    {
        $this->nombre = null;
        $this->imagen = null;
        $this->productoId = null;
    }

    // Crear un nuevo producto
    public function create()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'imagen' => 'required|image|max:2048',
        ]);

        // Crear carpeta si no existe
        if (!Storage::exists('public/ImagenesLocales')) {
            Storage::makeDirectory('public/ImagenesLocales');
        }

        // Guardar la imagen
        $rutaImagen = $this->imagen->store('public/ImagenesLocales');
        $nombreImagen = str_replace('public/', '', $rutaImagen);

        // Insertar producto en la base de datos
        DB::table('productos')->insert([
            'nombre' => $this->nombre,
            'imagen' => $nombreImagen,
            'created_at' => now(),
            'update_at' => now(),
        ]);

        // Cerrar modal y recargar datos
        $this->closeModal();
        $this->emit('refreshComponent');
    }


//     public function create()
// {
//     $this->validate([
//         'nombre' => 'required|string|max:255',
//         'imagen' => 'required|image|max:2048', // Máximo 2MB
//     ]);

//     // Redimensionar y guardar la imagen
//     $rutaImagen = $this->imagen->store('public/ImagenesLocales');
//     $nombreImagen = str_replace('public/', '', $rutaImagen);

//     $image = Image::make(storage_path('app/storage/' . $nombreImagen))
//                   ->resize(300, 300, function ($constraint) {
//                       $constraint->aspectRatio(); // Mantener proporción
//                   })
//                   ->encode('jpg', 75); // Reducir calidad al 75%
//     $image->save(storage_path('app/storage/' . $nombreImagen));

//     // Insertar en la base de datos
//     DB::table('productos')->insert([
//         'nombre' => $this->nombre,
//         'imagen' => $nombreImagen,
//         'created_at' => now(),
//         'update_at' => now(), // Corregido a `updated_at`
//     ]);

//     $this->closeModal();
//     $this->emit('refreshComponent');
// }


    // Actualizar un producto existente
    public function update()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'imagen' => 'nullable|image|max:2048', // Imagen es opcional en update
        ]);

        // Si hay una nueva imagen, la subimos
        if ($this->imagen) {
            // Crear carpeta si no existe
            if (!Storage::exists('public/ImagenesLocales')) {
                Storage::makeDirectory('public/ImagenesLocales');
            }

            // Eliminar imagen anterior (si existe)
            if ($this->imagen && Storage::exists('public/ImagenesLocales/' . $this->imagen)) {
                Storage::delete('public/ImagenesLocales/' . $this->imagen);
            }

            // Guardar la nueva imagen
            $rutaImagen = $this->imagen->store('public/ImagenesLocales');
            $nombreImagen = str_replace('public/', '', $rutaImagen);
        } else {
            // Si no hay nueva imagen, mantenemos la anterior
            $nombreImagen = $this->imagen;
        }

        // Actualizar el producto en la base de datos
        DB::table('productos')
            ->where('id', $this->productoId)
            ->update([
                'nombre' => $this->nombre,
                'imagen' => $nombreImagen,
                'update_at' => now(),
            ]);

        // Cerrar modal y recargar datos
        $this->closeModal();
        $this->emit('refreshComponent');
    }


    public function downloadPdf()
    {
        // Obtener los datos de la tabla productos utilizando DB
        $productos = DB::table('productos')->get();
    
        // Cargar la vista del PDF y pasarle los datos
        $pdf = PDF::loadView('pdf.productos', compact('productos'));
    
        // Descargar el PDF
        return response()->streamDownload(
            fn () => print($pdf->output()),
            'productos.pdf'
        );
    }

    
}
