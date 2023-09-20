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
        Schema::create('cheque_setting', function (Blueprint $table) {
            $table->id();
            $table->integer('acid')->default(0);
            $table->decimal('width',5,2)->nullable();
            $table->decimal('height',5,2)->nullable();
            $table->decimal('date_top',5,2)->nullable();
            $table->decimal('date_left',5,2)->nullable();
            $table->decimal('name_top',5,2)->nullable();
            $table->decimal('name_left',5,2)->nullable();
            $table->integer('name_char')->nullable();
            $table->decimal('amt_w1_top',5,2)->nullable();
            $table->decimal('amt_w1_left',5,2)->nullable();
            $table->integer('amt_w1_char')->nullable();
            $table->decimal('amt_w2_top',5,2)->nullable();
            $table->decimal('amt_w2_left',5,2)->nullable();
            $table->integer('amt_w2_char')->nullable();
            $table->decimal('amt_f_top',5,2)->nullable();
            $table->decimal('amt_f_left',5,2)->nullable();
            $table->decimal('for_top',5,2)->nullable();
            $table->decimal('for_left',5,2)->nullable();
            $table->integer('for_char')->nullable();
            $table->char('for_flag',1)->nullable();
            $table->decimal('sign1_top',5,2)->nullable();
            $table->decimal('sign1_left',5,2)->nullable();
            $table->char('sign1_flag',1)->nullable();
            $table->string('sign1',30)->nullable();
            $table->decimal('sign2_top',5,2)->nullable();
            $table->decimal('sign2_left',5,2)->nullable();
            $table->char('sign2_flag',1)->nullable();
            $table->string('sign2',30)->nullable();
            $table->decimal('sign3_top',5,2)->nullable();
            $table->decimal('sign3_left',5,2)->nullable();
            $table->char('sign3_flag',1)->default('N');
            $table->string('sign3',30)->nullable();
            $table->char('print_mode',1)->default('P');
            $table->integer('date_font')->default(10);
            $table->integer('name_font')->default(10);
            $table->integer('amt_w1_font')->default(10);
            $table->integer('amt_w2_font')->default(10);
            $table->integer('amt_f_font')->default(10);
            $table->char('tds_flag',1)->default('N');
            $table->decimal('tds_top',5,2)->default(0);
            $table->decimal('tds_left',5,2)->default(0);
            $table->integer('tds_char')->default(0);
            $table->integer('tds_font')->default(10);
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
        Schema::dropIfExists('cheque_setting');
    }
};
