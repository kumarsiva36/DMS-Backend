<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TechpackLog extends Model
{
    use HasFactory;

    protected $table = 'techpack_log';

    public static function generate_techpack_log($techpackID,$request,$ip_address='',$platform =''){
        $logArry = array();
        $logArry['teckpack_id'] =$techpackID;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Generate";
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        TechpackLog::insert($logArry);
    }

    public static function edit_techpack_log($request,$before_values,$after_values,$ip_address='',$platform =''){
        $logArry = array();
        $logArry['teckpack_id'] =$request->teckpack_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Edit";
        $logArry['before_values'] =json_encode($before_values);
        $logArry['after_values'] =json_encode($after_values);
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        TechpackLog::insert($logArry);
    }

    public static function techpack_status_update_log($request,$ip_address='',$platform =''){
        $logArry = array();
        $logArry['teckpack_id'] =isset($request->teckpack_id)?$request->teckpack_id:$request->techpack_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Status";
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        TechpackLog::insert($logArry);
    }

    public static function techpack_delete_update_log($request,$ip_address='',$platform =''){
        $logArry = array();
        $logArry['teckpack_id'] =isset($request->teckpack_id)?$request->teckpack_id:$request->techpack_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Delete";
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        TechpackLog::insert($logArry);
    }
    public static function techpack_add_media_log($refId,$request,$filedata,$ip_address='',$platform =''){

        $data=[];
        $data['orginalfilename']=$filedata['orginalfilename'];
        $data['techpack_type']=$filedata['techpack_type'];
       // $data['teckpack_id']=$filedata['techpack_id'];

        $logArry = array();
        $logArry['teckpack_id'] =$request->techpack_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="File Added";
        $logArry['after_values'] = json_encode($data);
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        TechpackLog::insert($logArry);
    }
    public static function techpack_delete_media_log($request,$filedata,$ip_address='',$platform =''){

        $data=[];
        $data['orginalfilename']=$filedata['orginalfilename'];
        $data['techpack_type']=$filedata['techpack_type'];
       // $data['teckpack_id']=$filedata['techpack_id'];
        $data['reason']=$request->reason?? '-';

        $logArry = array();
        $logArry['teckpack_id'] =$request->techpack_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="File Deleted";
        $logArry['before_values'] = json_encode($data);
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        TechpackLog::insert($logArry);
    }

    public static function update_techpack_comments_log($techpackID,$request,$ip_address='',$platform ='',$before_values,$after_values){
        $logArry = array();
        $logArry['teckpack_id'] =$techpackID;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Comment Added";
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        $logArry['before_values'] =json_encode($before_values);
        $logArry['after_values'] =json_encode($after_values);
        TechpackLog::insert($logArry);
    }



}
