<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RazorpayGateway
{
    public function createOrder(Order $order): string
    {
        $keyId = (string) config('storefront.razorpay.key_id');
        $keySecret = (string) config('storefront.razorpay.key_secret');

        if ($keyId === '' || $keySecret === '') {
            throw new RuntimeException('Razorpay credentials are missing. Add RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET in your environment.');
        }

        $response = Http::withBasicAuth($keyId, $keySecret)
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => (int) round($order->total * 100),
                'currency' => 'INR',
                'receipt' => $order->order_number,
                'notes' => [
                    'order_id' => (string) $order->id,
                    'customer_name' => $order->customer_name,
                ],
            ])
            ->throw()
            ->json();

        return $response['id'];
    }

    public function verifySignature(string $gatewayOrderId, string $gatewayPaymentId, string $signature): bool
    {
        $keySecret = (string) config('storefront.razorpay.key_secret');

        if ($keySecret === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $gatewayOrderId.'|'.$gatewayPaymentId, $keySecret);

        return hash_equals($expected, $signature);
    }
}
