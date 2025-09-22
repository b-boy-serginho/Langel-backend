<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory;

    // Indicar qué campos se pueden llenar
    protected $fillable = ['id_client', 'nro', 'total', 'date',  'hour', 'day', 'description'];

    protected $casts = ['total'=>'decimal:2'];

    // Relación con el modelo Product (Un detalle pertenece a un producto)
    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    // En el modelo Receipt
    public function details()
    {
        return $this->hasMany(Detail::class, 'id_receipt');
    }

}
