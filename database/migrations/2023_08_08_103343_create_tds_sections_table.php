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
        Schema::create('tds_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section',10)->unique();
            $table->decimal('rate1',7,2)->nullable();
            $table->integer('ac_id1')->nullable();
            $table->decimal('rate2',7,2)->nullable();
            $table->integer('ac_id2')->nullable();
            $table->decimal('rate3',7,2)->nullable();
            $table->integer('ac_id3')->nullable();
            $table->decimal('non_pan_rate',7,2)->nullable();
            $table->integer('ac_id4')->nullable();
            $table->decimal('higher_rate',7,2)->nullable();
            $table->integer('ac_id5')->nullable();
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
        Schema::dropIfExists('tds_sections');
    }
};
