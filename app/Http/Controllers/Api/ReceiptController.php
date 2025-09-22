<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $receipts = Receipt::with(['client','details.product'])->latest()->get();
        return response()->json($receipts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Este método se usa en formularios, no es necesario para una API RESTful.
        // Lo dejamos vacío.
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_client'   => 'required|exists:clients,id',
            'nro'         => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $now = now();
        $day = $now->locale('es')->dayName;

        $receipt = Receipt::create([
            'id_client'   => $validated['id_client'],
            'nro'         => $validated['nro'],
            'total'       => 0,
            'date'        => $now->toDateString(),
            'hour'        => $now->format('H:i:s'),
            'day'         => $day,
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json($receipt->load(['client']), 201);
    }


    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     // Validación de los datos recibidos
    //     $validated = $request->validate([
    //         'id_client' => 'required|exists:clients,id',
    //         'nro' => 'required|string|max:255',
    //         'description' => 'nullable|string', // description no es obligatorio
    //     ]);

    //     // Obtener la fecha y hora actual en Bolivia
    //     $currentDate = now(); // Esto usará la zona horaria de Bolivia configurada en config/app.php
    //     $dayOfWeek = Carbon::now()->locale('es')->isoFormat('dddd'); // Día de la semana en español

    //     // Crear el nuevo recibo con el total inicializado a 0
    //     $receipt = Receipt::create([
    //         'id_client' => $request->id_client,
    //         'nro' => $validated['nro'],
    //         'total' => 0,  // Inicializamos el total a 0
    //         'date' => $currentDate->toDateString(), // solo la fecha
    //         'hour' => $currentDate->toTimeString(), // solo la hora
    //         'day' => $dayOfWeek, // Guardamos el día de la semana en español
    //         'description' => $validated['description'],
    //     ]);

    //     return response()->json($receipt, 201); // Retorna el recibo creado con un código de estado 201
    // }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         $receipt = Receipt::with(['client','details.product'])->findOrFail($id);
        return response()->json($receipt);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Este método se usa en formularios, no es necesario para una API RESTful.
        // Lo dejamos vacío.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validación de los datos recibidos
        $validated = $request->validate([
            'id_client' => 'required|exists:clients,id',
            'nro' => 'required|string|max:255',
            // 'total' => 'required|numeric',
            // 'date' => 'required|date',
            // 'hour' => 'required|date_format:H:i',
            'description' => 'nullable|string',
        ]);

        // Encontrar el recibo
        $receipt = Receipt::findOrFail($id);

                // Obtener la fecha y hora actual en Bolivia
        $currentDate = now(); // Esto usará la zona horaria de Bolivia configurada en config/app.php
        $dayOfWeek = Carbon::now()->locale('es')->isoFormat('dddd'); // Día de la semana en español

        // Actualizar solo los campos requeridos y los campos automáticos
        $receipt->update([
            'id_client' => $request->id_client,
            'nro' => $validated['nro'],
            'description' => $validated['description'], // La descripción puede ser nula
            'total' => $receipt->total,  // Se mantiene el valor actual de 'total'
            'date' => $currentDate->toDateString(), // Fecha actual
            'hour' => $currentDate->toTimeString(), // Hora actual
            'day' => $dayOfWeek,  // Día de la semana
        ]);

        return response()->json($receipt);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Buscar el recibo por ID
        $receipt = Receipt::findOrFail($id);

        // Eliminar el recibo
        $receipt->delete();

        return response()->json(null, 204); // Respuesta sin contenido, con código de estado 204
    }

    /**
     * Update the total amount of a receipt after adding details.
     */
    public function updateTotal($receiptId)
    {
        $receipt = Receipt::with('details')->findOrFail($receiptId);

        $total = $receipt->details()->sum('amount'); // << clave
        $receipt->update(['total' => $total]);

        return response()->json($receipt->fresh(['client','details.product']));
    }
}
