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

        Schema::create('parameters', function (Blueprint $table) {
            $table->id();
            $table->string('para_name', 50);
            $table->decimal('s_no', 7, 2)->default(0);
            $table->string('para_disp_name', 200);
            $table->string('para_type', 10);  //(N)umeric,(D)ate,(S)tring
            $table->string('para_nature', 20); //Normal, A/c Help,Item Help
            $table->string('para_choice', 100)->nullable();
            $table->integer('para_length');
            $table->integer('para_decimals');
            $table->string('mandatory')->default('Yes');
            $table->string('para_value', 250)->default('');
            $table->string('input_required', 1)->default('Y');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameters');
    }
};
