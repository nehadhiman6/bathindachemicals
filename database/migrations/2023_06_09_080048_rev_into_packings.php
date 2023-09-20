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
        Schema::table('packings',function(Blueprint $table){
            if(Schema::hasColumn('packings','rate_diff_applicable') == false){
                $table->string('rate_diff_applicable',1)->nullable()->default('N')->after('name');
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
