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
        if (!Schema::hasTable('fabric_master')) {
            Schema::create('fabric_master', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('type',255)->nullable();
                $table->string('content',255)->nullable();
                $table->string('reference_id',255)->nullable();
                $table->timestamps();
                $table->index(['type','reference_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
