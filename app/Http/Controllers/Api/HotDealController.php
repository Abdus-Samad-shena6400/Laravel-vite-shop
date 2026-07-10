<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HotDeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;


class HotDealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = HotDeal::with('product');

        if ($request->has('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $hotDeals = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($hotDeals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|string|max:255',
            'status' => 'required|boolean',

            // Backward compatibility
            'deal_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);
        $originalPrice = (float) $product->price;
        $computedDealPrice = $originalPrice - ($originalPrice * ((float) $request->discount_percentage / 100));

        $data = [
            'product_id' => $request->product_id,
            'discount_percentage' => $request->discount_percentage,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
        ];

        if (Schema::hasColumn('hot_deals', 'deal_price')) {
            $data['deal_price'] = $request->filled('deal_price') ? $request->deal_price : $computedDealPrice;
        }

        if (Schema::hasColumn('hot_deals', 'description')) {
            $data['description'] = $request->description;
        }

        $hotDeal = HotDeal::create($data);



        return response()->json($hotDeal->load('product'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hotDeal = HotDeal::with('product')->findOrFail($id);
        return response()->json($hotDeal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|string|max:255',
            'status' => 'required|boolean',

            // Backward compatibility
            'deal_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);


        $hotDeal = HotDeal::findOrFail($id);

        $product = \App\Models\Product::findOrFail($request->product_id);
        $originalPrice = $product->price;
        $computedDealPrice = $originalPrice - ($originalPrice * $request->discount_percentage / 100);

        $data = [
            'product_id' => $request->product_id,
            'discount_percentage' => $request->discount_percentage,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
        ];

        if (Schema::hasColumn('hot_deals', 'deal_price')) {
            $data['deal_price'] = $request->filled('deal_price') ? $request->deal_price : $computedDealPrice;
        }

        if (Schema::hasColumn('hot_deals', 'description')) {
            $data['description'] = $request->description;
        }

        $hotDeal->update($data);


        return response()->json($hotDeal->load('product'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $hotDeal = HotDeal::findOrFail($id);
        $hotDeal->delete();

        return response()->json(['message' => 'Hot deal deleted successfully']);
    }
}
