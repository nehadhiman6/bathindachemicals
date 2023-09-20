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
        Schema::table('invoices',function(Blueprint $table){
            if(Schema::hasColumn('invoices','is_liqour') == false){
                $table->string('is_liqour')->default('N')->after('vessel_name');
            }
        });
        Schema::table('invoice_dets',function(Blueprint $table){
            if(Schema::hasColumn('invoice_dets','acid_sale') == false){
                $table->integer('acid_sale')->nullable()->after('net_amount');
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
