<?php

namespace App\Http\Controllers\WebSite\OrderStatus;

use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use Illuminate\Http\Request;
use App\Models\Buyer;
use App\Models\Factory;
use App\Models\Order;
use App\Models\OrderTask;
use App\Models\PCU;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use App\Common\CommonApp;
use App\Models\UpdateSkuQuantity;
use App\Models\MultipleDeliveryDates;
use DateTime;
use Illuminate\Support\Facades\Storage;

class OrderStatusDownloadPDF extends Controller
{
    /**
     * Handle the incoming request.
     * Download the Order Status PDF
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
        $whereCondition =[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['id','=',$request->orderNo]
        ];
        $orders = Order::where($whereCondition)->get();
        if((isset($request->user_id) && isset($request->staff_id)) && $request->user_id > 0 && $request->staff_id == 0){
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
        else if(isset($request->staff_id) && $request->staff_id > 0){
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
        $totalPendingTasksCounts=0;
        foreach($orders as $order) {
            $taskDetails=[];
            $pendingTaskArr['orderNo'] = $order->order_no;
            $pendingTaskArr['dateFormat']=$dateFormat;
            // $pendingTaskArr['orderLastDate'] = date('d-m-Y', strtotime($order->packing_end_date));
            $pendingTaskArr['styleCount'] = $this->getNumberOfStyle($whereCondition);
            $pendingTaskArr['serverURL'] = config('filesystems.disks.s3.url');
            $pendingTaskArr['useLogo'] = $dateFormatAndLanguage['useLogo'];
            $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
            $pendingTaskArr['delivery_date'] = ($delivery_date!='' && $delivery_date!=null)? date($dateFormat, strtotime($delivery_date)):"-";
            //$pendingTaskArr['userLogo'] = $dateFormatAndLanguage['userLogo'];
            $pendingTaskArr['userLogo'] =$dateFormatAndLanguage['useLogo'] !=0 ?  Storage::disk('s3')->temporaryUrl($dateFormatAndLanguage['userLogo'], '+15 minutes') : "";
            $taskDetails['orderNo'] = $order->order_no;
            $taskDetails['styleNo'] = $order->style_no;
            $taskDetails['styleId'] = $order->id;
            if($order->factory_id != NULL){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$order->factory_id]
                ];
                $taskDetails['factory'] = ($this->getDetails($forType,"Factory"))->name;
            }
            if(isset($order->pcu_id) && $order->pcu_id != NULL){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$order->pcu_id]
                ];
                $taskDetails['pcu'] = ($this->getDetails($forType,"PCU"))->name;
            }
            if(isset($order->buyer_id) && $order->buyer_id != NULL){
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

            ];
            $taskDetail = $this->getTaskDetails($forTaskDetails,$dateFormat);
           // $totalPendingTasksCounts+=$taskDetail['taskCount'];
           // $pendingTaskArr['pendingTask']=$totalPendingTasksCounts;
            //$taskDetails['pendingCount']=$taskDetail['taskCount'];
            $taskDetails['taskData']=$taskDetail['taskDetails'];
            $taskDetails['orderLastDate'] =($order->packing_end_date!='' && $order->packing_end_date!=null)? date($dateFormat, strtotime($order->packing_end_date)):"-";
            $pendingTaskArr['picDetails']=isset($taskDetail['picDetails'])?
            array_map("unserialize",array_unique(array_map("serialize",$taskDetail['picDetails']))):[];
            $pendingTaskArr['styleDetails'][] = $taskDetails;


            $prodData=$cutArr=$sewArr=$packArr=[];
            /* Cut */
            $cutArr['title']="Cutting";
            $cutArr['startDate']=date($dateFormat,strtotime($order->cutting_start_date));
            $cutArr['endDate']=date($dateFormat,strtotime($order->cutting_end_date));
            $cutArr['totalQuantity']=$order->total_quantity;
            $cutArr['updatedQuantity']=$this->getUpdatedProductionSum($order,"Cut");
            $cutArr['pendingQuantity']=$cutArr['totalQuantity'] - $cutArr['updatedQuantity'];
            $cuttingInterval = $this->dateDifference($order->cutting_start_date,$order->cutting_end_date);
            $cutArr['delay']= $cuttingInterval['delay'];
            $cutArr['type']= $cuttingInterval['type'];
            $cutArr['completion']= $order->cutting_completion;
            $prodData[]=$cutArr;
            /* Sew */
            $sewArr['title']="Sewing";
            $sewArr['startDate']=date($dateFormat,strtotime($order->sewing_start_date));
            $sewArr['endDate']=date($dateFormat,strtotime($order->sewing_end_date));
            $sewArr['totalQuantity']=$order->total_quantity;
            $sewArr['updatedQuantity']=$this->getUpdatedProductionSum($order,"Sew");
            $sewArr['pendingQuantity']=$sewArr['totalQuantity'] - $sewArr['updatedQuantity'];
            $sewingInterval=$this->dateDifference($order->sewing_start_date,$order->sewing_end_date);
            $sewArr['delay']=$sewingInterval['delay'];
            $sewArr['type']=$sewingInterval['type'];
            $sewArr['completion']= $order->sewing_completion;
            $prodData[]=$sewArr;
            /* Pack */
            $packArr['title']="Packing";
            $packArr['startDate']=date($dateFormat,strtotime($order->packing_start_date));
            $packArr['endDate']=date($dateFormat,strtotime($order->packing_end_date));
            $packArr['totalQuantity']=$order->total_quantity;
            $packArr['updatedQuantity']=$this->getUpdatedProductionSum($order,"Pack");
            $packArr['pendingQuantity']=$packArr['totalQuantity'] - $packArr['updatedQuantity'];
            $packingInterval = $this->dateDifference($order->packing_start_date,$order->packing_end_date);
            $packArr['delay']= $packingInterval['delay'];
            $packArr['type']= $packingInterval['type'];
            $packArr['completion']= $order->packing_completion;
            $prodData[]=$packArr;
            $pendingTaskArr['productionDetails'] = $prodData;
        }
        //dd($pendingTaskArr);
        view()->share("OrderStatusPDF",$pendingTaskArr);
      //  $pdf =  PDF::loadView('pendingTask', $pendingTaskArr)->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
       $pdf = Pdf::loadView('OrderStatusPDF');
       $pdf->setPaper('A4', 'portrait');
       //return $pdf->stream();
       $pdf->getOptions()->setIsFontSubsettingEnabled(true);
       $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
       return $pdf->download('OrderStatusPDF.pdf');
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
    public function getTaskDetails($Data,$dateFormat){
        $tasks=[];
        $taskDetail = OrderTask::where($Data)->where('is_subtask',0)
        ->select('id','task_title','task_schedule_start_date','task_schedule_end_date','task_accomplished_date',
        'task_pic')->orderBy('task_schedule_end_date','DESC')->get();
        $subTaskDetail = OrderTask::where($Data)->where('is_subtask',1)
        ->select('id','task_title','subtask_title','task_schedule_start_date','task_schedule_end_date','task_accomplished_date',
        'task_pic','parent_task_id')->orderBy('task_schedule_end_date','DESC')->get();
        foreach($taskDetail as $task){
            $taskDetails=[];
            $taskDetails['title'] = $task->task_title;
            $taskDetails['scheduledStartDate'] = $task->task_schedule_start_date != null ?date($dateFormat,strtotime($task->task_schedule_start_date)) : $task->task_schedule_start_date;
            $taskDetails['scheduledEndDate'] = $task->task_schedule_end_date != null?date($dateFormat,strtotime($task->task_schedule_end_date)):$task->task_schedule_end_date;
            $taskDetails['scheduledAccDate'] = $task->task_accomplished_date != null?date($dateFormat,strtotime($task->task_accomplished_date)):$task->task_accomplished_date;
            $taskDetails['pic'] = explode("||",$this->getPIC($task->task_pic))[0];
            $cuttingIntervalv = $this->dateDifference($task->task_schedule_start_date,$task->task_schedule_end_date);
            $taskDetails['days']= $cuttingIntervalv['delay'];
            $taskDetails['type']= $cuttingIntervalv['type'];
            foreach($subTaskDetail as $subtask){
                if($task->id === $subtask->parent_task_id){
                    $subtaskDetails=[];
                    $subtaskDetails['title'] = $subtask->subtask_title;
                    $subtaskDetails['scheduledStartDate'] = $subtask->task_schedule_start_date != null ?date($dateFormat,strtotime($subtask->task_schedule_start_date)) : $subtask->task_schedule_start_date;
                    $subtaskDetails['scheduledEndDate'] = $subtask->task_schedule_end_date != null?date($dateFormat,strtotime($subtask->task_schedule_end_date)):$subtask->task_schedule_end_date;
                    $subtaskDetails['scheduledAccDate'] = $subtask->task_accomplished_date != null?date($dateFormat,strtotime($subtask->task_accomplished_date)):$subtask->task_accomplished_date;
                    $subtaskDetails['pic'] = explode("||",$this->getPIC($subtask->task_pic))[0];
                    $cuttingIntervalve= $this->dateDifference($subtask->task_schedule_start_date,$subtask->task_schedule_end_date);
                    $subtaskDetails['days']= $cuttingIntervalve['delay'];
                    $subtaskDetails['type']= $cuttingIntervalve['type'];
                    $taskDetails['subtasks'][]=$subtaskDetails;
                }
            }
            $tasks['taskDetails'][]=$taskDetails;
        }
        //dd($tasks);
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

    /* To Get The Sum of the updated quantities */
    public function getUpdatedProductionSum($order,$type){
        $whereCondition=[
            ['company_id','=',$order->company_id],
            ['workspace_id','=',$order->workspace_id],
            ['order_id','=',$order->id],
            ['type_of_production','=',$type]
        ];
        $total = UpdateSkuQuantity::where($whereCondition)->sum('updated_quantity');
        return $total;
    }
    /* To Get the day difference */
    public static function dateDifference($startDate,$endDate)
    {
        $lastDate = new DateTime($endDate);
        $startDate = new DateTime($startDate);
        $today = new DateTime(date("Y-m-d"));
        // dd($startDate > $today);
        $interval = [];
        if($startDate > $today){
            $interval['delay'] = (int)$today->diff($startDate)->format("%r%a");
            $interval['type']="YetToBeStarted";
        }
        else if($startDate >= $today){
            $interval['delay'] = (int) 0;
            $interval['type']="StartsToday";
        }
        else{
            $interval['delay'] = (int)$today->diff($lastDate)->format("%r%a");
            $interval['type']="Progress";
        }

        return $interval;
    }
}
