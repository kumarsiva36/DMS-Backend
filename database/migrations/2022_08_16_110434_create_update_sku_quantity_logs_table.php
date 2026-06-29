<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateSkuQuantityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('update_sku_quantity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger("company_id");
            $table->bigInteger("workspace_id");
            $table->bigInteger("user_id");
            $table->bigInteger("staff_id");
            $table->bigInteger("sku_id");
            $table->bigInteger("order_id");
            $table->bigInteger("color_id");
            $table->bigInteger("size_id");
            $table->string("type_of_production");
            $table->bigInteger("updated_quantity");
            $table->date("sku_date");
            $table->string("device_details",255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('update_sku_quantity_logs');
    }
}
