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
            if(Schema::hasColumn('sale_order_det_packs','final_rate') == false){
                $table->decimal('final_rate',10,2)->nullable()->after('discount');
            }
        });

        Schema::table('sale_order_det_packs',function(Blueprint $table){
            if(Schema::hasColumn('sale_order_det_packs','brand_id') == false){
                $table->integer('brand_id')->nullable()->after('final_rate');
            }
        });
        Schema::table('sale_order_det_packs', function (Blueprint $table) {
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
        Schema::table('sale_order_dets', function (Blueprint $table) {
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
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
