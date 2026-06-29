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
        if (!Schema::hasTable('techpack_images')) {
            Schema::create('techpack_images', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id');
                $table->bigInteger('workspace_id');
                $table->bigInteger('user_id')->default(0);
                $table->bigInteger('staff_id')->default(0);
                $table->bigInteger('techpack_id');
                $table->bigInteger('techpack_details_id')->default(0);
                $table->string('reference_id',255)->nullable();
                $table->string('techpack_type',255)->nullable();
                $table->string('image_width',100)->nullable();
                $table->string('image_height',100)->nullable();
                $table->string('image')->nullable();
                $table->longText('convert_images')->nullable();
                $table->tinyInteger('comments')->unsigned()->comment('0=>"Label","1"=>"Comments"')->default(0);
                $table->tinyInteger('after_publish_edit')->unsigned()->comment('1=>"Yes","0"=>"No"')->default(0);
                $table->string('edit_reference_id',255)->nullable();
                $table->text('all_reference_id')->default(1);
                $table->tinyInteger('deleted')->unsigned()->comment('1=>"Yes","0"=>"No"')->default(0);
                $table->integer('created_by')->default(0);
                $table->string('created_by_type',20)->nullable();
                $table->integer('updated_by')->default(0);
                $table->string('update_by_type',20)->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['techpack_id','company_id','workspace_id']);
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
