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
        Schema::table('vouchers',function(Blueprint $table){
            if(Schema::hasColumn('vouchers','prefix') == false){
                $table->string('prefix',15)->nullable()->after('vcode');
            }
        });
        Schema::table('vouchers',function(Blueprint $table){
            if(Schema::hasColumn('vouchers','voucher_no') == true){
                $table->string('voucher_no',10)->change();
            }
        });
        Schema::table('vouchers',function(Blueprint $table){
            if(Schema::hasColumn('vouchers','voucher_type') == true){
                $table->char('voucher_type',1)->change();
            }
        });
        Schema::table('vouchers',function(Blueprint $table){
            if(Schema::hasColumn('vouchers','voucher_no_part') == true){
                $table->integer('voucher_no_part')->change();
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
