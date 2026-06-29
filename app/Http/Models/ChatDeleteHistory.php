<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatDeleteHistory extends Model
{
    use HasFactory;

    protected $table = 'chat_delete_histories';

    /* Add Chat Delete Log */
    public static function chatDeleteLog($data,$request){
        $chatArr=[];
        $chatArr['company_id'] = $data->company_id;
        $chatArr['workspace_id'] = $data->workspace_id;
        $chatArr['comment_type'] = $data->comment_type;
        $chatArr['comment_status'] = $data->comment_status;
        $chatArr['reply_to_id'] = $data->reply_to_id;
        $chatArr['chat_id'] = $data->id;
        $chatArr['user_id'] = $data->user_id;
        $chatArr['staff_id'] = $data->staff_id;
        $chatArr['sender_id'] = $data->sender_id;
        $chatArr['reciever_id'] = $data->reciever_id;
        $chatArr['sender_name'] = $data->sender_name ?? null;
        $chatArr['reciever_name'] = $data->reciever_name ?? null;
        $chatArr['page_type'] = $data->page_type;
        $chatArr['page_id'] = $data->page_id;
        $chatArr['text_type'] = $data->text_type;
        $chatArr['text'] = $data->text;
        $chatArr['original_name'] = $data->original_name??null;
        $chatArr['deleted_by'] = $request->the_user_id;
        $chatArr['user_type'] = $request->user_type;
        $chatArr['date_time'] = $data->date_time;
        $chatArr['created_at'] = $data->created_at;
        $chatArr['updated_at'] = $data->updated_at;
        $chatArr['deleted_at'] = date('Y-m-d H:i:s');
        ChatDeleteHistory::insert($chatArr);
    }
}
