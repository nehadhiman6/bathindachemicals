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
        Schema::create('item_freights', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->nullable();
            $table->date('wef_date')->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->string('uid', 50)->nullable()->unique();
            $table->timestamps();
        });


        Schema::create('item_freight_dets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_freight_id');
            $table->integer('city_id');
            $table->decimal('freight',15,2)->nullable();
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
        Schema::dropIfExists('freight_items');
    }
};
