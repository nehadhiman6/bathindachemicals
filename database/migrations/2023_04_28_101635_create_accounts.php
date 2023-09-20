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
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
           // $table->integer('ac_group_id')->nullable();// get from sub_ac_group_
            $table->integer('ac_sub_group_id')->nullable();
            $table->integer('sub_group_id1')->nullable();
            $table->integer('sub_group_id2')->nullable();
            $table->string('bill_wise',1)->default('N')->nullable();
            $table->string('active',1)->default('Y')->nullable();
            $table->string('remarks',400)->nullable();
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
        Schema::dropIfExists('accounts');
    }
};
