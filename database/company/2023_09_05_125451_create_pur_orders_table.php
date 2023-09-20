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
        Schema::create('pur_orders', function (Blueprint $table) {
            $table->id();
            $table->string('vcode', 10)->nullable();
            $table->integer('branch_id')->nullable();
            $table->string('po_type',30)->nullable();
            $table->integer('doc_no');
            $table->string('doc_no_print',50)->nullable();
            $table->integer('seller_acid')->nullable();
            $table->integer('buyer_acid')->nullable();
            $table->integer('pay_term_id')->nullable();
            $table->date('del_from')->nullable();
            $table->date('del_to')->nullable();
            $table->date('del_extended_date')->nullable();
            $table->string('condition',100)->nullable();
            $table->integer('broker_acid')->nullable();
            $table->char('direct',1)->default('N');
            $table->string('del_term',10)->nullable();
            $table->string('remarks',200)->nullable();
            $table->integer('created_by')->default(0)->nullable();
            $table->integer('updated_by')->default(0)->nullable();
            $table->timestamps();
        });

        Schema::create('pur_ord_dets', function (Blueprint $table) {
            $table->id();
            $table->integer('pur_ord_id')->nullable();
            $table->integer('item_id')->nullable();
            $table->integer('unit_id')->nullable();
            $table->decimal('qty_from',12,2)->nullable();
            $table->decimal('qty_to',12,2)->nullable();
            $table->decimal('qty_extended',12,2)->nullable();
            $table->decimal('rate',12,2)->nullable();
            $table->decimal('rate_extended',12,2)->nullable();
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
        Schema::dropIfExists('pur_orders');
    }
};
