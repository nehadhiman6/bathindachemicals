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
        Schema::create('trans_vat_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('vcode', 10)->index();
            $table->integer('vat_cst_id');
            $table->string('vat_cst', 1)->nullable(); //V OR C
            $table->decimal('vat_cst_on', 15, 2)->nullable();
            $table->decimal('vat_cst_rate', 15, 2)->nullable();
            $table->decimal('vat_cst_amt', 15, 2)->nullable();
            $table->decimal('surcharge_rate', 15, 2)->nullable();
            $table->decimal('surcharge_amt', 15, 2)->nullable();
            $table->integer('vat_cst_acid')->nullable();
            $table->integer('vat_cst_surcharge_acid')->nullable();
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
        Schema::dropIfExists('trans_vat_detail');
    }
};
