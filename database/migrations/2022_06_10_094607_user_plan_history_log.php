<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserPlanHistoryLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_plan_history_log')) {
            Schema::create('user_plan_history_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id');
                $table->bigInteger('user_id');
                $table->bigInteger('workspace_id');
                $table->string('user_name', 100);
                $table->string('user_email', 50);
                $table->string('language', 50);
                $table->string('plan_name','100');
                $table->string('plan_type','100')->nullable();
                $table->bigInteger('no_of_month');
                $table->bigInteger('no_of_days');
                $table->float('price', 50, 2);
                $table->float('special_price', 50, 2);
                $table->string('currency', 50);
                $table->mediumText('aws_s3_path');
                $table->bigInteger('purchased_plan_id');
                $table->string('purchased_plan_name',255);
                $table->string('purchased_plan_type',150);
                $table->string('purchased_plan_price',100);
                $table->string('purchased_plan_price_currency',100);
                $table->dateTime('plan_purchase_at');
                $table->enum('status', array('0', '1', '2', '3'))->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->dateTime('account_activated_at');
                $table->dateTime('account_expire_at');
                $table->bigInteger('no_of_group');
                $table->bigInteger('no_of_user');
                $table->bigInteger('no_of_style');
                $table->bigInteger('max_storage_size');
               // $table->bigInteger('no_of_original_defect');
                $table->bigInteger('report_range');
               // $table->string('inspection_app',255);
               // $table->enum('summary_data_download', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->enum('download_report', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->enum('notify_email_upcoming_task', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->enum('notify_email_delayed_task', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->enum('notify_whatsapp_upcoming_task', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->enum('notify_whatsapp_delayed_task', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->enum('notify_linemessenger_upcoming_task', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->enum('notify_linemessenger_delayed_task', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
               
              
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
