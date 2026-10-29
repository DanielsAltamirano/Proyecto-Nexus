<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormTemplate extends Model
{
    protected $fillable = ['title', 'schema', 'is_active'];

    protected $casts = [
        'schema' => 'array',
        'is_active' => 'boolean',
    ];
}
