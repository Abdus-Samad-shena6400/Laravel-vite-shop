<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->order->load([
            'user',
            'detail',
            'items.product'
        ]);
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