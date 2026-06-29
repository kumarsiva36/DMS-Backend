<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderStatusAction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('order_status_action')) {
            Schema::create('order_status_action', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->unsigned()->index();
                $table->foreign('company_id')->references('id')->on('company_settings');
                $table->bigInteger('workspace_id');
                $table->bigInteger('user_id');
                $table->mediumInteger('staff_id');
                $table->bigInteger('order_id')->unsigned()->index();
                $table->foreign('order_id')->references('id')->on('orders');
                $table->enum('status', ['0', '1', '2', '3'])->default('1')->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->mediumInteger('action_request')->nullable();
                $table->mediumInteger('action_request_by')->nullable();
                $table->string('action_request_user_type',100);
                $table->mediumInteger('action_done_by')->nullable();
                $table->string('action_done_user_type',100);
                $table->mediumText('action_request_comment')->nullable();
                $table->mediumText('action_done_comment')->nullable();
                $table->date('action_request_date');
                $table->date('action_done_date');
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
