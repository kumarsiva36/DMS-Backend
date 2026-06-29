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
        if (!Schema::hasTable('inquiry_factory_feedback')) {
            Schema::create('inquiry_factory_feedback', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('inquiry_id')->default(0);
                $table->integer('company_id')->default(0);
                $table->integer('workspace_id')->default(0);
                $table->integer('buyer_id')->default(0);
                $table->integer('factory_contact_id')->default(0);
                $table->tinyInteger('lowest_price')->default(0);
                $table->text('lowest_price_comments')->nullable();
                $table->tinyInteger('ontime_delivery')->default(0);
                $table->text('ontime_delivery_comments')->nullable();
                $table->tinyInteger('vendor_buyer_relation')->default(0);
                $table->text('vendor_buyer_relation_comments')->nullable();
                $table->tinyInteger('sample_submission')->default(0);
                $table->text('sample_submission_comments')->nullable();
                $table->tinyInteger('communication')->default(0);
                $table->text('communication_comments')->nullable();
                $table->tinyInteger('less_quality_issue')->default(0);
                $table->text('less_quality_issue_comments')->nullable();
                $table->tinyInteger('good_sell_through')->default(0);
                $table->text('good_sell_through_comments')->nullable();
                $table->tinyInteger('collaborative_approach')->default(0);
                $table->text('collaborative_approach_comments')->nullable();
                $table->tinyInteger('is_po_generated')->default(0);
                $table->dateTime('created_at')->default(now());
                $table->dateTime('updated_at')->default(now());
                //$table->index(['inquiry_id','company_id','workspace_id','buyer_id','factory_contact_id']);
                $table->index(
                    ['inquiry_id','company_id','workspace_id','buyer_id','factory_contact_id'],
                    'inq_factory_fb_main_idx'
                );
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
