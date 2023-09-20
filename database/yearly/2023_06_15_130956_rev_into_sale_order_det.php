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
         Schema::table('sale_order_dets',function(Blueprint $table){
            if(Schema::hasColumn('sale_order_dets','foc_brand_id') == false){
                $table->integer('foc_brand_id')->nullable()->after('foc_packing_id');
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
