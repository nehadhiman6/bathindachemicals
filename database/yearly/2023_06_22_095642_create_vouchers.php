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

        Schema::create('vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id')->nullable();
            $table->string('vcode', 10);
            $table->string('prefix', 15);
            $table->string('voucher_no',10)->nullable();  // PREFIX + SERIES NUMBER BRANCH WISE
            $table->integer('voucher_no_part')->nullable(); //ONLY SERIES NUMBER
            $table->date('voucher_date');
            $table->string('voucher_type', 20);  //Allowed Values : (R)eceipt,(P)aymen/(J)ournal/(I)nterest,(T)ransfer
            $table->string('tr_type', 1);    //  in case vchr_type if R/P  allowed values (C)ash,(B)ank if T then (D)r/(c)r if I then (C)ompplete,(T)ds if J then not Required
            $table->integer('acid_other')->default(0);   //required in case vchr_type T/I or Vchr_type = R/P and tr_type = B
            $table->integer('acid_tds')->default(0); //required in case vchr_type is I
            $table->string('approved', 1); //  allowed values (Y)es,(N)o
            $table->integer('approved_by')->default(0)->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->string('uid', 50)->nullable()->unique();
            $table->timestamps();
            $table->index(['vcode']);
        });

        Schema::create('voucher_dets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('voucher_id');
            $table->char('drcr', 1)->default('D');
            $table->integer('sno');
            $table->integer('ac_id');
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('disc_tds_amt', 15, 2)->default(0);
            $table->string('part', 500)->nullable();
            $table->string('cheque_no', 20)->nullable();
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
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('voucher_dets');

    }
};
