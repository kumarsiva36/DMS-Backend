<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('multiple_delivery_dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->default(0);
            $table->integer('company_id')->default(0);
            $table->integer('workspace_id')->default(0);
            $table->date('delivery_date')->nullable();
            $table->integer('total_delivered_quantity')->default(0);
            $table->tinyInteger('is_delivered')->default(0);
            $table->text('delivery_comments')->nullable();
            // $table->foreign('order_id')
            //     ->references('id')
            //     ->on('orders')
            //     ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multiple_delivery_dates');
    }
};
