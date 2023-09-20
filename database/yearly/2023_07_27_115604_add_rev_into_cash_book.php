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
        Schema::table('cash_book',function(Blueprint $table){
            if(Schema::hasColumn('cash_book','due_date') == false){
                $table->date('due_date')->nullable()->after('trans_date');
            }
        });
        Schema::table('cash_book',function(Blueprint $table){
            if(Schema::hasColumn('cash_book','weight') == false){
                $table->decimal('weight',12,3)->nullable()->after('amount');
            }
        });
        Schema::table('cash_book',function(Blueprint $table){
            if(Schema::hasColumn('cash_book','vchr_no') == false){
                $table->string('vchr_no',25)->nullable()->after('vcode');
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
