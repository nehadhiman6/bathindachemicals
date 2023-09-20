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
            if(Schema::hasColumn('sale_order_dets','foc_weight') == false){
                $table->decimal('foc_weight',12,3)->nullable()->after('foc_qty');
            }
        });

        Schema::table('sale_orders',function(Blueprint $table){
            if(Schema::hasColumn('sale_orders','dispatch_advice') == false){
                $table->string('dispatch_advice',1)->nullable()->after('net_amt');
            }
        });
        Schema::table('sale_order_dets',function(Blueprint $table){
            if(Schema::hasColumn('sale_order_dets','qty') == true){
                $table->decimal('qty',12,3)->nullable()->change();
            }
        });
        Schema::table('sale_order_dets',function(Blueprint $table){
            if(Schema::hasColumn('sale_order_dets','weight') == true){
                $table->decimal('weight',12,3)->nullable()->change();
            }
        });
        Schema::table('sale_order_dets',function(Blueprint $table){
            if(Schema::hasColumn('sale_order_dets','rate') == true){
                $table->decimal('rate',15,3)->nullable()->change();
            }
        });
        Schema::table('sale_order_dets',function(Blueprint $table){
            if(Schema::hasColumn('sale_order_dets','add_less') == false){
                $table->decimal('add_less',15,3)->nullable()->after('foc_weight');
            }
        });

        //pack

        Schema::table('sale_order_det_packs',function(Blueprint $table){
            if(Schema::hasColumn('sale_order_det_packs','qty') == true){
                $table->decimal('qty',12,3)->nullable()->change();
            }
        });
        Schema::table('sale_order_det_packs',function(Blueprint $table){
            if(Schema::hasColumn('sale_order_det_packs','weight') == true){
                $table->decimal('weight',12,3)->nullable()->change();
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
