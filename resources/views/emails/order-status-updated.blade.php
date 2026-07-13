<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Status Updated</title>
</head>

<body style="
    margin:0;
    padding:30px;
    background:#f4f6f9;
    font-family:Arial,sans-serif;
">

<div style="
    max-width:700px;
    margin:auto;
    background:#ffffff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 8px 30px rgba(0,0,0,.12);
">

    <!-- Header -->
    <div style="
        background:linear-gradient(135deg,#10b981,#059669);
        padding:40px;
        text-align:center;
        color:white;
    ">

       

        <h1 style="margin:0;font-size:34px;">
            📦 Order Status Updated
        </h1>

        <p style="font-size:18px;margin-top:10px;">
            BLS Team has updated your order status
        </p>

    </div>

    <!-- Content -->
    <div style="padding:40px;">

        <h2 style="color:#111827;">
            Hello {{ $order->user?->name ?? 'Customer' }},
        </h2>

        <p style="color:#6b7280;">
            Your order status has been updated successfully.
        </p>

        <div style="
            background:#f9fafb;
            border-radius:12px;
            padding:20px;
            margin:25px 0;
        ">

            <p>
                <strong>Order Number:</strong>
                {{ $order->order_number }}
            </p>

            <p>
                <strong>Payment Method:</strong>
                {{ ucwords(str_replace('_',' ',$order->payment_method)) }}
            </p>

            <p>
                <strong>Payment Status:</strong>
                {{ ucfirst($order->payment_status) }}
            </p>

            <p>
                <strong>Current Order Status:</strong>
                <span style="
                    color:#10b981;
                    font-weight:bold;
                    font-size:18px;
                ">
                    {{ ucfirst($order->order_status) }}
                </span>
            </p>

            <p>
                <strong>Updated At:</strong>
                {{ now()->format('F d, Y h:i A') }}
            </p>

        </div>

        @if($order->order_status == 'confirmed')
            <div style="
                background:#ecfdf5;
                border-left:5px solid #10b981;
                padding:15px;
                border-radius:10px;
                margin-top:20px;
            ">
                ✅ Your order has been confirmed and will be processed soon.
            </div>
        @elseif($order->order_status == 'processing')
            <div style="
                background:#eff6ff;
                border-left:5px solid #2563eb;
                padding:15px;
                border-radius:10px;
                margin-top:20px;
            ">
                ⚙️ Your order is currently being prepared.
            </div>
        @elseif($order->order_status == 'shipped')
            <div style="
                background:#fef3c7;
                border-left:5px solid #f59e0b;
                padding:15px;
                border-radius:10px;
                margin-top:20px;
            ">
                🚚 Great news! Your order has been shipped and is on the way.
            </div>
        @elseif($order->order_status == 'delivered')
            <div style="
                background:#ecfdf5;
                border-left:5px solid #10b981;
                padding:15px;
                border-radius:10px;
                margin-top:20px;
            ">
                🎉 Your order has been delivered successfully.
            </div>
        @elseif($order->order_status == 'cancelled')
            <div style="
                background:#fef2f2;
                border-left:5px solid #ef4444;
                padding:15px;
                border-radius:10px;
                margin-top:20px;
            ">
                ❌ Your order has been cancelled.
            </div>
        @endif

        <div style="text-align:center;margin-top:40px;">

            <a href="http://localhost:5173/my-orders/{{ $order->id }}"
               style="
                    background:#10b981;
                    color:white;
                    text-decoration:none;
                    padding:14px 35px;
                    border-radius:10px;
                    display:inline-block;
                    font-weight:bold;
               ">
                Track Order
            </a>

        </div>

        <p style="
            text-align:center;
            margin-top:40px;
            color:#6b7280;
        ">
            Thank you for choosing BLS Team ❤️
        </p>

    </div>

    <!-- Footer -->
    <div style="
        background:#f9fafb;
        text-align:center;
        padding:25px;
        color:#9ca3af;
        font-size:14px;
    ">
        © {{ date('Y') }} BLS Team. All rights reserved.
    </div>

</div>

</body>
</html>