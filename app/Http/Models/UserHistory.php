<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHistory extends Model
{
    use HasFactory;

    protected $table = 'user_logging_history';

    public static function getStaffLog($id){
       $log= UserHistory::where('logging_user_id',$id)->where('login_user_type',"Staff")
        ->where('login_status',"Success")->orderBy('created_at','desc')->first();
        return $log;
    }
    public static function getUserLog($request){
       $log= UserHistory::where('logging_user_id',$request->user_id)->where('login_user_type',"User")
       ->where('login_status',"Success")->orderBy('created_at','desc')->first();
        return $log;
    }
}
