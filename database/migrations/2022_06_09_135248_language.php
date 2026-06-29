<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Language extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('language')) {
            Schema::create('language', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name',255);
                $table->string('lang_code',255);
                $table->string('currency',255)->nullable();
                $table->string('currency_sign',255)->nullable();
                $table->enum('status', array('0', '1', '2', '3'))->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['name','lang_code','status']);
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
