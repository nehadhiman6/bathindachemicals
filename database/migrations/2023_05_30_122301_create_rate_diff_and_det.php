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


        Schema::create('rate_differences', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->nullable();
            $table->integer('party_cat_id')->nullable();
            $table->date('wef_date')->nullable();
            $table->string('date_applicable_on',1)->default('C')->nullable();
            $table->integer('created_by')->default(0)->nullable();
            $table->integer('updated_by')->default(0)->nullable();
            $table->string('uid', 50)->nullable()->unique();
            $table->timestamps();
            $table->unique(['item_id', 'party_cat_id','wef_date']);
        });
        Schema::create('rate_diff_dets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rate_diff_id');
            $table->decimal('rate_from',15,2)->nullable();
            $table->decimal('rate_to',15,2)->nullable();
            $table->decimal('rate_diff',15,2)->nullable();
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
        Schema::dropIfExists('rate_differences');
        Schema::dropIfExists('rate_diff_dets');
    }
};
