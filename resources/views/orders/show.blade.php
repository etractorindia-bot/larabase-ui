@extends('layouts.app', [
    'title' => 'Order '.$order->order_number,
    'bagCount' => 0,
    'wishlistCount' => 0,
])

@section('content')
    <div class="section-title">
        <div>
            <h1>Order confirmed</h1>
            <p class="muted" style="margin: .5rem 0 0;">{{ $order->order_number }} · {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</p>
        </div>
        <a href="{{ route('store.index') }}" class="button secondary">Back to store</a>
    </div>

    <div class="grid" style="grid-template-columns: minmax(0, 1.1fr) minmax(320px, .9fr); align-items: start;">
        <section class="card" style="padding: 1.3rem;">
            <h2 style="margin-top: 0;">Items</h2>
            <div class="grid" style="gap: .85rem;">
                @foreach ($order->items as $item)
                    <div class="card" style="padding: 1rem; border-radius: 18px; box-shadow:none;">
                        <div class="summary-row">
                            <div>
                                <strong>{{ $item->product_name }}</strong>
                                <div class="muted" style="font-size: .88rem;">{{ $item->category_label }} · Qty {{ $item->quantity }} · Size {{ $item->size }}</div>
                            </div>
                            <strong>₹{{ number_format((float) $item->line_total, 0) }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <aside class="grid" style="gap: 1rem;">
            <div class="card" style="padding: 1.3rem;">
                <h2 style="margin-top: 0;">Shipping</h2>
                <p style="margin: 0; line-height: 1.7;">
                    {{ $order->customer_name }}<br>
                    {{ $order->address_line_1 }}<br>
                    @if ($order->address_line_2)
                        {{ $order->address_line_2 }}<br>
                    @endif
                    {{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}<br>
                    {{ $order->phone }} · {{ $order->email }}
                </p>
            </div>
            <div class="card" style="padding: 1.3rem;">
                <h2 style="margin-top: 0;">Payment</h2>
                <div class="grid" style="gap: .6rem;">
                    <div class="summary-row"><span>Method</span><strong>{{ strtoupper($order->payment_method) }}</strong></div>
                    <div class="summary-row"><span>Provider</span><strong>{{ strtoupper($order->payment_provider) }}</strong></div>
                    <div class="summary-row"><span>Subtotal</span><strong>₹{{ number_format((float) $order->subtotal, 0) }}</strong></div>
                    <div class="summary-row"><span>Delivery</span><strong>{{ (float) $order->delivery_charge > 0 ? '₹'.number_format((float) $order->delivery_charge, 0) : 'FREE' }}</strong></div>
                    <div class="summary-row" style="font-size: 1.08rem;"><span>Total</span><strong>₹{{ number_format((float) $order->total, 0) }}</strong></div>
                </div>
            </div>
        </aside>
    </div>
@endsection
