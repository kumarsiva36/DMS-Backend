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
        if (!Schema::hasTable('inquiry_po_log')) {
            Schema::create('inquiry_po_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->integer('user_id')->default(0);
                $table->integer('staff_id')->default(0);
                $table->string('action',50)->nullable();
                $table->text('before_values')->nullable();
                $table->text('after_values')->nullable();
                $table->integer('po_id')->default(0);
                $table->string('ip_address',100)->nullable();
                $table->string('platform',50)->nullable();
                $table->timestamps();
                $table->index(['po_id','company_id','workspace_id']);
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
