<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;  // Asegúrate de incluir el modelo de Product
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::all();  // Obtiene todos los productos
        return response()->json($products);  // Devuelve los productos como una respuesta JSON
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        // Esta función generalmente no se usa en APIs RESTful, ya que es para formularios
        // Solo puedes manejar la creación a través del método store.
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        // Validación de los datos
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // Crear un nuevo producto
        $product = Product::create($validatedData);

        // Devolver respuesta
        return response()->json($product, 201);  // Devuelve el producto creado con el código 201 (creado)
    }

    /**
     * Display the specified product.
     */
    public function show(string $id)
    {
        // Buscar el producto por ID
        $product = Product::find($id);

        // Verificar si el producto existe
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);  // 404 si no se encuentra
        }

        // Devolver el producto
        return response()->json($product);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(string $id)
    {
        // Esta función generalmente no se usa en APIs RESTful, ya que es para formularios
        // Solo puedes manejar la edición a través del método update.
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, string $id)
    {
        // Buscar el producto por ID
        $product = Product::find($id);

        // Verificar si el producto existe
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Validación de los datos
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // Actualizar el producto
        $product->update($validatedData);

        // Devolver el producto actualizado
        return response()->json($product);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(string $id)
    {
        // Buscar el producto por ID
        $product = Product::find($id);

        // Verificar si el producto existe
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Eliminar el producto
        $product->delete();

        // Devolver respuesta
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
