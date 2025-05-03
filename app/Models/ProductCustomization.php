<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCustomization extends Model
{
    protected $fillable = [
        'customization_type',
        'customization_value',
        'description',
        'price',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItemCustomizations()
    {
        return $this->hasMany(OrderItemCustomization::class);
    }
}
