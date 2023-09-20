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
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vcode', 10)->nullable();
            $table->integer('branch_id')->nullable();
            $table->string('sale_order_no',40)->nullable();
            $table->date('sale_order_date')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('bill_party_id')->nullable();
            $table->integer('ship_party_id')->nullable();
            // $table->integer('delivery_term_id')->nullable(); // show only
            $table->integer('transport_id')->nullable(); //acid
            $table->string('vehical_no',15)->nullable();
            $table->string('transport_type',15)->nullable(); //helper
            $table->string('packed_loose',7)->nullable();
            $table->decimal('discount_amt',15,2)->nullable(); //lumpsum discount
            $table->decimal('freight_amt',15,2)->nullable();  //lumpsum
            $table->decimal('export_fee',15,2)->nullable();  // lumpsum
            $table->decimal('basic_amount',15,2)->nullable(); //total basic value + det basic
            $table->decimal('gst_vat_amt',15,2)->nullable();
            $table->decimal('tcs_per',15,2)->nullable();
            $table->decimal('tcs_amt',15,2)->nullable(); // only if tcs is applicable in party % on total+gst_vat+amt
            $table->decimal('net_amt',15,2)->nullable(); //toal+tcs
            $table->string('dispatch_advice',1)->nullable();
            $table->integer('created_by')->default(0)->nullable();
            $table->integer('updated_by')->default(0)->nullable();
            $table->string('uid', 50)->nullable()->unique();
            $table->timestamps();
        });

        // diff url for items so that only clients sale contract items shown
        Schema::create('sale_order_dets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sale_order_id')->nullable();
            $table->integer('item_id')->nullable();
            $table->integer('packing_id')->nullable();
            $table->integer('sale_contract_id')->nullable();
            $table->decimal('qty',12,3)->nullable(); // disable qty if new button
            $table->decimal('weight',12,3)->nullable(); // disable qty if new button
            $table->string('rate_on',1)->default('W');
            $table->decimal('rate',15,3)->nullable(); // disable
            $table->decimal('discount',15,2)->nullable(); // disable qty if new button
            $table->decimal('freight',15,2)->nullable();// disable qty if new button
            $table->decimal('packing_cost',15,2)->nullable(); // disable qty if new button in case of packing
            $table->decimal('amt_without_gst',15,2)->nullable();
            $table->decimal('basic_amount',15,2)->nullable();
            $table->decimal('final_rate',15,5)->nullable();
            $table->integer('gst_vat_id')->nullable();
            $table->decimal('gst_vat_rate',15,2)->nullable();  /// no data in trans gst
            $table->decimal('gst_vat_amount',15,2)->nullable();
            $table->decimal('net_amount',15,2)->nullable();
            $table->integer('foc_item_id')->nullable();
            $table->integer('foc_packing_id')->nullable();
            $table->integer('foc_brand_id')->nullable();
            $table->decimal('foc_qty',10,2)->nullable();
            $table->decimal('foc_weight',12,3)->nullable();
            $table->decimal('add_less',15,3)->nullable();
            $table->integer('created_by')->default(0)->nullable();
            $table->integer('updated_by')->default(0)->nullable();
            $table->timestamps();
        });

        Schema::create('sale_order_det_packs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sale_order_det_id')->nullable();
            $table->integer('packing_id')->nullable();
            $table->decimal('qty',10,3)->nullable();
            $table->decimal('weight',10,3)->nullable(); //disabled pack fomula weight * qty
            $table->string('rate_on',1)->default('W');
            $table->decimal('discount',15,2)->nullable();
            $table->decimal('final_rate',10,2)->nullable();
            $table->decimal('gst_vat_amount',15,2)->nullable();
            $table->decimal('add_less',15,2)->nullable();
            $table->decimal('amt_without_gst',15,2)->nullable();
            $table->decimal('net_rate',15,2)->nullable();
            $table->decimal('net_amount',15,2)->nullable();
            $table->integer('brand_id')->nullable();
            //Show final rate
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
        Schema::dropIfExists('sale_orders');
        Schema::dropIfExists('sale_order_dets');
        Schema::dropIfExists('sale_order_det_packs');
    }
};
