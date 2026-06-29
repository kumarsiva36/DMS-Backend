<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HolidaySettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('holiday_settings')) {
            Schema::create('holiday_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->unsigned()->index();
                $table->foreign('company_id')->references('id')->on('company_settings');
                $table->bigInteger('workspace_id')->default(0);
                $table->bigInteger('user_id')->default(0);
                $table->bigInteger('staff_id')->default(0);
                $table->string('name', 255)->nullable();
                $table->mediumText('description')->nullable();
                $table->date('holiday_start_date')->index();
                $table->date('holiday_end_date')->index();
                $table->string('days', 100)->nullable();
                $table->enum('status', ['0', '1', '2', '3'])->default('1')->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['company_id','workspace_id','status','holiday_start_date','holiday_end_date']);
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
