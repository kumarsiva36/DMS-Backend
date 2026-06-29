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
        if (!Schema::hasTable('inquiry_master')) {
            Schema::create('inquiry_master', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->default(0);
                $table->bigInteger('workspace_id')->default(0);
                $table->string('type',255)->nullable();
                $table->text('content')->nullable();
                $table->string('inq_reference_id',155)->nullable();
                $table->timestamps();
                $table->index(['company_id','workspace_id','inq_reference_id']);
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
