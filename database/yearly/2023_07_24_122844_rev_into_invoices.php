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
        Schema::table('invoices',function(Blueprint $table){
            if(Schema::hasColumn('invoices','ethanol_bill') == false){
                $table->string('ethanol_bill', 1)->default('N')->nullable()->after('local_outside');
            }
        });
        Schema::table('invoices',function(Blueprint $table){
            if(Schema::hasColumn('invoices','ethanol_category') == false){
                $table->string('ethanol_category', 50)->nullable()->after('ethanol_bill');
            }
        });
        Schema::table('invoices',function(Blueprint $table){
            if(Schema::hasColumn('invoices','excise_certificate') == false){
                $table->string('excise_certificate', 50)->nullable()->after('ethanol_Category');
            }
        });
        Schema::table('invoices',function(Blueprint $table){
            if(Schema::hasColumn('invoices','denaturant') == false){
                $table->string('denaturant', 50)->nullable()->after('excise_certificate');
            }
        });
        Schema::table('invoices',function(Blueprint $table){
            if(Schema::hasColumn('invoices','crotonaldehde') == false){
                $table->string('crotonaldehde',50)->nullable()->after('denaturant');
            }
        });
        Schema::table('invoices',function(Blueprint $table){
            if(Schema::hasColumn('invoices','denatonium_saccaride') == false){
                $table->string('denatonium_saccaride',50)->nullable()->after('crotonaldehde');
            }
        });
        Schema::table('invoices',function(Blueprint $table){
            if(Schema::hasColumn('invoices','denatonium_benzoate') == false){
                $table->string('denatonium_benzoate',50)->nullable()->after('denatonium_saccaride');
            }
        });
        Schema::table('invoices',function(Blueprint $table){
            if(Schema::hasColumn('invoices','tanker_seal_no') == false){
                $table->string('tanker_seal_no',50)->nullable()->after('denatonium_benzoate');
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
