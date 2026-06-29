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
        Schema::create('order_feedback', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('company_id');
            $table->integer('workspace_id');
            $table->string('feedback_given_by',255);
            $table->integer('feedback_given_id');
            $table->string('user_type',255);
            $table->integer('user_id');
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
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_feedback');
    }
};
