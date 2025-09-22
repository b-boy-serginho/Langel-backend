<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    // Indicar qué campos se pueden llenar
    protected $fillable = ['name', 'email', 'phone'];

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'id_client');
    }

}
