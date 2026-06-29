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
        if (!Schema::hasTable('inquiry_sku')) {
            Schema::create('inquiry_sku', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('inquiry_id')->default(0);
                $table->integer('color_id')->default(0);
                $table->integer('size_id')->default(0);
                $table->integer('color_ratio')->default(0);
                $table->integer('size_ratio')->default(0);
                $table->integer('quantity')->default(0);
                $table->dateTime('created_at')->default(now());
                $table->index(['inquiry_id','color_id','size_id']);
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
