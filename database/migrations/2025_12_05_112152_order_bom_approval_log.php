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
        if (!Schema::hasTable('order_bom_approval_log')) {
            Schema::create('order_bom_approval_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->integer('user_id')->default(0);
                $table->integer('staff_id')->default(0);
                $table->integer('order_id')->default(0);
                $table->integer('order_bom_id')->default(0);
                $table->dateTime('approval_date');
                $table->string('approval_type',255)->nullable();
                $table->string('approved_by',255)->nullable();
                $table->text('comments')->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['company_id','workspace_id']);
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
