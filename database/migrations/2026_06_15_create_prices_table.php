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
        Schema::create('price_records', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('market_id');
            $table->string('vegetable_id');
            
            // Allow null values because 'n.a.' values are common in dataset
            $table->float('price')->nullable();
            $table->float('price_yesterday')->nullable();
            $table->float('change_percent')->nullable();
            $table->string('trend')->nullable(); // 'up', 'down', 'flat', 'none'
            
            $table->timestamps();

            // Indexing for faster chart queries
            $table->index(['date', 'market_id', 'vegetable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_records');
    }
};
