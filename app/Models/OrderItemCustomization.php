<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemCustomization extends Model
{
    protected $fillable = [
        'order_item_id',
        'product_customization_id',
        'value',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function productCustomization()
    {
        return $this->belongsTo(ProductCustomization::class);
    }
}
