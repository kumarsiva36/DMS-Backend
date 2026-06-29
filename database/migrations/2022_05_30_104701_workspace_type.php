<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WorkspaceType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('workspace_type')) {
            Schema::create('workspace_type', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name',255);
                $table->enum('status', array('0', '1', '2', '3'))->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
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
