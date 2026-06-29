<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderTaskTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('order_task_template')) {
            Schema::create('order_task_template', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->unsigned()->index();
                $table->foreign('company_id')->references('id')->on('company_settings');
                $table->bigInteger('workspace_id');
                $table->bigInteger('user_id');
                $table->mediumInteger('staff_id');
                $table->bigInteger('order_id');
                $table->string('template_name',150);
                $table->enum('status', ['0', '1', '2', '3'])->default('1')->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->enum('is_default', array('0', '1'))->comment('0=>"Yes","1"=>"No"');
                $table->longText('task_template_structure');
                $table->integer('created_by');
                $table->string('created_user_type',150);
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
