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
            if(Schema::hasColumn('invoices','pay_term_id') == false){
                $table->integer('pay_term_id')->nullable()->after('cash_cr');
            }
        });
        Schema::table('invoices',function(Blueprint $table){
            if(Schema::hasColumn('invoices','due_date') == false){
                $table->date('due_date')->nullable()->after('invoice_date');
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
