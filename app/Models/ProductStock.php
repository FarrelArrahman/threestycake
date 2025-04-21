<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'product_id',
        'expiry_date',
        'stock_in_date',
        'stock_out_date',
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getAvailableStockAttribute()
    {
        return $this->whereNull('stock_out_date');
    }
}
