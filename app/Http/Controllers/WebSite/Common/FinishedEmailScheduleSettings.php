<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Jobs\FinishedOrderStatusDailyJob;
use App\Models\EmailScheduleSettings;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\OrderTask;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\UpdateSkuQuantity;
use App\Models\User;
use App\Models\UserPreferences;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Facades\Log;

class FinishedEmailScheduleSettings extends Controller
{
    //
    /* Email Completed Orders - Daily */
    public static function finishedOrders(){
        $isOrderStatusChecked = EmailScheduleSettings::where('email_schedule_task_id',3)->get();
        foreach($isOrderStatusChecked as $order){
            if(str_contains($order->days,date('D'))){
                if(date('D')!='Mon')
                {
                    //take the last monday
                    $staticstart = date('Y-m-d',strtotime('last Monday'));
                }else{
                    $staticstart = date('Y-m-d');
                }
                //always next saturday
                if(date('D')!='Sat')
                {
                    $staticfinish = date('Y-m-d',strtotime('next Saturday'));
                }else{
                    $staticfinish = date('Y-m-d');
                }
                $whereConditions = $whereCondition2 = $whereCondition1 = [
                    ['company_id',"=", $order->company_id],
                    ['workspace_id','=',$order->workspace_id],
                ];
                if($order->user_id > 0){
                    $user = User::where('id',$order->user_id)->first();
                    $dateFormat = (UserPreferences::where('user_id',$user->id)->first());
                    $userType = "User";
                    $language = GetUserLanguage::getLanguageOfUserWithId($order->company_id,$order->workspace_id,"User",$user->id);
                }
                if($order->staff_id > 0){
                    $user = Staff::where($whereConditions)->where('id',$order->staff_id)
                    ->select(DB::raw('CONCAT(first_name," ",last_name) as name'),'email','id')->first();
                    $dateFormat = (UserPreferences::where('staff_id',$user->id)->first());
                    $userType = "Staff";
                    $language = GetUserLanguage::getLanguageOfUserWithId($order->company_id,$order->workspace_id,"Staff",$user->id);
                }

                if($dateFormat == NUll | $dateFormat == ""){
                    $dateFormat = "d M Y";
                }else{
                    $dates =$dateFormat->date_format;
                    $dateFormat = $dates;
                }

                /* Check if the Staff has permission to view all the orders */
                if($userType ==="Staff"){
                    $staffRoleHasPermission = Staff::where('id',$user->id)->first();
                    $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
                    $whereCondition1[]=['permission_id','=','19'];
                    $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
                    /* If the Staff has permission to view all orders */
                    if(empty($isPermissionGiven)){
                        $whereCondition2[]=['staff_id','=',$user->id];
                        $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                        $ordersArr=[];
                        foreach($involedOrders as $order){
                            $whereCondition[]=['id','=',$order->order_id];
                            $whereCondition[]=['step_level','=',6];
                            $allOrders = Order::where($whereCondition)->where('status',"12")
                                            ->get();
                            array_pop($whereCondition);
                            foreach ($allOrders as $order){
                                $ordersArr[] = $order;
                            }
                        }
                        $orders = $ordersArr;
                    }else{
                        $orders = Order::where($whereConditions)->where('step_level','=',6)->where('status',"12")->get();
                    }
                }else{
                    $orders = Order::where($whereConditions)->where('step_level','=',6)->where('status',"12")
                    ->where("completed_user_email",'0')->get();
                }
                if(!empty($orders) && $order->company_id>0){
                foreach($orders as $order){
                    $details=[];
                    $details['to'] = $user->email;
                    $details['userName'] = $user->name;
                    $details['orderNo'] = $order->order_no;
                    $details['styleNo'] = $order->style_no;
                    /* Task Details Start*/
                    $Data=[
                        ['order_task_data.company_id','=',$order->company_id],
                        ['order_task_data.workspace_id','=',$order->workspace_id],
                        ['order_task_data.order_id','=',$order->id],
                    ];

                    $taskDetails =  OrderTask::where($Data)
                    ->leftjoin('staff','staff.id','order_task_data.task_pic')
                    ->select('task_title','task_schedule_start_date','task_schedule_end_date','task_accomplished_date','staff.first_name','staff.last_name')
                    ->get();

                    foreach ($taskDetails as $tasks){
                        $taskData=[];
                        $taskData['taskTitle']=$tasks->task_title;
                        $taskData['pic']=$tasks->first_name." ".$tasks->last_name;
                        $taskData['startDate']=$tasks->task_schedule_start_date != null ? date($dateFormat,strtotime($tasks->task_schedule_start_date)):$tasks->task_schedule_start_date;
                        $taskData['endDate']=$tasks->task_schedule_end_date!=null ? date($dateFormat,strtotime($tasks->task_schedule_end_date)):$tasks->task_schedule_end_date;
                        $taskData['accomplishedDate']=$tasks->task_accomplished_date != null?date($dateFormat,strtotime($tasks->task_accomplished_date)) : $tasks->task_accomplished_date;
                        if($tasks->task_accomplished_date != null && strtotime($tasks->task_accomplished_date)<=strtotime($tasks->task_schedule_end_date)){
                            $taskData['status']="Completed";
                            $details['taskData'][]=$taskData;
                        }
                        else if($tasks->task_accomplished_date != null && strtotime($tasks->task_accomplished_date)>strtotime($tasks->task_schedule_end_date)){
                            $taskData['status']="Delayed Completion";
                            $details['taskData'][]=$taskData;
                        }
                    }
                    /* Task Details End*/;
                    /* Production Details Start*/
                    $cutArr=$sewArr=$packArr=[];
                    /* Cut */
                    $cutArr['title']="Cutting";
                    $cutArr['startDate']=$order->cutting_start_date != null ? date($dateFormat,strtotime($order->cutting_start_date)):"";
                    $cutArr['endDate']=$order->cutting_end_date != null ? date($dateFormat,strtotime($order->cutting_end_date)):"";
                    $cutArrTotalQuantity=$order->total_quantity;
                    $cutArrUpdatedQuantity=FinishedEmailScheduleSettings::getUpdatedProductionSum($order,"Cut");
                    $cutArrLastupdatedDate=FinishedEmailScheduleSettings::getLastUpdatedProductionDate($order,"Cut");
                    $cutArr['status']=FinishedEmailScheduleSettings::getTheStatus($order->cutting_start_date,$order->cutting_end_date,$cutArrLastupdatedDate,$cutArrTotalQuantity,$cutArrUpdatedQuantity);
                    if($cutArr['status']==="Completed" || $cutArr['status']==="Delayed Completion"){
                        $details['prodData'][]=$cutArr;
                    }
                    /* Sew */
                    $sewArr['title']="Sewing";
                    $sewArr['startDate']=$order->sewing_start_date != null ? date($dateFormat,strtotime($order->sewing_start_date)): "";
                    $sewArr['endDate']=$order->sewing_end_date !=null ? date($dateFormat,strtotime($order->sewing_end_date)) : "";
                    $sewArrTotalQuantity=$order->total_quantity;
                    $sewArrUpdatedQuantity=FinishedEmailScheduleSettings::getUpdatedProductionSum($order,"Sew");
                    $sewArrLastupdatedDate=FinishedEmailScheduleSettings::getLastUpdatedProductionDate($order,"Sew");
                    $sewArr['status']=FinishedEmailScheduleSettings::getTheStatus($order->sewing_start_date,$order->sewing_end_date,$sewArrLastupdatedDate,$sewArrTotalQuantity,$sewArrUpdatedQuantity);
                    if($sewArr['status']==="Completed" || $sewArr['status']==="Delayed Completion"){
                        $details['prodData'][]=$sewArr;
                    }
                    /* Pack */
                    $packArr['title']="Packing";
                    $packArr['startDate']=$order->packing_start_date != null ? date($dateFormat,strtotime($order->packing_start_date)) : "";
                    $packArr['endDate']=$order->packing_end_date !=null ? date($dateFormat,strtotime($order->packing_end_date)):"";
                    $packArrTotalQuantity=$order->total_quantity;
                    $packArrUpdatedQuantity=FinishedEmailScheduleSettings::getUpdatedProductionSum($order,"Pack");
                    $packArrLastupdatedDate=FinishedEmailScheduleSettings::getLastUpdatedProductionDate($order,"Pack");
                    $packArr['status']=FinishedEmailScheduleSettings::getTheStatus($order->packing_start_date,$order->packing_end_date,$packArrLastupdatedDate,$packArrTotalQuantity,$packArrUpdatedQuantity);
                    if($packArr['status']==="Completed" || $packArr['status']==="Delayed Completion"){
                        $details['prodData'][]=$packArr;
                    }
                    /* Production Details End*/
                    if(in_array("prodData",(array_keys($details))) || in_array("taskData",(array_keys($details)))){
                        $details['language']=$language;
                        // Log::info($details);
                        $orderDetail = Order::where('id',$order->id)->first();
                        if($userType === "User"){
                            $orderDetail->completed_user_email = "1";
                            $orderDetail->save();
                            FinishedOrderStatusDailyJob::dispatch($details);
                        }
                        if($userType === "Staff"){
                            $staffList = $orderDetail->completed_staff_email;
                            if($staffList === "" || $staffList === NULL){
                                $staffList = $user->id.",";
                                $orderDetail->completed_staff_email = $staffList;
                                $orderDetail->save();
                                FinishedOrderStatusDailyJob::dispatch($details);
                            }
                            else{
                                if(!str_contains($staffList,$user->id.",")){
                                    $staffList = $staffList.$user->id.",";
                                    $orderDetail->completed_staff_email = $staffList;
                                    $orderDetail->save();
                                    FinishedOrderStatusDailyJob::dispatch($details);
                                }
                            }
                        }
                    }
                    dd($details);
                }

            }

            }
        }
    }

