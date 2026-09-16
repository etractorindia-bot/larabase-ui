# larabase-ui

Kids Fashion E-commerce Store - Laravel UI with 11 Categories, Cart, Checkout & Razorpay Integration

## What this repository contains

This repository is the app-layer payload for a fresh Laravel project. Copy these folders into a new Laravel 11 application and you get:

- 11 storefront categories: trending, seasonal, outfits, innerwear, party, vacation, school, sports, style edit, festival, and night
- Age filters from 0 to 9
- Session-backed wishlist and shopping bag
- Free delivery logic: **FREE above ₹999**, otherwise **₹49**
- Checkout with **UPI, card, netbanking, and COD**
- Razorpay order creation and signature verification for online payments
- Orders and order items tables
- A **20-product seeder** that covers every category

## Copy into a fresh Laravel app

Create a new project first:

```bash
laravel new kids-store
cd kids-store
```

Then copy the contents of this repository into the Laravel app root so these paths exist:

- `/app/Http/Controllers`
- `/app/Models`
- `/app/Support`
- `/config/storefront.php`
- `/database/migrations`
- `/database/seeders`
- `/resources/views`
- `/routes/web.php`

## Environment setup

Add these values to `.env`:

```env
APP_NAME="Larabase Kids Store"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite
# or your preferred MySQL/PostgreSQL configuration

SESSION_DRIVER=file
RAZORPAY_KEY_ID=rzp_test_your_key
RAZORPAY_KEY_SECRET=your_secret
```

## Run the app

```bash
php artisan migrate --seed
php artisan serve
```

Visit `http://127.0.0.1:8000`.

## Storefront flow

1. Browse the catalog and filter by category or age.
2. Pick a size and add items to the bag.
3. Wishlist items with the save button.
4. Bag totals update with the delivery rule:
   - `₹0` delivery when subtotal is above `₹999`
   - `₹49` delivery otherwise
5. Checkout supports:
   - `UPI`
   - `Credit / Debit Card`
   - `Netbanking`
   - `Cash on Delivery`
6. Online payments:
   - Laravel creates a Razorpay order server-side
   - Razorpay Checkout collects payment
   - Laravel verifies `razorpay_signature` before confirming the order
7. COD orders confirm immediately as a fallback flow.

## Files of interest

- `/app/Support/CartManager.php` - session bag and wishlist logic
- `/app/Support/RazorpayGateway.php` - Razorpay order creation and signature verification
- `/app/Http/Controllers/CheckoutController.php` - checkout and order creation flow
- `/app/Http/Controllers/PaymentController.php` - signature verification callback
- `/database/seeders/ProductSeeder.php` - 20 demo products across all 11 categories

## Notes

- Product photos are placeholder image URLs so the demo works without a media pipeline.
- The online payment path requires valid Razorpay credentials.
- If you want to harden the demo for production, add auth/admin, inventory controls, webhook reconciliation, and media uploads.
