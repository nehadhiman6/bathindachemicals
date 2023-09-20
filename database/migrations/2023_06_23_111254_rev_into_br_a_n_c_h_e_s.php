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
        Schema::table('branches',function(Blueprint $table){
            if(Schema::hasColumn('branches','type') == false){
                $table->string('type',1)->default('N')->nullable()->after('name'); //N for NoRMAL AND D FOR distillery
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
