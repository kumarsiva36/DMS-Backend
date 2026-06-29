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
        if (!Schema::hasTable('order_bom')) {
            Schema::create('order_bom', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('order_id')->default(0);
                $table->text('sewing_accessories')->nullable();
                $table->text('packing_accessories')->nullable();
                $table->text('miscellaneous')->nullable();
                $table->integer('is_approval')->default(0)->comment('0=>Waiting Approval,1=>Approved,2=>deleted,3=>Waiting Re-approval');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->integer('created_user_id')->default(0);
                $table->integer('created_staff_id')->default(0);
                $table->integer('updated_user_id')->default(0);
                $table->integer('updated_staff_id')->default(0);
                $table->index(['order_id']);
              });
           }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
