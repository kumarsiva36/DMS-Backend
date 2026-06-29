<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('order_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('company_id')->unsigned()->index();
            $table->foreign('company_id')->references('id')->on('company_settings');
            $table->bigInteger('workspace_id');
            $table->mediumInteger('user_id')->default(0);
            $table->mediumInteger('staff_id')->default(0);
            $table->mediumInteger('order_id')->default(0);
            $table->string('action')->nullable();
            $table->text('before_values')->nullable();
            $table->text('after_values')->nullable();
            $table->text('comments')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
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
