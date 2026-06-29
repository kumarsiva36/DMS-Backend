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
        if (!Schema::hasTable('fabric_supplier_response')) {
            Schema::create('fabric_supplier_response', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('inquiry_id')->default(0);
                $table->integer('supplier_id')->default(0);
                $table->float('price',10,2)->default(0);
                $table->text('comments')->nullable();
                $table->string('updated_by_type',100)->nullable();
                $table->integer('updated_by')->default(0);
                $table->timestamps();
                $table->index(['inquiry_id','supplier_id']);
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
