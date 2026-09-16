<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartManager
{
    public function add(Product $product, string $size, int $quantity = 1): void
    {
        $cart = session()->get('store_cart', []);
        $lineKey = $this->lineKey($product->id, $size);

        $existing = $cart[$lineKey] ?? [
            'product_id' => $product->id,
            'size' => $size,
            'quantity' => 0,
        ];

        $existing['quantity'] += max(1, $quantity);
        $cart[$lineKey] = $existing;

        session()->put('store_cart', $cart);
    }

    public function update(string $lineKey, int $quantity): void
    {
        $cart = session()->get('store_cart', []);

        if (! isset($cart[$lineKey])) {
            return;
        }

        if ($quantity <= 0) {
            unset($cart[$lineKey]);
        } else {
            $cart[$lineKey]['quantity'] = $quantity;
        }

        session()->put('store_cart', $cart);
    }

    public function remove(string $lineKey): void
    {
        $cart = session()->forget("store_cart.$lineKey");
    }

    public function clear(): void
    {
        session()->forget('store_cart');
    }

    public function items(): Collection
    {
        $rawItems = collect(session()->get('store_cart', []));
        $products = Product::query()
            ->whereIn('id', $rawItems->pluck('product_id')->all())
            ->get()
            ->keyBy('id');

        return $rawItems
            ->map(function (array $item, string $lineKey) use ($products) {
                $product = $products->get($item['product_id']);

                if (! $product) {
                    return null;
                }

                $quantity = max(1, (int) $item['quantity']);
                $unitPrice = (float) $product->price;

                return [
                    'line_key' => $lineKey,
                    'product' => $product,
                    'size' => $item['size'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $unitPrice * $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    public function summary(): array
    {
        $items = $this->items();
        $subtotal = (float) $items->sum('line_total');
        $delivery = $subtotal >= StoreCatalog::FREE_DELIVERY_THRESHOLD || $subtotal === 0.0
            ? 0.0
            : StoreCatalog::STANDARD_DELIVERY_CHARGE;

        return [
            'items' => $items,
            'item_count' => (int) $items->sum('quantity'),
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'total' => $subtotal + $delivery,
            'free_delivery_threshold' => StoreCatalog::FREE_DELIVERY_THRESHOLD,
            'free_delivery_remaining' => max(StoreCatalog::FREE_DELIVERY_THRESHOLD - $subtotal, 0),
            'wishlist_count' => count($this->wishlist()),
        ];
    }

    public function wishlist(): array
    {
        return array_values(array_unique(session()->get('store_wishlist', [])));
    }

    public function toggleWishlist(Product $product): bool
    {
        $wishlist = collect($this->wishlist());

        if ($wishlist->contains($product->id)) {
            session()->put('store_wishlist', $wishlist->reject(fn (int $id) => $id === $product->id)->values()->all());

            return false;
        }

        session()->put('store_wishlist', $wishlist->push($product->id)->unique()->values()->all());

        return true;
    }

    private function lineKey(int $productId, string $size): string
    {
        return sha1($productId.'|'.$size);
    }
}
