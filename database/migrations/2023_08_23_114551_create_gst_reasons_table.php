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
        Schema::create('gst_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('reason_code', 3);
            $table->string('reason_name', 50);
            $table->string('reason_disp_name', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gst_reasons');
    }
};
