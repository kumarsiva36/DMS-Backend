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
        if (!Schema::hasTable('inquiry_new_po_testing')) {
            Schema::create('inquiry_new_po_testing', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('po_id')->default(0);
                $table->bigInteger('po_parent_id')->default(0);
                $table->string('type',255)->nullable(0);
                $table->integer('color_id')->default(0);
                $table->integer('size_id')->default(0);
                $table->float('length_qty',10,2)->default(0);
                $table->index(['po_id','color_id','size_id'],'inquiry_new_po_testing_main_idx');
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
