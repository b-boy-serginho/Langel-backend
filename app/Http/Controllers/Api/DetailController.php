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
        // Devuelve todos los detalles
        $details = Detail::all();
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
        // Validamos los datos de entrada
        $request->validate([
            'id_product' => 'required|exists:products,id', // Debe existir en la tabla products
            'id_receipt' => 'required|exists:receipts,id', // Debe existir en la tabla receipts
            'quantity' => 'required|numeric',
            'amount' => 'required|numeric',
            'unit_price' => 'nullable|numeric',
        ]);

        // Obtener el producto utilizando el id_product del request
        $product = Product::findOrFail($request->id_product); // Aquí corregimos el error

        // Creamos un nuevo registro en la tabla details
        $detail = Detail::create([
            'id_product' => $request->id_product,
            'id_receipt' => $request->id_receipt,
            'quantity' => $request->quantity,
            'unit_price' => $product->price, // Calculamos el precio unitario
            'amount' => $request->quantity *$product->price,  // Calculamos el monto total
        ]);

        // Actualizamos el total del recibo después de crear el detalle
        $this->updateReceiptTotal($request->id_receipt);

        // Devolvemos el detalle creado
        return response()->json($detail, 201);
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
        // Buscamos el detalle
        $detail = Detail::find($id);

        // Si no se encuentra, retornamos un error 404
        if (!$detail) {
            return response()->json(['message' => 'Detail not found'], 404);
        }

        // Validamos los datos de entrada
        $request->validate([
            'id_product' => 'required|exists:products,id', // Debe existir en la tabla products
            'id_receipt' => 'required|exists:receipts,id', // Debe existir en la tabla receipts
            'quantity' => 'required|numeric',
            'amount' => 'required|numeric',
            'unit_price' => 'nullable|numeric',
        ]);

        // Actualizamos los valores
        $detail->update([
            'id_product' => $request->id_product,
            'id_receipt' => $request->id_receipt,
            'quantity' => $request->quantity,
            'amount' => $request->amount,
            'unit_price' => $request->unit_price,
        ]);

         // Actualizamos el total del recibo después de actualizar el detalle
        $this->updateReceiptTotal($request->id_receipt);

        return response()->json($detail);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Buscamos el detalle
        $detail = Detail::find($id);

        // Si no se encuentra, retornamos un error 404
        if (!$detail) {
            return response()->json(['message' => 'Detail not found'], 404);
        }

        // Eliminamos el registro
        $detail->delete();

        return response()->json(['message' => 'Detail deleted successfully']);
    }

    // Este método actualiza el total del recibo.
    private function updateReceiptTotal($receiptId)
    {
        // Obtener el recibo
        $receipt = Receipt::findOrFail($receiptId);

        // Obtener los detalles del recibo y calcular el total
        $total = $receipt->details->sum(function ($detail) {
            return $detail->amount; // Sumar los montos de los detalles
        });

        // Actualizar el total del recibo
        $receipt->update([
            'total' => $total,
        ]);
    }

}
