<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderPcu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('order_pcu')) {
            Schema::create('order_pcu', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name',255);
                $table->bigInteger('company_id')->unsigned()->index();
                $table->foreign('company_id')->references('id')->on('company_settings');
                $table->bigInteger('user_id');
                $table->bigInteger('workspace_id');
                $table->bigInteger('staff_id');
                $table->bigInteger('created_by');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['workspace_id','name']);
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
