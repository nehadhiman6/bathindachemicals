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

        Schema::table('packing_formula_dets',function(Blueprint $table){
            if(Schema::hasColumn('packing_formula_dets','weight') == true){
                $table->decimal('weight',10,3)->nullable()->change();
            }
        });
        Schema::table('packing_formula_dets',function(Blueprint $table){
            if(Schema::hasColumn('packing_formula_dets','muliplier') == true){
                $table->decimal('muliplier',10,3)->nullable()->change();
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
