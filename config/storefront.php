<?php

return [
    'free_delivery_threshold' => 999,
    'standard_delivery_charge' => 49,
    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID'),
        'key_secret' => env('RAZORPAY_KEY_SECRET'),
    ],
];
