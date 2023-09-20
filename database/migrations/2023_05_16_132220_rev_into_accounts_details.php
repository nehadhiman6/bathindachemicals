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
            if(Schema::hasColumn('account_details','tds_tcs') == true){
                $table->string('tds_tcs',10)->default('NA')->nullable()->change();
            }
        });
        Schema::table('account_details',function(Blueprint $table){
            if(Schema::hasColumn('account_details','ledger_ac_id') == false){
                $table->integer('ledger_ac_id')->nullable()->after('tds_tcs');
            }
        });
        Schema::table('account_details',function(Blueprint $table){
            if(Schema::hasColumn('account_details','credit_limit') == true){
                $table->decimal('credit_limit',15,2)->nullable()->change();
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
