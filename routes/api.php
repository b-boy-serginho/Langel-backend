<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReceiptController;
use App\Http\Controllers\Api\DetailController;


Route::get('/ping', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API funcionando correctamente 🚀'
    ]);
});

// routes/api.php
Route::middleware('api')->group(function () {
    Route::resource('clients', ClientController::class);
    Route::resource('products', ProductController::class);
    Route::resource('receipts', ReceiptController::class);
    Route::resource('details', DetailController::class);
    Route::get('receipts/client/{clientId}', [ReceiptController::class, 'indexByClient']);
});
