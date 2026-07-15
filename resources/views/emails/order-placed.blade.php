<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1f2937; margin: 0; padding: 30px; background: linear-gradient(to bottom right, #9333ea, #7c3aed);">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        
        <!-- Header -->
        <div style="background: linear-gradient(to right, #10b981, #059669); padding: 40px; text-align: center; color: white;">
            <div style="font-size: 60px; margin-bottom: 10px;">🎉</div>
            <h1 style="margin: 0; font-size: 32px; font-weight: bold;">Order Confirmed!</h1>
            <p style="margin-top: 10px; font-size: 18px; opacity: 0.9;">Thank you for your purchase</p>
        </div>

        <div style="padding: 30px;">
            <p style="font-size: 16px; color: #4b5563;">Hello {{ $order->user?->name ?? 'Customer' }},</p>
            <p style="font-size: 16px; color: #4b5563;">Your order has been successfully placed and is being processed.</p>
            
            <div style="background: linear-gradient(to bottom right, #f3f4f6, #e5e7eb); padding: 20px; border-radius: 15px; margin: 24px 0; border-left: 5px solid #10b981;">
                <h2 style="margin: 0 0 16px 0; color: #1f2937; font-size: 20px;">📋 Order Details</h2>
                <p style="margin: 8px 0;"><strong>Order Number:</strong> <span style="color: #059669; font-weight: bold;">{{ $order->order_number }}</span></p>
                <p style="margin: 8px 0;"><strong>Order Status:</strong> {{ ucfirst($order->order_status) }}</p>
                <p style="margin: 8px 0;"><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
                <p style="margin: 8px 0;"><strong>Payment Method:</strong> {{ ucwords(str_replace('_',' ',$order->payment_method)) }}</p>
                <p style="margin: 8px 0; font-size: 18px;"><strong>Total Amount:</strong> <span style="color: #059669; font-weight: bold; font-size: 22px;">${{ number_format($order->grand_total, 2) }}</span></p>
            </div>

            <h3 style="color: #1f2937; margin: 24px 0;">🛒 Order Items</h3>
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0; border-radius: 10px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
                <thead>
                    <tr style="background: linear-gradient(to right, #9333ea, #7c3aed); color: white;">
                        <th style="padding: 16px; text-align: left; font-weight: 600;">Product</th>
                        <th style="padding: 16px; text-align: right; font-weight: 600;">Price</th>
                        <th style="padding: 16px; text-align: center; font-weight: 600;">Qty</th>
                        <th style="padding: 16px; text-align: right; font-weight: 600;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr style="border-bottom: 1px solid #e5e7eb; background: {{ $loop->even ? '#f9fafb' : 'white' }};">
                        <td style="padding: 16px;">{{ $item->product->name }}</td>
                        <td style="padding: 16px; text-align: right;">${{ number_format($item->price, 2) }}</td>
                        <td style="padding: 16px; text-align: center;">{{ $item->quantity }}</td>
                        <td style="padding: 16px; text-align: right; font-weight: bold;">${{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($order->detail)
            <div style="background: linear-gradient(to bottom right, #ffedd5, #fed7aa); padding: 20px; border-radius: 15px; margin: 24px 0;">
                <h3 style="margin: 0 0 16px 0; color: #1f2937; font-size: 18px;">📍 Shipping Address</h3>
                <p style="margin: 4px 0; color: #374151;">
                    <strong>{{ $order->detail->first_name }} {{ $order->detail->last_name }}</strong><br>
                    {{ $order->detail->address1 }}<br>
                    @if($order->detail->address2){{ $order->detail->address2 }}<br>@endif
                    {{ $order->detail->city }}, {{ $order->detail->state }} {{ $order->detail->zip_code }}
                </p>
            </div>
            @endif

            <div style="text-align: center; margin: 32px 0;">
                <a href="http://localhost:5173/my-orders/{{ $order->id }}" 
                   style="display: inline-block; background: linear-gradient(to right, #10b981, #059669); color: white; text-decoration: none; padding: 15px 40px; border-radius: 30px; font-weight: bold; font-size: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
                    Track Your Order
                </a>
            </div>

            <p style="text-align: center; color: #9ca3af; font-size: 14px; margin-top: 32px;">
                📧 You will receive email notifications when your order status changes.
            </p>
        </div>

        <!-- Footer -->
        <div style="background: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0; color: #9ca3af; font-size: 14px;">Thank you for shopping with us! ❤️</p>
            <p style="margin-top: 4px; color: #d1d5db; font-size: 12px;">© {{ date('Y') }} Your Store. All rights reserved.</p>
        </div>
    </div>
</body>
</html>