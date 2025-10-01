<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client; // Asegúrate de que estás importando el modelo Client
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     Mostrar todos los clientes
     */
    public function index()
    {
        $clients = Client::all();
        return response()->json($clients);
    }

    /**
     * Store a newly created resource in storage.
     Crear un nuevo cliente
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'nullable|email|unique:clients,email',
            'phone' => 'nullable|string',
        ]);

        // Crear el cliente
        $client = Client::create($request->all());
        return response()->json($client, 201);  // 201 para indicar que se creó correctamente
    }

    /**
     * Display the specified resource.
     Mostrar un cliente específico
     */
    public function show($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json($client);
    }

    /**
     * Update the specified resource in storage.
     Actualizar un cliente
     */
    public function update(Request $request, $id)
    {        
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        // Validación antes de actualizar
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'nullable|email|unique:clients,email,' . $id,
            'phone' => 'nullable|string',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'phone.max' => 'El teléfono no puede tener más de 8 caracteres.',
        ]);

        // Actualizar el cliente
        $client->update($request->all());

        return response()->json($client);
    }

    /**
     * Remove the specified resource from storage.
     Eliminar un cliente
     */
    public function destroy($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $client->delete();

        return response()->json(['message' => 'Cliente eliminado']);
    }

    
}
