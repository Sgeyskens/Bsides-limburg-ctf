<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'product_type',
        'description',
        'name',
        'price',
        'image_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id', 'product_id');
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'product_property', 'product_id', 'property_id')
            ->withPivot('property_value')
            ->withTimestamps();
    }

    public function ratings()
    {
        return $this->hasMany(ProductRating::class, 'product_id', 'product_id');
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    public function getRatingsCountAttribute()
    {
        return $this->ratings()->count();
    }

    public function getPropertyValue(string $propertyName): ?string
    {
        $property = $this->properties->firstWhere('property_name', $propertyName);
        return $property ? $property->pivot->property_value : null;
    }
}