    /* To Get The Sum of the updated quantities */
    public static function getUpdatedProductionSum($order,$type){
        $whereCondition=[
            ['company_id','=',$order->company_id],
            ['workspace_id','=',$order->workspace_id],
            ['order_id','=',$order->id],
            ['type_of_production','=',$type]
        ];
        $total = UpdateSkuQuantity::where($whereCondition)->sum('updated_quantity');
        return (int)$total;
    }
    /* To get last updated date */
    public static function getLastUpdatedProductionDate($order,$type){
        $whereCondition=[
            ['company_id','=',$order->company_id],
            ['workspace_id','=',$order->workspace_id],
            ['order_id','=',$order->id],
            ['type_of_production','=',$type]
        ];
        $lastDate = UpdateSkuQuantity::where($whereCondition)->orderBy('updated_at','DESC')->first();
        if(empty($lastDate)){
            return 0;
        }else{
            $lastDate = date('Y-m-d',strtotime($lastDate->updated_at));
            return $lastDate;
        }
    }

    /* To Get the day difference */
    public static function dateDifference($endDate)
    {
        $lastDate = new DateTime($endDate);
        $today = new DateTime(date("Y-m-d"));

        $interval = $today->diff($lastDate)->format("%r%a");

        return (int)$interval;
    }

    /*To get the status for production*/
    public static function getTheStatus($startDate,$endDate,$lastUpdatedDate,$totalCount,$updatedCount){
        $status="";
        if($totalCount === $updatedCount && strtotime($lastUpdatedDate)<=strtotime($endDate)){
            $status = "Completed";
        }
        else if($totalCount === $updatedCount && strtotime($lastUpdatedDate)>strtotime($endDate)){
            $status = "Delayed Completion";
        }
        // else if($endDate != null && strtotime(date('Y-m-d'))>strtotime($endDate)){
        //     $status = "Delay";
        // }
        // else if(strtotime($startDate)<=strtotime(date('Y-m-d')) && strtotime(date('Y-m-d'))<=strtotime($endDate)){
        //     $status = "In Progress";
        // }
        // else if(strtotime($startDate)>strtotime(date('Y-m-d'))){
        //     $status = "Not Yet Started";
        // }
        return $status;
    }

}
