<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function customizations()
    {
        return $this->hasMany(ProductCustomization::class);
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function getAvailableStockAttribute()
    {
        return $this->stocks()->whereNull('stock_out_date');
    }

    public function getProductCountAttribute()
    {
        return $this->stocks()->count();
    }

    public function getAvailableStockCountAttribute()
    {
        return $this->stocks()->whereNull('stock_out_date')->count();
    }

}
