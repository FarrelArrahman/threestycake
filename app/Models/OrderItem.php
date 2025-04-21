<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public function customizations()
    {
        return $this->hasMany(OrderItemCustomization::class);
    }
}
