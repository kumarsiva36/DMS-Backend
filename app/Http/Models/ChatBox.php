<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class ChatBox extends Model
{
    use HasFactory;
    protected $connection = 'second_mysql';
    protected $table = 'chatbox';

    public static function getChatList($request){
        $data=[];$i=0;
        $users = ChatBox::where('chat_id','!=',NULL)
        ->where('user_id',$request->user_id)
        ->where('staff_id',$request->staff_id)
        ->select('user_id','staff_id','sender_name',DB::raw(' MAX(id) as id'))
        ->groupBy('chat_id')->orderBy('id','desc')->get();
        if(!empty($users)){
            foreach($users as $user){
                $res = ChatBox::where('id',$user->id)->select('message','sent_by','is_sent','created_at','status','chat_id','company_id','workspace_id')->first();
                $data[$i]['user_id']=$user->user_id;
                $data[$i]['staff_id']=$user->staff_id;
                $data[$i]['sender_name']=$user->sender_name;
                $data[$i]['message']=$res->message;
                $data[$i]['sent_by']=$res->sent_by;
                $data[$i]['is_sent']=$res->is_sent;
                $data[$i]['status']=$res->status;
                $data[$i]['chat_id']=$res->chat_id;
                $data[$i]['company_id']=$res->company_id;
                $data[$i]['workspace_id']=$res->workspace_id;
                $data[$i]['created_at']=date('Y-m-d H:i:s', strtotime($res->created_at));
                $i++;
            }
        }

        return $data;
    }

    public static function get_chat_detail($request){
        $data = ChatBox::where('user_id',$request->user_id)->where('staff_id',$request->staff_id)->where('chat_id',$request->chat_id)->get();
        return $data;
    }

}
