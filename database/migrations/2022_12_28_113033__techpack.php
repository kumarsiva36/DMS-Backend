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
        if (!Schema::hasTable('techpack')) {
            Schema::create('techpack', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id');
                $table->bigInteger('workspace_id');
                $table->bigInteger('user_id')->default(0);
                $table->bigInteger('staff_id')->default(0);
                $table->string('po_no',255)->nullable();
                $table->integer('po_id')->default(0);
                $table->bigInteger('parent_po_id')->default(0);
                $table->string('style_no',255)->nullable();
                $table->integer('article_id')->default(0);
                $table->string('article_name',155)->nullable();
                $table->integer('category_id')->default(0);
                $table->string('category_name',155)->nullable();
                $table->integer('fabric_id')->default(0);
                $table->string('fabric_name',155)->nullable();
                $table->integer('size_id')->default(0);
                $table->string('size_name',155)->nullable();
                $table->enum('is_publish', array('0', '1'))->comment('0=>"No","1"=>"Yes"')->default(0);
                $table->dateTime('published_date')->nullable();
                $table->enum('status', array('0', '1', '2', '3'))->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->bigInteger('created_by');
                $table->string('created_by_type',50)->nullable();
                $table->bigInteger('updated_by');
                $table->string('update_by_type',50)->nullable();
                $table->string('reference_id',255)->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['category_id','company_id','workspace_id']);
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
