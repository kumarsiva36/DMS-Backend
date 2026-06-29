<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderContacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('order_contacts')) {
            Schema::create('order_contacts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->unsigned()->index();
                $table->foreign('company_id')->references('id')->on('company_settings');
                $table->bigInteger('workspace_id');
                $table->bigInteger('user_id');
                $table->mediumInteger('staff_id');
                $table->bigInteger('order_id')->unsigned()->index();
                $table->foreign('order_id')->references('id')->on('orders');
                $table->enum('status', ['0', '1', '2', '3'])->default('1')->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['workspace_id','status']);
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
