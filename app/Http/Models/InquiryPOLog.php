<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ArticleName;
use App\Models\FabricType;
use App\Models\IncomeTerms;

class InquiryPOLog extends Model
{
    use HasFactory;

    protected $table = 'inquiry_po_log';

    public static function generate_po_log($poID,$request,$after_values=[],$ip_address='',$platform =''){
        $logArry = array();
        $logArry['po_id'] =$poID;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Generate";
        $logArry['after_values'] =json_encode($after_values);
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        InquiryPOLog::insert($logArry);
    }
    public static function edit_po_log($poID,$request,$after_values,$before_values,$ip_address='',$platform =''){
        $logArry = array();
        $logArry['po_id'] =$poID;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Edit";
        $logArry['before_values'] =$before_values;
        $logArry['after_values'] =json_encode($after_values);
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        InquiryPOLog::insert($logArry);
    }

    public static function cancel_po_log($request){
        $logArry = array();
        $logArry['po_id'] =$request->po_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Cancel";
        InquiryPOLog::insert($logArry);
    }

    public static function po_status_update_log($request,$ip_address='',$platform =''){
        $logArry = array();
        $logArry['po_id'] =$request->po_parent_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        $logArry['action'] ="Status";
        InquiryPOLog::insert($logArry);
    }

    public static function po_delete_update_log($request,$ip_address='',$platform =''){
        $logArry = array();
        $logArry['po_id'] =$request->po_parent_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Delete";
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        InquiryPOLog::insert($logArry);
    }

    public static function po_add_media_log($refId,$request,$filedata,$ip_address='',$platform ='',$comments='0'){

        $data=[];
        $data['orginalfilename']=$filedata['orginalfilename'];
        $data['media_type']=$comments==0?$filedata['media_type']:"Comments";

        $logArry = array();
        $logArry['po_id'] =$filedata['parent_po_id'];
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        if($comments==0)
            $logArry['action'] ="File Added";
        else
            $logArry['action'] ="Comments File Added";
        $logArry['after_values'] = json_encode($data);
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        InquiryPOLog::insert($logArry);
    }
    public static function po_delete_media_log($request,$filedata,$ip_address='',$platform =''){

        $data=[];
        $data['orginalfilename']=$filedata['orginalfilename'];
        $data['media_type']=$filedata['media_type'];
        $data['reason']=$request->reason?? '-';

        $logArry = array();
        $logArry['po_id'] =$filedata['parent_po_id'];
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="File Deleted";
        $logArry['before_values'] = json_encode($data);
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        InquiryPOLog::insert($logArry);
    }

    public static function generate_po_comments_log($po_id,$request,$ip_address='',$platform ='',$type=''){
        $logArry = array();
        $logArry['po_id'] =$po_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        if($type=='Audio')
            $logArry['action'] ="Audio Comment Added";
        else
            $logArry['action'] ="Comment Added";
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        InquiryPOLog::insert($logArry);
    }

    public static function po_delete_comments_file_log($request,$filedata,$ip_address='',$platform =''){

        $data=[];
        $data['orginalfilename']=$filedata['orginalfilename'];
        $data['reason']=$request->reason?? '-';

        $logArry = array();
        $logArry['po_id'] =$filedata['po_id'];
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Comments File Deleted";
        $logArry['before_values'] = json_encode($data);
        $logArry['ip_address'] = $ip_address;
        $logArry['platform'] = $platform;
        InquiryPOLog::insert($logArry);
    }



}
