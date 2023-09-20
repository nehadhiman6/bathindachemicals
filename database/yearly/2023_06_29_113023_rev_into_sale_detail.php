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
        Schema::table('invoice_dets',function(Blueprint $table){
            if(Schema::hasColumn('invoice_dets','add_less') == false){
                $table->decimal('add_less',15,2)->nullable()->after('amt_without_gst');
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
