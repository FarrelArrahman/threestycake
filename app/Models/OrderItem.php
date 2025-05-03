<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    public function customizations()
    {
        return $this->hasMany(OrderItemCustomization::class);
    }

    // public function productStock()
    // {
    //     return $this->belongsTo(ProductStock::class);
    // }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->price * $this->quantity;
    }

    public function getTotalCustomizationPriceAttribute()
    {
        return $this->customizations->sum('price');
    }

    public function getCustomizationSentencesAttribute()
    {
        if ($this->customizations == []) {
            return '-';
        }

        $customizations = $this->customizations;
        $customizationSentences = [];
        foreach ($customizations as $customization) {
            $customizationSentences[] = $customization->productCustomization->customization_type;
        }
        return implode(', ', $customizationSentences);
    }

    public function getCustomizationTotalPriceAttribute()
    {
        return $this->customizations->sum('price') * $this->quantity;
    }

    public function getTotalPriceWithCustomizationAttribute()
    {
        return $this->total_price + $this->customization_total_price;
    }
}
