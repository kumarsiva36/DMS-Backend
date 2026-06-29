<?php
namespace App\Common;

use App\Models\Order;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

class NotificationText
{
    /************** To Get the Reschedule Texts to store in it in the database ****************/
    public static function toGetRescheduleTexts($arr){
        $data=[];
        if($arr['type'] === "Reschedule"){
            $data['notification_title']="Task Reschedule Notification";
            $data['notification_description']="Kindly be informed that the task has been rescheduled to ".$arr['to'].". Task Name: ".$arr['taskName']."
             Original Planned Date: ".$arr['from']." Rescheduled Date: ".$arr['to']." Reason : ".$arr['reason'].".";
        }
        if($arr['type'] === "Reassign"){
            $too = Staff::where('id',$arr['to'])->select(DB::raw('CONCAT(first_name," ",last_name) as staffName'))->first();
            $froom = $arr['from'] == 0 ? "" :(Staff::where('id',$arr['from'])->select(DB::raw('CONCAT(first_name," ",last_name) as staffName'))->first());
            $data['notification_title']="Task Reassign Notification";
            $data['notification_description']="Kindly be informed that the task has been re-assigned to ".$too->staffName.". Task Name: ".$arr['taskName']."
             Original PIC: ".($froom == ""?"None":$froom->staffName)." Reassigned PIC: ".$too->staffName." Reason : ".$arr['reason'].".";
        }

        return $data;
    }

    /************** To Get the Accomplished Task Texts to store in it in the database ****************/
    public static function toGetAccomplishedTexts($arr){
        $data=[];
        $data['notification_title']="Task Accomplished Notification";
        $data['notification_description']="The Task ".$arr['taskName']." is accomplished on ".$arr['accomplishedOn']." by "
        .$arr['accomplishedBy'].".";
        return $data;
    }

    /**************** To Get Order Status *********************/
    public static function toGetOrderStatus($arr){
        $theOrder = Order::where('id',$arr['orderId'])->first();
        $data=[];
        $data['styleNo'] = $theOrder->style_no;
        if($arr['status']=== 'cancel'){
            $data['notification_title']="Order Cancelled Notification";
            $data['notification_description']="The Style No ".$theOrder['style_no']." has been Cancelled";
        }else{
            $data['notification_title']="Order Deletion Notification";
            $data['notification_description']="The Style No ".$theOrder['style_no']." has been Deleted";
        }

        return $data;
    }
}
