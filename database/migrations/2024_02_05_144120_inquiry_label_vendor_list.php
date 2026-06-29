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
        if (!Schema::hasTable('inquiry_label_vendor_list')) {
            Schema::create('inquiry_label_vendor_list', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->string('vendor_name',255)->nullable();
                $table->string('website',255)->nullable();
                $table->text('office_address')->nullable();
                $table->text('factory_address')->nullable();
                $table->text('contact_details')->nullable();
                $table->string('category_ids',255)->nullable();
                $table->string('created_user_type',20)->nullable();
                $table->integer('created_user_id')->default(0);
                $table->string('updated_user_type',20)->nullable();
                $table->integer('updated_user_id')->default(0);
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
