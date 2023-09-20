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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name',150)->unique();
            $table->string('print_name')->default(0)->nullable();
            $table->string('office_address',500)->default(0)->nullable();
            $table->string('website',25)->default(0)->nullable();
            $table->string('gst_number',16)->default(0)->nullable();
            $table->string('pan_number',10)->default(0)->nullable();
            $table->string('cin_number',25)->default(0)->nullable();
            $table->string('remarks',1000)->default(0)->nullable();
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
        Schema::dropIfExists('companies');
    }
};
