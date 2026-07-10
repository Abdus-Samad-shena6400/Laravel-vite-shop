<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get authenticated user's cart
     */
    public function index(Request $request)
    {
        $cart = CartItem::with('product.images')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        // Add hot deal information to each cart item
        $cart->transform(function ($cartItem) {
            $product = $cartItem->product;
            
            // Check for active hot deal
            $hotDeal = \App\Models\HotDeal::where('product_id', $product->id)
                ->visibleForStorefront()
                ->first();
            
            if ($hotDeal) {
                $product->hot_deal = $hotDeal;
                $product->deal_price = $hotDeal->deal_price;
                $product->discount_percentage = $hotDeal->discount_percentage;
            }
            
            // Set display price using the configured current price
            $product->price = $product->current_price;
            $product->stock = $product->quantity;
            
            return $cartItem;
        });

        return response()->json($cart);
    }

    /**
     * Add product to cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1']
        ]);

        $product = Product::findOrFail($request->product_id);

        // Prevent adding more than available stock
        if ($request->quantity > $product->quantity) {
            return response()->json([
                'message' => 'Requested quantity exceeds available stock.'
            ], 422);
        }

        $cartItem = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {

            $newQuantity = $cartItem->quantity + $request->quantity;

            if ($newQuantity > $product->quantity) {
                return response()->json([
                    'message' => 'Requested quantity exceeds available stock.'
                ], 422);
            }

            $cartItem->update([
                'quantity' => $newQuantity
            ]);

        } else {

            $cartItem = CartItem::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);

        }

        return response()->json([
            'message' => 'Product added to cart successfully.',
            'cart_item' => $cartItem->load('product.images')
        ]);
    }

    /**
     * Update quantity
     */
    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], 403);
        }

        $request->validate([
            'quantity' => ['required', 'integer', 'min:1']
        ]);

        if ($request->quantity > $cartItem->product->quantity) {
            return response()->json([
                'message' => 'Requested quantity exceeds available stock.'
            ], 422);
        }

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'message' => 'Cart updated successfully.'
        ]);
    }

    /**
     * Remove item
     */
    public function destroy(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], 403);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Item removed from cart.'
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'message' => 'Cart cleared successfully.'
        ]);
    }
}
