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
        Schema::create('pay_terms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('days_1')->nullable();
            $table->string('days_2')->nullable();
            $table->string('days_3')->nullable();
            $table->string('days_4')->nullable();
            $table->string('percentage_1')->nullable();
            $table->string('percentage_2')->nullable();
            $table->string('percentage_3')->nullable();
            $table->string('percentage_4')->nullable();
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
        Schema::dropIfExists('pay_terms');
    }
};
