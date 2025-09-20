<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    // Indicar qué campos se pueden llenar
    protected $fillable = ['name', 'price', 'description'];
}
