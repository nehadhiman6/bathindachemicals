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
        // Schema::table('companies', function (Blueprint $table) {
        //      if(Schema::hasColumn('companies','website') == true) {
        //         $table->string('website',25)->nullable()->change();
        //     }
        //     if(Schema::hasColumn('companies','gst_number') == true) {
        //         $table->string('gst_number',16)->nullable()->change();
        //     }
        //     if(Schema::hasColumn('companies','pan_number') == true) {
        //         $table->string('pan_number',10)->nullable()->change();
        //     }
        //     if(Schema::hasColumn('companies','cin_number') == true) {
        //         $table->string('cin_number',25)->nullable()->change();
        //     }
        //     if(Schema::hasColumn('companies','remarks') == true) {
        //         $table->string('remarks',500)->nullable()->change();
        //     }
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
