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
        Schema::table('item_unit_conversions', function (Blueprint $table) {
            if(Schema::hasColumn('item_unit_conversions','multiplier') == false) {
                $table->decimal('multiplier',15,2)->nullable()->default(0)->after('item_unit_id');
            }

            if(Schema::hasColumn('item_unit_conversions','divider') == false) {
                $table->decimal('divider',15,2)->nullable()->default(0)->after('multiplier');
            }
            if(Schema::hasColumn('item_unit_conversions','conversion_factor') == true) {
                $table->decimal('conversion_factor',15,2)->nullable()->default(0)->change();
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
