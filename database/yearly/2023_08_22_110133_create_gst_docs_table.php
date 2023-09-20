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
        Schema::create('gst_docs', function (Blueprint $table) {
            $table->id();
            $table->integer('gst_doc_id')->nullable()->unique();
            $table->string('vcode', 10); //GD +id
            $table->date('doc_date');
            $table->string('doc_type', 5);  //DN/CN/EV
            $table->string('doc_no_print',30)->nullable();
            $table->string('l_o_type', 1)->default('L');   // Local, Central
            $table->string('ac_gst_type',30);
            $table->integer('doc_no');
            $table->string('party_doc_no', 30)->nullable();
            $table->date('party_doc_date')->nullable();
            $table->integer('gst_reason_id')->nullable();
            $table->char('cash_cr', 1);  //C/R
            $table->char('pur_sale_type', 1);  //P/S
            $table->char('rev_charges', 1);  //Y/N
            $table->integer('ac_id');
            $table->string('gr_no', 30)->nullable();
            $table->date('gr_date')->nullable();
            $table->string('ref_no', 30)->nullable();
            $table->date('ref_date')->nullable();
            $table->integer('tpt_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->string('veh_no', 30)->nullable();
            $table->char('stock_entry', 1)->default('N');
            $table->decimal('add_less_other1_amount', 15, 2)->default(0);
            $table->integer('acid_add_less_other1')->nullable();
            $table->decimal('add_less_other2_amount', 15, 2)->default(0);
            $table->integer('acid_add_less_other2')->nullable();
            $table->decimal('add_oth_amt', 12, 2)->default(0);
            $table->decimal('less_oth_amt', 12, 2)->default(0);
            $table->string('remarks', 500)->nullable();
            $table->decimal('tds_per', 7, 3)->default(0);
            $table->decimal('tds_on', 15, 2)->default(0);
            $table->decimal('tds_amount', 15, 2)->default(0);
            $table->integer('tds_account')->nullable();
            $table->char('tds_type', 1)->default('B');
            $table->char('freight_type',1)->default('T');
            $table->decimal('freight_amt', 15, 3)->default(0);
            $table->string('eway_bill_no', 20)->nullable();
            $table->date('eway_bill_date')->nullable();
            $table->decimal('gst_amt', 12, 2);
            $table->decimal('round_off', 7, 2);
            $table->decimal('doc_amt', 12, 2);
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->string('uid', 50)->nullable()->unique();
            $table->timestamps();
            $table->index('vcode');
        });

        Schema::create('gst_doc_dets', function (Blueprint $table) {
            $table->id();
            $table->integer('doc_id');
            $table->integer('s_no');
            $table->integer('acid_doc');
            $table->integer('item_id');
            $table->string('item_desc', 200)->nullable();
            $table->char('rate_on', 1)->default('Q');
            $table->decimal('qty', 12, 2);
            $table->decimal('weight', 15, 3)->default(0);
            $table->decimal('length', 15, 3)->default(0);
            $table->decimal('rate', 12, 2);
            $table->decimal('disc_prec', 7, 2);
            $table->decimal('disc_amt', 12, 2);
            $table->decimal('net_disc', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->string('hsn_code', 15)->nullable();
            $table->integer('gst_id');
            $table->decimal('gst_adj_amt', 12, 2);
            $table->decimal('gst_amt', 12, 2);
            $table->decimal('add_oth_amt', 12, 2)->default(0);
            $table->decimal('less_oth_amt', 12, 2)->default(0);
            $table->decimal('net_amt', 12, 2);
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();
            $table->index('doc_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gst_docs');
        Schema::dropIfExists('gst_doc_dets');
    }
};
