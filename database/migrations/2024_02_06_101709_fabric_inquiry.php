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
        if (!Schema::hasTable('fabric_inquiry')) {
            Schema::create('fabric_inquiry', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('reference_id',255)->nullable();
                $table->text('supplier_ids')->nullable();
                $table->integer('user_id')->default(0);
                $table->integer('staff_id')->default(0);
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->string('yarn_count',255)->nullable();
                $table->string('yarn_quantity',255)->nullable();
                $table->string('yarn_quality',255)->nullable();
                $table->string('meterial',255)->nullable();
                $table->string('composition',255)->nullable();
                $table->integer('reference_inquiry')->default(0);
                $table->string('currency',60)->nullable();
                $table->date('delivery_date')->nullable();
                $table->date('inhouse_date')->nullable();
                $table->integer('updated_user_id')->default(0);
                $table->integer('updated_staff_id')->default(0);
                $table->timestamps();
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
