<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrderAction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('update_order_action')) {
            Schema::create('update_order_action', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->unsigned()->index();
                $table->foreign('company_id')->references('id')->on('company_settings');
                $table->bigInteger('user_id');
                $table->bigInteger('workspace_id');
                $table->bigInteger('staff_id');
                $table->bigInteger('order_id');
                $table->string('order_no',255);
                $table->longText('reason');
                $table->string('action_type',255);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['user_id','workspace_id','staff_id','order_id']);
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
