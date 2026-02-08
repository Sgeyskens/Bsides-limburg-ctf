<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $table = 'property';
    protected $primaryKey = 'property_id';

    protected $fillable = [
        'property_name',
        'property_type',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_property', 'property_id', 'product_id')
            ->withPivot('property_value')
            ->withTimestamps();
    }
}
