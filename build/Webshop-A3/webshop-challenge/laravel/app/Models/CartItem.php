<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;
    protected $table = 'cart_item';
    protected $primaryKey = 'cart_item_id';

    public function getRouteKeyName()
    {
        return 'cart_item_id';
    }

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'size',
        'added_date',
    ];

    protected $casts = [
        'added_date' => 'date',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
