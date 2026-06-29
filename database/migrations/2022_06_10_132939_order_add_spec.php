<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderAddSpec extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('order_add_spec')) {
            Schema::create('order_add_spec', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->unsigned()->index();
                $table->foreign('company_id')->references('id')->on('company_settings');
                $table->bigInteger('workspace_id');
                $table->bigInteger('user_id')->default('0');
                $table->mediumInteger('staff_id')->default('0');
                $table->bigInteger('order_id')->unsigned()->index();
                $table->foreign('order_id')->references('id')->on('orders');
                $table->mediumText('filename');
                $table->mediumText('orginalfilename');
                $table->mediumText('filepath');
                $table->integer("filesize");
                $table->mediumInteger('fileorder')->default('0');
                $table->enum('status', ['0', '1', '2', '3'])->default('1')->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['workspace_id']);
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
