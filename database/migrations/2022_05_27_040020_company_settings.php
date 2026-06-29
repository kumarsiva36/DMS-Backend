<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CompanySettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('company_settings')) {
            Schema::create('company_settings', function (Blueprint $table) {
               // $table->id();
                $table->bigIncrements('id');
                $table->string('company_name',150);
                $table->bigInteger('user_id')->unsigned()->index();
                $table->foreign('user_id')->references('id')->on('users');
                $table->string('logo',150)->nullable();
                $table->tinyInteger('use_logo')->default(0); //To Use company Logo in the PDF or not (1=>Use 0=>Use Default Logo)
                $table->string('contact_person',150);
                $table->string('contact_number',150);
                $table->mediumText('address1')->nullable();
                $table->mediumText('address2')->nullable();
                $table->string('city', 150)->nullable();
                $table->string('state', 150)->nullable();
                $table->string('zipcode', 100)->nullable();
                $table->bigInteger('country_id')->nullable();
                $table->string('language', 50)->nullable();
                $table->string('account_no',20)->nullable();
                $table->string('ifsc_code',30)->nullable();
                $table->string('gst_number',50)->nullable();
                $table->string('pan_number',50)->nullable();
                $table->string('currency', 50)->nullable();
                $table->string('timezone', 100)->nullable();
                $table->mediumText('aws_s3_path');
                $table->bigInteger('purchased_plan_id');
                $table->string('purchased_plan_name',255);
                $table->string('purchased_plan_type',100)->nullable();
                $table->string('purchased_plan_price',100);
                $table->string('purchased_plan_price_currency',100);
                $table->dateTime('plan_purchase_at');
                $table->enum('status', array('0', '1', '2', '3'))->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->dateTime('account_activated_at');
                $table->dateTime('account_expire_at');
                $table->bigInteger('no_of_group')->default(1);
                $table->bigInteger('no_of_user')->default(1);
                $table->bigInteger('no_of_style')->default(1);
                $table->bigInteger('no_of_workspace')->default(1);
                $table->double('storage_used',10,3)->default(0);
                $table->bigInteger('max_storage_size');
                $table->bigInteger('report_range');
                $table->enum('download_report', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->enum('notify_email_upcoming_task', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->enum('notify_email_delayed_task', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->enum('notify_whatsapp_upcoming_task', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->enum('notify_whatsapp_delayed_task', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->enum('notify_linemessenger_upcoming_task', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->enum('notify_linemessenger_delayed_task', array('8','9'))->comment('8=>"YES",9=>"NO"');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['status']);

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
