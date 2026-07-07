<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user:id,name', 'product:id,name']);

        // Filter by status if provided
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('status', true);
            } elseif ($request->status === 'pending') {
                $query->where('status', false);
            }
        }

        return $query->latest()->get();
    }

    public function productReviews(Product $product)
    {
        return Review::with('user:id,name')
            ->where('product_id', $product->id)
            ->where('status', true)
            ->latest()
            ->get();
    }

    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000'
        ]);

        // Check if user has purchased this product in a delivered order
        $hasPurchased = \App\Models\OrderItem::whereHas('order', function ($query) {
            $query->where('user_id', auth()->id())
                  ->where('order_status', 'delivered');
        })->where('product_id', $product->id)->exists();

        if (!$hasPurchased) {
            return response()->json([
                'message' => 'You can only review products you have purchased after the order has been delivered.'
            ], 422);
        }

        // One review per user per product
        $existing = Review::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You already reviewed this product.'
            ], 422);
        }

        $review = Review::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'status' => false
        ]);

        return response()->json([
            'message' => 'Review submitted successfully. Waiting for admin approval.',
            'review' => $review
        ]);
    }

    public function update(Request $request, Review $review)
    {
        $data = $request->validate([
            'status' => 'required|boolean'
        ]);

        $review->update($data);

        return response()->json([
            'message' => 'Review status updated successfully.',
            'review' => $review
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully.'
        ]);
    }
}