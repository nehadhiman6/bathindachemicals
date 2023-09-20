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

        Schema::table('sale_orders',function(Blueprint $table){
            if(Schema::hasColumn('sale_orders','branch_id') == false){
                $table->integer('branch_id')->nullable()->after('id');
            }
        });
        Schema::table('sale_orders',function(Blueprint $table){
            if(Schema::hasColumn('sale_orders','vcode') == false){
                $table->string('vcode', 10)->nullable()->after('id');
            }
        });
        Schema::table('sale_orders',function(Blueprint $table){
            if(Schema::hasColumn('sale_orders','sale_order_no') == false){
                $table->string('sale_order_no',40)->nullable()->after('branch_id');
            }
        });
        Schema::table('sale_orders',function(Blueprint $table){
            if(Schema::hasColumn('sale_orders','packed_loose') == false){
                $table->string('packed_loose',7)->nullable()->after('transport_type');
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
