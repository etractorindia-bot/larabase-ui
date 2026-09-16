<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Support\StoreCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['Tiny Trend Tee', 'trending', 'Playful everyday tee with breathable cotton jersey.', 2, 6, 399, 499, ['2Y', '3Y', '4Y', '5Y', '6Y'], ['Trending', 'Best Seller']],
            ['Little Layer Jacket', 'seasonal', 'Lightweight zip jacket for breezy evenings.', 4, 9, 899, 1099, ['4Y', '5Y', '6Y', '7Y', '8Y', '9Y'], ['Season Pick']],
            ['Rainbow Coord Set', 'outfits', 'Two-piece coordinated set for quick dressing.', 1, 5, 999, 1299, ['1Y', '2Y', '3Y', '4Y', '5Y'], ['Free Delivery']],
            ['Cloud Soft Vest Pack', 'innerwear', 'Combed cotton vests in a value pack of three.', 0, 4, 349, 449, ['0-6M', '6-12M', '1Y', '2Y', '3Y', '4Y'], ['Everyday Essential']],
            ['Sparkle Party Dress', 'party', 'Twirl-ready party dress with shimmer overlay.', 3, 8, 1499, 1799, ['3Y', '4Y', '5Y', '6Y', '7Y', '8Y'], ['Party Ready']],
            ['Palm Print Shorts', 'vacation', 'Relaxed-fit shorts built for holiday comfort.', 2, 7, 549, 699, ['2Y', '3Y', '4Y', '5Y', '6Y', '7Y'], ['Holiday Drop']],
            ['Bright Start Uniform Set', 'school', 'Smart school-ready polo and shorts combo.', 5, 9, 1199, 1399, ['5Y', '6Y', '7Y', '8Y', '9Y'], ['School Edit']],
            ['Sprint Active Set', 'sports', 'Moisture-friendly active set for movement days.', 4, 9, 1099, 1299, ['4Y', '5Y', '6Y', '7Y', '8Y', '9Y'], ['Performance']],
            ['Street Style Hoodie', 'style-edit', 'Oversized hoodie with streetwear-inspired finish.', 5, 9, 1299, 1499, ['5Y', '6Y', '7Y', '8Y', '9Y'], ['Style Edit']],
            ['Celebration Kurta Set', 'festival', 'Festive kurta set with soft lining and bright hues.', 2, 8, 1399, 1699, ['2Y', '3Y', '4Y', '5Y', '6Y', '7Y', '8Y'], ['Festival Hero']],
            ['Moonlight Sleep Suit', 'night', 'Button-front night suit with dreamy print.', 1, 6, 699, 799, ['1Y', '2Y', '3Y', '4Y', '5Y', '6Y'], ['Night Comfort']],
            ['Comet Graphic Tee', 'trending', 'Statement tee with fun character artwork.', 3, 9, 449, 599, ['3Y', '4Y', '5Y', '6Y', '7Y', '8Y', '9Y'], ['Fresh Drop']],
            ['Monsoon Splash Romper', 'seasonal', 'Quick-dry romper for rainy-day play.', 0, 2, 649, 799, ['0-6M', '6-12M', '1Y', '2Y'], ['Season Pick']],
            ['Weekend Denim Dungaree', 'outfits', 'Adjustable dungaree layered with a striped tee.', 1, 4, 1249, 1499, ['1Y', '2Y', '3Y', '4Y'], ['Bestseller']],
            ['Soft Hug Brief Set', 'innerwear', 'Five-pack briefs with smooth stretch waistband.', 2, 7, 399, 499, ['2Y', '3Y', '4Y', '5Y', '6Y', '7Y'], ['Multi Pack']],
            ['Starry Tux Set', 'party', 'Three-piece tuxedo set for memorable celebrations.', 4, 9, 1899, 2199, ['4Y', '5Y', '6Y', '7Y', '8Y', '9Y'], ['Premium']],
            ['Sunny Escape Shirt', 'vacation', 'Resort shirt with a crisp collar and palm print.', 3, 8, 799, 999, ['3Y', '4Y', '5Y', '6Y', '7Y', '8Y'], ['Holiday Drop']],
            ['Campus Cargo Joggers', 'school', 'Durable joggers with roomy cargo pockets.', 5, 9, 899, 1099, ['5Y', '6Y', '7Y', '8Y', '9Y'], ['School Edit']],
            ['Mini Marathon Leggings', 'sports', 'Stretch leggings with a soft brushed finish.', 3, 8, 649, 799, ['3Y', '4Y', '5Y', '6Y', '7Y', '8Y'], ['Activewear']],
            ['Dream Glow Kurti', 'festival', 'Festive kurti with mirror-work-inspired print.', 4, 9, 1199, 1399, ['4Y', '5Y', '6Y', '7Y', '8Y', '9Y'], ['Festival Hero']],
        ];

        $categoryLabels = StoreCatalog::categories();

        $payload = collect($products)->map(function (array $product) use ($categoryLabels) {
            [$name, $categorySlug, $description, $ageMin, $ageMax, $price, $compareAt, $sizes, $badges] = $product;

            return [
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
                'category_slug' => $categorySlug,
                'category_label' => $categoryLabels[$categorySlug],
                'age_min' => $ageMin,
                'age_max' => $ageMax,
                'price' => $price,
                'compare_at_price' => $compareAt,
                'sizes' => $sizes,
                'badges' => $badges,
                'image_url' => 'https://placehold.co/600x800/fdf2f8/7c3aed?text='.rawurlencode($name),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        Product::query()->upsert($payload, ['slug'], [
            'description',
            'category_slug',
            'category_label',
            'age_min',
            'age_max',
            'price',
            'compare_at_price',
            'sizes',
            'badges',
            'image_url',
            'updated_at',
        ]);
    }
}
