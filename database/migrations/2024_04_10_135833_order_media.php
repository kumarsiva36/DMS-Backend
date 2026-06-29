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
        if (!Schema::hasTable('order_media')) {
            Schema::create('order_media', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('order_id')->default(0);
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->string('media_type',50)->nullable();
                $table->string('filename',255)->nullable();
                $table->string('orginalfilename',255)->nullable();
                $table->string('filepath',255)->nullable();
                $table->float('filesize')->default(0.0);
                $table->timestamps();
                $table->index(['order_id','company_id','workspace_id','media_type']);
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
