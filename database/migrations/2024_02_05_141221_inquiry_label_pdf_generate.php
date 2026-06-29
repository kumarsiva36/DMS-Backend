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
        if (!Schema::hasTable('inquiry_label_pdf_generate')) {
            Schema::create('inquiry_label_pdf_generate', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->integer('inquiry_id')->default(0);
                $table->integer('user_id')->default(0);
                $table->string('user_name',255)->nullable();
                $table->integer('staff_id')->default(0);
                $table->string('staff_name',255)->nullable();
                $table->dateTime('created_at')->default(now());
                $table->index(['company_id','workspace_id','inquiry_id','user_id','staff_id'],'nquiry_label_pdf_main_idx');
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
