@extends('layouts.app', [
    'title' => 'Checkout',
    'bagCount' => $summary['item_count'],
    'wishlistCount' => $summary['wishlist_count'],
])

@section('content')
    <div class="section-title">
        <div>
            <h1>Checkout</h1>
            <p class="muted" style="margin: .5rem 0 0;">Use Razorpay for UPI, cards, or netbanking, or fall back to COD.</p>
        </div>
        <a href="{{ route('store.index') }}" class="button secondary">Back to store</a>
    </div>

    <div class="grid" style="grid-template-columns: minmax(0, 1.3fr) minmax(320px, .9fr); align-items: start;">
        <form method="POST" action="{{ route('checkout.store') }}" class="card" style="padding: 1.3rem; display: grid; gap: 1rem;">
            @csrf
            <div class="grid" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
                <div class="field">
                    <label for="customer_name">Full name</label>
                    <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                </div>
                <div class="field">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" value="{{ old('phone') }}" required>
                </div>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
            </div>
            <div class="field">
                <label for="address_line_1">Address line 1</label>
                <input id="address_line_1" name="address_line_1" value="{{ old('address_line_1') }}" required>
            </div>
            <div class="field">
                <label for="address_line_2">Address line 2</label>
                <input id="address_line_2" name="address_line_2" value="{{ old('address_line_2') }}">
            </div>
            <div class="grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
                <div class="field">
                    <label for="city">City</label>
                    <input id="city" name="city" value="{{ old('city') }}" required>
                </div>
                <div class="field">
                    <label for="state">State</label>
                    <input id="state" name="state" value="{{ old('state') }}" required>
                </div>
                <div class="field">
                    <label for="postal_code">PIN code</label>
                    <input id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required>
                </div>
            </div>

            <div class="card" style="padding: 1rem; border-radius: 20px; box-shadow: none;">
                <h2 style="margin-top: 0; font-size: 1.05rem;">Payment options</h2>
                <div class="grid" style="gap: .75rem;">
                    @foreach ($paymentMethods as $value => $label)
                        <label class="pill" style="justify-content: flex-start; box-shadow:none;">
                            <input type="radio" name="payment_method" value="{{ $value }}" {{ old('payment_method', 'upi') === $value ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="muted" style="margin-bottom: 0;">Online payments create a Razorpay order first and verify the returned signature before confirmation.</p>
            </div>

            <button type="submit">Continue to payment</button>
        </form>

        <aside class="card" style="padding: 1.3rem;">
            <h2 style="margin-top: 0;">Order summary</h2>
            <div class="grid" style="gap: .8rem;">
                @foreach ($summary['items'] as $item)
                    <div class="summary-row">
                        <div>
                            <strong>{{ $item['product']->name }}</strong>
                            <div class="muted" style="font-size: .88rem;">Qty {{ $item['quantity'] }} · Size {{ $item['size'] }}</div>
                        </div>
                        <strong>₹{{ number_format($item['line_total'], 0) }}</strong>
                    </div>
                @endforeach
            </div>
            <hr style="border: 0; border-top: 1px solid var(--border); margin: 1rem 0;">
            <div class="grid" style="gap: .6rem;">
                <div class="summary-row"><span>Subtotal</span><strong>₹{{ number_format($summary['subtotal'], 0) }}</strong></div>
                <div class="summary-row"><span>Delivery</span><strong>{{ $summary['delivery'] > 0 ? '₹'.number_format($summary['delivery'], 0) : 'FREE' }}</strong></div>
                <div class="summary-row" style="font-size: 1.08rem;"><span>Total</span><strong>₹{{ number_format($summary['total'], 0) }}</strong></div>
            </div>
        </aside>
    </div>
@endsection
