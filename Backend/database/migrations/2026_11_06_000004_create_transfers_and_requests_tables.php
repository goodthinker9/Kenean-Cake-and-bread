<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id('transfer_id');
            $table->string('transfer_number')->unique();
            $table->unsignedBigInteger('source_branch_id');
            $table->unsignedBigInteger('destination_branch_id');
            $table->enum('status', [
                'Draft',
                'Approved',
                'Dispatched',
                'In transit',
                'Partially received',
                'Received',
                'Disputed',
                'Cancelled'
            ])->default('Draft');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['destination_branch_id', 'status']);
            $table->foreign('source_branch_id')->references('branch_id')->on('branches')->cascadeOnDelete();
            $table->foreign('destination_branch_id')->references('branch_id')->on('branches')->cascadeOnDelete();
            $table->foreign('created_by')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('approved_by')->references('user_id')->on('users')->nullOnDelete();
        });

        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id('transfer_item_id');
            $table->unsignedBigInteger('transfer_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity_sent')->default(0);
            $table->integer('quantity_received')->default(0);
            $table->integer('damaged_quantity')->default(0);
            $table->integer('missing_quantity')->default(0);
            $table->text('discrepancy_reason')->nullable();
            $table->timestamps();

            $table->foreign('transfer_id')->references('transfer_id')->on('stock_transfers')->cascadeOnDelete();
            $table->foreign('product_id')->references('product_id')->on('products')->cascadeOnDelete();
        });

        Schema::create('stock_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->unsignedBigInteger('branch_id');
            $table->enum('status', ['Pending', 'Converted', 'Rejected'])->default('Pending');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('converted_transfer_id')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('branch_id')->on('branches')->cascadeOnDelete();
            $table->foreign('requested_by')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('converted_transfer_id')->references('transfer_id')->on('stock_transfers')->nullOnDelete();
        });

        Schema::create('stock_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity_requested');
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('stock_requests')->cascadeOnDelete();
            $table->foreign('product_id')->references('product_id')->on('products')->cascadeOnDelete();
        });

        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE stock_transfers ENABLE ROW LEVEL SECURITY');
            DB::statement('ALTER TABLE stock_transfer_items ENABLE ROW LEVEL SECURITY');
            DB::statement('ALTER TABLE stock_requests ENABLE ROW LEVEL SECURITY');
            DB::statement('ALTER TABLE stock_request_items ENABLE ROW LEVEL SECURITY');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_request_items');
        Schema::dropIfExists('stock_requests');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
    }
};
