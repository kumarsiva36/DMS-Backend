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
        if (!Schema::hasTable('email_notification_settings')) {
        Schema::create('email_notification_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('company_id')->nullable();
            $table->integer('workspace_id')->nullable();
            $table->integer('user_id')->default(0)->nullable();
            $table->integer('staff_id')->default(0)->nullable();
            $table->integer('notify_admin')->default(0)->nullable();
            $table->string('order_type',150)->nullable();
            $table->string('no_of_delays',100)->nullable();
            $table->mediumText('email_ids')->nullable();
            $table->integer('email_no_of_delays')->default(0)->nullable();
            $table->enum('is_consolidated_mail', ['0', '1'])->comment('0=>"No",1="Yes"');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->index(['company_id','workspace_id','user_id','staff_id','notify_admin']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_notification_settings');
    }
};
