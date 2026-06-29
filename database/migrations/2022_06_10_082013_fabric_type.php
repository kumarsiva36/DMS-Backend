<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FabricType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('fabric_type')) {
            Schema::create('fabric_type', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name',255)->nullable();
                $table->bigInteger('company_id')->default(0);
                $table->bigInteger('workspace_id')->default(0);
                $table->bigInteger('user_id')->default(0);
                $table->bigInteger('staff_id')->default(0);
                $table->enum('is_default', array('0', '1'))->comment('0=>"Yes","1"=>"No"');
                $table->enum('status', array('0', '1', '2', '3'))->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->string('inquiry_reference_id',255)->default("0");
                $table->bigInteger('created_by')->default(0);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['name','company_id','workspace_id','is_default','status','inquiry_reference_id']);
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
