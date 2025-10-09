<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Detail extends Model
{
    use HasFactory;

    // Si tu tabla NO tiene created_at / updated_at, descomenta:
    // public $timestamps = false;

    protected $fillable = [
        'id_product',
        'id_receipt',
        'quantity',
        'unit_price',
        'amount',
    ];

    protected $casts = [
        'quantity'   => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount'     => 'decimal:2',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'id_receipt');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
}
