<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Roles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name',255);
                $table->bigInteger('company_id');
                $table->bigInteger('workspace_id');
                $table->bigInteger('user_id');
                $table->bigInteger('staff_id');
                $table->enum('is_default', array('0', '1'))->comment('0=>"Yes","1"=>"No"');
                $table->integer('order_sequence')->default(0);
                $table->enum('status', array('0', '1', '2', '3'))->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->bigInteger('created_by');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['name','company_id','workspace_id','is_default','status']);
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

    }
}
