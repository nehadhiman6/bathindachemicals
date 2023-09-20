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
        Schema::create('trans_gst_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id')->nullable();
            $table->string('vcode', 10);
            $table->integer('det_id');
            $table->integer('gst_det_id');
            $table->string('gst_name', 15);
            $table->decimal('gst_rate', 7, 3);
            $table->decimal('gst_on', 12, 2);
            $table->decimal('gst_value', 12, 2);
            $table->integer('acid_gst');
            $table->integer('acid_pur_sale')->default(0);
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
        Schema::dropIfExists('trans_gst');
    }
};
