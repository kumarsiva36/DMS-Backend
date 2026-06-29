<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Emailscheduletask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('email_schedule_task')) {
            Schema::create('email_schedule_task', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name',200);
                $table->enum('status', ['0', '1','2'])->default('1')->comment('0=>"default","1"=>"Active","2"=>"Deactivated"');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['name','status']);
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
