<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotificationSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('notification_settings')) {
            Schema::create('notification_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->default(0);
                $table->bigInteger('workspace_id')->default(0);
                $table->bigInteger('user_id')->default(0);
                $table->bigInteger('staff_id')->default(0);
                $table->enum('email_daily_reminder', ['6', '7'])->default('6')->comment('"6"=>"Notify","7"=>"Not Notify"');
                $table->enum('email_weekly_reminder', ['6', '7'])->default('6')->comment('"6"=>"Notify","7"=>"Not Notify"');
                $table->enum('email_task_accomplishment', ['6', '7'])->default('6')->comment('"6"=>"Notify","7"=>"Not Notify"');
                $table->enum('email_task_reschedule', ['6', '7'])->default('6')->comment('"6"=>"Notify","7"=>"Not Notify"');
                $table->enum('email_due_today', ['6', '7'])->default('6')->comment('"6"=>"Notify","7"=>"Not Notify"');
                $table->enum('email_due_tomorrow', ['6', '7'])->default('6')->comment('"6"=>"Notify","7"=>"Not Notify"');
                $table->enum('email_daily_schedule', ['6', '7'])->default('6')->comment('"6"=>"Notify","7"=>"Not Notify"');
                $table->enum('whatsapp', ['6', '7'])->default('6')->comment('"6"=>"Notify","7"=>"Not Notify"');
                $table->enum('linemessenger', ['6', '7'])->default('6')->comment('"6"=>"Notify","7"=>"Not Notify"');
                $table->enum('sms', ['6', '7'])->default('6')->comment('"6"=>"Notify","7"=>"Not Notify"');
                $table->enum('backup', ['6', '7'])->default('6')->comment('"6"=>"Notify","7"=>"Not Notify"');
                $table->enum('is_consolidated_mail', ['0', '1'])->default(0)->comment('0=>"No",1="Yes"');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['company_id','workspace_id']);
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
