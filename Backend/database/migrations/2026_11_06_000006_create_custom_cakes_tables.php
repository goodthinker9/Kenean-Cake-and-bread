<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_cake_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->string('request_number')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id');
            $table->string('cake_size'); // Small, Medium, Large, Tiered
            $table->string('flavor'); // Chocolate, Vanilla, Red Velvet, etc.
            $table->text('design')->nullable();
            $table->string('message')->nullable();
            $table->date('needed_by');
            $table->decimal('quoted_price', 10, 2)->nullable();
            $table->decimal('deposit_required_percentage', 5, 2)->default(50.00);
            $table->decimal('deposit_paid', 10, 2)->default(0.00);
            $table->enum('status', [
                'Submitted',
                'Quoted',
                'Confirmed',
                'Preparing',
                'Ready',
                'Picked up',
                'Delivered',
                'Rejected',
                'Cancelled'
            ])->default('Submitted');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('branch_id')->references('branch_id')->on('branches')->cascadeOnDelete();
        });

        Schema::create('custom_cake_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->unsignedBigInteger('request_id');
            $table->string('image_url');
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('custom_cake_requests')->cascadeOnDelete();
        });

        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE custom_cake_requests ENABLE ROW LEVEL SECURITY');
            DB::statement('ALTER TABLE custom_cake_images ENABLE ROW LEVEL SECURITY');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_cake_images');
        Schema::dropIfExists('custom_cake_requests');
    }
};
