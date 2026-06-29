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
        if (!Schema::hasTable('inquiry_po_forwarder')) {
            Schema::create('inquiry_po_forwarder', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->string('company_name',255)->nullable();
                $table->text('address')->nullable();
                $table->string('contact_person',255)->nullable();
                $table->string('contact_phone',255)->nullable();
                $table->string('contact_email',255)->nullable();
                $table->string('category_ids',255)->nullable();
                $table->integer('created_user_id')->default(0);
                $table->integer('created_staff_id')->default(0);
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
