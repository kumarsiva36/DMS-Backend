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
        if (!Schema::hasTable('sam_report_settings')) {
            Schema::create('sam_report_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->default(0);
                $table->bigInteger('workspace_id')->default(0);
                $table->bigInteger('order_id')->default(0);
                $table->string('order_no',255)->nullable();
                $table->string('style_no',255)->nullable();
                $table->string('production_type',15)->nullable();
                $table->integer('shift_id')->default(0);
                $table->string('shift_value',255)->nullable();
                $table->integer('unit_id')->default(0);
                $table->integer('unit_value')->default(0);
                $table->double('sam_value')->default(0);
                $table->integer('supervisor_id')->default(0);
                $table->string('supervisor_name',255)->nullable();
                $table->integer('line_no_id')->default(0);
                $table->integer('line_no_value')->default(0);
                $table->integer('no_of_tailors')->default(0);
                $table->integer('no_of_helpers')->default(0);
                $table->double('tailor_salary')->default(0);
                $table->double('helper_salary')->default(0);
                $table->date('report_date')->nullable();
                $table->time('from_time')->nullable();
                $table->time('to_time')->nullable();
                $table->tinyInteger('break_hours')->default(0);
                $table->tinyInteger('additional_hours')->default(0)->comment("0-No,1-Yes");
                $table->time('additional_from_time')->nullable();
                $table->time('additional_to_time')->nullable();
                $table->tinyInteger('additional_salary_type')->default(1)->comment("1-Flat, 2-Percentage");
                $table->double('additional_tailor_salary')->default(0);
                $table->double('additional_helper_salary')->default(0);
                $table->double('factory_factor')->default(1);
                $table->integer('alert_percentage')->default(50);
                $table->integer('user_id')->default(0);
                $table->integer('staff_id')->default(0);
                $table->integer('updated_user_id')->default(0);
                $table->integer('updated_staff_id')->default(0);
                $table->timestamps();
                $table->index(['company_id','workspace_id','order_id','production_type','shift_id','unit_id'],'sam_report_settings_main_idx');
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
