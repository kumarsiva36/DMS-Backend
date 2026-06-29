<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','mobile_number','ip_address','user_type','company_name','otp','username',
        'otp_generated_time','last_loggedin_time','status','country_id','lang_code','company_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getUserByEmail($request){
        $user = User::where('email',$request->email)->first();
        return $user;
    }

    public static function getUserByID($id){
       $user =  User::where('id',$id)->first();
       return $user;
    }

    public static function logout($request,$header){
        $token = $header->user()->token();
        $userLog = UserHistory::getUserLog($request);
        $userLog->logged_out_datetime = date('Y-m-d H:i:s');
        $userLog->save();
        $token->revoke();
    }
}
