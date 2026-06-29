<?php

namespace App\Http\Controllers\WebSite\Order\PendingTasks;
ini_set('memory_limit', -1);
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Support\Facades\App;
use App\Common\CommonApp;
use App\Models\MultipleDeliveryDates;
use Illuminate\Support\Facades\Storage;

class DownloadPDF extends Controller
{
    /**
     * Download the Pending Tasks
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
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
        App::setLocale($language);
        $pendingTaskArr=[];
        $filterArr=[];
        if(isset($request->noOfDays)){
            $filterArr['dayCount']=abs($request->noOfDays);
            if($request->selector == "<=")
                $filterArr['operator']=">=";
            elseif($request->selector == ">=")
                $filterArr['operator']="<=";
            else
                $filterArr['operator']="=";
        }
        if(isset($request->picId)){
            $staff = Staff::getStaffByID($request->picId);
            $filterArr['pic']=$staff->first_name." ".$staff->last_name;
        }
        $pendingTaskArr['advFilter']=$filterArr;
        $totalPendingTasksCounts=0;
        foreach($orders as $order) {
            $taskDetails=[];
            $taskDetails['orderNo'] = $order->order_no;
            $taskDetails['styleNo'] = $order->style_no;
            $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
            $taskDetails['delivery_date'] = $delivery_date;
            $taskDetails['styleId'] = $order->id;
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
            $forTaskDetails=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['order_id','=',$order->id],
                ['task_accomplished_date', '=' ,NULL],
            ];
            if(isset($request->noOfDays)){
                $forTaskDetails[]=[DB::raw('DATEDIFF(task_schedule_end_date, NOW())'),$request->selector,$request->noOfDays];
             }
             if(isset($request->picId)){
                 $forTaskDetails[]=['task_pic',"=",$request->picId];
             }
            $taskDetail = $this->getTaskDetails($forTaskDetails,$dateFormat,$request);
            $totalPendingTasksCounts+=$taskDetail['taskCount'];
            $taskDetails['pendingCount']=$taskDetail['taskCount'];
            $taskDetails['taskData']=$taskDetail['taskDetails'];
            $taskDetails['orderLastDate'] = date($dateFormat, strtotime($order->packing_end_date));
            if(count($taskDetails['taskData'])>0){
                $pendingTaskArr['orderNo'] = $order->order_no;
                $pendingTaskArr['dateFormat']=$dateFormat;
                $pendingTaskArr['serverURL'] = config('filesystems.disks.s3.url');
                $pendingTaskArr['useLogo'] = $dateFormatAndLanguage['useLogo'];
                //$pendingTaskArr['userLogo'] = $dateFormatAndLanguage['userLogo'];
                $pendingTaskArr['userLogo'] =$dateFormatAndLanguage['useLogo'] !=0 ?  Storage::disk('s3')->temporaryUrl($dateFormatAndLanguage['userLogo'], '+15 minutes') : "";
                // $pendingTaskArr['orderLastDate'] = date('d-m-Y', strtotime($order->packing_end_date));
                $pendingTaskArr['styleCount'] = $this->getNumberOfStyle($whereCondition);
                $pendingTaskArr['pendingTask']=$totalPendingTasksCounts;
                $pendingTaskArr['picDetails']=isset($taskDetail['picDetails'])?
                array_map("unserialize",array_unique(array_map("serialize",$taskDetail['picDetails']))):[];
                $pendingTaskArr['styleDetails'][] = $taskDetails;
            }
        }
        view()->share("pendingTask",$pendingTaskArr);
        $pdf = Pdf::loadView('pendingTaskPDF');
        // $path = public_path() . '/PendingTask/' .$request->orderNo.date('d-m-Y').'.pdf';
        // $pdf->save($path);
        // return response()->download($path);
        $pdf->setPaper('A4', 'portrait');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
        // $canvas = $pdf->getDomPDF()->getCanvas();
        // $imageURL = 'images/dms-log-with-tag.png';
        // $imgWidth = 200;
        // $imgHeight = 150;
        // $x = (200);
        // $y = (300);
        // $canvas->set_opacity(.2,"Multiple");
        // $canvas->image($imageURL, $x, $y, $imgWidth, $imgHeight,$resolution = "high");
        // $pdf->setCanvas($canvas);
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

    /* To get the number of Styles */
    public function getNumberOfStyle($Data){
        $OrderCount = Order::select('id')->where($Data)->get();
        return  count($OrderCount);

    }

    /* To get the Task Details and to filter the data */
    public function getTaskDetails($Data,$dateFormat,$request){
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
        }
        else{
            $taskDetail = OrderTask::where($Data)->select('id','task_title','task_schedule_end_date','task_schedule_start_date',
            'task_pic',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'))
            ->having('noOfDays', "<" ,0)
            ->orderBy("noOfDays",'ASC')->get();
        }
        $tasks['taskCount'] = count($taskDetail);
        if(count($taskDetail) === 0){
            $tasks['taskDetails']=[];
            return $tasks;
        }
        foreach($taskDetail as $task){
            $taskDetails=[];
            $picdetail=[];
            $taskDetails['title'] = $task->task_title;
            $taskDetails['startDate'] = $task->task_schedule_start_date != null ?date($dateFormat,strtotime($task->task_schedule_start_date)) : $task->task_schedule_start_date;
            $taskDetails['scheduledDate'] = $task->task_schedule_end_date != null ?date($dateFormat,strtotime($task->task_schedule_end_date)) : $task->task_schedule_end_date;
            $taskIntervals = $this->dateDifference($task->task_schedule_start_date,$task->task_schedule_end_date,$task->noOfDays);
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
                        $subtaskIntervals = $this->dateDifference($subtask->task_schedule_start_date,
                        $subtask->task_schedule_end_date,$subtask->noOfDays);
                        $subtaskArr['days'] = $subtaskIntervals['delay'];
                        $subtaskArr['type'] = $subtaskIntervals['type'];
                        $subtaskArr["pic"] = explode("||",$this->getPIC($subtask->task_pic))[0];
                        $taskDetails['subtasks'][]=$subtaskArr;
                    }
                }
            }
            $tasks['taskDetails'][]=$taskDetails;
            /* Subtask End */
        }
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
