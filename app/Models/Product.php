<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'featured_image',
        'short_description',
        'description',
        'regular_price',
        'sale_price',
        'quantity',
        'featured',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $appends = ['price', 'image_url'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function hotDeal()
    {
        return $this->hasOne(HotDeal::class)
            ->where('status', true)
            ->where('end_time', '>', now());
    }

    public function getCurrentPriceAttribute()
    {
        $hotDeal = $this->relationLoaded('hotDeal')
            ? $this->getRelation('hotDeal')
            : $this->hotDeal;

        if ($hotDeal) {
            return (float) ($hotDeal->deal_price ?? ($this->regular_price - (($this->regular_price * $hotDeal->discount_percentage) / 100)));
        }

        return (float) ($this->sale_price ?? $this->regular_price);
    }

    public function getPriceAttribute()
    {
        return $this->current_price;
    }

    public function getImageUrlAttribute()
    {
        if ($this->featured_image) {
            return asset('storage/' . $this->featured_image);
        }
        // Fallback to first gallery image if no featured image
        if ($this->images && $this->images->isNotEmpty()) {
            return asset('storage/' . $this->images->first()->image);
        }
        return null;
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
