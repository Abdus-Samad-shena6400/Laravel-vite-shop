<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function products(Request $request)
{
    $query = Product::with(['category', 'brand', 'images'])
        ->where('status', 1);

    // Search
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Category
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Brand
    if ($request->filled('brand_id')) {
        $query->where('brand_id', $request->brand_id);
    }

    // Sorting
    switch ($request->sort) {

        case 'price_low':
            $query->orderBy('price');
            break;

        case 'price_high':
            $query->orderByDesc('price');
            break;

        case 'oldest':
            $query->oldest();
            break;

        default:
            $query->latest();
            break;
    }

    $products = $query->paginate(12);
    $products->getCollection()->transform(function ($product) {
        $product->price = $product->sale_price ?? $product->regular_price;
        
        // Add average rating and review count
        $reviews = $product->reviews()->where('status', true)->get();
        $product->average_rating = $reviews->isEmpty() ? 0 : $reviews->avg('rating');
        $product->review_count = $reviews->count();
        
        return $product;
    });

    return response()->json($products);
}

public function product($id)
{
    $product = Product::with([
        'category',
        'brand',
        'images',
        'reviews' => function ($q) {
            $q->where('status', true)
              ->with('user')
              ->latest();
        }
    ])
        ->where('status', 1)
        ->findOrFail($id);

    $product->price = $product->sale_price ?? $product->regular_price;

    $relatedProducts = Product::with(['category', 'brand'])
        ->where('status', 1)
        ->where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->latest()
        ->take(4)
        ->get();

    $relatedProducts->transform(function ($p) {
        $p->price = $p->sale_price ?? $p->regular_price;
        return $p;
    });

    return response()->json([
        'product' => $product,
        'related_products' => $relatedProducts,
    ]);
}

    public function categories()
    {
        return response()->json(
            Category::where('status', 1)
                ->latest()
                ->get()
        );
    }

    public function brands()
    {
        return response()->json(
            Brand::where('status', 1)
                ->latest()
                ->get()
        );
    }
}