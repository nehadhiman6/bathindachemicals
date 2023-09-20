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
        Schema::create('sales_contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vcode', 10)->nullable();
            $table->integer('branch_id')->nullable();
            $table->date('contract_date')->nullable();
            $table->string('contract_no',40)->nullable();
            $table->integer('ac_id')->nullable();
            $table->date('valid_from_date')->nullable();
            $table->date('valid_to_date')->nullable();
            $table->date('valid_extended_upto')->nullable();
            $table->integer('pay_term_id')->nullable();
            $table->integer('broker_id')->nullable();
            $table->decimal('brokerage_rate', 15, 2)->nullable();
            $table->string('gst_terms',1)->nullable();
            $table->string('delivery_terms',1)->nullable();
            $table->integer('packing_id')->nullable();
            $table->string('bargain_no',10)->nullable();
            $table->date('bargain_date')->nullable();
            $table->string('cust_po_no',15)->nullable();
            $table->date('cust_po_date')->nullable();
            $table->string('sap_po_no',25)->nullable();
            $table->date('sap_po_date')->nullable();
            $table->string('broker_type',10)->nullable();
            $table->string('remarks', 200)->nullable();
            $table->string('packed_loose',7)->nullable();
            $table->string('status', 1)->default('O');
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->string('uid', 50)->nullable()->unique();
            $table->timestamps();
        });


        Schema::create('sales_contract_dets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sale_contract_id');
            $table->integer('item_id')->nullable();
            $table->decimal('qty',15,3)->nullable();
            $table->integer('unit_id')->nullable();
            $table->decimal('rate',15,2)->nullable();
            $table->decimal('bargain_price_diff',15,2)->nullable();
            $table->string('packed_loose')->nullable();
            $table->string('tolerance_per')->nullable();
            $table->string('remarks', 200)->nullable();
            $table->decimal('close_qty',15,3)->nullable();
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
        Schema::dropIfExists('sales_contracts');
        Schema::dropIfExists('sales_contract_dets');
    }
};
