<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        // Search by order number or customer name
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q
                    ->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by order status
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        $orders = $query
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
            'items' => 'required|array|min:1'
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;

            foreach ($request->items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $shipping = 0;
            $tax = 0;
            $discount = 0;

            $grandTotal = $subtotal + $shipping + $tax - $discount;

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . now()->format('YmdHis'),
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'tax' => $tax,
                'discount' => $discount,
                'grand_total' => $grandTotal,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'notes' => $request->notes
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['price'] * $item['quantity']
                ]);

                Product::where('id', $item['product_id'])
                    ->decrement('quantity', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully.',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'items.product'
        ]);

        return response()->json($order);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled'
        ]);

        $order->update([
            'order_status' => $validated['order_status']
        ]);

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order' => $order
        ]);
    }

    public function updatePaymentStatus(Request $request, Order $order)
{
    $validated = $request->validate([
        'payment_status' => 'required|in:pending,paid,failed',
    ]);

    $order->payment_status = $validated['payment_status'];

    if ($validated['payment_status'] === 'paid') {
        $order->order_status = 'confirmed';
    }

    $order->save();

    return response()->json([
        'message' => 'Payment status updated successfully.',
        'order' => $order,
    ]);
}
    public function myOrders(Request $request)
    {
        return Order::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);
    }

    public function myOrderShow(Request $request, Order $order)
{
    if ($order->user_id != $request->user()->id) {
        abort(403);
    }

    $order->load([
        'user',
        'detail',
        'items.product'
    ]);

    // Add reviewed status to each item
    $order->items->each(function ($item) use ($request) {
        $item->reviewed = \App\Models\Review::where('user_id', $request->user()->id)
            ->where('product_id', $item->product_id)
            ->exists();
    });

    return response()->json($order);
}

public function cancel(Order $order)
{
    if ($order->user_id !== auth()->id()) {
        return response()->json([
            'message' => 'Unauthorized.'
        ], 403);
    }

    if (! in_array($order->order_status, ['pending', 'processing'])) {
        return response()->json([
            'message' => 'This order can no longer be cancelled.'
        ], 422);
    }

    $order->update([
        'order_status' => 'cancelled'
    ]);

    return response()->json([
        'message' => 'Order cancelled successfully.'
    ]);
}

    public function destroyMyOrder(Request $request, Order $order)
    {
        if ($order->user_id != $request->user()->id) {
            abort(403);
        }

        // Only allow deleting pending or cancelled orders
        if (!in_array($order->order_status, ['pending', 'cancelled'])) {
            return response()->json([
                'message' => 'This order cannot be deleted.'
            ], 422);
        }

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully.'
        ]);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully.'
        ]);
    }
}
