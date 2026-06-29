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
        if (!Schema::hasTable('excel_import_logs')) {
            Schema::create('excel_import_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->default(0);
                $table->bigInteger('workspace_id')->default(0);
                $table->string('table_name',255)->nullable();
                $table->string('data')->nullable();
                $table->bigInteger('order_id')->default(0);
                $table->string('ip_address',255)->nullable();
                $table->dateTime('created_at');
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
