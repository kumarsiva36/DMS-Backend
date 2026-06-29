<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Orders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->unsigned()->index();
                $table->foreign('user_id')->references('id')->on('users');
                $table->bigInteger('company_id')->unsigned()->index();
                $table->foreign('company_id')->references('id')->on('company_settings');
                $table->bigInteger('workspace_id')->index();
                $table->bigInteger('staff_id')->nullable();
                // $table->foreign('workspace_id')->references('id')->on('workspace');
                $table->string('order_no',255);
                $table->string('style_no',255);
                $table->bigInteger('buyer_id')->nullable()->index();
                $table->bigInteger('pcu_id')->nullable()->index();
                $table->bigInteger('factory_id')->nullable()->index();
                $table->bigInteger('fabric_id');
                $table->bigInteger('category_id')->default('0')->nullable();
                $table->bigInteger('article_id');
                $table->date('inquiry_date')->nullable();
                $table->float('order_price', 50, 2)->default('0.00')->nullable();
                $table->tinyInteger('income_terms')->default('0')->nullable();
                $table->bigInteger('total_quantity');
                $table->bigInteger('no_of_deliverys');
                $table->enum('quantity_wise', ['Quantity-Wise', 'SKU-Wise'])->default('Quantity-Wise');
                $table->integer('tolerance_volume')->nullable();
                $table->double('tolerance_perc')->nullable();
                $table->date('cutting_start_date')->nullable();
                $table->date('cutting_end_date')->nullable();
                $table->date('cutting_accomplished_date')->nullable();
                $table->date('sewing_start_date')->nullable();
                $table->date('sewing_end_date')->nullable();
                $table->date('sewing_accomplished_date')->nullable();
                $table->date('packing_start_date')->nullable();
                $table->date('packing_end_date')->nullable();
                $table->date('packing_accomplished_date')->nullable();
                $table->string('ref_img',255)->nullable();
                $table->string('cut_weekoffs',255)->nullable();
                $table->string('sew_weekoffs',255)->nullable();
                $table->string('pack_weekoffs',255)->nullable();
                $table->string('usual_weekoff',255)->nullable();
                $table->string('currency_type',60)->nullable();
                $table->bigInteger('order_task_template')->nullable();
                $table->tinyInteger('task_feeded')->default('0')->nullable();
                $table->integer('pending_tasks')->nullable();
                $table->integer('cutting_completion')->nullable();
                $table->integer('sewing_completion')->nullable();
                $table->integer('packing_completion')->nullable();
                $table->tinyInteger('step_level')->default('0')->nullable();
                $table->enum('is_tolerance_req', ['0', '1'])->default('0')->comment('0=>"Not Required","1"=>"Required"');
                $table->enum('status', ['0', '1', '2', '3','10','11','12'])->default('1')->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted","10"=>"Cancelled","11"=>"Closed","12"=>"Complete"');
                $table->date('completed_on')->nullable();
                $table->enum('completed_user_email', ['0', '1'])->default('0')->comment('0=>"NotSent","1"=>"Sent"');
                $table->string('completed_staff_email',255)->nullable();
                $table->tinyInteger('status_request')->default('0')->nullable();
                // $table->tinyInteger('cut_status')->default('0')->nullable();
                // $table->tinyInteger('sew_status')->default('0')->nullable();
                // $table->tinyInteger('pack_status')->default('0')->nullable();
                $table->string('order_priority',150)->nullable();
                $table->integer('action_done_user_id')->default('0')->nullable();
                $table->integer('action_done_staff_id')->default('0')->nullable();
                $table->dateTime('action_done_at')->nullable();
                $table->date('delivery_date')->nullable();
                //$table->text('delivery_dates')->nullable();
                $table->integer('units')->default('0')->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['order_no','style_no']);

            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
