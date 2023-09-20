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
        Schema::table('items',function(Blueprint $table){
            if(Schema::hasColumn('items','brokerage_per_unit') == false){
                $table->decimal('brokerage_per_unit',15,2)->nullable()->after('tcs_applicable');
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
