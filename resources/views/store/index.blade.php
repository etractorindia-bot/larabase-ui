@extends('layouts.app', [
    'title' => 'Kids Fashion Store',
    'bagCount' => $cartSummary['item_count'],
    'wishlistCount' => $cartSummary['wishlist_count'],
])

@section('content')
    <div class="section-title">
        <div>
            <h1>Upgraded kids fashion store</h1>
            <p class="muted" style="margin: .5rem 0 0;">Browse all 11 categories, filter for ages 0–9, save to wishlist, and test free-delivery checkout.</p>
        </div>
        <div class="pill-row">
            <span class="pill">{{ $products->count() }} products</span>
            <span class="pill">11 aisles</span>
            <span class="pill">Session bag + wishlist</span>
        </div>
    </div>

    <div class="grid" style="grid-template-columns: minmax(0, 1.7fr) minmax(300px, .95fr); align-items: start;">
        <div class="grid" style="gap: 1rem;">
            <section class="card" style="padding: 1.2rem;">
                <div class="section-title" style="margin-bottom: .75rem;">
                    <div>
                        <h2 style="font-size: 1.1rem;">Filters</h2>
                        <p class="muted" style="margin: .35rem 0 0;">Pick a category and age to tighten the demo catalog.</p>
                    </div>
                    <a href="{{ route('store.index') }}" class="button secondary">Reset</a>
                </div>
                <div class="grid" style="gap: 1rem;">
                    <div>
                        <div class="muted" style="margin-bottom: .5rem; font-weight: 700;">Categories</div>
                        <div class="pill-row">
                            @foreach ($categories as $slug => $label)
                                <a href="{{ route('store.index', array_filter(['category' => $slug, 'age' => $selectedAge], fn ($value) => $value !== '' && $value !== null)) }}"
                                   class="pill"
                                   style="{{ $selectedCategory === $slug ? 'background:#7c3aed;color:white;border-color:#7c3aed;' : '' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <div class="muted" style="margin-bottom: .5rem; font-weight: 700;">Age</div>
                        <div class="pill-row">
                            @foreach ($ages as $age)
                                <a href="{{ route('store.index', array_filter(['category' => $selectedCategory, 'age' => $age], fn ($value) => $value !== '' && $value !== null)) }}"
                                   class="pill"
                                   style="{{ $selectedAge === $age ? 'background:#fb7185;color:white;border-color:#fb7185;' : '' }}">{{ $age }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid" style="grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));">
                @forelse ($products as $product)
                    <article class="card" style="overflow: hidden; display: grid;">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="width: 100%; aspect-ratio: 3 / 4; object-fit: cover; background: #fdf2f8;">
                        <div style="padding: 1rem; display: grid; gap: .8rem;">
                            <div class="pill-row" style="gap: .45rem;">
                                <span class="badge">{{ $product->category_label }}</span>
                                <span class="badge" style="background:#ffe4e6;color:#be123c;">Age {{ $product->age_min }}–{{ $product->age_max }}</span>
                            </div>
                            <div>
                                <h3 style="margin: 0 0 .3rem;">{{ $product->name }}</h3>
                                <p class="muted" style="margin: 0; font-size: .92rem;">{{ $product->description }}</p>
                            </div>
                            <div class="pill-row" style="gap: .4rem;">
                                @foreach (($product->badges ?? []) as $badge)
                                    <span class="pill" style="padding: .4rem .6rem; font-size: .78rem; box-shadow: none;">{{ $badge }}</span>
                                @endforeach
                            </div>
                            <div class="price">
                                ₹{{ number_format((float) $product->price, 0) }}
                                @if ($product->compare_at_price)
                                    <del>₹{{ number_format((float) $product->compare_at_price, 0) }}</del>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('cart.add', $product) }}" class="grid" style="gap: .75rem;">
                                @csrf
                                <div class="field">
                                    <label for="size-{{ $product->id }}">Size</label>
                                    <select id="size-{{ $product->id }}" name="size">
                                        @foreach ($product->sizes as $size)
                                            <option value="{{ $size }}">{{ $size }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="display: flex; gap: .65rem; align-items: center;">
                                    <button type="submit" class="full">Add to bag</button>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('wishlist.toggle', $product) }}">
                                @csrf
                                <button type="submit" class="secondary full">{{ in_array($product->id, $wishlist, true) ? 'Remove from wishlist' : 'Save to wishlist' }}</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="card" style="padding: 1.25rem; grid-column: 1 / -1;">
                        No products matched that filter combination.
                    </div>
                @endforelse
            </section>
        </div>

        <aside class="card" style="padding: 1.2rem; position: sticky; top: 6rem;">
            <div class="section-title" style="margin-bottom: .8rem;">
                <div>
                    <h2 style="font-size: 1.1rem;">Bag summary</h2>
                    <p class="muted" style="margin: .35rem 0 0;">{{ $cartSummary['item_count'] }} item(s) in the session cart.</p>
                </div>
            </div>

            <div class="grid" style="gap: .9rem;">
                @forelse ($cartSummary['items'] as $item)
                    <div class="card" style="padding: .9rem; border-radius: 18px; box-shadow: none;">
                        <div style="display: flex; justify-content: space-between; gap: .75rem;">
                            <div>
                                <div style="font-weight: 700;">{{ $item['product']->name }}</div>
                                <div class="muted" style="font-size: .88rem;">{{ $item['product']->category_label }} · Size {{ $item['size'] }}</div>
                            </div>
                            <div style="font-weight: 700;">₹{{ number_format($item['line_total'], 0) }}</div>
                        </div>
                        <div style="display: flex; gap: .5rem; margin-top: .8rem;">
                            <form method="POST" action="{{ route('cart.update', $item['line_key']) }}" style="display:flex; gap:.5rem; flex:1;">
                                @csrf
                                @method('PATCH')
                                <input type="number" min="0" max="10" name="quantity" value="{{ $item['quantity'] }}" style="width: 74px; padding: .65rem .75rem; border-radius: 12px; border:1px solid var(--border);">
                                <button type="submit" class="secondary" style="flex:1;">Update</button>
                            </form>
                            <form method="POST" action="{{ route('cart.remove', $item['line_key']) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="accent">Remove</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="muted">Your bag is empty. Add a product to unlock checkout.</div>
                @endforelse
            </div>

            <div class="grid" style="gap: .55rem; margin-top: 1rem;">
                <div class="summary-row"><span>Subtotal</span><strong>₹{{ number_format($cartSummary['subtotal'], 0) }}</strong></div>
                <div class="summary-row"><span>Delivery</span><strong>{{ $cartSummary['delivery'] > 0 ? '₹'.number_format($cartSummary['delivery'], 0) : 'FREE' }}</strong></div>
                <div class="summary-row" style="font-size: 1.08rem;"><span>Total</span><strong>₹{{ number_format($cartSummary['total'], 0) }}</strong></div>
                @if ($cartSummary['free_delivery_remaining'] > 0)
                    <div class="pill" style="box-shadow:none;">Add ₹{{ number_format($cartSummary['free_delivery_remaining'], 0) }} more for free delivery.</div>
                @else
                    <div class="pill" style="box-shadow:none; background:#ecfdf5; color:#15803d; border-color:#bbf7d0;">Free delivery unlocked.</div>
                @endif
                <a href="{{ route('checkout.index') }}" class="button full" style="text-align: center;">Go to checkout</a>
            </div>
        </aside>
    </div>
@endsection
