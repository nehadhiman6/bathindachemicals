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
        Schema::create('ac_sub_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sub_group_name', 100);
            $table->integer('ac_group_id');
            $table->char('party_applicable',1)->default('N');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ac_sub_groups');
    }
};
