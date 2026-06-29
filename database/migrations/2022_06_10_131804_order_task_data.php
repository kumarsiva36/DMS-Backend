<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderTaskData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('order_task_data')) {
            Schema::create('order_task_data', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->unsigned()->index();
                $table->foreign('company_id')->references('id')->on('company_settings');
                $table->bigInteger('workspace_id')->index();
                $table->bigInteger('user_id');
                $table->mediumInteger('staff_id')->nullable();
                $table->bigInteger('order_id')->unsigned()->index();
                $table->foreign('order_id')->references('id')->on('orders');
                $table->bigInteger('template_id')->index();
                $table->string('cat_title',100);
                // $table->mediumInteger('cat_id')->nullable();
                $table->mediumInteger('cat_seq_no')->nullable();
                $table->mediumInteger('task_seq_no')->nullable();
                $table->string('task_title',100);
                $table->integer('parent_task_id')->nullable();
                $table->tinyInteger('is_subtask')->default(0)->nullable();
                $table->string('subtask_title',100)->nullable();
                $table->date('actual_start_date')->nullable();
                $table->date('task_schedule_start_date')->nullable();
                $table->date('task_schedule_end_date')->nullable();
                $table->mediumInteger('task_pic')->nullable();
                $table->date('task_accomplished_date')->nullable();
                $table->integer('created_by');
                $table->string('created_user_type',150);
                $table->mediumText('reschedule_reason')->nullable();
                $table->mediumInteger('reschedule_order_task_data_id')->nullable();
                $table->tinyInteger('rescheduled')->default('0')->nullable();
                $table->string('category_contacts',150)->nullable();
                $table->string('task_contacts',150)->nullable();
                $table->text('reason_actual_start_date')->nullable();
                $table->tinyInteger('inprogress_percentage')->unsigned()->default(0);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['task_pic']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
