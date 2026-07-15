<?php

namespace App\Http\Controllers;

use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class OrderNotificationController extends Controller
{
    public function sendNotifications()
    {
        $orders = Order::with([
            'user',
            'detail',
            'items.product'
        ])->whereIn(
            'notification_status',
            [1, 2, 3]
        )->get();

        foreach ($orders as $order) {
            if ($order->user && $order->user->email) {
                Mail::to($order->user->email)
                    ->send(new OrderStatusUpdatedMail($order));
                // Mark as notified
                $order->notification_status = 99;
                $order->save();
            }
        }
    }
}
