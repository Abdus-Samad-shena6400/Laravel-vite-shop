<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentSucceeded($paymentIntent);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentFailed($paymentIntent);
                break;

            default:
                Log::info('Unhandled event type: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    protected function handlePaymentSucceeded($paymentIntent)
    {
        // Find order by transaction_id (payment_intent_id)
        $order = Order::where('transaction_id', $paymentIntent->id)->first();

        if ($order) {
            // Only update if not already paid to prevent duplicate processing
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'order_status' => 'processing',
                ]);

                Log::info('Payment succeeded for order: ' . $order->order_number);
            } else {
                Log::info('Order already paid: ' . $order->order_number);
            }
        } else {
            Log::warning('Order not found for payment intent: ' . $paymentIntent->id);
        }
    }

    protected function handlePaymentFailed($paymentIntent)
    {
        // Find order by transaction_id (payment_intent_id)
        $order = Order::where('transaction_id', $paymentIntent->id)->first();

        if ($order) {
            // Only update if not already failed
            if ($order->payment_status !== 'failed') {
                $order->update([
                    'payment_status' => 'failed',
                    'order_status' => 'cancelled',
                ]);

                Log::info('Payment failed for order: ' . $order->order_number);
            } else {
                Log::info('Order already marked as failed: ' . $order->order_number);
            }
        } else {
            Log::warning('Order not found for payment intent: ' . $paymentIntent->id);
        }
    }
}
