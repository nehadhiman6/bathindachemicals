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
        Schema::table('ac_sub_groups', function (Blueprint $table) {
            if (Schema::hasColumn('ac_sub_groups', 'party_applicable') == false) {
                $table->char('party_applicable',1)->default('N')->after('ac_group_id');
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
