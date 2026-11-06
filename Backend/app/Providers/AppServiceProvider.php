<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Supabase / PostgreSQL: ensure the search path is always set
        // to the public schema so table names resolve without qualification.
        if (config('database.default') === 'pgsql') {
            DB::statement("SET search_path TO public");
        }
    }
}
