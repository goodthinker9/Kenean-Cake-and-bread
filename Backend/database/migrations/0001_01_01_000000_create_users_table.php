<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id('branch_id');
            $table->string('branch_name');
            $table->string('branch_code')->unique();
            $table->enum('branch_type', ['Main', 'Regular'])->default('Regular');
            $table->string('region')->default('Addis Ababa');
            $table->string('city')->default('Addis Ababa');
            $table->string('address');
            $table->string('phone');
            $table->string('opening_hours')->default('07:00 - 21:00');
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->enum('role', ['Super Admin', 'Main Branch Manager', 'Branch Admin', 'Branch Staff', 'Customer'])->default('Customer');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('branch_id')->references('branch_id')->on('branches')->nullOnDelete();
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->foreign('manager_id')->references('user_id')->on('users')->nullOnDelete();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id('token_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->index();
            $table->string('token');
            $table->string('token_hash')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->unsignedBigInteger('user_id');
            $table->string('city');
            $table->string('region');
            $table->string('street');
            $table->text('notes')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
        });

        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id('zone_id');
            $table->unsignedBigInteger('branch_id');
            $table->string('zone_name');
            $table->decimal('delivery_fee', 10, 2)->default(0.00);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();

            $table->foreign('branch_id')->references('branch_id')->on('branches')->cascadeOnDelete();
        });

        // Enable Row Level Security on all tables in this migration.
        // No policies are added — Laravel connects as the DB owner and bypasses RLS.
        // This satisfies the spec requirement: RLS enabled, no policies.
        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE branches ENABLE ROW LEVEL SECURITY');
            DB::statement('ALTER TABLE users ENABLE ROW LEVEL SECURITY');
            DB::statement('ALTER TABLE password_reset_tokens ENABLE ROW LEVEL SECURITY');
            DB::statement('ALTER TABLE sessions ENABLE ROW LEVEL SECURITY');
            DB::statement('ALTER TABLE addresses ENABLE ROW LEVEL SECURITY');
            DB::statement('ALTER TABLE delivery_zones ENABLE ROW LEVEL SECURITY');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('branches');
    }
};
