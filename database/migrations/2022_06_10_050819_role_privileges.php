<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RolePrivileges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('role_privileges')) {
            Schema::create('role_privileges', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id');
                $table->bigInteger('workspace_id');
                $table->bigInteger('user_id');
                $table->bigInteger('role_id')->unsigned()->index();
                $table->foreign('role_id')->references('id')->on('roles');
                $table->bigInteger('permission_id');
                $table->enum('status', array('0', '1', '2', '3'))->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['company_id','workspace_id','permission_id']);
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
