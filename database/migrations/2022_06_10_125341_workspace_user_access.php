<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WorkspaceUserAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('workspace_access_user')) {
            Schema::create('workspace_access_user', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id');
                $table->bigInteger('workspace_id');
                $table->bigInteger('user_id');
                $table->bigInteger('staff_id')->nullable();
                $table->bigInteger('order_id')->nullable();
                $table->bigInteger('role_id')->nullable();
                $table->string('staff_type',255);
                $table->integer('created_by');
                $table->string('created_user_type',150);
                $table->enum('status', ['0', '1', '2', '3'])->default('1')->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
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
