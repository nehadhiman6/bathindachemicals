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
        Schema::create('ac_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('group_name', 100);
            $table->string('under', 20);
            $table->string('nature', 20)->nullable();
            $table->char('affect_gp', 1);  //   Y/N
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ac_groups');
    }
};
