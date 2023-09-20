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

        Schema::create('bills', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vcode', 10);
            $table->date('trans_date');
            $table->integer('det_id')->default(0);
            $table->char('dr_cr', 1)->default('D');
            $table->string('ref', 50);
            $table->date('ref_date');
            $table->char('ref_type', 1)->default('N');   // values (N)ew,(A)gainst ref
            $table->string('ref_key', 75);
            $table->integer('ac_id');
            $table->integer('branch_id');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('part', 1000)->nullable();
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
        Schema::dropIfExists('bills');
    }
};
