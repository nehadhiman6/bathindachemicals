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
        Schema::create('vat_cst', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->decimal('vat_rate',15,2)->nullable();
            $table->decimal('cst_rate',15,2)->nullable();
            $table->decimal('sur_on_vat',15,2)->nullable();
            $table->decimal('sur_on_cst',15,2)->nullable();
            $table->integer('vat_ac_id')->nullable();
            $table->integer('cst_ac_id')->nullable();
            $table->integer('sur_vat_ac_id')->nullable();
            $table->integer('sur_cst_ac_id')->nullable();
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
        Schema::dropIfExists('vat_cst');
    }
};
