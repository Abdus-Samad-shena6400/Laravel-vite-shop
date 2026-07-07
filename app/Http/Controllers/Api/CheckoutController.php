<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'country_code' => 'required|string|max:3',
            'notes' => 'nullable|string',
            'payment_method' => 'required|in:cash_on_delivery',
            'cart' => 'required|array|min:1',
        ]);
        DB::beginTransaction();

        try {
            $subtotal = 0;

            foreach ($validated['cart'] as $item) {
                \Log::info('Cart item:', $item);

                if (!isset($item['product']) || !isset($item['product']['id'])) {
                    throw new \Exception('Invalid cart item structure.');
                }

                $product = Product::find($item['product']['id']);

                if (!$product) {
                    throw new \Exception('Product with ID ' . $item['product']['id'] . ' not found. Please remove it from your cart and try again.');
                }

                \Log::info('Product found:', $product->toArray());

                $price = $product->sale_price ?? $product->regular_price;
                if ($price == 0) {
                    throw new \Exception('Product price not found for product ID: ' . $product->id);
                }

                $subtotal += $price * $item['quantity'];

                if ($product->quantity < $item['quantity']) {
                    throw new \Exception($product->name . ' does not have enough stock.');
                }

                $product->decrement('quantity', $item['quantity']);
            }

            $shipping = $subtotal > 150 ? 0 : 15;

            $tax = $subtotal * 0.1;

            $grandTotal = $subtotal + $shipping + $tax;

            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'tax' => $tax,
                'discount' => 0,
                'grand_total' => $grandTotal,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            OrderDetail::create([
                'order_id' => $order->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'],
                'address1' => $validated['address1'],
                'address2' => $validated['address2'] ?? null,
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip_code' => $validated['zip_code'],
                'country_code' => $validated['country_code'],
            ]);

            // Create order items
            foreach ($validated['cart'] as $item) {
                $product = Product::find($item['product']['id']);
                $price = $product->sale_price ?? $product->regular_price;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']['id'],
                    'price' => $price,
                    'quantity' => $item['quantity'],
                    'total' => $price * $item['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully.',
                'order_id' => $order->id,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
