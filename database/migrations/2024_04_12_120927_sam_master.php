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
        if (!Schema::hasTable('sam_master')) {
            Schema::create('sam_master', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->default(0);
                $table->bigInteger('workspace_id')->default(0);
                $table->string('value',255)->nullable();
                $table->string('type',255)->nullable();
                $table->time('shift_from_time')->nullable();
                $table->time('shift_end_time')->nullable();
                $table->integer('line_unit')->default(0);
                $table->integer('line_no_of_machines')->default(0);
                $table->string('line_machine_type',255)->nullable();
                $table->string('supervisor_id',255)->nullable();
                $table->integer('user_id')->default(0);
                $table->integer('staff_id')->default(0);
                $table->dateTime('created_at');
                $table->index(['company_id','workspace_id','user_id','staff_id']);
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
