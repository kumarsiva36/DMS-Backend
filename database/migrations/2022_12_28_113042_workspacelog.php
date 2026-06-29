<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Workspacelog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workspace_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->mediumInteger('company_id')->nullable();
            $table->mediumInteger('user_id')->nullable();
            $table->mediumInteger('workspace_id')->nullable();
            $table->string('workspace_name')->nullable();
            $table->string('workspace_type')->nullable();
            $table->string('company_name')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('aws_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workspace_logs');
    }
}
