<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Detail extends Model
{
    use HasFactory;

    // Especificamos los campos que pueden ser asignados masivamente (mass assignable)
    protected $fillable = [
        'id_product',
        'id_receipt',
        'quantity',
        'amount',
        'unit_price',
    ];

    // Relación con el modelo Product (Un detalle pertenece a un producto)
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    // Relación con el modelo Receipt (Un detalle pertenece a un recibo)
    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'id_receipt');
    }
}
