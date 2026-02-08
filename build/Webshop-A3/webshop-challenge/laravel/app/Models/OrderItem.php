<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_item';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'quantity',
        'price_per_unit',
        'size',
    ];

    protected $casts = [
        'price_per_unit' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
