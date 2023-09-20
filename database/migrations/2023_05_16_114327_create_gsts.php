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
        Schema::create('gst', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->date('wef_date');
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();
        });
        Schema::create('gst_types', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gst_id')->nullable();
            $table->date('wef_date')->nullable();
            $table->string('gst_type', 25)->nullable();
            $table->integer('created_by')->default(0)->nullable();
            $table->integer('updated_by')->default(0)->nullable();
            $table->timestamps();
        });
        Schema::create('gst_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gst_id')->nullable();
            $table->integer('gst_type_id')->nullable();
            $table->string('name', 15)->nullable();
            $table->decimal('rate', 7, 3)->nullable();
            $table->string('rate_on', 10)->default('Amount')->nullable();
            $table->integer('acid_input')->nullable();
            $table->integer('acid_output')->nullable();
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
        Schema::dropIfExists('gsts');
    }
};
