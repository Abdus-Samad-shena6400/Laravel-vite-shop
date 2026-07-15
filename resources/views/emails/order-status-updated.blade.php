<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Updated</title>
</head>

<body style="margin: 0; padding: 30px; background: linear-gradient(to bottom right, #9333ea, #7c3aed); font-family: Arial, sans-serif;">

<div style="max-width: 700px; margin: 0 auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">

    <!-- Header -->
    <div style="background: linear-gradient(to right, #10b981, #059669); padding: 40px; text-align: center; color: white;">
        <div style="font-size: 60px; margin-bottom: 10px;">📦</div>
        <h1 style="margin: 0; font-size: 34px; font-weight: bold;">
            Order Status Updated
        </h1>
        <p style="font-size: 18px; margin-top: 10px; opacity: 0.9;">
            BLS Team has updated your order status
        </p>
    </div>

    <!-- Content -->
    <div style="padding: 40px;">
        <h2 style="color: #111827; margin: 0 0 8px 0;">
            Hello {{ $order->user?->name ?? 'Customer' }},
        </h2>
        <p style="color: #4b5563; margin: 0 0 24px 0;">
            Your order status has been updated successfully.
        </p>

        <div style="background: linear-gradient(to bottom right, #f3f4f6, #e5e7eb); border-radius: 12px; padding: 20px; margin: 24px 0; border-left: 5px solid #10b981;">
            <p style="margin: 8px 0;">
                <strong>Order Number:</strong>
                <span style="color: #059669; font-weight: bold;">{{ $order->order_number }}</span>
            </p>
            <p style="margin: 8px 0;">
                <strong>Payment Method:</strong>
                {{ ucwords(str_replace('_',' ',$order->payment_method)) }}
            </p>
            <p style="margin: 8px 0;">
                <strong>Payment Status:</strong>
                {{ ucfirst($order->payment_status) }}
            </p>
            <p style="margin: 8px 0;">
                <strong>Current Order Status:</strong>
                <span style="color: #059669; font-weight: bold; font-size: 18px;">
                    {{ ucfirst($order->order_status) }}
                </span>
            </p>
            <p style="margin: 8px 0;">
                <strong>Updated At:</strong>
                {{ now()->format('F d, Y h:i A') }}
            </p>
        </div>

        @if($order->order_status == 'confirmed')
            <div style="background: linear-gradient(to bottom right, #f0fdf4, #dcfce7); border-left: 5px solid #10b981; padding: 20px; border-radius: 10px; margin-top: 20px;">
                <span style="font-size: 24px;">✅</span> Your order has been confirmed and will be processed soon.
            </div>
        @elseif($order->order_status == 'processing')
            <div style="background: linear-gradient(to bottom right, #eff6ff, #dbeafe); border-left: 5px solid #2563eb; padding: 20px; border-radius: 10px; margin-top: 20px;">
                <span style="font-size: 24px;">⚙️</span> Your order is currently being prepared.
            </div>
        @elseif($order->order_status == 'shipped')
            <div style="background: linear-gradient(to bottom right, #fefce8, #fef9c3); border-left: 5px solid #eab308; padding: 20px; border-radius: 10px; margin-top: 20px;">
                <span style="font-size: 24px;">🚚</span> Great news! Your order has been shipped and is on the way.
            </div>
        @elseif($order->order_status == 'delivered')
            <div style="background: linear-gradient(to bottom right, #f0fdf4, #dcfce7); border-left: 5px solid #10b981; padding: 20px; border-radius: 10px; margin-top: 20px;">
                <span style="font-size: 24px;">🎉</span> Your order has been delivered successfully.
            </div>
        @elseif($order->order_status == 'cancelled')
            <div style="background: linear-gradient(to bottom right, #fef2f2, #fee2e2); border-left: 5px solid #ef4444; padding: 20px; border-radius: 10px; margin-top: 20px;">
                <span style="font-size: 24px;">❌</span> Your order has been cancelled.
            </div>
        @elseif($order->order_status == 'pending')
            <div style="background: linear-gradient(to bottom right, #fefce8, #fef9c3); border-left: 5px solid #eab308; padding: 20px; border-radius: 10px; margin-top: 20px;">
                <span style="font-size: 24px;">⏳</span> Your order is pending confirmation.
            </div>
        @endif

        <div style="text-align: center; margin-top: 40px;">
            <a href="http://localhost:5173/my-orders/{{ $order->id }}"
               style="display: inline-block; background: linear-gradient(to right, #10b981, #059669); color: white; text-decoration: none; padding: 15px 40px; border-radius: 30px; font-weight: bold; font-size: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
                Track Order
            </a>
        </div>
        <p style="text-align: center; margin-top: 40px; color: #4b5563;">
            Thank you for choosing BLS Team ❤️
        </p>

    </div>

    <!-- Footer -->
    <div style="background: #f9fafb; text-align: center; padding: 24px; color: #9ca3af; font-size: 14px; border-top: 1px solid #e5e7eb;">
        © {{ date('Y') }} BLS Team. All rights reserved.
    </div>
</div>
</body>
</html>