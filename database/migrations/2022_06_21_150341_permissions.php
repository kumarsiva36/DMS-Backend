<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Permissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('module',200);
                $table->string('display_module',200);
                $table->string('sub_module',200);
                $table->string('display_name',200);
                $table->enum('status', ['0', '1'])->default('1')->comment('0=>"Inactive","1"=>"Active"');
                $table->mediumInteger('order_sequence');
                $table->tinyInteger('type')->default('1')->comment('1=>"DMS","2"=>"Inquiry"');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->index(['module','sub_module','status','order_sequence']);
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
