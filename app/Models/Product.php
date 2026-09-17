<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
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
    ];

    protected function casts(): array
    {
        return [
            'sizes' => 'array',
            'badges' => 'array',
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
        ];
    }
}
