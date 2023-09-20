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
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_name');
            $table->string('item_code',15)->nullable();
            $table->integer('main_group_id')->nullable();
            $table->integer('sub_group_id')->nullable();
            $table->integer('other_sub_group_id')->nullable();
            $table->integer('item_unit_id')->nullable();
            $table->string('store_item',1)->nullable();
            $table->decimal('minimum_level', 12, 3)->nullable();
            $table->decimal('maximum_level', 12, 3)->nullable();
            $table->decimal('reorder_level', 12, 3)->nullable();
            $table->string('item_type',50)->nullable();
            $table->string('item_type2',50)->nullable();
            $table->integer('pur_ledger_acid')->nullable();
            $table->integer('sale_ledger_acid')->nullable();
            $table->integer('tsf_pur_ledger_acid')->nullable();
            $table->integer('tsf_sale_ledger_acid')->nullable();
            $table->integer('rebate_acid')->nullable();
            $table->string('quality_check',1)->nullable();
            $table->string('hsn_code')->nullable();
            $table->string('gst_vat',1)->nullable();
            $table->integer('gst_id')->nullable();
            $table->integer('vat_cst_id')->nullable();
            $table->decimal('vat_rate',12,3)->nullable();
            $table->string('tcs_applicable',1)->nullable();
            $table->integer('tcs_acid')->nullable();
            $table->string('ethanol_parameters',1)->nullable();
            $table->string('remarks',1000)->nullable();
            $table->string('ethenol_parameters_remarks',1000)->nullable();
            $table->string('active',1)->default('Y')->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();
        });

        Schema::create('item_branches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();
        });

        Schema::create('item_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->nullable();
            $table->integer('store_id')->nullable();
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
        Schema::dropIfExists('items');
    }
};
