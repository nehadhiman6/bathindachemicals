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
        Schema::create('attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('attachment_id')->nullable()->unique();
            $table->string('file_name', 100);
            $table->string('file_ext', 10);
            $table->string('mime_type', 25);
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('resourceable_type', 100);
            $table->integer('resourceable_id');
            $table->integer('attachment_id');
            $table->string('doc_type', 25)->nullable();
            $table->string('doc_description', 500)->nullable();
            $table->string('year', 8)->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('resources');
    }
};
