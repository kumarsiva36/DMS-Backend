<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderSku extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('order_sku')) {
            Schema::create('order_sku', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->unsigned()->index();
                $table->foreign('user_id')->references('id')->on('users');
                $table->bigInteger('company_id')->unsigned()->index();
                $table->foreign('company_id')->references('id')->on('company_settings');
                $table->bigInteger('workspace_id');
                $table->bigInteger('staff_id');
                $table->bigInteger('sku_color_id');
                $table->bigInteger('sku_size_id');
                $table->bigInteger('order_id')->unsigned()->index();
                $table->foreign('order_id')->references('id')->on('orders');
                $table->bigInteger('sku_quantity');
                $table->bigInteger('sku_finished_quantity')->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['workspace_id','sku_color_id','sku_size_id']);
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
