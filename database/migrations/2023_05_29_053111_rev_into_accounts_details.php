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
        Schema::table('account_details',function(Blueprint $table){
            if(Schema::hasColumn('account_details','print_name') == false){
                $table->string('print_name')->nullable()->after('ac_id');
            }
        });
        Schema::table('account_details',function(Blueprint $table){
            if(Schema::hasColumn('account_details','party_cat_id') == false){
                $table->integer('party_cat_id')->nullable()->after('print_name');
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
