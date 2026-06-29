<?php

namespace App\Http\Controllers\WebSite\Reports;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\OrderTask;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;
use App\Common\CommonApp;
use Illuminate\Support\Facades\App;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Buyer;
use App\Models\Factory;
use App\Models\PCU;
use App\Models\MultipleDeliveryDates;
use Illuminate\Support\Facades\Storage;

class TaskReports extends Controller
{
    /* Task Reports Controllers*/
    public function taskReports(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'statusFilter'=>'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code" =>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition =[
            ['company_id',"=",$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['status','=','1'],
            ['step_level','=','6']
        ];
        if(isset($request->factory_id)){
            $whereCondition[]=['factory_id','=',$request->factory_id];
        }
        if(isset($request->buyer_id)){
            $whereCondition[]=['buyer_id','=',$request->buyer_id];
        }
        if(isset($request->pcu_id) && $request->pcu_id!=0 && $request->pcu_id!=''){
            $whereCondition[]=['pcu_id','=',$request->pcu_id];
        }
        // $whereCondition[]=['status','=',"1"];


        // if($request->statusFilter === "Deleted"){
        //     array_splice($whereCondition,5);
        //     $whereCondition[]=['status','=',"3"];
        // }
        // else if($request->statusFilter === "Cancelled"){
        //     array_splice($whereCondition,5);
        //     $whereCondition[]=['status','=',"10"];
        // }
        if(isset($request->staff_id) && $request->staff_id > 0){
            $whereCondition1= $whereCondition2 = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $staffRoleHasPermission = Staff::select('role_id','company_id')->where('id',$request->staff_id)->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['permission_id','=','19'];
            $whereCondition1[]=['company_id','=',$request->company_id];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $theOrders = [];
                foreach($involedOrders as $order) {
                    if(isset($request->styleNo)){
                        if($request->styleNo === $order->order_id){
                            $theOrder = Order::where($whereCondition)->where("id", $order->order_id)->first();
                        }
                    }else{
                        $theOrder = Order::where($whereCondition)->where("id", $order->order_id)->first();
                    }
                    if(!empty($theOrder)) {
                        $theOrders[]=$theOrder;
                    }
                }
                $orders=$theOrders;
            }else{
                if(isset($request->styleNo)){
                    $whereCondition[]=['id','=',$request->styleNo];
                }
                $orders = Order::where($whereCondition)->orderBy('id','DESC')->get();
            }
        }else{
            if(isset($request->styleNo)){
                $whereCondition[]=['id','=',$request->styleNo];
            }
            $orders = Order::where($whereCondition)->orderBy('id','DESC')->get();
        }
        $pendingTaskArr=[];
        foreach($orders as $order) {
            $taskDetails=[];
            $taskDetails['orderNo'] = $order->order_no;
            $taskDetails['styleNo'] = $order->style_no;
            $forTaskDetails=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['order_id','=',$order->id],
            ];
            $taskDetail = $this->getTaskDetails($forTaskDetails,$request);
            /* For Each Style Details */
            if(!empty($taskDetail['taskDetails'])){
                $taskDetails['taskData']=$taskDetail['taskDetails'];
                $pendingTaskArr['styleDetails'][] = $taskDetails;
            }
        }
        !empty($pendingTaskArr)?$pendingTaskArr:$pendingTaskArr['styleDetails']=[];
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$pendingTaskArr]);
        return CommonApp::webEncrypt($res);
    }

    /* To get the Task Details and to filter the data */
    public function getTaskDetails($Data,$request){
        $tasks=[];

        /* The Status Filter Starts */
        if($request->statusFilter === "All"){

        }
        else if($request->statusFilter === "Completed"){
            $Data[]=['task_accomplished_date','!=',NULL];
        }
        else if($request->statusFilter === "DelCompletion"){
            $Data[]=['task_accomplished_date','>',DB::raw('task_schedule_end_date')];
        }
        else if($request->statusFilter === "Delay"){
            $Data[]=['task_accomplished_date','=',NULL];
            $Data[]=['task_schedule_end_date','<',date('Y-m-d')];
        }
        else if($request->statusFilter === "Notassign"){
            $Data[]=["task_schedule_start_date","=",NULL];
            $Data[]=['task_schedule_end_date','=',NULL];
         }
        else if($request->statusFilter === "YetToStart"){
            $Data[]=["task_schedule_start_date",">",date("Y-m-d")];

       }
        else if($request->statusFilter === "InProgress"){
            $Data[]=["task_schedule_start_date",'<=',date("Y-m-d")];
            $Data[]=['task_accomplished_date','=',NULL];
        }
        else if($request->statusFilter === "Rescheduled"){
            $Data[]=['rescheduled','=',1];
        }
        else if($request->statusFilter === "PossibleDelay"){
            $Data[]=["task_schedule_start_date",'<=',date("Y-m-d")];
            $Data[]=["task_schedule_end_date",'>=',date("Y-m-d")];
            $Data[]=['task_accomplished_date','=',NULL];
        }

        // $subtasks = OrderTask::where($Data)->where('is_subtask',1)->get();

        /* The Status Filter Ends */
        /* Advanced Filter Starts */
        if(isset($request->pic_id)){
            $Data[]=['task_pic','=',$request->pic_id];
        }
        /* Subtask Starts */
        if((isset($request->aStartDate) && isset($request->aEndDate))&&($request->statusFilter === "Completed"
            || $request->statusFilter === "All" ||$request->statusFilter === "DelCompletion" || $request->statusFilter === "Rescheduled")){
            $subtasks = OrderTask::where($Data)->where('is_subtask',1)
            ->whereBetween('task_accomplished_date',[date("Y-m-d",strtotime($request->aStartDate)),date("Y-m-d",strtotime($request->aEndDate))])
            ->select('id','task_title','task_schedule_end_date','task_schedule_start_date','parent_task_id','subtask_title',
            'task_accomplished_date','task_pic',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'),
            'inprogress_percentage',DB::raw('DATEDIFF(task_schedule_end_date, task_schedule_start_date) as TotalnoOfDays'))->get();
        }
        else{
            $subtasks = OrderTask::where($Data)->where('is_subtask',1)
            ->select('id','task_title','task_schedule_end_date','task_schedule_start_date','parent_task_id','subtask_title',
            'task_accomplished_date','task_pic',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'),
            'inprogress_percentage',DB::raw('DATEDIFF(task_schedule_end_date, task_schedule_start_date) as TotalnoOfDays'))->get();
        }
        /* Subtask Ends */
        $Data[]=['is_subtask','=',0];
        if((isset($request->aStartDate) && isset($request->aEndDate))&&($request->statusFilter === "Completed"
            || $request->statusFilter === "All" ||$request->statusFilter === "DelCompletion" || $request->statusFilter === "Rescheduled")){
            $taskDetail = OrderTask::where($Data)
            ->whereBetween('task_accomplished_date',[date("Y-m-d",strtotime($request->aStartDate)),date("Y-m-d",strtotime($request->aEndDate))])
            ->select('id','task_title','task_schedule_end_date','task_schedule_start_date',
            'task_accomplished_date','task_pic',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'),
            'inprogress_percentage',DB::raw('DATEDIFF(task_schedule_end_date, task_schedule_start_date) as TotalnoOfDays'))->get();
        }
        /* Advanced Filter Ends */
        // if($request->has('noOfDays')){
        //     $taskDetail = OrderTask::where($Data)->select('task_title','task_schedule_end_date','task_accomplished_date',
        //     'task_pic',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'))
        //     ->having('noOfDays', "<" ,0)
        //     ->orderBy("noOfDays",'ASC')->get();
        //     // dd($taskDetail);
        //     if(count($taskDetail)===0){
        //         $tasks['taskDetails']=[];
        //         $tasks['taskCount'] = count($taskDetail);
        //         return $tasks;
        //     }
        // }
        else{
            $taskDetail = OrderTask::where($Data)->select('id','task_title','task_schedule_end_date','task_schedule_start_date',
            'task_accomplished_date','task_pic',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'),
            'inprogress_percentage',DB::raw('DATEDIFF(task_schedule_end_date, task_schedule_start_date) as TotalnoOfDays'))->get();
        }
        $possible_delay=0;
        foreach($taskDetail as $task){
            $taskDetails=[];
            $taskDetails['id'] = $task->id;
            $taskDetails['title'] = $task->task_title;
            $taskDetails['startDate'] = $task->task_schedule_start_date;
            $taskDetails['scheduledDate'] = $task->task_schedule_end_date;
            $taskDetails['actualStartDate'] = $task->actual_start_date;
            $taskDetails['accomplishedDate'] = $task->task_accomplished_date;
            $taskDetails['inprogress_percentage'] = $task->inprogress_percentage;
            if($request->statusFilter === "PossibleDelay"){
                $total_days = (int)$task->TotalnoOfDays+1;
                $per_day_perc = 100/$total_days;
                $remain_perc = $per_day_perc* ((int)$task->noOfDays+1);
                $possible_delay = (($remain_perc+(int)$task->inprogress_percentage) > 99)?0:1;
            }

            if($task->task_accomplished_date === NULL){
                $taskIntervals = $this->dateDifference($task->task_schedule_start_date,$task->task_schedule_end_date,$task->noOfDays);
                $taskDetails['days'] = $taskIntervals['delay'];
                $taskDetails['type'] = $taskIntervals['type'];
            }
            $taskDetails['pic'] = $this->getPIC($task->task_pic);
            if(count($subtasks)>0 || !empty($subtasks)){
                foreach($subtasks as $subtask){
                    if($subtask->parent_task_id === $task->id){
                        $subtaskArr=[];
                        $subtaskArr["id"] = $subtask->id;
                        $subtaskArr["title"] = $subtask->cat_title;
                        $subtaskArr["subtitle"] = $subtask->task_title;
                        $subtaskArr["subtasktitle"] = $subtask->subtask_title;
                        $subtaskArr["startDate"] = $subtask->task_schedule_start_date;
                        $subtaskArr["actualStartDate"] = $subtask->actual_start_date;
                        $subtaskArr["scheduledDate"] = $subtask->task_schedule_end_date;
                        $subtaskArr["accomplishedDate"] = $subtask->task_accomplished_date;
                        $subtaskArr['inprogress_percentage'] = $subtask->inprogress_percentage;
                        if($request->statusFilter === "PossibleDelay"){
                            $total_days = (int)$subtask->TotalnoOfDays+1;
                            $per_day_perc = 100/$total_days;
                            $remain_perc = $per_day_perc* ((int)$subtask->noOfDays+1);
                            $possible_delay = (($remain_perc+(int)$subtask->inprogress_percentage) > 99)?0:1;
                        }

                        if($subtask->task_accomplished_date === NULL){
                            $subtaskIntervals = $this->dateDifference($subtask->task_schedule_start_date,
                            $subtask->task_schedule_end_date,$subtask->noOfDays);
                            $subtaskArr['days'] = $subtaskIntervals['delay'];
                            $subtaskArr['type'] = $subtaskIntervals['type'];
                        }
                        $subtaskArr['pic'] = $this->getPIC($subtask->task_pic);
                        // $l++;
                        if($request->statusFilter === "PossibleDelay"){
                            if($possible_delay===1){
                                $taskDetails['subtasks'][]=$subtaskArr;
                            }

                        }else{
                            $taskDetails['subtasks'][]=$subtaskArr;
                        }

                    }
                }
            }
            if($request->statusFilter === "PossibleDelay"){
                if($possible_delay===1){
                    $tasks['taskDetails'][]=$taskDetails;
                }

            }else{
                $tasks['taskDetails'][]=$taskDetails;
            }


        }
        !empty($tasks)?$tasks : $tasks['taskDetails']=[];
        return $tasks;
    }

    /* To get the PIC Name and ID */
    public function getPIC($data){
        if($data === 0){
            return "";
        }
        else{
            $pic = Staff::where('id',$data)->first();
            return ($pic->first_name." ".$pic->last_name);
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

    public function possibleDelyTaskReports(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code" =>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition =[
            ['company_id',"=",$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['id','=',$request->order_id]
        ];
        if(isset($request->factory_id) && $request->factory_id!=0 && $request->factory_id!=''){
            $whereCondition[]=['factory_id','=',$request->factory_id];
        }
        if(isset($request->buyer_id) && $request->buyer_id!=0 && $request->buyer_id!=''){
            $whereCondition[]=['buyer_id','=',$request->buyer_id];
        }
        if(isset($request->pcu_id) && $request->pcu_id!=0 && $request->pcu_id!=''){
            $whereCondition[]=['pcu_id','=',$request->pcu_id];
        }

        if(isset($request->staff_id) && $request->staff_id > 0){
            $whereCondition1= $whereCondition2 = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $staffRoleHasPermission = Staff::select('role_id','company_id')->where('id',$request->staff_id)->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['permission_id','=','19'];
            $whereCondition1[]=['company_id','=',$request->company_id];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=[['staff_id','=',$request->staff_id],['order_id','=',$request->order_id]];
                $involedOrders = OrderContacts::where($whereCondition2)->count();

                if($involedOrders > 0){
                   $orders = Order::where($whereCondition)->orderBy('id','DESC')->get();
                }

            }else{

                $orders = Order::where($whereCondition)->orderBy('id','DESC')->get();
            }
        }else{

            $orders = Order::where($whereCondition)->orderBy('id','DESC')->get();
        }
        $taskDetail=[];
        foreach($orders as $order) {
            $forTaskDetails=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['order_id','=',$order->id],
            ];
            $request->statusFilter = "PossibleDelay";
            $taskDetail = $this->getTaskDetails($forTaskDetails,$request);

        }
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$taskDetail]);
        return CommonApp::webEncrypt($res);
    }

    public function downloadPossibleDelyTaskReports(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
        $whereCondition =[
            ['company_id',"=",$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['id','=',$request->order_id]
        ];
        if(isset($request->factory_id) && $request->factory_id!=0 && $request->factory_id!=''){
            $whereCondition[]=['factory_id','=',$request->factory_id];
        }
        if(isset($request->buyer_id) && $request->buyer_id!=0 && $request->buyer_id!=''){
            $whereCondition[]=['buyer_id','=',$request->buyer_id];
        }
        if(isset($request->pcu_id) && $request->pcu_id!=0 && $request->pcu_id!=''){
            $whereCondition[]=['pcu_id','=',$request->pcu_id];
        }
        if(isset($request->staff_id) && $request->staff_id > 0){
            $whereCondition1= $whereCondition2 = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $staffRoleHasPermission = Staff::select('role_id','company_id')->where('id',$request->staff_id)->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['permission_id','=','19'];
            $whereCondition1[]=['company_id','=',$request->company_id];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=[['staff_id','=',$request->staff_id],['order_id','=',$request->order_id]];
                $involedOrders = OrderContacts::where($whereCondition2)->count();

                if($involedOrders > 0){
                   $orders = Order::where($whereCondition)->orderBy('id','DESC')->get();
                }

            }else{

                $orders = Order::where($whereCondition)->orderBy('id','DESC')->get();
            }
        }else{
            $orders = Order::where($whereCondition)->orderBy('id','DESC')->get();
        }

        if(isset($request->user_id) && $request->user_id>0){
            $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
            $dateFormat = $dateFormatAndLanguage['dateFormat'];
            $language = $dateFormatAndLanguage['language'];
        }
        else if(isset($request->staff_id) && $request->staff_id>0){
            $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
            $dateFormat = $dateFormatAndLanguage['dateFormat'];
            $language = $dateFormatAndLanguage['language'];
        }
        App::setlocale($language);
        $advFilterArr = [];


        $pendingTaskArr=[];
        $pendingTaskArr['statusFilter']='';
        $pendingTaskArr['dateFormat']=$dateFormat;
        $onlySelectedStyle="";
        foreach($orders as $order) {
            $taskDetails=[];
            if($order->factory_id != NULL){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$order->factory_id]
                ];
                $taskDetails['factory'] = ($this->getDetails($forType,"Factory"))->name;
            }
            if($order->pcu_id != NULL){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$order->pcu_id]
                ];
                $taskDetails['pcu'] = ($this->getDetails($forType,"PCU"))->name;
            }
            if($order->buyer_id != NULL){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$order->buyer_id]
                ];
                $taskDetails['buyer'] =  ($this->getDetails($forType,"Buyer"))->name;
            }
            $taskDetails['orderNo'] = $order->order_no;
            $taskDetails['styleNo'] = $order->style_no;
            $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
            $taskDetails['delivery_date'] = $delivery_date;


            $onlySelectedStyle = $order->style_no;
            $forTaskDetails=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['order_id','=',$order->id],
            ];
            $request->statusFilter = "PossibleDelay";
            $taskDetail = $this->getTaskDetails($forTaskDetails,$request,$dateFormat);

            /* For Each Style Details */
            if(!empty($taskDetail['taskDetails'])){
                $taskDetails['taskData']=$taskDetail['taskDetails'];
                $taskDetails['orderLastDate'] = date($dateFormat, strtotime($order->packing_end_date));
                $pendingTaskArr['styleDetails'][] = $taskDetails;
            }

        }
        if(isset($request->styleNo))
            $advFilterArr['styleNo'] = $onlySelectedStyle;

        $pendingTaskArr['advFilter']=$advFilterArr;
        $pendingTaskArr['serverURL'] = config('filesystems.disks.s3.url');
        $pendingTaskArr['useLogo'] = $dateFormatAndLanguage['useLogo'];
        //$pendingTaskArr['userLogo'] = $dateFormatAndLanguage['userLogo'];
        $pendingTaskArr['userLogo'] =$dateFormatAndLanguage['useLogo'] !=0 ?  Storage::disk('s3')->temporaryUrl($dateFormatAndLanguage['userLogo'], '+15 minutes') : "";
        // return $pendingTaskArr;
        view()->share("pendingTask",$pendingTaskArr);
        $pdf = Pdf::loadView('PossibleDelayTaskReportPDF');

        $pdf->setPaper('A4', 'portrait');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
        return $pdf->download();
    }

    /* To get the Factory/Buyer/PCU details */
    public function getDetails($data, $type){
        if($type === "Factory"){
            $name = Factory::where($data)->select('name')->first();
        }
        if($type === "PCU"){
            $name = PCU::where($data)->select('name')->first();
        }
        if($type === "Buyer"){
            $name = Buyer::where($data)->select('name')->first();
        }
        return $name;
    }
}
