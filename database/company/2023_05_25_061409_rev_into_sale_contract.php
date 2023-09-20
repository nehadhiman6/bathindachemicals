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
            Schema::table('sales_contracts',function(Blueprint $table){
                if(Schema::hasColumn('sales_contracts','vcode') ==false){
                    $table->string('vcode', 10)->nullable()->after('id');
                }
            });
            Schema::table('sales_contracts',function(Blueprint $table){
                if(Schema::hasColumn('sales_contracts','delivery_terms') ==false){
                    $table->string('delivery_terms', 1)->nullable()->after('gst_terms');
                }
            });
            Schema::table('sales_contracts',function(Blueprint $table){
                if(Schema::hasColumn('sales_contracts','packing_id') == false){
                    $table->integer('packing_id')->nullable()->after('delivery_terms');
                }
            });
            Schema::table('sales_contracts',function(Blueprint $table){
                if(Schema::hasColumn('sales_contracts','sap_po_no') == false){
                    $table->string('sap_po_no',25)->nullable()->after('cust_po_date');
                }
            });
            Schema::table('sales_contracts',function(Blueprint $table){
                if(Schema::hasColumn('sales_contracts','sap_po_date') == false){
                    $table->date('sap_po_date')->nullable()->after('sap_po_no');
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
