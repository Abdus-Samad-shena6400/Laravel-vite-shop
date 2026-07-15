<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
            'payment_method' => 'required|in:cash_on_delivery,bank_transfer,stripe',
            'transaction_id' => 'required_if:payment_method,bank_transfer|nullable|string|max:255',
            'payment_intent_id' => 'required_if:payment_method,stripe|nullable|string',
            'cart' => 'required|array|min:1',
            'coupon_id' => 'nullable|integer|exists:coupons,id',
            'coupon_code' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
        ]);

        // Verify Stripe payment if payment method is stripe
        if ($validated['payment_method'] === 'stripe') {
            if (empty($validated['payment_intent_id'])) {
                return response()->json(['message' => 'Payment intent ID is required for Stripe payments.'], 400);
            }

            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                $paymentIntent = \Stripe\PaymentIntent::retrieve($validated['payment_intent_id']);

                if ($paymentIntent->status !== 'succeeded') {
                    return response()->json(['message' => 'Payment has not been completed.'], 400);
                }

                // Verify amount matches (optional but recommended)
                // $expectedAmount = $this->calculateOrderTotal($validated['cart']);
                // if ($paymentIntent->amount / 100 !== $expectedAmount) {
                //     return response()->json(['message' => 'Payment amount does not match order total.'], 400);
                // }

                $validated['transaction_id'] = $validated['payment_intent_id'];
                $validated['payment_status'] = 'paid';
            } catch (\Exception $e) {
                Log::error('Stripe payment verification failed: ' . $e->getMessage());
                return response()->json(['message' => 'Payment verification failed.'], 400);
            }
        }
        DB::beginTransaction();

        try {
            $subtotal = 0;

            foreach ($validated['cart'] as $item) {
                Log::info('Cart item:', $item);

                if (!isset($item['product']) || !isset($item['product']['id'])) {
                    throw new \Exception('Invalid cart item structure.');
                }

                $product = Product::find($item['product']['id']);

                if (!$product) {
                    throw new \Exception('Product with ID ' . $item['product']['id'] . ' not found. Please remove it from your cart and try again.');
                }

                Log::info('Product found:', $product->toArray());

                $product->load('hotDeal');

                $price = $product->current_price;
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

            $discount = $validated['discount'] ?? 0;

            $grandTotal = $subtotal + $shipping + $tax - $discount;

            // Set payment status based on payment method
            if ($validated['payment_method'] === 'stripe') {
                $paymentStatus = $validated['payment_status'] ?? 'paid';
            } elseif ($validated['payment_method'] === 'bank_transfer') {
                $paymentStatus = 'pending';
            } else {
                $paymentStatus = 'pending';  // cash_on_delivery
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'shipping' => number_format($shipping, 2, '.', ''),
                'tax' => number_format($tax, 2, '.', ''),
                'discount' => number_format($discount, 2, '.', ''),
                'grand_total' => number_format($grandTotal, 2, '.', ''),
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'order_status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'coupon_id' => $validated['coupon_id'] ?? null,
                'coupon_code' => $validated['coupon_code'] ?? null,
                'transaction_id' => $validated['transaction_id'] ?? null,
                'order_email_sent' => 0,
                'status_email_sent' => 0,
            ]);

            // Increment coupon usage count if coupon was used
            if (!empty($validated['coupon_id'])) {
                $coupon = Coupon::find($validated['coupon_id']);
                if ($coupon) {
                    $coupon->increment('used_count');
                }
            }

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
                $product->load('hotDeal');

                $price = $product->current_price;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']['id'],
                    'price' => number_format($price, 2, '.', ''),
                    'quantity' => $item['quantity'],
                    'total' => number_format($price * $item['quantity'], 2, '.', ''),
                    'original_price' => number_format($product->regular_price, 2, '.', ''),
                    'discount_percentage' => $product->hotDeal?->discount_percentage ?? 0,
                    'is_hot_deal' => $product->hotDeal ? true : false,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully.',
                'order_id' => $order->id,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function createPaymentIntent(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string',
        ]);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $validated['amount'] * 100,  // Convert to cents
            'currency' => $validated['currency'],
        ]);

        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
        ]);
    }
}
