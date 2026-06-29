<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaymentHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('payment_history')) {
            Schema::create('payment_history', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id');
                $table->string('user_name',150);
                $table->string('user_email',150);
                $table->string('mobile',150);
                $table->string('payment_type',150);
                $table->string('plan_name',100);
                $table->integer('plan_id');
                $table->string('plan_type',100)->nullable();
                $table->string('plan_price',100);
                $table->string('plan_discount',100);
                $table->string('plan_subtotal',100);
                $table->string('plan_grandtotal',100);
                $table->string('payment_currency',100);
                $table->mediumText('reference_id')->nullable();
                $table->mediumText('reason')->nullable();
                $table->string('payment_intent',255)->nullable();
                $table->string('payment_status');
                $table->string('ipaddress');
                $table->dateTime('payment_date');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['user_id','plan_id']);

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
