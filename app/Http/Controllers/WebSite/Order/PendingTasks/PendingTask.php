<?php

namespace App\Http\Controllers\Website\Order\PendingTasks;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Factory;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\OrderTask;
use App\Models\PCU;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;
use App\Common\CommonApp;

class PendingTask extends Controller
{
    /* To Get the Pending Task Details for the Order */
    public function pendingTasks(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id'=>'required',
            'workspace_id'=>'required',
            'orderNo'=>'required'
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition =[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_no','=',$request->orderNo],
            ['step_level','=','6'],
            ['status','=','1']
        ];
        if(isset($request->staff_id) && $request->staff_id > 0){
            $whereCondition1= $whereCondition2 = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $staffRoleHasPermission = Staff::select('role_id','company_id','workspace_id')->where('id',$request->staff_id)->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['permission_id','=','19'];
            $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
            $whereCondition1[]=['workspace_id','=',$staffRoleHasPermission->workspace_id];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $theOrders = [];
                foreach($involedOrders as $order) {
                    $theOrder = Order::where("id", $order->order_id)->where("step_level","6")->first();
                   if(!empty($theOrder)){
                    if($request->orderNo == $theOrder->order_no) {
                        $theOrders[]=$theOrder;
                    }
                }
                }
                $orders=$theOrders;
            }else{
                $orders = Order::where($whereCondition)->get();
            }
        }else{
            $orders = Order::where($whereCondition)->get();
        }
        $pendingTaskArr=$picArray=[];
        $totalPendingTasksCounts=$totalTasks=$totalNotYetStarted=$totalDelays=$totalLessThanFiveDays
        =$totalFiveToTenDays=$totalGtTenDays=$totalDelayedCompletion=0;
        $pendingTaskArr['styleDetails']=[];
        foreach($orders as $order) {
            $taskDetails=[];
            $pendingTaskArr['orderNo'] = $order->order_no;
            $pendingTaskArr['styleCount'] = $this->getNumberOfStyle($whereCondition);
            $taskDetails['orderNo'] = $order->order_no;
            $taskDetails['styleNo'] = $order->style_no;
            $taskDetails['styleId'] = $order->id;
            // if($order->factory_id != NULL){
            //     $forType = [
            //         ['company_id','=',$request->company_id],
            //         ['workspace_id','=',$request->workspace_id],
            //         ['id','=',$order->factory_id]
            //     ];
            //     $taskDetails['factory'] = ($this->getDetails($forType,"Factory"))->name;
            // }
            // if($order->pcu_id != NULL){
            //     $forType = [
            //         ['company_id','=',$request->company_id],
            //         ['workspace_id','=',$request->workspace_id],
            //         ['id','=',$order->pcu_id]
            //     ];
            //     $taskDetails['pcu'] = ($this->getDetails($forType,"PCU"))->name;
            // }
            // if($order->buyer_id != NULL){
            //     $forType = [
            //         ['company_id','=',$request->company_id],
            //         ['workspace_id','=',$request->workspace_id],
            //         ['id','=',$order->buyer_id]
            //     ];
            //     $taskDetails['buyer'] =  ($this->getDetails($forType,"Buyer"))->name;
            // }
            $forTaskDetails=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['order_id','=',$order->id],
                ['task_accomplished_date', '=' ,NULL],
            ];
            if(isset($request->noOfDays)){
               $forTaskDetails[]=[DB::raw('DATEDIFF(task_schedule_end_date, NOW())'),$request->selector,$request->noOfDays];
            //    dd($forTaskDetails);
            }
            if(isset($request->picId)){
                $forTaskDetails[]=['task_pic',"=",$request->picId];
            }
            $taskDetail = $this->getTaskDetails($forTaskDetails,$request);
            // dd($taskDetail);
            /* To Add the Total to main Array */
            $totalPendingTasksCounts+=$taskDetail['taskCount'];
            $totalTasks+=array_key_exists("totalTaskCount",$taskDetail) ? $taskDetail['totalTaskCount'] : 0;
            $totalDelays+=array_key_exists('noOfDelays',$taskDetail)? $taskDetail['noOfDelays'] : 0;
            $totalNotYetStarted+=array_key_exists('notYetScheduled',$taskDetail)?$taskDetail['notYetScheduled'] : 0;
            $totalLessThanFiveDays+=array_key_exists('lessThanFiveDaysDelay',$taskDetail)?$taskDetail['lessThanFiveDaysDelay']:0;
            $totalFiveToTenDays+=array_key_exists('fivetoTenDaysDelay',$taskDetail)?$taskDetail['fivetoTenDaysDelay']:0;
            $totalGtTenDays+=array_key_exists('gtTenDays',$taskDetail)?$taskDetail['gtTenDays']:0;
            $totalDelayedCompletion+=array_key_exists('delayedCompletion',$taskDetail)?$taskDetail['delayedCompletion']:0;
            $pendingTaskArr['pendingTask']=$totalPendingTasksCounts;
            $pendingTaskArr['totalTasks']=$totalTasks;
            $pendingTaskArr['totalDelays']=$totalDelays;
            $pendingTaskArr['totalNotYetStarted']=$totalNotYetStarted;
            $pendingTaskArr['totalLessThanFiveDays']=$totalLessThanFiveDays;
            $pendingTaskArr['totalFiveToTenDays']=$totalFiveToTenDays;
            $pendingTaskArr['totalGtTenDays']=$totalGtTenDays;
            $pendingTaskArr['totalDelayedCompletion']=$totalDelayedCompletion;

