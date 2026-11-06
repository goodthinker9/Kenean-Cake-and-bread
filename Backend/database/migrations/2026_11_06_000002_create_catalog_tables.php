<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->unsignedBigInteger('category_id');
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->integer('shelf_life_hours')->default(48);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();

            $table->foreign('category_id')->references('category_id')->on('categories')->cascadeOnDelete();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->unsignedBigInteger('product_id');
            $table->string('image_url');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('product_id')->references('product_id')->on('products')->cascadeOnDelete();
        });

        Schema::create('branch_product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('price', 10, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['branch_id', 'product_id']);
            $table->foreign('branch_id')->references('branch_id')->on('branches')->cascadeOnDelete();
            $table->foreign('product_id')->references('product_id')->on('products')->cascadeOnDelete();
        });

        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE categories ENABLE ROW LEVEL SECURITY');
            DB::statement('ALTER TABLE products ENABLE ROW LEVEL SECURITY');
            DB::statement('ALTER TABLE product_images ENABLE ROW LEVEL SECURITY');
            DB::statement('ALTER TABLE branch_product_prices ENABLE ROW LEVEL SECURITY');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_product_prices');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
