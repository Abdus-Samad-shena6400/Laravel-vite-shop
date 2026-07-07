<?php

namespace App\Http\Controllers\Api;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $query = Order::with('user')
        ->where('payment_status', 'paid');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->latest()->get();

        return response()->json([
            'orders' => $orders,
            'summary' => [
                'total_orders' => $orders->count(),
                'total_revenue' => $orders->sum('grand_total'),
                'average_order' => round($orders->avg('grand_total'), 2),
            ]
        ]);
    }

    public function exportPdf(Request $request)
{
    $query = Order::with('user');

    if ($request->filled('from')) {

        $query->whereDate('created_at','>=',$request->from);

    }

    if ($request->filled('to')) {

        $query->whereDate('created_at','<=',$request->to);

    }

    $orders = $query->latest()->get();

    $summary = [

        'total_orders' => $orders->count(),

        'total_revenue' => $orders->sum('grand_total'),

        'average_order' => round($orders->avg('grand_total'),2)

    ];

    $pdf = Pdf::loadView('report.sales-pdf', [

        'orders' => $orders,

        'summary' => $summary

    ]);

    return $pdf->download('sales-report.pdf');
}
}
