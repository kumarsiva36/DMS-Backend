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
        if (!Schema::hasTable('fabric_contact')) {
            Schema::create('fabric_contact', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('supplier',255)->nullable();
                $table->string('contact_person',255)->nullable();
                $table->string('contact_number',255)->nullable();
                $table->string('contact_email',255)->nullable();
                $table->text('address')->nullable();
                $table->string('city',255)->nullable();
                $table->timestamps();
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
