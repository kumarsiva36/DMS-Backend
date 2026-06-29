<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LanguageTranslator extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('language_translator')) {
            Schema::create('language_translator', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('module_id');
                $table->bigInteger('sub_module_id');
                $table->string('text_name',255);
                $table->longText('lang_en');
                $table->longText('lang_ja');
                $table->longText('lang_zh');
                $table->longText('lang_ko');
                $table->longText('lang_vi');
                $table->longText('lang_km');
                $table->longText('lang_th');
                $table->longText('lang_bn');
                $table->longText('lang_id');
                $table->longText('lang_tr');
                $table->longText('lang_hi');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
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
