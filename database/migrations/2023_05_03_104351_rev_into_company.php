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
        Schema::table('companies',function(Blueprint $table){
            if(Schema::hasColumn('companies','year') == false){
                $table->string('year',8)->after('remarks')->nullable();
            }
        });
        Schema::table('companies',function(Blueprint $table){
            if(Schema::hasColumn('companies','prv_yr_comp_id') == false){
                $table->integer('prv_yr_comp_id')->default(0)->after('year');
            }
        });
        Schema::table('companies',function(Blueprint $table){
            if(Schema::hasColumn('companies','next_yr_comp_id') == false){
                $table->integer('next_yr_comp_id')->default(0)->after('prv_yr_comp_id');
            }
        });
        Schema::table('companies',function(Blueprint $table){
            if(Schema::hasColumn('companies','shared_comp_id') == false){
                $table->integer('shared_comp_id')->default(0)->after('next_yr_comp_id');
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
