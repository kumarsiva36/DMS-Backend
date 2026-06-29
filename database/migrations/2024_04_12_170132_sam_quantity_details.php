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
        if (!Schema::hasTable('sam_quantity_details')) {
            Schema::create('sam_quantity_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('report_id')->default(0);
                $table->string('time_slot',100)->nullable();
                $table->integer('quantity')->default(0);
                $table->text('comments')->nullable();
                $table->integer('user_id')->default(0);
                $table->integer('staff_id')->default(0);
                $table->dateTime('created_at');
                $table->index(['report_id','time_slot','user_id','staff_id']);
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
