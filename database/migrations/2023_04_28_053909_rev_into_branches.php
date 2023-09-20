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
        Schema::table('branches',function(Blueprint $table){
            if(Schema::hasColumn('branches','ifsc_id') == false){
                $table->integer('ifsc_id')->after('ifsc_code');
            }
        });

        Schema::table('branches',function(Blueprint $table){
            if(Schema::hasColumn('branches','ifsc_code') == true){
                $table->dropColumn('ifsc_code');
            }
        });

        // Schema::rename('i_f_s_c_s','ifscs');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
