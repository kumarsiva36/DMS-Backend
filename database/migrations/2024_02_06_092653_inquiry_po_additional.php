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
        if (!Schema::hasTable('inquiry_po_additional')) {
            Schema::create('inquiry_po_additional', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('po_id')->default(0);
                $table->string('label',255)->nullable();
                $table->text('label_description')->nullable();
                $table->string('media_type',255)->nullable();
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->timestamps();
                $table->index(['po_id','company_id','workspace_id']);
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
