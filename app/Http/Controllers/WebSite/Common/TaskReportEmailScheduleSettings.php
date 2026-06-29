<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Jobs\TaskReportOrderStatusDailyJob;
use App\Models\Buyer;
use App\Models\EmailScheduleSettings;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\OrderTask;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\UpdateSkuQuantity;
use App\Models\User;
use App\Models\UserPreferences;
use App\Models\EmailScheduleReport;
use App\Models\Factory;
use App\Models\MultipleDeliveryDates;
use App\Models\PCU;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use App\Http\Controllers\WebSite\Company\CompanySettings as CompanyCompanySettings;
use Illuminate\Support\Facades\Storage;
ini_set('memory_limit',-1);
class TaskReportEmailScheduleSettings extends Controller
{
    //

    public static function finishedOrders(){
        $isOrderStatusChecked = EmailScheduleSettings::where('email_schedule_task_id',4)->get();
        //$isOrderStatusChecked = EmailScheduleSettings::where('email_schedule_task_id',4)->where('user_id',8)->get();
        foreach($isOrderStatusChecked as $order){
            if(str_contains($order->days,date('D'))){

                $whereConditions = $whereCondition2 = $whereCondition1 = [
                    ['company_id',"=", $order->company_id],
                    ['workspace_id','=',$order->workspace_id]
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
                $userLogo = CompanyCompanySettings::getUserLogoStatus($order->company_id);
                $companyLogo = $userLogo['useLogo'] == 1 ? $userLogo['userLogo'] : "";
                $companyLogo_url =$companyLogo !="" ?  Storage::disk('s3')->temporaryUrl($companyLogo, '+15 minutes') : "";
                $is_consolidated_mail = $order->is_consolidated_mail ?? 1;
                $whereConditions_sel_orders = [
                    ['company_id',"=", $order->company_id],
                    ['workspace_id','=',$order->workspace_id],
                    ['user_id','=',$order->user_id],
                    ['staff_id','=',$order->staff_id],
                    ['email_schedule_task_id','=',$order->email_schedule_task_id],
                ];
                $order_ids_arr=[];
                $order_ids = EmailScheduleReport::where($whereConditions_sel_orders)->pluck('order_ids')->first();
                if($order_ids!=null){
                    $order_ids_arr = explode(',',$order_ids);
                }

                $orders = Order::whereIN('id',$order_ids_arr)->get();
                $i=0;$details_arr = array();

                if(!empty($orders) && $order->company_id>0){
                foreach($orders as $order){
                    //Log::info($order->order_no);
                    $details=[];
                    $details['to'] = $user->email;
                    $details['userName'] = $user->name;
                    $details['orderNo'] = $order->order_no;
                    $details['styleNo'] = $order->style_no;
                    $details['buyer'] = Buyer::where('id',$order->buyer_id)->pluck('name')->first();
                    $details['pcu'] = PCU::where('id',$order->pcu_id)->pluck('name')->first();
                    $details['factory'] = Factory:: where('id',$order->factory_id)->pluck('name')->first();
                    $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
                    $details['delivery_date'] = $delivery_date;
                    /* Task Details Start*/
                    $Data=[
                        ['order_task_data.company_id','=',$order->company_id],
                        ['order_task_data.workspace_id','=',$order->workspace_id],
                        ['order_task_data.order_id','=',$order->id],
                    ];

                    $taskDetails =  OrderTask::where($Data)
                    ->leftjoin('staff','staff.id','order_task_data.task_pic')
                    ->select('order_task_data.task_title','order_task_data.task_schedule_start_date','order_task_data.task_schedule_end_date','order_task_data.inprogress_percentage',
                    'order_task_data.task_accomplished_date',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'),'staff.first_name','staff.last_name')->get();

                    foreach ($taskDetails as $tasks){
                        $taskData=[];
                        $taskData['taskTitle']=$tasks->task_title;
                        $taskData['pic']=$tasks->first_name." ".$tasks->last_name;
                        $taskData['startDate']=$tasks->task_schedule_start_date != null ? date($dateFormat,strtotime($tasks->task_schedule_start_date)):$tasks->task_schedule_start_date;
                        $taskData['endDate']=$tasks->task_schedule_end_date!=null ? date($dateFormat,strtotime($tasks->task_schedule_end_date)):$tasks->task_schedule_end_date;
                        $taskData['accomplishedDate']=$tasks->task_accomplished_date != null?date($dateFormat,strtotime($tasks->task_accomplished_date)) : $tasks->task_accomplished_date;
                        $taskData['noOfDays']=$tasks->noOfDays;
                        $taskData['inprogress_percentage']=$tasks->inprogress_percentage;
                        $taskData['days'] = NULL;
                        $taskData['type'] = NULL;
                        if($tasks->task_accomplished_date === NULL){
                            $taskIntervals = TaskReportEmailScheduleSettings::TaskdateDifference($tasks->task_schedule_start_date,$tasks->task_schedule_end_date,$tasks->noOfDays);
                            $taskData['days'] = $taskIntervals['delay'];
                            $taskData['type'] = $taskIntervals['type'];
                        }
                        // if($tasks->task_accomplished_date != null && strtotime($tasks->task_accomplished_date)<=strtotime($tasks->task_schedule_end_date)){
                        //     $taskData['status']="Completed";
                        // }
                        // else if($tasks->task_accomplished_date != null && strtotime($tasks->task_accomplished_date)>strtotime($tasks->task_schedule_end_date)){
                        //     $taskData['status']="Delayed Completion";
                        // }
                        // else if($tasks->task_accomplished_date == null && $tasks->task_schedule_end_date!=null && strtotime(date('Y-m-d'))>strtotime($tasks->task_schedule_end_date)){
                        //     $taskData['status']="Delay";
                        // }
                        // else if($tasks->task_accomplished_date == null && $tasks->task_schedule_end_date!=null && strtotime(date('Y-m-d'))<strtotime($tasks->task_schedule_end_date)){
                        //     $taskData['status']="InProgress";
                        // }
                        // else if($tasks->task_schedule_start_date == null && $tasks->task_schedule_end_date == null){
                        //     $taskData['status']="Not Scheduled";
                        // }
                        $details['taskData'][]=$taskData;
                    }
                    /* Task Details End*/;
                    $details['language']=$language;
                    $details['dateFormat']=$dateFormat;
                    $details['companyLogo_url']=$companyLogo_url;
                    $details['type']='Task';
                    $details_arr[$i]=$details;
                    $i++;
                    //dd($details);
                    if($is_consolidated_mail==0){
                        $details_arr['filename'] = "OrderTaskStatus(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";//$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                        $details_arr['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details_arr['filename'];
                        $pdf_generate = TaskReportEmailScheduleSettings::create_mail_pdf($details_arr);
                        if($pdf_generate==1){
                            try{
                                TaskReportOrderStatusDailyJob::dispatch($details_arr);
                            }catch(Exception $e){
                                Log::info($e->getMessage(). ' TASK-->OrderTaskStatus'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                            }
                        }
                        //TaskReportOrderStatusDailyJob::dispatch($details_arr);
                        $i=0;
                    }

                }
                if($is_consolidated_mail==1 && !empty($details_arr)){
                    $details_arr['filename'] = "OrderTaskStatus(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                    $details_arr['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details_arr['filename'];
                    $pdf_generate = TaskReportEmailScheduleSettings::create_mail_pdf($details_arr);
                    if($pdf_generate==1){
                        try{
                            TaskReportOrderStatusDailyJob::dispatch($details_arr);
                        }catch(Exception $e){
                            Log::info($e->getMessage(). ' TASK-->OrderTaskStatus'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                        }
                    }
                    //TaskReportOrderStatusDailyJob::dispatch($details_arr);
                }
            }

            }
        }
    }

    /* To Get the day difference */
    public static function TaskdateDifference($startdate,$endDate,$days)
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

    public static function create_mail_pdf($details){
        $filePath = public_path() . '/Notifications';
        if (!file_exists($filePath)) {
            File::makeDirectory($filePath, 0777, true, true);
        }
        $folderPath = public_path() . '/Notifications/'.date('Y_m_d');
        if (!file_exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true, true);
        }
        $data['responses']=$details;
        $data['dateFormat']=$details[0]['dateFormat'] ?? 'Y-m-d';
        $data['useLogo'] = 0;
        $data['userLogo'] ="";
        if(count($details)>0){
            view()->share("details_arr",$details);
            $pdf = Pdf::loadView('PDF_OrderTaskStatus');
            $pdf->setPaper('A4', 'portrait');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            $pdf->setOption("enable_php", true);
            $path = $folderPath.'/'.$details['filename'];
            $pdf->save($path);
            //return $pdf->download();
        }
        return 1;
    }

    public static function productionReportOrders(){
        $isOrderStatusChecked = EmailScheduleSettings::where('email_schedule_task_id',5)->get();
        //$isOrderStatusChecked = EmailScheduleSettings::where('email_schedule_task_id',5)->where('user_id',8)->get();
        foreach($isOrderStatusChecked as $order){
            if(str_contains($order->days,date('D'))){

                $whereConditions = $whereCondition2 = $whereCondition1 = [
                    ['company_id',"=", $order->company_id],
                    ['workspace_id','=',$order->workspace_id]
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
                $userLogo = CompanyCompanySettings::getUserLogoStatus($order->company_id);
                $companyLogo = $userLogo['useLogo'] == 1 ? $userLogo['userLogo'] : "";
                $companyLogo_url =$companyLogo !="" ?  Storage::disk('s3')->temporaryUrl($companyLogo, '+15 minutes') : "";
                $is_consolidated_mail = $order->is_consolidated_mail ?? 1;
                $whereConditions_sel_orders = [
                    ['company_id',"=", $order->company_id],
                    ['workspace_id','=',$order->workspace_id],
                    ['user_id','=',$order->user_id],
                    ['staff_id','=',$order->staff_id],
                    ['email_schedule_task_id','=',$order->email_schedule_task_id],
                ];
                $order_ids_arr=[];
                $order_ids = EmailScheduleReport::where($whereConditions_sel_orders)->pluck('order_ids')->first();
                if($order_ids!=null){
                    $order_ids_arr = explode(',',$order_ids);
                }

                $orders = Order::whereIN('id',$order_ids_arr)->get();
                $i=0;$details_arr = array();

                if(!empty($orders) && $order->company_id>0){
                    foreach($orders as $order){
                        //Log::info($order->order_no);
                        $details=[];
                        $details['to'] = $user->email;
                        $details['userName'] = $user->name;
                        $details['orderNo'] = $order->order_no;
                        $details['styleNo'] = $order->style_no;
                        $details['buyer'] = Buyer::where('id',$order->buyer_id)->pluck('name')->first();
                        $details['pcu'] = PCU::where('id',$order->pcu_id)->pluck('name')->first();
                        $details['factory'] = Factory:: where('id',$order->factory_id)->pluck('name')->first();
                        $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
                        $details['delivery_date'] = ($delivery_date!=null) ? date($dateFormat,strtotime($delivery_date)):"";

                        /* Production Details Start*/
                        $cutArr=$sewArr=$packArr=[];
                        /* Cut */
                        $cutArr['title']="Cutting";
                        $cutArr['startDate']=$order->cutting_start_date != null ? date($dateFormat,strtotime($order->cutting_start_date)):"";
                        $cutArr['endDate']=$order->cutting_end_date != null ? date($dateFormat,strtotime($order->cutting_end_date)):"";
                        $cutArr['totalQuantity'] = $cutArrTotalQuantity=$order->total_quantity;
                        $cutArr['updatedQuantity'] = $cutArrUpdatedQuantity=TaskReportEmailScheduleSettings::getUpdatedProductionSum($order,"Cut");
                        // $cutArrLastupdatedDate=TaskReportEmailScheduleSettings::getLastUpdatedProductionDate($order,"Cut");
                        $cutArr['pendingQuantity']=$cutArr['totalQuantity'] - $cutArr['updatedQuantity'];
                        $completed_per = ($cutArrUpdatedQuantity/$cutArrTotalQuantity)*100;
                        $cutArr['comp_per']=round($completed_per,2).'%';
                        $cutArr['noOfDays']=TaskReportEmailScheduleSettings::dateDifference($order->cutting_end_date);
                        $cutArr['actualEndDate']=$order->cutting_end_date;
                        $cutArr['accomplishedDate']=$order->cutting_accomplished_date;
                        $cuttingInterval = TaskReportEmailScheduleSettings::TaskdateDifference($order->cutting_start_date,$order->cutting_end_date, $cutArr['noOfDays']);
                        $cutArr['delay']= $cuttingInterval['delay'];
                        $cutArr['type']= $cuttingInterval['type'];
                        //$cutArr['status']=TaskReportEmailScheduleSettings::getTheStatus($order->cutting_start_date,$order->cutting_end_date,$cutArrLastupdatedDate,$cutArrTotalQuantity,$cutArrUpdatedQuantity);
                        //if($cutArr['status']==="Completed" || $cutArr['status']==="Delayed Completion"){
                            $details['prodData'][]=$cutArr;
                        // }
                        /* Sew */
                        $sewArr['title']="Sewing";
                        $sewArr['startDate']=$order->sewing_start_date != null ? date($dateFormat,strtotime($order->sewing_start_date)): "";
                        $sewArr['endDate']=$order->sewing_end_date !=null ? date($dateFormat,strtotime($order->sewing_end_date)) : "";
                        $sewArr['totalQuantity'] = $sewArrTotalQuantity=$order->total_quantity;
                        $sewArr['updatedQuantity'] = $sewArrUpdatedQuantity=TaskReportEmailScheduleSettings::getUpdatedProductionSum($order,"Sew");
                        //$sewArrLastupdatedDate=TaskReportEmailScheduleSettings::getLastUpdatedProductionDate($order,"Sew");
                        $sewArr['pendingQuantity']=$sewArr['totalQuantity'] - $sewArr['updatedQuantity'];
                        $completed_per = ($sewArrUpdatedQuantity/$sewArrTotalQuantity)*100;
                        $sewArr['comp_per']=round($completed_per,2).'%';
                        $sewArr['noOfDays']=TaskReportEmailScheduleSettings::dateDifference($order->sewing_end_date);
                        $sewArr['actualEndDate']=$order->sewing_end_date;
                        $sewArr['accomplishedDate']=$order->cutting_accomplished_date;
                        $sewingInterval = TaskReportEmailScheduleSettings::TaskdateDifference($order->sewing_start_date,$order->sewing_end_date, $sewArr['noOfDays']);
                        $sewArr['delay']= $sewingInterval['delay'];
                        $sewArr['type']= $sewingInterval['type'];
                        //$sewArr['status']=TaskReportEmailScheduleSettings::getTheStatus($order->sewing_start_date,$order->sewing_end_date,$sewArrLastupdatedDate,$sewArrTotalQuantity,$sewArrUpdatedQuantity);
                        //if($sewArr['status']==="Completed" || $sewArr['status']==="Delayed Completion"){
                            $details['prodData'][]=$sewArr;
                        //}
                        /* Pack */
                        $packArr['title']="Packing";
                        $packArr['startDate']=$order->packing_start_date != null ? date($dateFormat,strtotime($order->packing_start_date)) : "";
                        $packArr['endDate']=$order->packing_end_date !=null ? date($dateFormat,strtotime($order->packing_end_date)):"";
                        $packArr['totalQuantity'] = $packArrTotalQuantity=$order->total_quantity;
                        $packArr['updatedQuantity'] = $packArrUpdatedQuantity=TaskReportEmailScheduleSettings::getUpdatedProductionSum($order,"Pack");
                        // $packArrLastupdatedDate=TaskReportEmailScheduleSettings::getLastUpdatedProductionDate($order,"Pack");
                        $packArr['pendingQuantity']=$packArr['totalQuantity'] - $packArr['updatedQuantity'];
                        $completed_per = ($packArrUpdatedQuantity/$packArrTotalQuantity)*100;
                        $packArr['comp_per']=round($completed_per,2).'%';
                        $packArr['noOfDays']=TaskReportEmailScheduleSettings::dateDifference($order->packing_end_date);
                        $packArr['actualEndDate']=$order->packing_end_date;
                        $packArr['accomplishedDate']=$order->cutting_accomplished_date;
                        $packingInterval = TaskReportEmailScheduleSettings::TaskdateDifference($order->packing_start_date,$order->packing_end_date, $packArr['noOfDays']);
                        $packArr['delay']= $packingInterval['delay'];
                        $packArr['type']= $packingInterval['type'];
                        //$packArr['status']=TaskReportEmailScheduleSettings::getTheStatus($order->packing_start_date,$order->packing_end_date,$packArrLastupdatedDate,$packArrTotalQuantity,$packArrUpdatedQuantity);
                        //if($packArr['status']==="Completed" || $packArr['status']==="Delayed Completion"){
                            $details['prodData'][]=$packArr;
                        //}
                        /* Production Details End*/
                        $details['language']=$language;
                        $details['dateFormat']=$dateFormat;
                        $details['companyLogo_url']=$companyLogo_url;
                        $details['type']='Production';
                        $details_arr[$i]=$details;
                        $i++;
                        if($is_consolidated_mail==0){
                            $details_arr['filename'] = "OrderProductionStatus(".date('Y-m-d').")_".rand(100000,999999)."_".$user->id.".pdf";//$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                            $details_arr['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details_arr['filename'];
                            $pdf_generate = TaskReportEmailScheduleSettings::create_mail_pdf($details_arr);
                            if($pdf_generate==1){
                                try{
                                    TaskReportOrderStatusDailyJob::dispatch($details_arr);
                                }catch(Exception $e){
                                    Log::info($e->getMessage(). ' TASK-->OrderProductionStatus'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                                }
                            }
                            //TaskReportOrderStatusDailyJob::dispatch($details_arr);
                            $i=0;
                        }

                    }
                    //dd($details_arr);
                    //Log::info($details_arr);
                    if($is_consolidated_mail==1 && !empty($details_arr)){
                        $details_arr['filename'] = "OrderProductionStatus(".date('Y-m-d').")_".rand(100000,999999)."_".$user->id.".pdf";;
                        $details_arr['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details_arr['filename'];
                        $pdf_generate = TaskReportEmailScheduleSettings::create_mail_pdf($details_arr);
                        if($pdf_generate==1){
                            try{
                                TaskReportOrderStatusDailyJob::dispatch($details_arr);
                            }catch(Exception $e){
                                Log::info($e->getMessage(). ' TASK-->OrderProductionStatus'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                            }
                        }
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
        else if($endDate != null && strtotime(date('Y-m-d'))>strtotime($endDate)){
            $status = "Delay";
        }
        else if(strtotime($startDate)<=strtotime(date('Y-m-d')) && strtotime(date('Y-m-d'))<=strtotime($endDate)){
            $status = "In Progress";
        }
        else if(strtotime($startDate)>strtotime(date('Y-m-d'))){
            $status = "Not Yet Started";
        }
        return $status;
    }

}
