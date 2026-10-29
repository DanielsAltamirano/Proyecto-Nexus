<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Agrega esta línea para permitir la asignación del nombre y descripción
    protected $fillable = ['name', 'description'];
}