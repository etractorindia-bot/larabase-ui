@extends('layouts.app', [
    'title' => 'Complete payment',
    'bagCount' => $summary['item_count'],
    'wishlistCount' => $summary['wishlist_count'],
])

@push('head')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endpush

@section('content')
    <div class="section-title">
        <div>
            <h1>Complete your payment</h1>
            <p class="muted" style="margin: .5rem 0 0;">Order {{ $order->order_number }} is waiting for Razorpay confirmation.</p>
        </div>
        <a href="{{ route('store.index') }}" class="button secondary">Keep shopping</a>
    </div>

    <div class="grid" style="grid-template-columns: minmax(0, 1.15fr) minmax(320px, .85fr); align-items: start;">
        <section class="card" style="padding: 1.3rem; display: grid; gap: 1rem;">
            <div class="pill-row">
                <span class="pill">Gateway: Razorpay</span>
                <span class="pill">Method selected: {{ strtoupper($selectedMethod) }}</span>
            </div>
            <div>
                <h2 style="margin: 0 0 .5rem;">Online payment flow</h2>
                <ol class="muted" style="margin: 0; padding-left: 1.25rem; line-height: 1.7;">
                    <li>The server already created a Razorpay order for ₹{{ number_format((float) $order->total, 0) }}.</li>
                    <li>Tap the payment button below to open the Razorpay Checkout popup.</li>
                    <li>After success, the signature is posted back to Laravel for verification before the order is confirmed.</li>
                </ol>
            </div>
            <button id="razorpay-pay-button">Pay now</button>
            <form id="razorpay-verify-form" method="POST" action="{{ route('checkout.verify') }}" style="display:none;">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <input type="hidden" name="razorpay_order_id">
                <input type="hidden" name="razorpay_payment_id">
                <input type="hidden" name="razorpay_signature">
            </form>
        </section>

        <aside class="card" style="padding: 1.3rem;">
            <h2 style="margin-top: 0;">Order summary</h2>
            <div class="grid" style="gap: .8rem;">
                @foreach ($order->items as $item)
                    <div class="summary-row">
                        <div>
                            <strong>{{ $item->product_name }}</strong>
                            <div class="muted" style="font-size: .88rem;">{{ $item->category_label }} · Qty {{ $item->quantity }} · Size {{ $item->size }}</div>
                        </div>
                        <strong>₹{{ number_format((float) $item->line_total, 0) }}</strong>
                    </div>
                @endforeach
            </div>
            <hr style="border: 0; border-top: 1px solid var(--border); margin: 1rem 0;">
            <div class="grid" style="gap: .6rem;">
                <div class="summary-row"><span>Subtotal</span><strong>₹{{ number_format((float) $order->subtotal, 0) }}</strong></div>
                <div class="summary-row"><span>Delivery</span><strong>{{ (float) $order->delivery_charge > 0 ? '₹'.number_format((float) $order->delivery_charge, 0) : 'FREE' }}</strong></div>
                <div class="summary-row" style="font-size: 1.08rem;"><span>Total</span><strong>₹{{ number_format((float) $order->total, 0) }}</strong></div>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
<script>
    const payButton = document.getElementById('razorpay-pay-button');
    const verifyForm = document.getElementById('razorpay-verify-form');

    payButton.addEventListener('click', function () {
        const options = {
            key: @json($razorpayKey),
            amount: {{ (int) round(((float) $order->total) * 100) }},
            currency: 'INR',
            name: 'Larabase Kids Store',
            description: 'Kids fashion order {{ $order->order_number }}',
            order_id: @json($order->gateway_order_id),
            prefill: {
                name: @json($order->customer_name),
                email: @json($order->email),
                contact: @json($order->phone),
            },
            theme: { color: '#7c3aed' },
            handler: function (response) {
                verifyForm.querySelector('[name="razorpay_order_id"]').value = response.razorpay_order_id;
                verifyForm.querySelector('[name="razorpay_payment_id"]').value = response.razorpay_payment_id;
                verifyForm.querySelector('[name="razorpay_signature"]').value = response.razorpay_signature;
                verifyForm.submit();
            }
        };

        const razorpay = new Razorpay(options);
        razorpay.open();
    });
</script>
@endpush
