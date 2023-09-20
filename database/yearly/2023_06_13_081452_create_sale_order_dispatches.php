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
        Schema::create('sale_order_dispatches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dispatch_id')->nullable();
            $table->integer('sale_order_id')->nullable();
            $table->integer('item_id')->nullable();
            $table->integer('packing_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->string('destination',700)->nullable();
            $table->decimal('qty',15,3)->nullable();
            $table->decimal('weight',15,3)->nullable();
            $table->integer('created_by')->default(0)->nullable();
            $table->integer('updated_by')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_order_dispatches');
    }
};
