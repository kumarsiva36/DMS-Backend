<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PlanPriceDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('plan_price_details')) {
            Schema::create('plan_price_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('plan_name','100');
                $table->string('type','100');
                $table->bigInteger('no_of_month');
                $table->bigInteger('no_of_days');
                $table->float('price', 50, 2);
                $table->float('yearly_price', 50, 2);
                $table->float('special_price', 50, 2);
                $table->string('currency', 50);
                $table->bigInteger('no_of_group');
                $table->bigInteger('no_of_user');
                $table->bigInteger('no_of_style');
                $table->bigInteger('no_of_workspace');
                $table->bigInteger('max_storage_size');
               // $table->bigInteger('no_of_original_defect');
                $table->bigInteger('report_range')->comment('specify in days');
              //  $table->string('inspection_app','100')->comment('INLINE','AQL','RANDOM','100%');
               // $table->enum('summary_data_download', array('8', '9'))->comment('8=>YES,9=>NO');
                $table->enum('download_report', array('8', '9'))->comment('8=>YES,9=>NO');
                $table->enum('notify_email_upcoming_task', array('8', '9'))->comment('8=>YES,9=>NO');
                $table->enum('notify_email_delayed_task', array('8', '9'))->comment('8=>YES,9=>NO');
                $table->enum('notify_whatsapp_upcoming_task', array('8', '9'))->comment('8=>YES,9=>NO');
                $table->enum('notify_whatsapp_delayed_task', array('8', '9'))->comment('8=>YES,9=>NO');
                $table->enum('notify_linemessenger_upcoming_task', array('8', '9'))->comment('8=>YES,9=>NO');
                $table->enum('notify_linemessenger_delayed_task', array('8', '9'))->comment('8=>YES,9=>NO');
                $table->enum('status',array('0', '1', '2', '3','10'))->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted","10" =>"customization"');
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
