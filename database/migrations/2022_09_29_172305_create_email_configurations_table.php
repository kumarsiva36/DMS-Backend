<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_configurations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('company_id')->unsigned()->index();
            $table->foreign('company_id')->references('id')->on('company_settings');
            $table->bigInteger('workspace_id')->default(0);
            $table->string('mailer',255)->nullable();
            $table->string('host',255)->nullable();
            $table->integer('port')->default(0);
            $table->string('username',255)->nullable();
            $table->string('password',255)->nullable();
            $table->string('encryption',255)->nullable();
            $table->string('from_address',255)->nullable();
            $table->string('from_name',255)->nullable();
            $table->enum('use_config', ['1', '2'])->default('1')->comment('"1"=>"Yes","2"=>"No"');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate()->useCurrent();
            $table->index(['company_id','workspace_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_configurations');
    }
}
