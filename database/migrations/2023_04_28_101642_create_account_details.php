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
        Schema::create('account_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ac_id')->nullable()->unique();
            $table->string('ac_code',20)->nullable();
            $table->string('add1',1000)->nullable();
            $table->string('add2',1000)->nullable();
            $table->string('add3',1000)->nullable();
            $table->integer('city_id')->nullable();
            $table->string('pincode')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('email')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('party_gst_status')->nullable();
            $table->string('gst_no')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_per_phone')->nullable();
            $table->string('trade_name')->nullable();
            $table->integer('payment_term_id')->nullable();
            $table->decimal('credit_limit',15,2)->nullable();
            $table->string('tds_tcs', 10)->default('NA')->nullable();
            $table->integer('ledger_ac_id')->default(0)->nullable();
            $table->integer('client_id')->default(0)->nullable();
            $table->integer('vendor_id')->default(0)->nullable();
            $table->string('vat_no')->nullable();
            $table->string('cst_no')->nullable();
            $table->integer('ifsc_id')->nullable();
            $table->string('account_no')->nullable();
            $table->string('beneficiary_name')->nullable();
            $table->string('msme_type')->nullable();
            $table->string('e_invoice_applicable',1)->nullable();
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
        Schema::dropIfExists('account_details');
    }
};
