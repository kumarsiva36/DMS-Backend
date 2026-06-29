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
        Schema::create('order_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('company_id')->default(0);
            $table->integer('workspace_id')->default(0);
            $table->integer('order_id')->default(0);
            $table->text('comments')->nullable();
            $table->text('document_url')->nullable();
            $table->text('audio_url')->nullable();
            $table->text('video_url')->nullable();
            $table->text('reason')->nullable();
            $table->string('created_by_type',10)->nullable();
            $table->string('updated_by_type',10)->nullable();
            $table->integer('created_by_id')->default(0);
            $table->integer('updated_by_id')->default(0);
            $table->timestamps();
            $table->index(['company_id','workspace_id','order_id'],'order_comments_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_comments');
    }
};
