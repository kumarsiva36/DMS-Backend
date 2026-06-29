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
        if (!Schema::hasTable('inquiry_po_media')) {
            Schema::create('inquiry_po_media', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('po_id')->default(0);
                $table->bigInteger('po_parent_id')->default(0);
                $table->string('temp_id',255)->nullable();
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->integer('inquiry_id')->default(0);
                $table->string('media_type',50)->nullable();
                $table->string('filename',255)->nullable();
                $table->string('orginalfilename',255)->nullable();
                $table->string('filepath',255)->nullable();
                $table->text('datas')->nullable();
                $table->float('filesize')->default(0.0);
                $table->timestamps();
                $table->index(['po_id','company_id','workspace_id','media_type'],'inquiry_po_media_main_idx');
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
