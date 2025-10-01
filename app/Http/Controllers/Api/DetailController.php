<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Detail; // Importamos el modelo Detail
use App\Models\Receipt; // Importamos el modelo Recibo
use App\Models\Product; // Importamos el modelo Prodcto

use Illuminate\Http\Request;

class DetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $details = Detail::with([
            'product',             // producto del detalle
            'receipt.client'       // recibo con el cliente relacionado
        ])->get();
        // $details = Detail::with(['receipt', 'product'])->get(); // Cargar tanto el recibo como el producto relacionado
        return response()->json($details);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Esta función generalmente se usa para mostrar un formulario en el frontend
        // Como estamos usando API, no es necesario hacer nada aquí.
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
       $validated = $request->validate([
            'id_product' => 'required|exists:products,id',
            'id_receipt' => 'required|exists:receipts,id',
            'quantity'   => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        $product   = Product::findOrFail($validated['id_product']);
        $unitPrice = $validated['unit_price'] ?? $product->price;
        $amount    = $unitPrice * $validated['quantity'];

        $detail = Detail::create([
            'id_product' => $validated['id_product'],
            'id_receipt' => $validated['id_receipt'],
            'quantity'   => $validated['quantity'],
            'unit_price' => $unitPrice,
            'amount'     => $amount,
        ]);

        $this->updateReceiptTotal($validated['id_receipt']);

        return response()->json($detail->load(['receipt','product']), 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Buscamos el detalle por ID
        $detail = Detail::find($id);

        // Si no se encuentra, retornamos un error 404
        if (!$detail) {
            return response()->json(['message' => 'Detail not found'], 404);
        }

        return response()->json($detail);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Esta función generalmente se usa para mostrar un formulario en el frontend
        // Como estamos usando API, no es necesario hacer nada aquí.
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, string $id)
    {
        $detail = Detail::find($id);
        if (!$detail) {
            return response()->json(['message' => 'Detail not found'], 404);
        }

        $validated = $request->validate([
            'id_product' => 'required|exists:products,id',
            'id_receipt' => 'required|exists:receipts,id',
            'quantity'   => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        $product   = Product::findOrFail($validated['id_product']);
        $unitPrice = $validated['unit_price'] ?? $product->price;
        $amount    = $unitPrice * $validated['quantity'];

        // Actualizamos el detalle
        $detail->update([
            'id_product' => $validated['id_product'],
            'id_receipt' => $validated['id_receipt'],
            'quantity'   => $validated['quantity'],
            'unit_price' => $unitPrice,
            'amount'     => $amount,
        ]);

        // Recalcular el total del recibo
        $this->updateReceiptTotal($validated['id_receipt']);

        // Devolver el detalle actualizado
        return response()->json($detail->load(['receipt', 'product']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         $detail = Detail::find($id);
        if (!$detail) {
            return response()->json(['message' => 'Detail not found'], 404);
        }

        $receiptId = $detail->id_receipt;
        $detail->delete();

        $this->updateReceiptTotal($receiptId);

        return response()->json(['message' => 'Detail deleted successfully']);
    }


    /** Recalcula el total del recibo */
    private function updateReceiptTotal(int $receiptId): void
    {
        $total = (float) Detail::where('id_receipt', $receiptId)->sum('amount');
        Receipt::where('id', $receiptId)->update(['total' => $total]);
    }

}
