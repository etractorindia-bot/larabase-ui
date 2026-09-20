<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\CartManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public function add(Request $request, Product $product, CartManager $cart): RedirectResponse
    {
        $validated = $request->validate([
            'size' => ['required', Rule::in($product->sizes ?? [])],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $cart->add($product, $validated['size'], (int) ($validated['quantity'] ?? 1));

        return back()->with('status', 'Added to bag.');
    }

    public function update(Request $request, string $lineKey, CartManager $cart): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:10'],
        ]);

        $cart->update($lineKey, (int) $validated['quantity']);

        return back()->with('status', (int) $validated['quantity'] === 0 ? 'Item removed from bag.' : 'Bag updated.');
    }

    public function remove(string $lineKey, CartManager $cart): RedirectResponse
    {
        $cart->remove($lineKey);

        return back()->with('status', 'Item removed from bag.');
    }
}
