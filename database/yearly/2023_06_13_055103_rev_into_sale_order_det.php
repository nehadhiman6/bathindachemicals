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
            if(Schema::hasColumn('sale_order_dets','amt_without_gst') == false){
                $table->decimal('amt_without_gst',15,2)->nullable()->after('packing_cost');
            }
        });

        Schema::table('sale_order_dets',function(Blueprint $table){
            if(Schema::hasColumn('sale_order_dets','final_rate') == false){
                $table->decimal('final_rate',15,2)->nullable()->after('basic_amount');
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
