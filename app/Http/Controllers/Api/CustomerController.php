<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $customers = $query
            ->withCount('orders')
            ->latest()
            ->paginate(10);

        return response()->json($customers);
    }

    public function show(User $user)
    {
    $user->load(['orders' => function ($query) {
        $query->latest();
    }]);

    $totalSpent = $user->orders()->sum('grand_total');

    return response()->json([
        'customer' => $user,
        'orders' => $user->orders,
        'statistics' => [
            'total_orders' => $user->orders()->count(),
            'total_spent' => $totalSpent,
        ]
    ]);
    }
}

