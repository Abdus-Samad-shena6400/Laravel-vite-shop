<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();

        $totalCategories = Category::count();

        $totalBrands = Brand::count();

        $totalOrders = Order::count();

        $totalCustomers = User::count();

        $totalRevenue = Order::where('payment_status', 'paid')
            ->sum('grand_total');

        $latestOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        /* $lowStockProducts = Product::where('quantity', '<=', 5)
            ->orderBy('quantity')
            ->take(5)
            ->get(); */
        $revenueChart = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);

            $revenueChart[] = [
                'date' => $date->format('D'),
                'total' => Order::whereDate('created_at', $date)
                    ->where('payment_status', 'paid')
                    ->sum('grand_total')
            ];
        }
        $pendingOrders = Order::where('order_status', 'pending')->count();

        $processingOrders = Order::where('order_status', 'processing')->count();

        $deliveredOrders = Order::where('order_status', 'delivered')->count();

        $cancelledOrders = Order::where('order_status', 'cancelled')->count();

        return response()->json([
            'statistics' => [
                'products' => $totalProducts,
                'categories' => $totalCategories,
                'brands' => $totalBrands,
                'orders' => $totalOrders,
                'customers' => $totalCustomers,
                'revenue' => $totalRevenue,
                'pendingOrders' => $pendingOrders,
                'processingOrders' => $processingOrders,
                'deliveredOrders' => $deliveredOrders,
                'cancelledOrders' => $cancelledOrders,
            ],
            'latestOrders' => $latestOrders,
            'revenueChart' => $revenueChart,
            'orderStatus' => [
                'pending' => $pendingOrders,
                'processing' => $processingOrders,
                'delivered' => $deliveredOrders,
                'cancelled' => $cancelledOrders,
            ]
        ]);
    }
}
