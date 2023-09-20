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
        Schema::table('transfers', function (Blueprint $table) {
            if(Schema::hasColumn('transfers','rec_slip_no') == false){
                $table->string('rec_slip_no',10)->nullable()->after('net_wt');
            }

            if(Schema::hasColumn('transfers','rec_total_wt') == false){
                $table->decimal('rec_total_wt',10,2)->nullable()->after('rec_slip_no');
            }

            if(Schema::hasColumn('transfers','rec_tare_wt') == false){
                $table->decimal('rec_tare_wt',10,2)->nullable()->after('rec_total_wt');
            }

            if(Schema::hasColumn('transfers','rec_net_wt') == false){
                $table->decimal('rec_net_wt',10,2)->nullable()->after('rec_tare_wt');
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
