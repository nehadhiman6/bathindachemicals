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
        Schema::create('item_unit_conversions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->default(0);
            $table->integer('item_unit_id')->default(0);
            $table->decimal('multiplier',15,2)->nullable()->default(0);
            $table->decimal('divider',15,2)->nullable()->default(0);
            $table->decimal('conversion_factor',15,2)->nullable()->default(0);
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_unit_conversions');
    }
};
