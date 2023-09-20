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
        Schema::create('account_openings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ac_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->string('dr_cr',1)->nullable();
            $table->decimal('opening_amount',15, 3)->default(0);
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();
        });

        Schema::create('cash_book', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vcode', 10);
            $table->integer('s_no')->default(0);
            $table->string('vchr_no', 25)->nullable();
            $table->date('trans_date');
            $table->due_date('trans_date')->nullable();
            $table->char('dr_cr', 1)->default('D');
            $table->char('cash_cr', 1)->default('C');  //  T in case of oprening
            $table->integer('ac_id');
            $table->integer('branch_id');
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('weight', 12, 3)->default(0);
            $table->string('part', 500);
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
        Schema::dropIfExists('account_openings');
        Schema::dropIfExists('cash_book');
    }
};
