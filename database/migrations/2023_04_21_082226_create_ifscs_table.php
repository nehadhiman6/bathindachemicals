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
        Schema::create('ifscs', function (Blueprint $table) {
            $table->id();
            $table->integer('bank_id')->default(0)->nullable();
            $table->string('ifsc_code',11)->nullable();
            $table->string('branch',100)->nullable();
            $table->string('micr_code',9)->nullable();
            $table->string('bsr_code',7)->nullable();
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
        Schema::dropIfExists('ifscs');
    }
};
