<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('price_records', function (Blueprint $table) {
            $table->float('price_min')->nullable();
            $table->float('price_max')->nullable();
            $table->float('price_average')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_records', function (Blueprint $table) {
            $table->dropColumn(['price_min', 'price_max', 'price_average']);
        });
    }
};
