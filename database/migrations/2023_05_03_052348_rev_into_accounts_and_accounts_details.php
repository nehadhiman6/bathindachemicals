<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use  Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('accounts',function(Blueprint $table){
            if(Schema::hasColumn('accounts','ac_code') == false){
                $table->string('ac_code')->nullable();
            }
        });

        Schema::table('account_details',function(Blueprint $table){
            if(Schema::hasColumn('account_details','ac_code') == true){
                $table->dropColumn('ac_code');
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
