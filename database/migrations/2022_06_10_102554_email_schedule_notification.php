<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmailScheduleNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('email_schedule_notification')) {
            Schema::create('email_schedule_notification', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->default(0);
                $table->bigInteger('workspace_id')->default(0);
                $table->bigInteger('user_id')->default(0);
                $table->bigInteger('staff_id')->default(0);
                $table->bigInteger('email_schedule_task_id')->default(0);
                $table->string('name', 100)->nullable();
                $table->bigInteger('email_to_staff_id')->default(0);
                $table->bigInteger('email_to_user_id')->default(0);
                $table->string('days', 100)->nullable();
                $table->enum('is_consolidated_mail', ['0', '1'])->default(0)->comment('0=>"No",1="Yes"');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['company_id','workspace_id','user_id','staff_id','email_schedule_task_id','is_consolidated_mail']);
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
