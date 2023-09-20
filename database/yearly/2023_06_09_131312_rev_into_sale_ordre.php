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
            if(Schema::hasColumn('sale_orders','own_vehicle') == false){
                $table->string('own_vehicle',1)->default('N')->nullable()->after('net_amt');
            }
        });

        Schema::table('sale_orders',function(Blueprint $table){
            if(Schema::hasColumn('sale_orders','freight') == false){
                $table->decimal('freight',15,2)->nullable()->after('own_vehicle');
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
