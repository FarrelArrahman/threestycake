<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function getTotalPriceAttribute()
    {
        $orderTotal = $this->orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $customizationTotal = $this->orderItems->sum(function ($item) {
            return $item->customizations->sum(function ($customization) {
                return $customization->productCustomization->price;
            });
        });

        return $orderTotal + $customizationTotal;
    }
    
    public function getTotalQuantityAttribute()
    {
        return $this->orderItems->sum('quantity');
    }
}
