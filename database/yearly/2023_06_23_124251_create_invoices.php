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

        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vcode', 10)->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('sale_order_id')->nullable();
            $table->string('invoice_no')->nullable();  // PREFIX + SERIES NUMBER BRANCH WISE
            $table->integer('invoice_no_part')->nullable();  //ONLY SERIES NUMBER
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('invoice_type',)->nullable(); //G
            $table->integer('client_id')->nullable();
            $table->integer('bill_party_id')->nullable();
            $table->integer('ship_party_id')->nullable();
            $table->string('client_po_no')->nullable();
            $table->date('client_po_date')->nullable();
            $table->string('sap_po_no')->nullable();
            $table->date('sap_po_date')->nullable();
            $table->string('transport_type')->nullable();
            $table->integer('transport_id')->nullable();
            $table->string('vehical_no')->nullable();
            $table->string('gr_lr_no')->nullable();
            $table->date('gr_lr_date')->nullable();
            $table->string('permit_no')->nullable();
            $table->date('permit_date')->nullable();
            $table->string('excise_pass_no')->nullable();
            $table->date('excise_pass_date')->nullable();
            $table->string('l_38_no')->nullable();
            $table->date('l_38_date')->nullable();
            $table->string('delivery_terms')->nullable();
            $table->char('cash_cr', 1)->nullable();
            $table->integer('pay_term_id')->nullable();
            $table->string('supply_type', 50)->nullable();
            $table->string('d20_no')->nullable();
            $table->date('d20_date')->nullable();
            $table->string('remarks')->nullable();
            $table->string('remarks_2')->nullable();
            $table->string('benificiary_name')->default('')->nullable();
            $table->string('bank_account_number')->default('')->nullable();
            $table->integer('ifsc_id')->nullable();
            $table->decimal('bar_code_fees', 15, 2)->nullable();
            $table->decimal('freight_per_case', 15, 2)->nullable();
            $table->decimal('freight_amount', 15, 2)->nullable();
            $table->decimal('court_fee', 15, 2)->nullable();
            $table->string('reverse_charge_applicable', 1)->nullable();
            $table->decimal('add_excise_per_pl', 15, 2)->nullable();
            $table->decimal('add_excise_amount', 15, 2)->nullable();
            $table->decimal('less_excise_per_pl', 15, 2)->nullable();
            $table->decimal('less_excise_amount', 15, 2)->nullable();
            $table->decimal('freight', 15, 2)->nullable();
            $table->decimal('export_fee', 15, 2)->nullable();
            $table->decimal('gst_vat_amt', 15, 2)->nullable();
            $table->decimal('surcharge_amt', 15, 2)->nullable();
            $table->decimal('discount_amt', 15, 2)->nullable();
            $table->decimal('basic_amount', 15, 2)->nullable();
            $table->decimal('tcs_per', 15, 2)->nullable();
            $table->decimal('tcs_amount', 15, 2)->nullable();
            $table->decimal('round_off', 15, 2)->nullable();
            $table->decimal('net_amt', 15, 2)->nullable();
            $table->string('vessel_name', 15, 2)->nullable();
            $table->string('packed_loose')->nullable();
            $table->string('is_liqour')->default('N');
            $table->string('local_outside', 1)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('uid', 50)->nullable()->unique();
            $table->timestamps();
        });
        Schema::create('invoice_dets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->nullable();
            $table->integer('item_id')->nullable();
            $table->integer('unit_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('packing_id')->nullable();
            $table->string('hsn_code')->nullable();
            $table->decimal('qty',12,3)->nullable(); // disable qty if new button
            $table->decimal('weight',12,3)->nullable(); // disable qty if new button
            $table->string('rate_on',1)->default('W');
            $table->decimal('rate',15,3)->nullable(); // disable
            $table->decimal('discount',15,2)->nullable(); // disable qty if new button
            $table->decimal('freight',15,2)->nullable();// disable qty if new button
            $table->decimal('packing_cost',15,2)->nullable(); // disable qty if new button in case of packing
            $table->decimal('amt_without_gst',15,2)->nullable();
            $table->decimal('add_less',15,2)->nullable();
            $table->decimal('basic_amount',15,2)->nullable();
            $table->decimal('final_rate',15,5)->nullable();
            $table->integer('gst_id')->nullable();
            $table->integer('vat_cst_id')->nullable();
            $table->decimal('gst_vat_amount',15,2)->nullable();
            $table->decimal('surcharge_amount',15,2)->nullable();
            $table->decimal('net_amount',15,2)->nullable();
            $table->integer('acid_sale')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('bl_dets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->nullable();
            $table->string('bl_no')->nullable();
            $table->date('bl_date')->nullable();
            $table->decimal('bl_qty',15,3)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_dets');
        Schema::dropIfExists('bl_dets');

    }
};
