<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\CartManager;
use App\Support\StoreCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(Request $request, CartManager $cart): View
    {
        $selectedCategory = $request->string('category')->toString();
        $selectedAge = $request->filled('age') ? (int) $request->integer('age') : null;

        $products = Product::query()
            ->when($selectedCategory !== '', fn ($query) => $query->where('category_slug', $selectedCategory))
            ->when($selectedAge !== null, fn ($query) => $query
                ->where('age_min', '<=', $selectedAge)
                ->where('age_max', '>=', $selectedAge))
            ->orderBy('category_label')
            ->orderBy('name')
            ->get();

        return view('store.index', [
            'products' => $products,
            'categories' => StoreCatalog::categories(),
            'ages' => range(0, 9),
            'selectedCategory' => $selectedCategory,
            'selectedAge' => $selectedAge,
            'cartSummary' => $cart->summary(),
            'wishlist' => $cart->wishlist(),
        ]);
    }

    public function toggleWishlist(Product $product, CartManager $cart): RedirectResponse
    {
        $added = $cart->toggleWishlist($product);

        return back()->with('status', $added ? 'Added to wishlist.' : 'Removed from wishlist.');
    }
}
