<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateSkuQuantitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('update_sku_quantities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger("company_id")->index();
            $table->bigInteger("workspace_id")->index();
            $table->bigInteger("user_id");
            $table->bigInteger("staff_id");
            $table->bigInteger("sku_id")->index();
            $table->bigInteger("order_id")->index();
            $table->bigInteger("color_id");
            $table->bigInteger("size_id");
            $table->string("type_of_production")->index();
            $table->bigInteger("updated_quantity");
            $table->bigInteger("target_value");
            $table->date("sku_date");
            $table->timestamps();
            $table->index(['color_id','size_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('update_sku_quantities');
    }
}
