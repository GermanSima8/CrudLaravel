<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;



// Obtener todos los productos (GET)
Route::get('/productos', function () {
    $productos = DB::table('productos')->get();
    return response()->json($productos);
});

// Crear un producto (POST)
Route::post('/productos', function (Request $request) {
    $request->validate([
        'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Nombre de la carpeta dentro de `storage/app/public`
    $carpeta = 'imagenes_locales';

    // Crear la carpeta si no existe
    if (!Storage::exists("public/$carpeta")) {
        Storage::makeDirectory("public/$carpeta");
    }

    // Guardar la imagen en la carpeta
    $rutaImagen = $request->file('imagen')->store("public/$carpeta");

    // Obtener solo el nombre del archivo
    $nombreImagen = basename($rutaImagen);

    // Guardar la ruta de la imagen en el campo `nombre`
    DB::table('productos')->insert([
        'nombre' => "$carpeta/$nombreImagen", // Guardar la ruta relativa de la imagen
        'created_at' => now(),
        'update_at' => now(),
    ]);

    return response()->json([
        'message' => 'Producto creado con éxito.',
        'imagen_url' => asset("storage/$carpeta/$nombreImagen"),
    ]);
});

// Actualizar un producto (PUT)
Route::put('/productos/{id}', function (Request $request, $id) {
    $validatedData = $request->validate([
        'nombre' => 'required|string|max:255',
        'imagen' => 'nullable|image|max:2048',
    ]);

    $producto = DB::table('productos')->where('id', $id)->first();

    if (!$producto) {
        return response()->json(['error' => 'Producto no encontrado'], 404);
    }

    $rutaImagen = $producto->imagen; // Mantener la imagen actual si no se sube una nueva

    if ($request->hasFile('imagen')) {
        // Eliminar la imagen anterior
        if (file_exists(storage_path('app/public/' . $producto->imagen))) {
            unlink(storage_path('app/public/' . $producto->imagen));
        }

        // Guardar la nueva imagen
        $nuevaRutaImagen = $request->file('imagen')->store('public/ImagenesLocales');
        $rutaImagen = str_replace('public/', '', $nuevaRutaImagen);
    }

    // Actualizar el producto
    DB::table('productos')->where('id', $id)->update([
        'nombre' => $validatedData['nombre'],
        'imagen' => $rutaImagen,
        'update_at' => now(),
    ]);

    return response()->json(['message' => 'Producto actualizado correctamente'], 200);
});

// Eliminar un producto (DELETE)
Route::delete('/productos/{id}', function ($id) {
    // Buscar el producto en la base de datos
    $producto = DB::table('productos')->where('id', $id)->first();

    if (!$producto) {
        return response()->json(['error' => 'Producto no encontrado'], 404);
    }

    // Eliminar la imagen del producto de almacenamiento
    if (Storage::exists('public/' . $producto->imagen)) {
        Storage::delete('public/' . $producto->imagen);
    }

    // Eliminar el producto de la base de datos
    DB::table('productos')->where('id', $id)->delete();

    return response()->json(['message' => 'Producto eliminado correctamente'], 200);
});

