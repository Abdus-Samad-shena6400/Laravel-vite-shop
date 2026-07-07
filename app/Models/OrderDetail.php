<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'first_name',
        'last_name',
        'phone',
        'address1',
        'address2',
        'city',
        'state',
        'zip_code',
        'country_code'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
