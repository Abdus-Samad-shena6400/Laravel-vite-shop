<!DOCTYPE html>
<html>

<head>

    <title>Sales Report</title>

    <style>

        body{
            font-family: DejaVu Sans,sans-serif;
            font-size:12px;
        }

        h2{
            text-align:center;
            margin-bottom:20px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        table th,
        table td{
            border:1px solid #ccc;
            padding:8px;
            text-align:left;
        }

        table th{
            background:#f3f4f6;
        }

        .summary{
            margin-bottom:20px;
        }

    </style>

</head>

<body>

<h2>Sales Report</h2>

<div class="summary">

    <p><strong>Total Orders:</strong> {{ $summary['total_orders'] }}</p>

    <p><strong>Total Revenue:</strong> ${{ number_format($summary['total_revenue'],2) }}</p>

    <p><strong>Average Order:</strong> ${{ number_format($summary['average_order'],2) }}</p>

</div>

<table>

<thead>

<tr>

    <th>Order #</th>

    <th>Customer</th>

    <th>Total</th>

    <th>Status</th>

    <th>Payment</th>

    <th>Date</th>

</tr>

</thead>

<tbody>

@foreach($orders as $order)

<tr>

    <td>{{ $order->order_number }}</td>

    <td>{{ $order->user->name }}</td>

    <td>${{ number_format($order->grand_total,2) }}</td>

    <td>{{ ucfirst($order->order_status) }}</td>

    <td>{{ ucfirst($order->payment_status) }}</td>

    <td>{{ $order->created_at->format('d-m-Y') }}</td>

</tr>

@endforeach

</tbody>

</table>

</body>

</html>