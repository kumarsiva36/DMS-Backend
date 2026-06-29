<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Jobs\OrderStatusDailyJob;
use App\Models\Buyer;
use App\Models\EmailScheduleSettings as ModelsEmailScheduleSettings;
use App\Models\Factory;
use App\Models\MultipleDeliveryDates;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\OrderTask;
use App\Models\PCU;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\UpdateSkuQuantity;
use App\Models\User;
use App\Models\UserPreferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use App\Http\Controllers\WebSite\Company\CompanySettings as CompanyCompanySettings;
use Illuminate\Support\Facades\Storage;
ini_set('memory_limit',-1);
class EmailScheduleSettings extends Controller
{
    //
    /* Email Order Status Daily */
    public static function orderStatus(){
        $isOrderStatusChecked = ModelsEmailScheduleSettings::where('email_schedule_task_id',1)->get();
        //$isOrderStatusChecked = ModelsEmailScheduleSettings::where('email_schedule_task_id',1)->where('user_id','8')->get();
        foreach($isOrderStatusChecked as $order){
            // Log::info([$order->days]);
            if(str_contains($order->days,date('D')))
            {
                // Log::info("I am Inside the Loop");
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
                if($dateFormat == NUll || $dateFormat == ""){
                    $dateFormat = "d M Y";
                }else{
                    $dates =$dateFormat->date_format;
                    $dateFormat = $dates;
                }
                $userLogo = CompanyCompanySettings::getUserLogoStatus($order->company_id);
                $companyLogo = $userLogo['useLogo'] == 1 ? $userLogo['userLogo'] : "";
                $companyLogo_url =$companyLogo !="" ?  Storage::disk('s3')->temporaryUrl($companyLogo, '+15 minutes') : "";
                //$dateFormat = "d M Y";
                $is_consolidated_mail = $order->is_consolidated_mail ?? 1;
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
                        $whereCondition[]=['step_level','=',6];
                        $whereCondition[]=['status','=',"1"];
                        foreach($involedOrders as $order){
                            $whereCondition[]=['id','=',$order->order_id];
                            $allOrder = Order::where($whereCondition)
                                            ->first();
                            // Log::info("Order");
                            array_pop($whereCondition);
                            $ordersArr[] = $allOrder;
                        }
                        $orders = $ordersArr;
                    }else{
                        $orders = Order::where($whereConditions)->where('step_level','=',6)->where('status',"1")->get();
                    }
                }else{
                    $orders = Order::where($whereConditions)->where('step_level','=',6)->where('status',"1")->get();
                }

                //$orders = Order::where('step_level','=',6)->where('order_no',"ORD005")->where('status',"1")->get();

                $i=0;$details_arr = array();
                if(!empty($orders) && $order->company_id>0){
                    foreach($orders as $order){
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

                        $taskDetails =  OrderTask::where($Data)->where('task_schedule_start_date','!=',NULL)->where('task_schedule_end_date','!=',NULL)
                        ->leftjoin('staff','staff.id','order_task_data.task_pic')
                        ->select('task_title','task_schedule_start_date','task_schedule_end_date',
                        'task_accomplished_date',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'),'staff.first_name','staff.last_name')
                        ->get();

                        foreach ($taskDetails as $tasks){

                            $taskData=[];
                            $taskData['taskTitle']=$tasks->task_title;
                            $taskData['pic']=$tasks->first_name." ".$tasks->last_name;
                            $taskData['startDate']=($tasks->task_schedule_start_date != null || $tasks->task_schedule_start_date != '') ? date($dateFormat,strtotime($tasks->task_schedule_start_date)):$tasks->task_schedule_start_date;
                            $taskData['endDate']=($tasks->task_schedule_end_date!=null || $tasks->task_schedule_end_date!='') ? date($dateFormat,strtotime($tasks->task_schedule_end_date)):$tasks->task_schedule_end_date;
                            $taskData['accomplishedDate']=$tasks->task_accomplished_date != null?date($dateFormat,strtotime($tasks->task_accomplished_date)) : $tasks->task_accomplished_date;
                            $taskData['noOfDays']=$tasks->noOfDays;
                            $taskData['days'] = NULL;
                            $taskData['type'] = NULL;
                            if($tasks->task_accomplished_date === NULL){
                                $taskIntervals = EmailScheduleSettings::TaskdateDifference($tasks->task_schedule_start_date,$tasks->task_schedule_end_date,$tasks->noOfDays);
                                $taskData['days'] = $taskIntervals['delay'];
                                $taskData['type'] = $taskIntervals['type'];
                            }
                            // if($tasks->task_schedule_start_date == null && $tasks->task_schedule_end_date == null){
                            //     $taskData['status']="Not Yet Scheduled";
                            // }
                            // else if($tasks->task_accomplished_date === null && strtotime(date('Y-m-d'))>strtotime($tasks->task_schedule_end_date)){
                            //     $taskData['status']="Delay";
                            // }
                            // else if($tasks->task_accomplished_date != null && strtotime($tasks->task_accomplished_date)>strtotime($tasks->task_schedule_end_date)){
                            //     $taskData['status']="Delayed Completion";
                            // }
                            // else if($tasks->task_accomplished_date === null && strtotime(date('Y-m-d'))>=strtotime($tasks->task_schedule_start_date)){
                            //     $taskData['status']="In Progress";
                            // }
                            // else if($tasks->task_accomplished_date === null && strtotime(date('Y-m-d'))<strtotime($tasks->task_schedule_start_date)){
                            //     $taskData['status']="Not Yet Started";
                            // }
                            // else if($tasks->task_accomplished_date != null && strtotime($tasks->task_accomplished_date)<=strtotime($tasks->task_schedule_end_date)){
                            //     $taskData['status']="Completed";
                            //    // $details['taskData'][]=$taskData;
                            // }
                            $details['taskData'][]=$taskData;
                        }
                        /* Task Details End*/
                        /* Production Details Start*/
                        $prodData=$cutArr=$sewArr=$packArr=[];
                        /* Cut */
                        $cutArr['title']="Cutting";
                        $cutArr['startDate']=$order->cutting_start_date != null ? date($dateFormat,strtotime($order->cutting_start_date)):"";
                        $cutArr['endDate']=$order->cutting_end_date != null ? date($dateFormat,strtotime($order->cutting_end_date)):"";
                        $cutArr['totalQuantity']=$order->total_quantity;
                        $cutArr['actualEndDate']=$order->cutting_end_date;
                        $cutArr['accomplishedDate']=$order->cutting_accomplished_date;
                        $cutArr['updatedQuantity']=EmailScheduleSettings::getUpdatedProductionSum($order,"Cut");
                        // $cutArr['lastupdatedDate']=EmailScheduleSettings::getLastUpdatedProductionDate($order,"Cut");
                        $cutArr['pendingQuantity']=$cutArr['totalQuantity'] - $cutArr['updatedQuantity'];
                        $cutArr['noOfDays']=EmailScheduleSettings::dateDifference($order->cutting_end_date);
                        //$cutArr['status']=EmailScheduleSettings::getTheStatus($order->cutting_start_date,$order->cutting_end_date,$order->cutting_accomplished_date,$cutArr['totalQuantity'],$cutArr['updatedQuantity']);
                        $cuttingInterval = EmailScheduleSettings::TaskdateDifference($order->cutting_start_date,$order->cutting_end_date, $cutArr['noOfDays']);
                        $cutArr['delay']= $cuttingInterval['delay'];
                        $cutArr['type']= $cuttingInterval['type'];
                        // if($cutArr['pendingQuantity'] >0){
                            $prodData[]=$cutArr;
                        // }
                        /* Sew */
                        $sewArr['title']="Sewing";
                        $sewArr['startDate']=$order->sewing_start_date != null ? date($dateFormat,strtotime($order->sewing_start_date)): "";
                        $sewArr['endDate']=$order->sewing_end_date !=null ? date($dateFormat,strtotime($order->sewing_end_date)) : "";
                        $sewArr['totalQuantity']=$order->total_quantity;
                        $sewArr['updatedQuantity']=EmailScheduleSettings::getUpdatedProductionSum($order,"Sew");
                        $sewArr['actualEndDate']=$order->sewing_end_date;
                        $sewArr['accomplishedDate']=$order->sewing_accomplished_date;
                        // $sewArr['lastupdatedDate']=EmailScheduleSettings::getLastUpdatedProductionDate($order,"Sew");
                        $sewArr['pendingQuantity']=$sewArr['totalQuantity'] - $sewArr['updatedQuantity'];
                        $sewArr['noOfDays']=EmailScheduleSettings::dateDifference($order->sewing_end_date);
                        //$sewArr['status']=EmailScheduleSettings::getTheStatus($order->sewing_start_date,$order->sewing_end_date,$order->sewing_accomplished_date,$sewArr['totalQuantity'],$sewArr['updatedQuantity']);
                        $sewingInterval=EmailScheduleSettings::TaskdateDifference($order->sewing_start_date,$order->sewing_end_date,$sewArr['noOfDays']);
                        $sewArr['delay']=$sewingInterval['delay'];
                        $sewArr['type']=$sewingInterval['type'];
                        // if($sewArr['pendingQuantity'] >0){
                            $prodData[]=$sewArr;
                        // }
                        /* Pack */
                        $packArr['title']="Packing";
                        $packArr['startDate']=$order->packing_start_date != null ? date($dateFormat,strtotime($order->packing_start_date)) : "";
                        $packArr['endDate']=$order->packing_end_date !=null ? date($dateFormat,strtotime($order->packing_end_date)):"";
                        $packArr['totalQuantity']=$order->total_quantity;
                        $packArr['updatedQuantity']=EmailScheduleSettings::getUpdatedProductionSum($order,"Pack");
                        // $packArr['lastupdatedDate']=EmailScheduleSettings::getLastUpdatedProductionDate($order,"Pack");
                        $packArr['pendingQuantity']=$packArr['totalQuantity'] - $packArr['updatedQuantity'];
                        $packArr['actualEndDate']=$order->packing_end_date;
                        $packArr['accomplishedDate']=$order->packing_accomplished_date;
                        $packArr['noOfDays']=EmailScheduleSettings::dateDifference($order->packing_end_date);
                        //$packArr['status']=EmailScheduleSettings::getTheStatus($order->packing_start_date,$order->packing_end_date,$order->packing_accomplished_date,$packArr['totalQuantity'],$packArr['updatedQuantity']);
                        $packingInterval = EmailScheduleSettings::TaskdateDifference($order->packing_start_date,$order->packing_end_date,$packArr['noOfDays']);
                        $packArr['delay']= $packingInterval['delay'];
                        $packArr['type']= $packingInterval['type'];
                        // if($packArr['pendingQuantity'] >0){
                            $prodData[]=$packArr;
                        // }
                        /* Production Details End*/
                        $details['prodData']=$prodData;
                        $details['language']=$language;
                        $details['dateFormat']=$dateFormat;
                        $details['companyLogo_url']=$companyLogo_url;
                        // Log::info("Data for EmailScheduleSettings");
                        // Log::info($details);
                        $details_arr[$i]=$details;
                        $i++;
                        if($is_consolidated_mail==0){
                        // print_r($details_arr);
                            $details_arr['filename'] = "OrderStatus(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";//$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                            $details_arr['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details_arr['filename'];
                            $pdf_generate = EmailScheduleSettings::create_mail_pdf($details_arr);
                            if($pdf_generate==1){
                                try{
                                    OrderStatusDailyJob::dispatch($details_arr);
                                }catch(Exception $e){
                                    Log::info($e->getMessage(). ' TASK-->OrderStatus'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                                }
                            }
                            $i=0;
                        }
                    }
                    if($i>0 && $is_consolidated_mail==1 && !empty($details_arr)){
                        $details_arr['filename'] = "OrderStatus(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                        $details_arr['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details_arr['filename'];
                        $pdf_generate = EmailScheduleSettings::create_mail_pdf($details_arr);
                        if($pdf_generate==1){
                            try{
                                OrderStatusDailyJob::dispatch($details_arr);
                            }catch(Exception $e){
                                Log::info($e->getMessage(). ' TASK-->OrderStatus'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                            }
                        }
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
        //dd($details);
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
            $pdf = Pdf::loadView('PDF_MailOrderStatus');
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
    public static function getTheStatus($startDate,$endDate,$accomplishedDate,$totalCount,$updatedCount){
        $status="";
        if($totalCount === $updatedCount && $accomplishedDate != NULL &&strtotime($accomplishedDate)<=strtotime($endDate)){
            $status = "Completed";
        }
        if($totalCount === $updatedCount && $accomplishedDate != NULL && strtotime($accomplishedDate)>strtotime($endDate)){
            $status = "Delayed Completion";
        }
        if($endDate != null && strtotime(date('Y-m-d'))>strtotime($endDate)){
            $status = "Delay";
        }
        // if($updatedCount == 0 && strtotime(date('Y-m-d'))>strtotime($endDate)){
        //     $status = "Delayed Start";
        // }
        if(strtotime($startDate)<=strtotime(date('Y-m-d')) && strtotime(date('Y-m-d'))<=strtotime($endDate)){
            $status = "In Progress";
        }
        if(strtotime($startDate)>strtotime(date('Y-m-d'))){
            $status = "Not Yet Started";
        }
        // if($updatedCount != 0 && strtotime($startDate)>strtotime(date('Y-m-d')) && strtotime($lastUpdatedDate)<=strtotime($startDate)){
        //     $status = "Early Start";
        // }

        return $status;
    }

    public static function deleteEmailPDFs(){
        //$folder = date('Y_m_d',strtotime("-1 days"));
        $folder = date('Y_m_d');
        $filePath = public_path() . '/Notifications/'.$folder;
        if (file_exists($filePath)) {
           EmailScheduleSettings::rrmdir($filePath,'1');
        }

        $order_pdfs_folder = public_path() . '/OrderInfo';
        if (file_exists($order_pdfs_folder)) {
            EmailScheduleSettings::rrmdir($order_pdfs_folder,'0');
         }
    }

    public static function rrmdir($dir,$delete_folder)
    {
        if (is_dir($dir))
        {
            $objects = scandir($dir);

            foreach ($objects as $object)
            {
                if ($object != '.' && $object != '..')
                {
                    if (filetype($dir.'/'.$object) == 'dir' && !stristr($dir, 'OrderInfo')) {EmailScheduleSettings::rrmdir($dir.'/'.$object,'1');}
                    else {unlink($dir.'/'.$object);}
                }
            }

            reset($objects);
            if($delete_folder=='1')
                rmdir($dir);
        }
    }
}
