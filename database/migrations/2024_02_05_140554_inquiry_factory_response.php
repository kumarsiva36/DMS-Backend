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
        if (!Schema::hasTable('inquiry_factory_response')) {
            Schema::create('inquiry_factory_response', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('inquiry_id')->default(0);
                $table->integer('factory_id')->default(0);
                $table->integer('factory_contact_id')->default(0);
                $table->float('price')->default(0.0);
                $table->text('comments')->nullable();
                $table->text('notification_read_by')->nullable();
                $table->string('updated_by_type',255)->nullable();
                $table->tinyInteger('is_po_generated')->default(0);
                $table->integer('updated_by')->default(0);
                $table->timestamps();
                $table->index(['inquiry_id','factory_id','factory_contact_id']);
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
