<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Staff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('staff')) {
        Schema::create('staff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('company_id')->unsigned()->index();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->foreign('company_id')->references('id')->on('company_settings');
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->bigInteger('role_id')->unsigned()->index();
            $table->foreign('role_id')->references('id')->on('roles');
            $table->string('first_name', 150);
            $table->string('last_name', 150)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('email')->nullable();
            $table->mediumText('address1')->nullable();
            $table->mediumText('address2')->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->integer('country')->nullable();
            $table->string('zipcode', 50)->nullable();
            $table->string('language', 50)->nullable();
            $table->string('timezone', 100)->nullable();
            $table->string('password')->nullable();
            $table->string('otp')->nullable();
            $table->string('user_type', 50)->nullable();
            $table->integer('staff_type')->default(1)->comment('1=>inhouse,2=>others');
            $table->string('profile_img', 255)->nullable();
            $table->dateTime('otp_generated_time')->nullable();
            $table->enum('status', ['0', '1', '2', '3'])->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
            $table->enum('is_confidentional', ['0', '1'])->comment('0=>no,1=>yes');
            $table->dateTime('last_seen');
            $table->text('fcm_token')->nullable();
            $table->text('device_details')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->index(['status']);

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
