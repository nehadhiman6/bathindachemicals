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
        Schema::create('packing_formulas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->nullable();
            $table->integer('party_cat_id')->nullable();
            $table->date('wef_date')->nullable();
            $table->integer('packing_id')->nullable();
            $table->string('date_applicable_on',1)->nullable(); //C,D
            $table->integer('created_by')->default(0)->nullable();
            $table->integer('updated_by')->default(0)->nullable();
            $table->string('uid', 50)->nullable()->unique();
            $table->timestamps();
            $table->unique(['item_id', 'party_cat_id','wef_date','packing_id']);
        });
        Schema::create('packing_formula_dets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pack_formula_id');
            $table->integer('brand_id')->nullable();
            $table->integer('packing_id')->nullable();
            $table->decimal('conversion',10,3)->nullable();
            $table->decimal('tin_cost',10,2)->nullable();
            $table->decimal('extra',10,3)->nullable();
            $table->decimal('divisor',10,3)->nullable(); // not nullable and ZERO
            $table->decimal('muliplier',10,3)->nullable();
            $table->decimal('packing_cost',10,2)->nullable();
            $table->decimal('freight',10,2)->nullable();
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
        Schema::dropIfExists('packing_formulas');
        Schema::dropIfExists('packing_formula_dets');

    }
};
