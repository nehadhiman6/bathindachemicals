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
        Schema::table('rate_differences',function(Blueprint $table){
            if(Schema::hasColumn('rate_differences','date_applicable_on') == false){
                $table->string('date_applicable_on',1)->nullable()->after('wef_date');
            }
        });
        Schema::table('packing_formula_dets',function(Blueprint $table){
            if(Schema::hasColumn('packing_formula_dets','weight') == false){
                $table->decimal('weight',10,2)->nullable()->after('packing_id');
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
