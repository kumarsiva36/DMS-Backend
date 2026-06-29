<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncomeTerms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('income_terms')) {
            Schema::create('income_terms', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name',255)->nullable();
                $table->mediumText('description',255)->nullable();
                $table->bigInteger('created_by')->default(0);
                $table->enum('status', array('0', '1', '2', '3'))->default(0)->comment('0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted"');
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();
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
