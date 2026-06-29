<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Buyer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('order_buyer')) {
            Schema::create('order_buyer', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name',255)->nullable();
                $table->bigInteger('company_id')->unsigned()->index();
                $table->foreign('company_id')->references('id')->on('company_settings');
                $table->bigInteger('user_id')->default(0);
                $table->bigInteger('workspace_id')->default(0);
                $table->bigInteger('staff_id')->default(0);
                $table->bigInteger('created_by');
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
