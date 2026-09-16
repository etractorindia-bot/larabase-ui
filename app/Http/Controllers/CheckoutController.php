<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\CartManager;
use App\Support\RazorpayGateway;
use App\Support\StoreCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class CheckoutController extends Controller
{
    public function index(CartManager $cart): RedirectResponse|View
    {
        $summary = $cart->summary();

        if ($summary['item_count'] === 0) {
            return redirect()->route('store.index')->with('status', 'Add something to the bag before checkout.');
        }

        return view('checkout.index', [
            'summary' => $summary,
            'paymentMethods' => StoreCatalog::paymentMethods(),
        ]);
    }

    public function store(Request $request, CartManager $cart, RazorpayGateway $gateway): RedirectResponse|View
    {
        $summary = $cart->summary();

        if ($summary['item_count'] === 0) {
            return redirect()->route('store.index')->with('status', 'Your bag is empty.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['required', 'string', 'max:25'],
            'address_line_1' => ['required', 'string', 'max:150'],
            'address_line_2' => ['nullable', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'max:120'],
            'postal_code' => ['required', 'string', 'max:20'],
            'payment_method' => ['required', 'in:upi,card,netbanking,cod'],
        ]);

        $order = DB::transaction(function () use ($validated, $summary) {
            $order = Order::query()->create([
                'order_number' => 'KID-'.strtoupper((string) str()->ulid()),
                'customer_name' => $validated['customer_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address_line_1' => $validated['address_line_1'],
                'address_line_2' => $validated['address_line_2'] ?? null,
                'city' => $validated['city'],
                'state' => $validated['state'],
                'postal_code' => $validated['postal_code'],
                'payment_method' => $validated['payment_method'],
                'payment_provider' => $validated['payment_method'] === 'cod' ? 'cod' : 'razorpay',
                'status' => $validated['payment_method'] === 'cod' ? 'confirmed' : 'pending_payment',
                'payment_status' => $validated['payment_method'] === 'cod' ? 'cod_pending' : 'awaiting_payment',
                'currency' => 'INR',
                'subtotal' => $summary['subtotal'],
                'delivery_charge' => $summary['delivery'],
                'total' => $summary['total'],
                'meta' => [
                    'free_delivery_threshold' => $summary['free_delivery_threshold'],
                ],
            ]);

            foreach ($summary['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'category_label' => $item['product']->category_label,
                    'size' => $item['size'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                ]);
            }

            return $order->load('items');
        });

        if ($validated['payment_method'] === 'cod') {
            $cart->clear();

            return redirect()->route('orders.show', $order)->with('status', 'COD order placed successfully.');
        }

        try {
            $gatewayOrderId = $gateway->createOrder($order);
        } catch (RuntimeException $exception) {
            $order->update([
                'status' => 'payment_configuration_required',
                'payment_status' => 'failed',
            ]);

            return redirect()->route('checkout.index')->withErrors([
                'payment_method' => $exception->getMessage(),
            ])->withInput();
        }

        $order->update(['gateway_order_id' => $gatewayOrderId]);

        return view('checkout.pay', [
            'order' => $order->fresh('items'),
            'summary' => $summary,
            'razorpayKey' => config('storefront.razorpay.key_id'),
            'selectedMethod' => $validated['payment_method'],
        ]);
    }
}
