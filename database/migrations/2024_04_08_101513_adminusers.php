<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('adminusers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name',255)->nullable();
            $table->string('last_name',255)->nullable();
            $table->string('mobile',50)->nullable();
            $table->string('email',255)->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city',255)->nullable();
            $table->string('state',255)->nullable();
            $table->integer('country')->default(0);
            $table->string('zipcode',50)->nullable();
            $table->string('language',50)->nullable();
            $table->string('timezone',100)->nullable();
            $table->string('password',255)->nullable();
            $table->string('otp',10)->nullable();
            $table->dateTime('otp_generated_time')->nullable();
            $table->enum('status', ['0', '1','2', '3'])->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
