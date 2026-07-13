<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;


class WishlistController extends Controller
{
  


    public function index()
    {
        return Wishlist::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        Wishlist::firstOrCreate([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id
        ]);

        return response()->json([
            'message' => 'Added to wishlist.'
        ]);
    }   

    public function destroy($id)
    {
        Wishlist::where('user_id', auth()->id())
            ->where('id', $id)
            ->delete();

        return response()->json([
            'message' => 'Removed from wishlist.'
        ]);
    }
}
