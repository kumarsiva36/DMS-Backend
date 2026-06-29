<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ActivateDeacivate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activate_deactivate_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('company_id');
            $table->bigInteger('user_id')->default(0);
            $table->bigInteger('staff_id')->default(0);
            $table->string('page_type',255);
            $table->string('action_type',255);
            $table->string('changed_by',255);
            $table->string('user_type')->nullable();
            $table->text('reason')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activate_deactivate_logs');
    }
}
