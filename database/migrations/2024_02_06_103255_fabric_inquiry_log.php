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
        if (!Schema::hasTable('fabric_inquiry_log')) {
            Schema::create('fabric_inquiry_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->integer('user_id')->default(0);
                $table->integer('staff_id')->default(0);
                $table->string('action',10)->nullable();
                $table->text('before_values')->nullable();
                $table->text('after_values')->nullable();
                $table->integer('inquiry_id')->default(0);
                $table->timestamps();
                $table->index(['company_id','workspace_id','inquiry_id']);
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
