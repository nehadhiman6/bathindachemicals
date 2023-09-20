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
        Schema::table('item_groups',function(Blueprint $table){
            if(Schema::hasColumn('item_groups','oil') == false){
                $table->string('oil',1)->default('N')->nullable()->after('s_group_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rev_into_item_sub_groups');
    }
};
