<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('username', 150);
            $table->string('mobile_number', 100);
            // $table->string('company_name');
            $table->string('user_type', 100);
            $table->integer('country_id');
            $table->string('lang_code');
            $table->ipAddress('ip_address')->nullable();
            $table->mediumInteger('company_id')->nullable();
            $table->bigInteger('otp')->nullable();
            $table->dateTime('otp_generated_time')->nullable();
            $table->string('payment_intent',255)->nullable();
            $table->dateTime('last_loggedin_time')->nullable();
            $table->enum('status', ['0', '1', '2', '3'])->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->text('rememberToken')->nullable();
            $table->text('fcm_token')->nullable();
            $table->text('device_details')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('users');
    }
}
