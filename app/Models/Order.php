<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
        protected $fillable = [
            'user_id',
            'order_number',
            'subtotal',
            'shipping',
            'tax',
            'discount',
            'grand_total',
            'payment_method',
            'payment_status',
            'order_status',
            'notes',
            'coupon_id',
            'coupon_code',
            'transaction_id',
            'payment_proof',
            
        ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function detail()
    {
        return $this->hasOne(OrderDetail::class);
    }
}
