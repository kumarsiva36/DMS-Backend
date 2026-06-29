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
        Schema::create('partial_deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->integer('company_id');
            $table->integer('workspace_id');
            $table->string('user_type');
            $table->integer('user_id');
            $table->integer('staff_id');
            $table->date('delivery_date');
            $table->integer('color_id');
            $table->integer('size_id');
            $table->integer('quantity');
            $table->text('delivery_comments')->nullable();
            $table->timestamps();
            // $table->foreign('order_id')
            //     ->references('id')
            //     ->on('orders')
            //     ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partial_deliveries');
    }
};
