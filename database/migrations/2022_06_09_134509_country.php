<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Country extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('country')) {
            Schema::create('country', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name',255)->nullable();
                $table->string('code',6)->nullable();
                $table->string('currency',255);
                $table->string('language',255);
                $table->string('currency_sign',255);
                $table->dateTime('created_at');
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
