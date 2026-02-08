<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $primaryKey = 'order_id';

    public function getRouteKeyName()
    {
        return 'order_id';
    }

    protected $fillable = [
        'user_id',
        'order_date',
        'total_amount',
        'status',
        'shipping_address',
        'billing_address',
        'tracking_number',
        'discount_code',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function getOrderNumberAttribute()
    {
        return 'F13-' . $this->order_date->format('Y') . '-' . str_pad($this->order_id, 4, '0', STR_PAD_LEFT);
    }

    public function getItemCountAttribute()
    {
        return $this->items->sum('quantity');
    }
}