            /* For Each Style Details */
            $taskDetails['pendingCount']=$taskDetail['taskCount'];
            $taskDetails['totalTask']=array_key_exists("totalTaskCount",$taskDetail) ? $taskDetail['totalTaskCount'] : 0;
            $taskDetails['delays']=array_key_exists('noOfDelays',$taskDetail)? $taskDetail['noOfDelays'] : 0;
            $taskDetails['noYetStarted']=array_key_exists('notYetScheduled',$taskDetail)?$taskDetail['notYetScheduled'] : 0;
            $taskDetails['lessThanFiveDaysDelay'] =array_key_exists('lessThanFiveDaysDelay',$taskDetail)?$taskDetail['lessThanFiveDaysDelay']:0;
            $taskDetails['fivetoTenDaysDelay'] =array_key_exists('fivetoTenDaysDelay',$taskDetail)?$taskDetail['fivetoTenDaysDelay']:0;
            $taskDetails['gtTenDays'] =array_key_exists('gtTenDays',$taskDetail)?$taskDetail['gtTenDays']:0;
            $taskDetails['delayedCompletion']=array_key_exists('delayedCompletion',$taskDetail)?$taskDetail['delayedCompletion']:0;
            $taskDetails['taskData']=$taskDetail['taskDetails'];
            /* To Set The PIC Details form the not accomplished task details */
            if(array_key_exists('picDetails',$taskDetail)){
                foreach($taskDetail['picDetails'] as $pic){
                    $picArray[] = $pic;
                }
            }
            count($taskDetails['taskData'])>0 ? $pendingTaskArr['styleDetails'][] = $taskDetails : "";
        }
        $pendingTaskArr['picDetails']=isset($picArray)?
        array_map("unserialize",array_unique(array_map("serialize",$picArray))):[];
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$pendingTaskArr]);
        return CommonApp::webEncrypt($res);
    }

    /* To get the Factory/Buyer/PCU details */
    // public function getDetails($data, $type){
    //     if($type === "Factory"){
    //         $name = Factory::where($data)->select('name')->first();
    //     }
    //     if($type === "PCU"){
    //         $name = PCU::where($data)->select('name')->first();
    //     }
    //     if($type === "Buyer"){
    //         $name = Buyer::where($data)->select('name')->first();
    //     }
    //     return $name;
    // }

    /* To get the number of Styles */
    public function getNumberOfStyle($Data){
        $OrderCount = Order::select('id')->where($Data)->get();
        return  count($OrderCount);

    }

    /* To get the Task Details and to filter the data */
    public function getTaskDetails($Data,$request){
        $tasks=[];
        if(isset($request->noOfDays)){
            $subtasks = OrderTask::where($Data)->where('is_subtask',1)->select('id','task_title','task_schedule_end_date','parent_task_id',
            'task_schedule_start_date','task_accomplished_date','task_pic','subtask_title',
            DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'))
            ->having('noOfDays', "<" ,0)
            ->orderBy("noOfDays",'ASC')->get();
        }else{
            $subtasks = OrderTask::where($Data)->where('is_subtask',1)->select('id','task_title','task_schedule_end_date','parent_task_id',
            'task_schedule_start_date','task_accomplished_date','task_pic','subtask_title',
            DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'))
            ->having('noOfDays', "<" ,0)
            ->orderBy("noOfDays",'ASC')
            ->get();
        }
        $Data[]=['is_subtask',0];
        if(isset($request->noOfDays)){
            $taskDetail = OrderTask::where($Data)->select('id','task_title','task_schedule_end_date','task_accomplished_date',
            'task_schedule_start_date','task_pic',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'))
            ->having('noOfDays', "<" ,0)
            ->orderBy("noOfDays",'ASC')->get();
            // dd($taskDetail);
        }
        else{
            $taskDetail = OrderTask::where($Data)->select('id','task_title','task_schedule_end_date','task_schedule_start_date',
            'task_accomplished_date','task_pic',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'))
            ->having('noOfDays', "<" ,0)
            ->orderBy("noOfDays",'ASC')
            ->get();
        }
        $tasks['taskCount'] = count($taskDetail);
        if(count($taskDetail)===0){
            $tasks['taskDetails']=[];
            $tasks['taskCount'] = count($taskDetail);
            return $tasks;
        }
        // dd( array_splice($Data, 0, 3));
        $whereCondition = array_splice($Data, 0, 3);
        $totalTaskCount = OrderTask::where($whereCondition)->count();
        $tasks['totalTaskCount'] = $totalTaskCount;
        $notYetScheduled = $noOfDelays = $lessThanFiveDaysDelay= $fivetoTenDaysDelay = $gtTenDays =0;
        $delayedCompletion = OrderTask::where($whereCondition)
        ->where('task_accomplished_date','>',DB::raw('task_schedule_end_date'))->count();
        if(empty($taskDetail) || count($taskDetail) == 0){
            $tasks['taskDetails']=[];
        }else{
            foreach($taskDetail as $task){
                $taskDetails=[];
                $picdetail=[];
                $taskDetails['title'] = $task->task_title;
                $taskDetails['startDate'] = $task->task_schedule_start_date;
                $taskDetails['actualStartDate'] = $task->actual_start_date;
                $taskDetails['scheduledDate'] = $task->task_schedule_end_date;
                $taskIntervals = $this->dateDifference(( $task->actual_start_date? $task->actual_start_date:$task->task_schedule_start_date),$task->task_schedule_end_date,$task->noOfDays);
                $taskDetails['days'] = $taskIntervals['delay'];
                $taskDetails['type'] = $taskIntervals['type'];
                $taskDetails['pic'] = explode("||",$this->getPIC($task->task_pic))[0];
                if($taskDetails['pic']!=""){
                    $picdetail['name']=$taskDetails['pic'];
                    $picdetail['id']=explode("||",$this->getPIC($task->task_pic))[1];
                    $tasks['picDetails'][]=$picdetail;
                }
                /* Subtask Start */
                if(count($subtasks)>0 || !empty($subtasks)){
                    foreach($subtasks as $subtask){
                        if($subtask->parent_task_id === $task->id){
                            $subtaskArr=[];
                            $subtaskArr["title"] = $subtask->cat_title;
                            $subtaskArr["subtitle"] = $subtask->task_title;
                            $subtaskArr["subtasktitle"] = $subtask->subtask_title;
                            $subtaskArr["startDate"] = $subtask->task_schedule_start_date;
                            $subtaskArr["actualStartDate"] = $subtask->actual_start_date;
                            $subtaskArr["scheduledDate"] = $subtask->task_schedule_end_date;
                            $subtaskIntervals = $this->dateDifference(($subtask->actual_start_date?$subtask->actual_start_date:$subtask->task_schedule_start_date),
                            $subtask->task_schedule_end_date,$subtask->noOfDays);
                            $subtaskArr['days'] = $subtaskIntervals['delay'];
                            $subtaskArr['type'] = $subtaskIntervals['type'];
                            $subtaskArr["pic"] = explode("||",$this->getPIC($subtask->task_pic))[0];
                            $taskDetails['subtasks'][]=$subtaskArr;
                            if(strtotime($subtask->task_schedule_start_date) > strtotime(date('Y-m-d'))){
                                $notYetScheduled += 1;
                            }
                            if($subtask->noOfDays < 0){
                                $noOfDelays += 1;
                                if(abs($subtask->noOfDays)<5){
                                    $lessThanFiveDaysDelay += 1;
                                }
                                if(abs($subtask->noOfDays)>=5 && abs($subtask->noOfDays)<=10 ){
                                    $fivetoTenDaysDelay += 1;
                                }
                                if(abs($subtask->noOfDays)>10){
                                    $gtTenDays += 1;
                                }
                            }
                        }
                    }
                }
                $tasks['taskDetails'][]=$taskDetails;
                /* Subtask End */
                /* For Additional Data */
                /* if($task->task_schedule_start_date == NULL && $task->task_schedule_end_date == NULL && $task->task_pic == ""){
                    $notYetScheduled += 1;
                } */
                if(strtotime($task->actual_start_date?$task->actual_start_date:$task->task_schedule_start_date) > strtotime(date('Y-m-d'))){
                    $notYetScheduled += 1;
                }
                if($task->noOfDays < 0){
                    $noOfDelays += 1;
                    if(abs($task->noOfDays)<5){
                        $lessThanFiveDaysDelay += 1;
                    }
                    if(abs($task->noOfDays)>=5 && abs($task->noOfDays)<=10 ){
                        $fivetoTenDaysDelay += 1;
                    }
                    if(abs($task->noOfDays)>10){
                        $gtTenDays += 1;
                    }
                }
            }
        }
        $tasks['notYetScheduled']= $notYetScheduled;
        $tasks['noOfDelays']= $noOfDelays;
        $tasks['lessThanFiveDaysDelay']= $lessThanFiveDaysDelay;
        $tasks['fivetoTenDaysDelay']= $fivetoTenDaysDelay;
        $tasks['gtTenDays']= $gtTenDays;
        $tasks['delayedCompletion']= $delayedCompletion;
        return $tasks;
    }

    /* To get the PIC Name and ID */
    public function getPIC($data){
        if($data === 0){
            return ""."||"."";
        }
        else{
            $pic = Staff::where('id',$data)->first();
            return ($pic->first_name." ".$pic->last_name."||".$pic->id);
        }
    }

    /* To Get the day difference */
    public static function dateDifference($startdate,$endDate,$days)
    {
        $lastDate = new DateTime($endDate);
        $startDate = new DateTime($startdate);
        $today = new DateTime(date("Y-m-d"));
        // dd($startDate > $today);
        $interval = [];
        if($startdate === "" || $startdate === null){
            $interval['delay'] = null;
            $interval['type']="";
        }
        else if($startDate > $today){
            $interval['delay'] = (int)$today->diff($startDate)->format("%r%a");
            $interval['type']="YetToBeStarted";
        }
        else if($startDate >= $today){
            $interval['delay'] = (int) 0;
            $interval['type']="StartsToday";
        }
        else{
            $interval['delay'] = $days;
            $interval['type']="Progress";
        }

        return $interval;
    }
}
