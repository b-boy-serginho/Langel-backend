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
            'nro'         => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $now = now();
        $day = $now->locale('es')->dayName;

        $receipt = Receipt::create([
            'id_client'   => $validated['id_client'],
            'nro'         => $validated['nro'] ?? null,
            'total'       => 0,
            'date'        => $now->toDateString(),
            'hour'        => $now->format('H:i:s'),
            'day'         => $day,
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'message' => 'Recibo creado exitosamente',
            'receipt' => $receipt->load(['client'])
        ], 201);
    }
    
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
            'nro' => 'nullable|string',
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

        return response()->json([
            'message' => 'Recibo actualizado exitosamente',
            'receipt' => $receipt
        ]);
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

       return response()->json(['message' => 'Recibo eliminado exitosamente'], 204);// Respuesta sin contenido, con código de estado 204
    }

    /**
     * Update the total amount of a receipt after adding details.
     */
    public function updateTotal($receiptId)
    {
        $receipt = Receipt::with('details')->findOrFail($receiptId);

        $total = $receipt->details()->sum('amount'); // << clave
        $receipt->update(['total' => $total]);

        return response()->json([
            'message' => 'Total actualizado exitosamente',
            'receipt' => $receipt->fresh(['client','details.product'])
        ]);
    }

    // public function indexByClient($clientId)
    // {
    //     // Obtener los recibos solo del cliente con el ID proporcionado
    //     $receipts = Receipt::with(['client', 'details.product'])
    //         ->where('id_client', $clientId) // Filtramos por el id del cliente
    //         ->latest() // Ordenamos por la fecha más reciente
    //         ->get();

    //     // Agrupar en secciones de 6 recibos cada una
    //     $perSection = 6;
    //     $sections = $receipts->chunk($perSection)->values()->map(function ($chunk, $index) {
    //         return [
    //             'section' => $index + 1,
    //             'receipts' => $chunk->values(),
    //         ];
    //     });

    //     return response()->json([
    //         'client_id' => $clientId,
    //         'per_section' => $perSection,
    //         'sections' => $sections,
    //     ]);
    // }   

    public function indexByClient(Request $request, $clientId)
    {
        // Validate client exists (opcional pero recomendable)
        // Client::findOrFail($clientId);

        $perSection = (int) $request->query('pageSize', 6);
        $page = max(1, (int) $request->query('page', 1));

        // total count (solo cuenta)
        $total = Receipt::where('id_client', $clientId)->count();
        $totalSections = (int) ceil($total / max(1, $perSection));

        $receipts = Receipt::with(['client', 'details.product'])
            ->where('id_client', $clientId)
            ->latest()
            ->skip(($page - 1) * $perSection)
            ->take($perSection)
            ->get();

        // Suma de 'total' para la sección actual (página)
        $sectionTotal = $receipts->sum('total');

        // Sumas por cada sección (opcional, calcula todas las secciones)
        $totalsPluck = Receipt::where('id_client', $clientId)
            ->latest()
            ->pluck('total'); // solo trae el campo 'total' para eficiencia

        $sectionsTotals = $totalsPluck
            ->chunk($perSection)
            ->values()
            ->map(function ($chunk, $index) {
                return [
                    'section' => $index + 1,
                    'count' => $chunk->count(),
                    'sum' => (float) $chunk->sum(),
                ];
            });

        return response()->json([
            'client_id' => $clientId,
            'page' => $page,
            'pageSize' => $perSection,
            'total' => $total,
            'totalSections' => $totalSections,
            'sectionTotal' => (float) $sectionTotal,
            'sectionsTotals' => $sectionsTotals,
            'receipts' => $receipts,
        ]);
    }

}
