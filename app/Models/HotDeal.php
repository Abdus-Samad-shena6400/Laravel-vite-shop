<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotDeal extends Model
{
    protected $fillable = [
        'product_id',
        'deal_price',
        'discount_percentage',
        'title',
        'subtitle',
        'button_text',
        'button_url',
        'start_time',
        'end_time',
        'description',
        'status'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>', now());
    }

    public function scopeVisibleForStorefront($query)
    {
        return $query->where('status', true)
            ->where('end_time', '>', now());
    }
}
