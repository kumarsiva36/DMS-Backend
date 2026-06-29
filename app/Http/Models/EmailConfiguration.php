<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EmailConfiguration extends Model
{
    use HasFactory;

    protected $table = 'email_configurations';

    public static function addEmailConfigs($request){
        $emailConfigArr=[];
        $emailConfigArr['from_name']=$request->company_id;
        $emailConfigArr['from_name']=$request->workspace_id;
        $emailConfigArr['mailer']=$request->mailer;
        $emailConfigArr['host']=$request->host;
        $emailConfigArr['port']=$request->port;
        $emailConfigArr['username']=$request->username;
        $emailConfigArr['password']=Hash::make($request->password);
        $emailConfigArr['encryption']=$request->encryption;
        $emailConfigArr['from_address']=$request->from_address;
        $emailConfigArr['from_name']=$request->from_name;
        $emailConfigArr['use_config']=$request->use_config;
        $emailConfigArr['created_at']=date('Y-m-d H:i:s');
        $emailConfigArr['updated_at']=date('Y-m-d H:i:s');
        EmailConfiguration::insert($emailConfigArr);
    }
}
