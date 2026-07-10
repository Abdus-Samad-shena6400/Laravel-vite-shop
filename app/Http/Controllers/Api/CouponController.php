<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
 use Carbon\Carbon;


class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        return response()->json(
            $query->latest()->paginate(10)
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:coupons,code',   
            'name' => 'required',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|boolean',
        ]);

        Coupon::create($data);

        return response()->json([
            'message' => 'Coupon created successfully.'
        ]);
    }

    public function show(Coupon $coupon)
    {
        return response()->json($coupon);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'code' => 'required|unique:coupons,code,' . $coupon->id,
            'name' => 'required',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|boolean',
        ]);

        $coupon->update($data);

        return response()->json([
            'message' => 'Coupon updated successfully.'
        ]);
    }

  

public function apply(Request $request)
{
    $request->validate([
        'code' => 'required|string',
        'subtotal' => 'required|numeric|min:0'
    ]);

    $coupon = Coupon::where('code', strtoupper($request->code))
        ->first();

    if (!$coupon) {
        return response()->json([
            'message' => 'Invalid coupon code.'
        ], 404);
    }

    if (!$coupon->status) {
        return response()->json([
            'message' => 'This coupon is inactive.'
        ], 422);
    }

    if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
        return response()->json([
            'message' => 'Coupon is not active yet.'
        ], 422);
    }

    if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
        return response()->json([
            'message' => 'Coupon has expired.'
        ], 422);
    }

    if ($coupon->usage_limit > 0 && $coupon->used_count >= $coupon->usage_limit) {
        return response()->json([
            'message' => 'Coupon usage limit exceeded.'
        ], 422);
    }

    if ($request->subtotal < $coupon->minimum_amount) {
        return response()->json([
            'message' => 'Minimum order amount is $'.$coupon->minimum_amount
        ], 422);
    }

    if ($coupon->type == 'percentage') {

        $discount = ($request->subtotal * $coupon->value) / 100;

        if ($coupon->maximum_discount &&
            $discount > $coupon->maximum_discount) {

            $discount = $coupon->maximum_discount;
        }

    } else {

        $discount = $coupon->value;

        if ($discount > $request->subtotal) {
            $discount = $request->subtotal;
        }
    }

    return response()->json([
        'success' => true,
        'coupon' => [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => round($discount,2)
        ]
    ]);
}

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return response()->json([
            'message' => 'Coupon deleted successfully.'
        ]);
    }
}