<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('company_id');
            $table->bigInteger('workspace_id');
            $table->tinyInteger('comment_type')->comment('"1"=>"Request","2"=>"Response"');
            $table->tinyInteger('comment_status')->comment("'1'=>'Approved','2'=>'Rejected','3'=>'Submission','4'=>'ReSubmission'");
            $table->bigInteger('reply_to_id')->default(0);
            $table->bigInteger('user_id');
            $table->bigInteger('staff_id');
            $table->bigInteger('sender_id');
            $table->bigInteger('reciever_id');
            $table->string('sender_name')->nullable();
            $table->string('reciever_name')->nullable();
            $table->string('page_type'); /* Type of the Page as Task Update, Data Input */
            $table->bigInteger('page_id'); /* If Task Update, It Corresponds to Task Id and If Data Input It Corresponds to Order ID */
            $table->string('text_type'); /* Type Of Text */
            $table->text('text'); /* If File, filepath and If text, Its Text */
            $table->text('original_name')->nullable(); /* If File, the original name pf the file */
            $table->bigInteger('file_size')->default(0);
            $table->dateTime('date_time');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->index(['id','company_id','workspace_id','user_id','staff_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chats');
    }
}
