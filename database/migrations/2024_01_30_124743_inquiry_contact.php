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
        if (!Schema::hasTable('inquiry_contact')) {
            Schema::create('inquiry_contact', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('factory_id')->default(0);
                $table->string('factory',255)->nullable();
                $table->text('contact_person')->nullable();
                $table->string('contact_number',255)->nullable();
                $table->string('contact_email',255)->nullable();
                $table->text('address')->nullable();
                $table->string('city',255)->nullable();
                $table->timestamps();
                $table->index(['factory_id']);
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
