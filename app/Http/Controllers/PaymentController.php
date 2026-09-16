<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\CartManager;
use App\Support\RazorpayGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function verify(Request $request, CartManager $cart, RazorpayGateway $gateway): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $order = Order::query()->findOrFail($validated['order_id']);

        abort_unless(
            $order->gateway_order_id === $validated['razorpay_order_id']
            && $gateway->verifySignature(
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature'],
            ),
            422,
            'Payment signature verification failed.'
        );

        $order->update([
            'gateway_payment_id' => $validated['razorpay_payment_id'],
            'gateway_signature' => $validated['razorpay_signature'],
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $cart->clear();

        return redirect()->route('orders.show', $order)->with('status', 'Payment captured and order confirmed.');
    }
}
