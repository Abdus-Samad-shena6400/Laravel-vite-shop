<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Order Confirmation</title>
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
        background:linear-gradient(135deg,#4f46e5,#7c3aed);
        padding:40px;
        text-align:center;
        color:white;
    ">

       
        <h1 style="margin:0;font-size:34px;">
            🎉 Order Confirmed
        </h1>

        <p style="font-size:18px;margin-top:10px;">
            Thank you for shopping with BLS Team
        </p>

    </div>

    <!-- Content -->
    <div style="padding:40px;">

        <h2 style="color:#111827;">
            Hello {{ $order->user->name }},
        </h2>

        <p style="color:#6b7280;">
            Your order has been placed successfully.
        </p>

        <div style="
            background:#f9fafb;
            border-radius:12px;
            padding:20px;
            margin:25px 0;
        ">

            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
            
           

            <p><strong>Payment Method:</strong> {{ ucwords(str_replace('_',' ',$order->payment_method)) }}</p>
            <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
            <p><strong>Order Status:</strong> {{ ucfirst($order->order_status) }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y h:i A') }}</p>
             <p><strong>Subtotal:</strong>
    ${{ number_format($order->subtotal,2) }}
</p>

<p><strong>Shipping:</strong>
    ${{ number_format($order->shipping,2) }}
</p>

<p><strong>Tax:</strong>
    ${{ number_format($order->tax,2) }}
</p>

@if($order->discount > 0)
<p><strong>Discount:</strong>
    -${{ number_format($order->discount,2) }}
</p>
@endif

<hr>

<p style="font-size:20px;font-weight:bold;color:#4f46e5;">
    Grand Total:
    ${{ number_format($order->grand_total,2) }}
</p>

        </div>

        <h3 style="color:#111827;">
            Shipping Address
        </h3>

        <p style="line-height:1.8;color:#4b5563;">
            {{ $order->detail->first_name }}
            {{ $order->detail->last_name }}<br>

            {{ $order->detail->address1 }}<br>

            @if($order->detail->address2)
                {{ $order->detail->address2 }}<br>
            @endif

            {{ $order->detail->city }},
            {{ $order->detail->state }},
            {{ $order->detail->zip_code }}<br>

            {{ $order->detail->country_code }}<br>

            Phone: {{ $order->detail->phone }}
        </p>

        

        <div style="text-align:center;margin-top:40px;">

            <a href="http://localhost:5173/my-orders/{{ $order->id }}"
               style="
                    background:#4f46e5;
                    color:white;
                    text-decoration:none;
                    padding:14px 35px;
                    border-radius:10px;
                    display:inline-block;
                    font-weight:bold;
               ">
                View Order
            </a>

        </div>

        <p style="
            text-align:center;
            margin-top:40px;
            color:#6b7280;
        ">
            We will notify you whenever your order status changes.
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