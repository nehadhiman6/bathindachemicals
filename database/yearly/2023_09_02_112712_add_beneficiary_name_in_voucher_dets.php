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
        Schema::table('voucher_dets', function (Blueprint $table) {
            if(Schema::hasColumn('voucher_dets','beneficiary_name') == false){
                $table->string('beneficiary_name',100)->nullable()->after('cheque_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_dets', function (Blueprint $table) {
            //
        });
    }
};
