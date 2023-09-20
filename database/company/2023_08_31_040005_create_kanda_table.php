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
        Schema::create('kanda', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('api_key',100)->nullable();
            $table->string('criteria',10)->nullable();
            $table->string('prefix',10)->unique();
            $table->string('tkt_prefix',10)->nullable();
            $table->string('vsiteid',10)->nullable();
            $table->string('url',100)->nullable();
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
        Schema::dropIfExists('kanda');
    }
};
