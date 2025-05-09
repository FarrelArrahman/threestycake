<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'method',
        'proof_image',
        'status',
        'payment_date',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusAttribute()
    {
        return $this->attributes['status'] ?? 'not yet';
    }

    public function confirmed()
    {
        return $this->status === 'confirmed';
    }
}
