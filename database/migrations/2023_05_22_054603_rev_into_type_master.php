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
        Schema::table('type_masters',function(Blueprint $table){
            if(Schema::hasColumn('type_masters','limit') == true){
                $table->decimal('limit',15,2)->after('type')->nullable()->change();
            }
        });

        Schema::table('type_masters',function(Blueprint $table){
            if(Schema::hasColumn('type_masters','type') == true){
                $table->string('type',20)->nullable()->change();
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
