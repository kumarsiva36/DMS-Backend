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
        if (!Schema::hasTable('inquiry_material_label')) {
            Schema::create('inquiry_material_label', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('po_id')->default(0);
                $table->integer('inquiry_id')->default(0);
                $table->string('reference_id',155)->nullable();
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->string('type',255)->nullable();
                $table->text('content')->nullable();
                $table->string('content_type',25)->nullable();
                $table->tinyInteger('status')->default(0)->comment('0-open, 1-close,2-approved,3-rejected');
                $table->tinyInteger('publish_status')->default(1)->comment('0-unpublish, 1-published');
                $table->string('id_type',25)->nullable();
                $table->integer('id_value')->default(0);
                $table->integer('user_id')->default(0);
                $table->string('user_type',10)->nullable();
                $table->integer('updated_user_id')->default(0);
                $table->string('updated_user_type',10)->nullable();
                $table->string('orginalfilename',255)->nullable();
                $table->float('filesize')->default(0.0);
                $table->integer('vendor_id')->default(0);
                $table->timestamps();
                $table->index(['po_id','inquiry_id','company_id','workspace_id'],'inquiry_material_label_main_idx');
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
