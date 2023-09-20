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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('vcode', 10)->nullable();
            $table->integer('branch_id_from')->nullable();
            $table->integer('branch_id_to')->nullable();
            $table->date('iss_date')->nullable();
            $table->integer('iss_doc_no')->nullable();
            $table->string('iss_doc_no_print',30)->nullable();
            $table->date('rec_date')->nullable();
            $table->integer('rec_doc_no')->nullable();
            $table->string('rec_doc_no_print',30)->nullable();
            $table->char('status',1); // R/I
            $table->integer('acid_tpt')->nullable();
            $table->string('vehical_no',30)->nullable();
            $table->string('gr_no', 30)->nullable();
            $table->date('gr_date')->nullable();
            $table->string('slip_no', 10)->nullable();
            $table->decimal('total_wt', 10, 2)->default(0);
            $table->decimal('tare_wt', 10, 2)->default(0);
            $table->decimal('net_wt', 10, 2)->default(0);
            $table->string('remarks', 200)->nullable();
            $table->integer('received_by')->nullable();
            $table->string('uid', 50)->nullable()->unique();
            $table->integer('created_by')->default(0)->nullable();
            $table->integer('updated_by')->default(0)->nullable();
            $table->timestamps();
        });

        Schema::create('transfer_dets', function (Blueprint $table) {
            $table->id();
            $table->integer('transfer_id');
            $table->integer('item_id');
            $table->integer('unit_id')->nullable();
            $table->integer('packing_id')->nullable();
            $table->decimal('qty',12,3)->nullable();
            $table->decimal('weight',12,3)->nullable();
            $table->decimal('rate',12,3)->nullable();
            $table->string('rate_on',1)->default('W');
            $table->decimal('rec_qty',12,3)->nullable();
            $table->decimal('rec_weight',12,3)->nullable();
            $table->decimal('amount',12,3)->nullable();
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
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('transfer_dets');
    }
};
