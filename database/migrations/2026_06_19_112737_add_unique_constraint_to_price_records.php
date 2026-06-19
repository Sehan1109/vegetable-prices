<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds a unique composite constraint so updateOrCreate is reliable
     * and re-running the daily cron never creates duplicate rows.
     */
    public function up(): void
    {
        Schema::table('price_records', function (Blueprint $table) {
            // Drop the existing plain index first (created in the original migration)
            $table->dropIndex(['date', 'market_id', 'vegetable_id']);

            // Add the unique constraint
            $table->unique(['date', 'market_id', 'vegetable_id'], 'price_records_date_market_veg_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_records', function (Blueprint $table) {
            $table->dropUnique('price_records_date_market_veg_unique');
            $table->index(['date', 'market_id', 'vegetable_id']);
        });
    }
};
