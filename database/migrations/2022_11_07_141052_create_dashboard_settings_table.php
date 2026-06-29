<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDashboardSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('dashboard_settings')) {
        Schema::create('dashboard_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('company_id')->unsigned()->index();
            $table->foreign('company_id')->references('id')->on('company_settings');
            $table->bigInteger('workspace_id')->default(0);
            $table->bigInteger('user_id')->default(0);
            $table->mediumInteger('staff_id')->default(0);
            $table->mediumInteger('widget_id')->default(0);
            $table->string('order_ids',255)->nullable();
            $table->timestamps();
            $table->index(['company_id','workspace_id','user_id','staff_id','widget_id','order_ids']);
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
        Schema::dropIfExists('dashboard_settings');
    }
}
