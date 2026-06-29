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
        if (!Schema::hasTable('inquiry_mail_sent_details')) {
            Schema::create('inquiry_mail_sent_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('inquiry_id')->default(0);
                $table->string('email',255)->nullable();
                $table->text('subject')->nullable();
                $table->text('content')->nullable();
                $table->string('sent_by',255)->nullable();
                $table->timestamps();
                $table->index(['inquiry_id']);
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
