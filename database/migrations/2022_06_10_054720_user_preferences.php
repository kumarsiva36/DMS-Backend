<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserPreferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_preferences')) {
            Schema::create('user_preferences', function (Blueprint $table) {
                $table->bigIncrements('id');
                // $table->string('name',255);
                $table->bigInteger('company_id');
                $table->bigInteger('workspace_id');
                $table->bigInteger('user_id');
                $table->bigInteger('staff_id');
                $table->string('date_format');
                $table->bigInteger('language_id');
                // $table->bigInteger('language_id')->unsigned()->index();
                // $table->foreign('language_id')->references('id')->on('language');
                $table->string('time_zone_format',150);
                $table->string('dashboard_widget_ids',255)->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['workspace_id','company_id']);
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

    }
}
