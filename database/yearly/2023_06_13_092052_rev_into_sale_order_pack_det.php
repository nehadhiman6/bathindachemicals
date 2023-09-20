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
        Schema::table('sale_order_det_packs',function(Blueprint $table){
            if(Schema::hasColumn('sale_order_det_packs','amt_without_gst') == false){
                $table->decimal('amt_without_gst',15,2)->nullable()->after('final_rate');
            }
            if(Schema::hasColumn('sale_order_det_packs','net_rate') == false){
                $table->decimal('net_rate',15,2)->nullable()->after('amt_without_gst');
            }
            if(Schema::hasColumn('sale_order_det_packs','add_less') == false){
                $table->decimal('add_less',15,2)->nullable()->after('final_rate');
            }
            if(Schema::hasColumn('sale_order_det_packs','gst_vat_amount') == false){
                $table->decimal('gst_vat_amount',15,2)->nullable()->after('final_rate');
            }
            if(Schema::hasColumn('sale_order_det_packs','net_amount') == false){
                $table->decimal('net_amount',15,2)->nullable()->after('net_rate');
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
