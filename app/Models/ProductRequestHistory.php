<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRequestHistory extends Model
{
    protected $fillable = [
        'product_request_id',
        'user_id',
        'action',
        'details',
    ];

    public function productRequest()
    {
        return $this->belongsTo(ProductRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
