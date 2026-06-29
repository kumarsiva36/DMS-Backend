<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DashboardNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('dashboard_notification')) {
            Schema::create('dashboard_notification', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->unsigned()->index();
                $table->foreign('company_id')->references('id')->on('company_settings');
                $table->bigInteger('workspace_id')->default(0);
                $table->bigInteger('user_id')->default(0);
                $table->bigInteger('staff_id')->nullable();
                $table->bigInteger('order_id')->nullable()->index();
                $table->string('notification_title', 100)->nullable();
                $table->mediumText('notification_description')->nullable();
                $table->mediumText('notification_details')->nullable();
                $table->string('notification_type', 100)->nullable();
                $table->integer('is_read')->default(0);
                $table->integer('notified_user')->nullable();
                $table->mediumText('notification_url')->nullable();
                $table->integer('notification_status')->default(0);
                $table->string('notify_status_code',50)->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['company_id','workspace_id','user_id','staff_id','order_id']);
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
