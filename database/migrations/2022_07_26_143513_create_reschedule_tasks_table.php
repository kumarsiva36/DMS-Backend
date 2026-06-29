<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRescheduleTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reschedule_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('company_id')->index();
            $table->bigInteger('workspace_id')->index();
            $table->bigInteger('orderTaskData_id')->index();
            $table->bigInteger('order_id')->index();
            $table->integer('template_id')->index();
            $table->string('cat_title');
            $table->string('task_title');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('rescheduled_start_date')->nullable();
            $table->date('rescheduled_end_date')->nullable();
            $table->text('reason');
            $table->integer('rescheduled_by')->index();
            $table->integer('prev_pic_id');
            $table->integer('pic_id');
            $table->enum('rescheduled_type',array('Reschedule','Reassign'));
            $table->string('user_type');
            $table->dateTime('created_at');
            $table->index(['rescheduled_by','prev_pic_id','pic_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reschedule_tasks');
    }
}
