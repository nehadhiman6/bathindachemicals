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
        Schema::create('lower_tds_setting', function (Blueprint $table) {
            $table->id();
            $table->integer('ac_id')->nullable();
            $table->integer('tds_sec_id')->nullable();
            $table->string('certificate_no')->nullable();
            $table->date('date')->nullable();
            $table->decimal('rate',7,2)->nullable();
            $table->integer('acid_tds')->default(0);
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->decimal('amount',7,2)->nullable();
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
        Schema::dropIfExists('lower_tds_setting');
    }
};
