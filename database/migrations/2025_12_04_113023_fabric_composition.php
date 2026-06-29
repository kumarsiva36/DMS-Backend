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
        if (!Schema::hasTable('fabric_composition')) {
            Schema::create('fabric_composition', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name',255)->nullable();
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->integer('user_id')->default(0);
                $table->integer('staff_id')->default(0);
                $table->enum('is_default', ['0', '1'])->default('0')->comment('0=>"Yes","1"=>"No"');
                $table->enum('status', ['0', '1','2'])->default('1')->comment('0=>"default","1"=>"Active","2"=>"Deactivated"');
                $table->string('inquiry_reference_id',255)->nullable();
                $table->integer('created_by')->default(0);
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['name','company_id','workspace_id','is_default','status'],'fabric_composition_idx');
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
