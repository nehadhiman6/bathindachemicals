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
            Schema::table('transfers', function (Blueprint $table) {
                if(Schema::hasColumn('transfers','amount') == false){
                    $table->decimal('amount',15,2)->after('net_wt');
                }
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
