<?php
namespace App\Common;

use App\Models\NotificationDashboard;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

class NotificationAddition
{
    /*************To Add value in the Nofitication dashboard table************/
    public static function addNotifications($data,$notiData){
        $notiJson=[];
        if($data['notification_type']=== "Accomplished"){
            $notiJson['taskName'] = $notiData['taskName'];
            $notiJson['accomplishedOn'] = $notiData['accomplishedOn'];
            $notiJson['accomplishedBy'] = $notiData['accomplishedBy'];
        }
        if($data['notification_type']=== "Reschedule" || $data['notification_type']==="Reassign"){
            $notiJson['taskName'] = $notiData['taskName'];
            if($data['notification_type']=== "Reschedule"){
                $notiJson['to'] = $notiData['to'];
                $notiJson['from'] = $notiData['from'] == "" || $notiData['from'] == 0 ? "None" : $notiData['from'];
            }
            if($data['notification_type']==="Reassign"){
                $too = Staff::where('id',$notiData['to'])->select(DB::raw('CONCAT(first_name," ",last_name) as staffName'))->first();
                $froom = $notiData['from'] == "0"? "" :(Staff::where('id',$notiData['from'])->select(DB::raw('CONCAT(first_name," ",last_name) as staffName'))->first());
                $notiJson['to'] = $too->staffName;
                $notiJson['from'] = $froom == "" ? "None" :$froom->staffName;
            }
            $notiJson['reasons'] = $notiData['reason'];
        }
        if($data['notification_type'] === 'OrderCancelled' || $data['notification_type'] === 'OrderDeleted'  || $data['notification_type'] === 'Order'){
            $notiJson['style_no'] = $data['texts']['styleNo'];
            $notiJson['status']=$notiData['status'];
        }
        $notificationArr=[];
        $notificationArr['company_id']=$data['company_id'];
        $notificationArr['workspace_id'] =$data['workspace_id'];
        $notificationArr['user_id'] =$data['user_id'];
        $notificationArr['staff_id'] =$data['staff_id'];
        $notificationArr['order_id'] =$data['order_id'];
        $notificationArr['notification_title'] =$data['texts']['notification_title'];
        $notificationArr['notification_description'] =$data['texts']['notification_description'];
        $notificationArr['notification_details'] = json_encode($notiJson);
        $notificationArr['notification_type'] =$data['notification_type'];
        $notificationArr['is_read'] =0;
        $notificationArr['notified_user']=0;
        $notificationArr['notification_url']="";
        $notificationArr['notification_status']=1;
        $notificationArr['notify_status_code'] ="";
        $notificationArr['created_at']=date("Y-m-d H:i:s");
        $notificationArr['updated_at']= date("Y-m-d H:i:s");
        NotificationDashboard::insert($notificationArr);
    }
}
