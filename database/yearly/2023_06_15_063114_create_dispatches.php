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
        Schema::table('sale_order_dispatches',function(Blueprint $table){
            if(Schema::hasColumn('sale_order_dispatches','dispatch_id') == false){
                $table->integer('dispatch_id')->nullable()->after('id');
            }
        });
        Schema::create('dispatches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vcode',15)->nullable();
            $table->integer('branch_id')->nullable();
            $table->date('dispatch_date')->nullable();
            $table->string('dispatch_advise_no',40)->nullable();
            $table->string('dispatch_advise_no_part',40)->nullable();
            $table->string('dispatch_advise',1)->default('N')->nullable();
            $table->text('sale_order_ids')->nullable();
            $table->integer('created_by')->default(0)->nullable();
            $table->integer('updated_by')->default(0)->nullable();
            $table->string('uid', 50)->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
