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
        Schema::create('order_bom_approval_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('company_id');
            $table->integer('workspace_id');
            $table->integer('user_id')->default(0)->nullable();
            $table->integer('staff_id')->default(0)->nullable();
            $table->integer('order_id');
            $table->integer('order_bom_id');
            $table->dateTime('approval_date');
            $table->string('approval_type')->default('Approval');
            $table->mediumText('comments')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
