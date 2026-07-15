<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;

class OrderStatusUpdatedMail extends Mailable
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this
            ->subject(
                'Order Status Updated - ' .
                $this->order->order_number
            )
            ->view('emails.order-status-updated');
    }
}