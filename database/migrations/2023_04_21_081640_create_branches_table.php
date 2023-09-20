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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('print_name')->default(0)->nullable();
            $table->string('address_1',300)->default(0)->nullable();
            $table->string('address_2',300)->default(0)->nullable();
            $table->string('address_3',300)->default(0)->nullable();
            $table->integer('city_id')->default(0)->nullable();
            $table->integer('company_id')->default(0)->nullable();
            $table->string('pincode',15)->default('')->nullable();
            $table->string('phone',20)->default()->nullable();
            $table->string('fax',25)->default('')->nullable();
            $table->string('email')->default('')->nullable();
            $table->string('tan')->default('')->nullable();
            $table->string('tin')->default('')->nullable();
            $table->string('vat')->default('')->nullable();
            $table->string('fssai')->default('')->nullable();
            $table->string('account_id')->default(0)->nullable();
            $table->string('active',1)->default('')->nullable();
            $table->string('remarks')->default('')->nullable();
            $table->string('benificiary_name')->default('')->nullable();
            $table->string('bank_account_number')->default(0)->nullable();
            $table->integer('ifsc_id')->nullable();
            $table->integer('created_by')->default(0)->nullable();
            $table->integer('updated_by')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
