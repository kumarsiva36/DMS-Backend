<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserLoggingHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_logging_history')) {
            Schema::create('user_logging_history', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->default('0');
                $table->bigInteger('logging_user_id');
                $table->string('logging_user_name', 100);
                $table->string('ipaddress', 100);
                $table->mediumText('browser_details');
                $table->string('login_status', 100);
                $table->string('login_user_type', 100);
                $table->string('log_type', 100);
                $table->dateTime('logging_in_datetime');
                $table->dateTime('logged_out_datetime')->nullable();
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
