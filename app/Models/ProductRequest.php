<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRequest extends Model
{
    protected $fillable = [
        'product_id',
        'branch_id',
        'user_id',
        'quantity',
        'status',
        'notes',
        'transfer_id',
        'purchase_order_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function histories()
    {
        return $this->hasMany(ProductRequestHistory::class);
    }
}
