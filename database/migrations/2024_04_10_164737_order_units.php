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
        if (!Schema::hasTable('order_units')) {
            Schema::create('order_units', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name',255)->nullable();
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->integer('user_id')->default(0);
                $table->integer('staff_id')->default(0);
                $table->tinyInteger('bom_unit')->default(0)->comment("0->Order Unit, 1->BOM Unit");
                $table->tinyInteger('is_default')->default(0)->comment("0=>Yes, 1=>No");
                $table->tinyInteger('status')->default(0)->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->timestamps();
                $table->index(['company_id','workspace_id','bom_unit','is_default','status'],'order_units_main_idx');
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
