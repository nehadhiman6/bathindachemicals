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
        Schema::create('uqcs', function (Blueprint $table) {
            $table->id();
            $table->string('uqc', 5);
            $table->string('uqc_name', 50);
            $table->string('uqc_disp_name', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uqc');
    }
};
