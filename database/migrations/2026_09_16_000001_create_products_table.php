<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('category_slug');
            $table->string('category_label');
            $table->unsignedTinyInteger('age_min');
            $table->unsignedTinyInteger('age_max');
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->json('sizes');
            $table->json('badges')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();

            $table->index(['category_slug', 'age_min', 'age_max']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
