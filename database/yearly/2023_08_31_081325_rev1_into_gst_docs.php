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
        Schema::table('gst_docs', function (Blueprint $table) {
            if(Schema::hasColumn('gst_docs','bill_no') == false){
                $table->string('bill_no',25)->nullable()->after('doc_no');
            }

            if(Schema::hasColumn('gst_docs','fyear') == false){
                $table->string('fyear',8)->nullable()->after('bill_no');
            }
        });

        Schema::table('gst_doc_dets', function (Blueprint $table) {
            if(Schema::hasColumn('gst_doc_dets','packing_id') == false){
                $table->integer('packing_id')->nullable()->after('item_desc');
            }

            if(Schema::hasColumn('gst_doc_dets','brand_id') == false){
                $table->integer('brand_id')->nullable()->after('packing_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
