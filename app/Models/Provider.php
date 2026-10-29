<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    // Permitimos asignación masiva
    protected $fillable = ['name', 'razon_social', 'rfc', 'phone', 'address'];
}