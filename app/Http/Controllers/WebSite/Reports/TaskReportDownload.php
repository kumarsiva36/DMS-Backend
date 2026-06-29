<?php

namespace App\Http\Controllers\WebSite\Reports;

use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use App\Models\Buyer;
use App\Models\Factory;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\OrderTask;
use App\Models\PCU;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Facades\App;
use App\Common\CommonApp;
use App\Models\MultipleDeliveryDates;
use Illuminate\Support\Facades\Storage;

class TaskReportDownload extends Controller
{
    /**
     * Handle the incoming request.
     * Download the Task Report
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
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
        if(isset($request->pcu_id) && $request->pcu_id!=0){
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
        if(isset($request->user_id) && $request->user_id>0){
            $whereConditionToSend=[
                ['company_id','=',$request->company_id],
                ['id','=',$request->user_id]
            ];
            // $dateFormat=GetUserSettings::getPeopleDateFormat("User",$whereConditionToSend);
            // $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"User",$request->user_id);
            $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
            $dateFormat = $dateFormatAndLanguage['dateFormat'];
            $language = $dateFormatAndLanguage['language'];
        }
        else if(isset($request->staff_id) && $request->staff_id>0){
            $whereConditionToSend=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['id','=',$request->staff_id]
            ];
            // $dateFormat=GetUserSettings::getPeopleDateFormat("Staff",$whereConditionToSend);
            // $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"Staff",$request->staff_id);
            $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
            $dateFormat = $dateFormatAndLanguage['dateFormat'];
            $language = $dateFormatAndLanguage['language'];
        }
        App::setlocale($language);
        $advFilterArr = [];
        if(isset($request->aStartDate) && isset($request->aEndDate)){
            $advFilterArr['startDate'] = $request->aStartDate;
            $advFilterArr['endDate'] = $request->aEndDate;
        }
        if(isset($request->pic_id)&& $request->pic_id!=0)
            $advFilterArr['pic']= $this->getPIC($request->pic_id);

        $pendingTaskArr=[];
        $pendingTaskArr['statusFilter']=$request->statusFilter;
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
            $taskDetail = $this->getTaskDetails($forTaskDetails,$request,$dateFormat);
            //dd($taskDetail);
            /* For Each Style Details */
            if(!empty($taskDetail['taskDetails'])){
                $taskDetails['taskData']=$taskDetail['taskDetails'];
                $taskDetails['orderLastDate'] = date($dateFormat, strtotime($order->packing_end_date));
                $pendingTaskArr['styleDetails'][] = $taskDetails;
            }
            // $pendingTaskArr['styleDetails'][] = $taskDetails;
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
        $pdf = Pdf::loadView('TaskReportPDF');
        // $path = public_path() . '/PendingTask/' .$request->orderNo.date('d-m-Y').'.pdf';
        // $pdf->save($path);
        // return response()->download($path);
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

    /* To get the Task Details and to filter the data */
    public function getTaskDetails($Data,$request,$dateFormat){
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
            $Data[]=['task_accomplished_date','=',NULL];
        }
        else if($request->statusFilter === "Rescheduled"){
            $Data[]=['rescheduled','=',1];
        }
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
            'task_accomplished_date','task_pic',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'))->get();
        }
        else{
            $subtasks = OrderTask::where($Data)->where('is_subtask',1)
            ->select('id','task_title','task_schedule_end_date','task_schedule_start_date','parent_task_id','subtask_title',
            'task_accomplished_date','task_pic',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'))->get();
        }
        /* Subtask Ends */
        $Data[]=['is_subtask','=',0];
        if((isset($request->aStartDate) && isset($request->aEndDate))&&($request->statusFilter === "Completed"
            || $request->statusFilter === "All" ||$request->statusFilter === "DelCompletion" || $request->statusFilter === "Rescheduled")){
            $taskDetail = OrderTask::where($Data)
            ->whereBetween('task_accomplished_date',[date("Y-m-d",strtotime($request->aStartDate)),date("Y-m-d",strtotime($request->aEndDate))])
            ->select('id','task_title','task_schedule_end_date','task_schedule_start_date',
            'task_accomplished_date','task_pic',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'))->get();
        }
        /* Advanced Filter Ends */
        else{
        $taskDetail = OrderTask::where($Data)->select('id','task_title','task_schedule_end_date','task_schedule_start_date',
            'task_accomplished_date','task_pic',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'))->get();
        }
        foreach($taskDetail as $task){
            $taskDetails=[];
            $taskDetails['title'] = $task->task_title;
            $taskDetails['startDate'] = $task->task_schedule_start_date != null ?date($dateFormat,strtotime($task->task_schedule_start_date)) : $task->task_schedule_start_date;
            $taskDetails['actualStartDate'] = $task->actual_start_date != null ?date($dateFormat,strtotime($task->actual_start_date)) : $task->actual_start_date;
            $taskDetails['scheduledDate'] = $task->task_schedule_end_date != null ?date($dateFormat,strtotime($task->task_schedule_end_date)) : $task->task_schedule_end_date;
            $taskDetails['accomplishedDate'] = $task->task_accomplished_date != null ?date($dateFormat,strtotime($task->task_accomplished_date)) : $task->task_accomplished_date;
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
                        $subtaskArr["startDate"] = $subtask->task_schedule_start_date != null ?date($dateFormat,strtotime($subtask->task_schedule_start_date)) : $subtask->task_schedule_start_date;
                        $subtaskArr["actualStartDate"] = $subtask->actual_start_date != null ?date($dateFormat,strtotime($subtask->actual_start_date)) : $subtask->actual_start_date;
                        $subtaskArr["scheduledDate"] = $subtask->task_schedule_end_date != null ?date($dateFormat,strtotime($subtask->task_schedule_end_date)) : $subtask->task_schedule_end_date;
                        $subtaskArr["accomplishedDate"] = $subtask->task_accomplished_date != null ?date($dateFormat,strtotime($subtask->task_accomplished_date)) : $subtask->task_accomplished_date;
                        if($subtask->task_accomplished_date === NULL){
                            $subtaskIntervals = $this->dateDifference($subtask->task_schedule_start_date,
                            $subtask->task_schedule_end_date,$subtask->noOfDays);
                            $subtaskArr['days'] = $subtaskIntervals['delay'];
                            $subtaskArr['type'] = $subtaskIntervals['type'];
                        }
                        $subtaskArr["pic"] = $this->getPIC($subtask->task_pic);
                        // $l++;
                        $taskDetails['subtasks'][]=$subtaskArr;
                    }
                }
            }
            $tasks['taskDetails'][]=$taskDetails;
        }
        // dd($tasks);
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
}
