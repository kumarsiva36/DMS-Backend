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
        if (!Schema::hasTable('inquiry_po_comments')) {
            Schema::create('inquiry_po_comments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('po_id')->default(0);
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->string('comment_type',10)->nullable();
                $table->text('comment_data')->nullable();
                $table->tinyInteger('is_translate')->default(0)->comment('1-Yes, 0-No');
                $table->text('translated_data')->nullable();
                $table->string('filename',255)->nullable();
                $table->string('orginalfilename',255)->nullable();
                $table->float('filesize',10,2)->default(0);
                $table->string('filepath',255)->nullable()->comment('audio file path');
                $table->string('image_width',20)->nullable();
                $table->string('image_height',20)->nullable();
                $table->text('convert_images')->nullable();
                $table->integer('user_id')->default(0);
                $table->integer('staff_id')->default(0);
                $table->tinyInteger('admin_read')->default(0);
                $table->string('staff_read',255)->default(0);
                $table->integer('created_by')->default(0);
                $table->string('created_by_type',20)->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['company_id','workspace_id','po_id'],'inquiry_po_comments_idx');
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
