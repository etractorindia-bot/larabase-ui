<?php

namespace App\Support;

class StoreCatalog
{
    public const FREE_DELIVERY_THRESHOLD = 999;
    public const STANDARD_DELIVERY_CHARGE = 49;

    public static function categories(): array
    {
        return [
            'trending' => 'Trending',
            'seasonal' => 'Seasonal',
            'outfits' => 'Outfits',
            'innerwear' => 'Innerwear',
            'party' => 'Party',
            'vacation' => 'Vacation',
            'school' => 'School',
            'sports' => 'Sports',
            'style-edit' => 'Style Edit',
            'festival' => 'Festival',
            'night' => 'Night',
        ];
    }

    public static function paymentMethods(): array
    {
        return [
            'upi' => 'UPI',
            'card' => 'Credit / Debit Card',
            'netbanking' => 'Netbanking',
            'cod' => 'Cash on Delivery',
        ];
    }
}
