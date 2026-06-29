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
        if (!Schema::hasTable('order_task_inprogerss_percentage_update')) {
            Schema::create('order_task_inprogerss_percentage_update', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->unsigned()->index();
                $table->integer('workspace_id')->unsigned()->index();
                $table->bigInteger('task_id')->default(0);
                $table->integer('order_id')->default(0);
                $table->integer('template_id')->default(0);
                $table->text('cat_title')->nullable();
                $table->text('task_title')->nullable();
                $table->integer('pic_id')->default(0);
                $table->string('pic_name',255)->default(0);
                $table->tinyInteger('previous_percentage')->unsigned()->default(0);
                $table->tinyInteger('update_percentage')->unsigned()->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_task_inprogerss_percentage_update');
    }
};
