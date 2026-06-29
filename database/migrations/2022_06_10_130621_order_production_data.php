<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderProductionData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('order_production_data')) {
            Schema::create('order_production_data', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->index();
                $table->bigInteger('workspace_id')->index();
                $table->bigInteger('user_id');
                $table->mediumInteger('staff_id')->nullable();
                $table->bigInteger('order_id')->unsigned()->index();
                $table->foreign('order_id')->references('id')->on('orders');
                $table->date('date_of_production')->index();
                $table->string('type_of_production',150)->index();
                $table->tinyInteger('is_accomplished')->default(0);
                $table->mediumInteger('target_value')->nullable();
                $table->mediumInteger('actual_value')->nullable();
                $table->mediumInteger('holiday_flag')->nullable();
                $table->mediumText('holiday_detail')->nullable();
                $table->enum('status', ['0', '1', '2', '3'])->default('1')->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
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
