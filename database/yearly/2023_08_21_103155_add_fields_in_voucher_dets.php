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
            if(Schema::hasColumn('voucher_dets','weight') == false){
                $table->decimal('weight',7,2)->nullable()->after('cheque_no');
            }

            if(Schema::hasColumn('voucher_dets','section_id') == false){
                $table->integer('section_id')->nullable()->after('weight');
            }

            if(Schema::hasColumn('voucher_dets','rate') == false){
                $table->decimal('rate',7,2)->nullable()->after('section_id');
            }

            if(Schema::hasColumn('voucher_dets','tds_on') == false){
                $table->decimal('tds_on',12,2)->nullable()->after('rate');
            }

            if(Schema::hasColumn('voucher_dets','tds_adj') == false){
                $table->decimal('tds_adj',7,2)->nullable()->after('tds_on');
            }

            if(Schema::hasColumn('voucher_dets','tds_amt') == false){
                $table->decimal('tds_amt',12,2)->nullable()->after('tds_adj');
            }

            if(Schema::hasColumn('voucher_dets','acid_tds') == false){
                $table->integer('acid_tds')->nullable()->after('tds_amt');
            }

            if(Schema::hasColumn('voucher_dets','tds_part') == false){
                $table->string('tds_part',100)->nullable()->after('acid_tds');
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
