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
        Schema::create('fix_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->date('wef_date')->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->string('uid', 50)->nullable()->unique();
            $table->timestamps();
        });


        Schema::create('fix_rate_dets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fix_rate_id')->nullable();
            $table->integer('item_id')->nullable();
            $table->integer('packing_id')->nullable();
            $table->decimal('rate',15,2)->nullable();
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
        Schema::dropIfExists('fix_rates');
        Schema::dropIfExists('fix_rate_dets');
    }
};
