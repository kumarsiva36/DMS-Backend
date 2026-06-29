<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Jobs\DailyRemainderJob;
use App\Jobs\DueTodayMailJob;
use App\Jobs\DueTomorrowMailJob;
use App\Jobs\EmailNotificationDelayTaskJob;
use App\Jobs\WeeklyRemainderJob;
use App\Models\EmailNotificationSettings;
use App\Models\MultipleDeliveryDates;
use App\Models\NotificationSettings;
use App\Models\Order;
use App\Models\OrderTask;
use App\Models\Staff;
use App\Models\User;
use App\Models\UserPreferences;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use DateTime;
use App\Http\Controllers\WebSite\Company\CompanySettings as CompanyCompanySettings;
ini_set('memory_limit',-1);
class EmailForUserSettings extends Controller
{
    /* Function For sending tasks that are due today */
    public static function tasksDueToday(){
        $theNotification = NotificationSettings::where('email_due_today',"6")->get();
        foreach($theNotification as $notification) {
            $whereConditions = [
                ['company_id',"=", $notification->company_id],
                ['workspace_id','=',$notification->workspace_id]
            ];
            if($notification->user_id > 0){
                $user = User::where('id',$notification->user_id)->first();
                $language = GetUserLanguage::getLanguageOfUserWithId($notification->company_id,$notification->workspace_id,"User",$user->id);
                $dateFormat = (UserPreferences::where('user_id',$user->id)->first());
            }
            if($notification->staff_id > 0){
                $user = Staff::where($whereConditions)->where('id',$notification->staff_id)
                ->select(DB::raw('CONCAT(first_name," ",last_name) as name'),'email','id')->first();
                $language = GetUserLanguage::getLanguageOfUserWithId($notification->company_id,$notification->workspace_id,"Staff",$user->id);
                $dateFormat = (UserPreferences::where('staff_id',$user->id)->first());
            }
            if(empty($dateFormat) || $dateFormat->date_format == NUll || $dateFormat->date_format == "" || empty($dateFormat->date_format)){
                $dateFormat = "d M Y";
            }else{
                $dates =$dateFormat->date_format;
                $dateFormat = $dates;
            }
            $userLogo = CompanyCompanySettings::getUserLogoStatus($notification->company_id);
            $companyLogo = $userLogo['useLogo'] == 1 ? $userLogo['userLogo'] : "";
            $companyLogo_url =$companyLogo !="" ?  Storage::disk('s3')->temporaryUrl($companyLogo, '+15 minutes') : "";
            $is_consolidated_mail = $notification->is_consolidated_mail ?? 1;
            $details=[];
            $details['to'] = $user->email;
            $details['userName'] = trim($user->name);
            $details['language']=$language;
            $details['dateFormat']=$dateFormat;
            $details['companyLogo_url']=$companyLogo_url;

            $orders = Order::where('orders.company_id',$notification->company_id)->where('orders.workspace_id',$notification->workspace_id)
            ->where('orders.status',"1")
            ->leftjoin('order_factory','order_factory.id','orders.factory_id')
            ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
            ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
            ->select('orders.id','order_buyer.name as buyer','order_pcu.name as pcu','order_factory.name as factory','orders.order_no','orders.style_no')
            ->get();

            $i=0;$mail_dispatch=0;
            foreach($orders as $order){
                $orderTasksDueToday = OrderTask::where('order_task_data.order_id', $order->id)->where('order_task_data.task_accomplished_date',NULL)
                ->where('order_task_data.task_schedule_end_date',date('Y-m-d'))
                ->leftjoin('staff','staff.id','order_task_data.task_pic')
                ->select('order_task_data.task_title','order_task_data.task_schedule_start_date','order_task_data.task_schedule_end_date',
                    'order_task_data.task_accomplished_date','staff.first_name','staff.last_name')
                ->get();
                if(count($orderTasksDueToday)>0){
                    $taskDetails=[];
                    foreach($orderTasksDueToday as $task){
                        $detailed = [];
                        $detailed['taskName'] = $task->task_title;
                        $detailed['pic']=$task->first_name." ".$task->last_name;
                        $taskDetails[]= $detailed;
                    }
                    $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
                    $details['delivery_date'][$i] = $delivery_date;
                    $details['orderNo'][$i] = $order->order_no;
                    $details['styleNo'][$i] = $order->style_no;
                    $details['buyer'][$i] = $order->buyer;
                    $details['pcu'][$i] = $order->pcu;
                    $details['factory'][$i] = $order->factory;
                    $details['taskDetails'][$i] = $taskDetails;
                    $details['count'] = $i+1;
                    $mail_dispatch=1;
                    $i++;

                    if($is_consolidated_mail==0){
                        $details['filename'] = "TodayDueTasks(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                        $details['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details['filename'];
                        $pdf_generate = EmailForUserSettings::create_mail_pdf($details,'dueToday');
                        if($pdf_generate==1){
                            try{
                                DueTodayMailJob::dispatch($details);
                            }catch(Exception $e){
                                Log::info($e->getMessage(). ' TASK-->dueToday'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                            }
                        }
                        $i=0;
                    }

                }
            }
            if($mail_dispatch==1 && $is_consolidated_mail==1 && !empty($details)){
                //print_r($details);
                $details['filename'] = "TodayDueTasks(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                $details['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details['filename'];
                $pdf_generate = EmailForUserSettings::create_mail_pdf($details,'dueToday');
                if($pdf_generate==1){
                    try{
                        DueTodayMailJob::dispatch($details);
                    }catch(Exception $e){
                        Log::info($e->getMessage(). ' TASK-->dueToday'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                    }
                }

            }
        }
    }

    public static function create_mail_pdf($details,$type){
        $filePath = public_path() . '/Notifications';
        if (!file_exists($filePath)) {
            File::makeDirectory($filePath, 0777, true, true);
        }
        $folderPath = public_path() . '/Notifications/'.date('Y_m_d');
        if (!file_exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true, true);
        }
        $data['responses']=$details;
        $data['dateFormat']=$details['dateFormat'];
        $data['useLogo'] = 0;
        $data['userLogo'] ="";
        if(count($details)>0){
            view()->share("details",$details);
            if($type=='dueToday')
                $pdf = Pdf::loadView('PDF_TaskDueTodayRemainder');
            else if($type=='dueTomorrow')
                $pdf = Pdf::loadView('PDF_TaskDueTomorrowRemainder');
            else if($type=='dailyRemainder')
                $pdf = Pdf::loadView('PDF_TaskDailyRemainder');
            else if($type=='weeklyRemainder')
                $pdf = Pdf::loadView('PDF_TaskWeeklyRemainder');
            else if($type=='Escalation')
                $pdf = Pdf::loadView('PDF_TaskDelayNotification');
            else
                $pdf = Pdf::loadView('PDF_TaskDueTodayRemainder');
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

    /* Function For sending tasks that are due Tomorrow */
    public static function tasksDueTomorrow(){
        $theNotification = NotificationSettings::where('email_due_tomorrow',"6")->get();
        foreach($theNotification as $notification) {
            $whereConditions = [
                ['company_id',"=", $notification->company_id],
                ['workspace_id','=',$notification->workspace_id]
            ];
            if($notification->user_id > 0){
                $user = User::where('id',$notification->user_id)->first();
                $language = GetUserLanguage::getLanguageOfUserWithId($notification->company_id,$notification->workspace_id,"User",$user->id);
                $dateFormat = (UserPreferences::where('user_id',$user->id)->first());
            }
            if($notification->staff_id > 0){
                $user = Staff::where($whereConditions)->where('id',$notification->staff_id)
                ->select(DB::raw('CONCAT(first_name," ",last_name) as name'),'email','id')->first();
                $language = GetUserLanguage::getLanguageOfUserWithId($notification->company_id,$notification->workspace_id,"Staff",$user->id);
                $dateFormat = (UserPreferences::where('staff_id',$user->id)->first());
            }
            if(empty($dateFormat) || $dateFormat->date_format == NUll || $dateFormat->date_format == "" || empty($dateFormat->date_format)){
                $dateFormat = "d M Y";
            }else{
                $dates =$dateFormat->date_format;
                $dateFormat = $dates;
            }
            $userLogo = CompanyCompanySettings::getUserLogoStatus($notification->company_id);
            $companyLogo = $userLogo['useLogo'] == 1 ? $userLogo['userLogo'] : "";
            $companyLogo_url =$companyLogo !="" ?  Storage::disk('s3')->temporaryUrl($companyLogo, '+15 minutes') : "";
            $is_consolidated_mail=$notification->is_consolidated_mail??1;
            $details=[];
            $details['to'] = $user->email;
            $details['userName'] = trim($user->name);
            $details['language']=$language;
            $details['dateFormat']=$dateFormat;
            $details['companyLogo_url']=$companyLogo_url;

            $orders = Order::where('orders.company_id',$notification->company_id)->where('orders.workspace_id',$notification->workspace_id)
            ->where('orders.status',"1")
            ->leftjoin('order_factory','order_factory.id','orders.factory_id')
            ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
            ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
            ->select('orders.id','order_buyer.name as buyer','order_pcu.name as pcu','order_factory.name as factory','orders.order_no','orders.style_no')
            ->get();

            $i=0;$mail_dispatch=0;
            foreach($orders as $order) {
                $orderTasksDueToday = OrderTask::where('order_task_data.order_id', $order->id)->where('order_task_data.task_accomplished_date',NULL)
                ->where('task_schedule_end_date',date('Y-m-d',strtotime("+1 day")))
                ->leftjoin('staff','staff.id','order_task_data.task_pic')
                ->select('order_task_data.task_title','order_task_data.task_schedule_start_date','order_task_data.task_schedule_end_date',
                    'order_task_data.task_accomplished_date','staff.first_name','staff.last_name')
                ->get();
                if(count($orderTasksDueToday)>0){
                    $taskDetails=[];
                    foreach($orderTasksDueToday as $task){
                        $detailed = [];
                        $detailed['taskName'] = $task->task_title;
                        $detailed['pic']=$task->first_name." ".$task->last_name;
                        $taskDetails[]= $detailed;
                    }
                    $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
                    $details['delivery_date'][$i] = $delivery_date;
                    $details['orderNo'][$i] = $order->order_no;
                    $details['styleNo'][$i] = $order->style_no;
                    $details['buyer'][$i] = $order->buyer;
                    $details['pcu'][$i] = $order->pcu;
                    $details['factory'][$i] = $order->factory;
                    $details['taskDetails'][$i] = $taskDetails;
                    $details['count'] = $i+1;
                    $mail_dispatch=1;
                    // DueTomorrowMailJob::dispatch($details);
                    $i++;
                    if($is_consolidated_mail==0){
                        $details['filename'] = "DueTomorrowTasks(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";//$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                        $details['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details['filename'];
                        $pdf_generate = EmailForUserSettings::create_mail_pdf($details,'dueTomorrow');
                        if($pdf_generate==1){
                            try{
                                DueTomorrowMailJob::dispatch($details);
                            }catch(Exception $e){
                                Log::info($e->getMessage(). ' TASK-->dueTomorrow'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                            }
                        }
                        $i=0;
                    }
                }
            }
            if($mail_dispatch==1 && $is_consolidated_mail==1 && !empty($details)){
                $details['filename'] = "DueTomorrowTasks(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                $details['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details['filename'];
                $pdf_generate = EmailForUserSettings::create_mail_pdf($details,'dueTomorrow');
                if($pdf_generate==1){
                    try{
                        DueTomorrowMailJob::dispatch($details);
                    }catch(Exception $e){
                        Log::info($e->getMessage(). ' TASK-->dueTomorrow'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                    }
                }
            }
        }
    }

    /* Function For Daily Remainder of tasks that are due today */
    public static function taskDailyRemainder(){
        $theNotification = NotificationSettings::where('email_daily_reminder',"6")->get();
        foreach($theNotification as $notification) {
            $whereConditions = [
                ['company_id',"=", $notification->company_id],
                ['workspace_id','=',$notification->workspace_id]
            ];
            //dd($whereConditions);
            if($notification->user_id > 0){
                $user = User::where('id',$notification->user_id)->first();
                $dateFormat = (UserPreferences::where('user_id',$user->id)->first());
                $language = GetUserLanguage::getLanguageOfUserWithId($notification->company_id,$notification->workspace_id,"Staff",$user->id);
            }
            if($notification->staff_id > 0){
                $user = Staff::where($whereConditions)->where('id',$notification->staff_id)
                ->select(DB::raw('CONCAT(first_name," ",last_name) as name'),'email','id')->first();
                $dateFormat = (UserPreferences::where('staff_id',$user->id)->first());
                $language = GetUserLanguage::getLanguageOfUserWithId($notification->company_id,$notification->workspace_id,"Staff",$user->id);
            }

            if(empty($dateFormat) || $dateFormat->date_format == NUll || $dateFormat->date_format == "" || empty($dateFormat->date_format)){
                $dateFormat = "d M Y";
            }else{
                $dates =$dateFormat->date_format;
                $dateFormat = $dates;
            }
            $userLogo = CompanyCompanySettings::getUserLogoStatus($notification->company_id);
            $companyLogo = $userLogo['useLogo'] == 1 ? $userLogo['userLogo'] : "";
            $companyLogo_url =$companyLogo !="" ?  Storage::disk('s3')->temporaryUrl($companyLogo, '+15 minutes') : "";
            $is_consolidated_mail=$notification->is_consolidated_mail??1;
            $details=[];
            $details['to'] = $user->email;
            $details['userName'] = trim($user->name);
            $details['language']=$language;
            $details['dateFormat']=$dateFormat;
            $details['companyLogo_url']=$companyLogo_url;

            $orders = Order::where('orders.company_id',$notification->company_id)->where('orders.workspace_id',$notification->workspace_id)
            ->where('orders.status',"1")
            ->leftjoin('order_factory','order_factory.id','orders.factory_id')
            ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
            ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
            ->select('orders.id','order_buyer.name as buyer','order_pcu.name as pcu','order_factory.name as factory','orders.order_no','orders.style_no')
            ->get();
            $i=0;$mail_dispatch=0;
            foreach($orders as $order) {
                $orderTasksDueToday = OrderTask::where('order_task_data.order_id', $order->id)->where('order_task_data.task_accomplished_date',NULL)
                ->where('order_task_data.task_schedule_start_date',date('Y-m-d'))
                ->leftjoin('staff','staff.id','order_task_data.task_pic')
                ->select('order_task_data.task_title','order_task_data.task_schedule_start_date','order_task_data.task_schedule_end_date',
                    'order_task_data.task_accomplished_date','staff.first_name','staff.last_name')
                ->get();

                if(count($orderTasksDueToday)>0){
                    $taskDetails=[];
                    foreach($orderTasksDueToday as $task){
                        $detailed = [];
                        $detailed['taskName'] = $task->task_title;
                        $detailed['pic']=$task->first_name." ".$task->last_name;
                        $detailed['startDate'] = date($dateFormat,strtotime($task->task_schedule_start_date));
                        $detailed['endDate'] = date($dateFormat,strtotime($task->task_schedule_end_date));
                        $taskDetails[]= $detailed;
                    }
                    $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
                    $details['delivery_date'][$i] = $delivery_date;
                    $details['orderNo'][$i] = $order->order_no;
                    $details['styleNo'][$i] = $order->style_no;
                    $details['buyer'][$i] = $order->buyer;
                    $details['pcu'][$i] = $order->pcu;
                    $details['factory'][$i] = $order->factory;
                    $details['taskDetails'][$i] = $taskDetails;
                    $details['count'] = $i+1;
                    $mail_dispatch=1;
                   // DailyRemainderJob::dispatch($details);
                    $i++;
                    if($is_consolidated_mail==0){
                        $details['filename'] = "TasksStartsToday(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";//$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                        $details['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details['filename'];
                        $pdf_generate = EmailForUserSettings::create_mail_pdf($details,'dailyRemainder');
                        if($pdf_generate==1){
                            try{
                                DailyRemainderJob::dispatch($details);
                            }catch(Exception $e){
                                Log::info($e->getMessage(). ' TASK-->dailyRemainder'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                            }
                        }
                        $i=0;
                    }
                }

            }
            if($mail_dispatch==1 && $is_consolidated_mail==1 && !empty($details)){
                $details['filename'] = "TasksStartsToday(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                $details['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details['filename'];
                $pdf_generate = EmailForUserSettings::create_mail_pdf($details,'dailyRemainder');
                if($pdf_generate==1){
                    try{
                        DailyRemainderJob::dispatch($details);
                    }catch(Exception $e){
                        Log::info($e->getMessage(). ' TASK-->dailyRemainder'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                    }
                }
            }
        }
    }

    /* Function For Daily Remainder of tasks that are due today */
    public static function taskWeeklyRemainder(){
        $theNotification = NotificationSettings::where('email_weekly_reminder',"6")->get();
        foreach($theNotification as $notification) {
            $whereConditions = [
                ['company_id',"=", $notification->company_id],
                ['workspace_id','=',$notification->workspace_id]
            ];
            if($notification->user_id > 0){
                $user = User::where('id',$notification->user_id)->first();
                $dateFormat = (UserPreferences::where('user_id',$user->id)->first());
                $language = GetUserLanguage::getLanguageOfUserWithId($notification->company_id,$notification->workspace_id,"User",$user->id);
            }
            if($notification->staff_id > 0){
                $user = Staff::where($whereConditions)->where('id',$notification->staff_id)
                ->select(DB::raw('CONCAT(first_name," ",last_name) as name'),'email','id')->first();
                $dateFormat = (UserPreferences::where('staff_id',$user->id)->first());
                $language = GetUserLanguage::getLanguageOfUserWithId($notification->company_id,$notification->workspace_id,"Staff",$user->id);
            }

            if(empty($dateFormat) || $dateFormat->date_format == NUll || $dateFormat->date_format == "" || empty($dateFormat->date_format)){
                $dateFormat = "d M Y";
            }else{
                $dates =$dateFormat->date_format;
                $dateFormat = $dates;
            }
            $userLogo = CompanyCompanySettings::getUserLogoStatus($notification->company_id);
            $companyLogo = $userLogo['useLogo'] == 1 ? $userLogo['userLogo'] : "";
            $companyLogo_url =$companyLogo !="" ?  Storage::disk('s3')->temporaryUrl($companyLogo, '+15 minutes') : "";
            $is_consolidated_mail=$notification->is_consolidated_mail??1;
            $details=[];
            $details['to'] = $user->email;
            $details['userName'] = trim($user->name);
            $details['language']=$language;
            $details['dateFormat']=$dateFormat;
            $details['companyLogo_url']=$companyLogo_url;

            $orders = Order::where('orders.company_id',$notification->company_id)->where('orders.workspace_id',$notification->workspace_id)
            ->where('orders.status',"1")
            ->leftjoin('order_factory','order_factory.id','orders.factory_id')
            ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
            ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
            ->select('orders.id','order_buyer.name as buyer','order_pcu.name as pcu','order_factory.name as factory','orders.order_no','orders.style_no')
            ->get();
            $i=0;$mail_dispatch=0;
            foreach($orders as $order) {
                $orderTasksDueToday = OrderTask::where('order_task_data.order_id', $order->id)->where('order_task_data.task_accomplished_date',NULL)
                ->whereBetween('order_task_data.task_schedule_start_date',[date('Y-m-d'),date('Y-m-d',strtotime("+7 day"))])
                ->leftjoin('staff','staff.id','order_task_data.task_pic')
                ->select('order_task_data.task_title','order_task_data.task_schedule_start_date','order_task_data.task_schedule_end_date',
                    'order_task_data.task_accomplished_date','staff.first_name','staff.last_name')
                ->get();
                if(count($orderTasksDueToday)>0){
                    $taskDetails=[];
                    foreach($orderTasksDueToday as $task){
                        $detailed = [];
                        $detailed['taskName'] = $task->task_title;
                        $detailed['pic']=$task->first_name." ".$task->last_name;
                        $detailed['startDate'] = date($dateFormat,strtotime($task->task_schedule_start_date));
                        $detailed['endDate'] = date($dateFormat,strtotime($task->task_schedule_end_date));
                        $taskDetails[]= $detailed;
                    }
                    $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
                    $details['delivery_date'][$i] = $delivery_date;
                    $details['orderNo'][$i] = $order->order_no;
                    $details['styleNo'][$i] = $order->style_no;
                    $details['buyer'][$i] = $order->buyer;
                    $details['pcu'][$i] = $order->pcu;
                    $details['factory'][$i] = $order->factory;
                    $details['taskDetails'][$i] = $taskDetails;
                    $details['count'] = $i+1;
                    $mail_dispatch=1;
                    $i++;
                    if($is_consolidated_mail==0){
                        $details['filename'] = "WeeklyTaskRemainder(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";//$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                        $details['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details['filename'];
                        $pdf_generate = EmailForUserSettings::create_mail_pdf($details,'weeklyRemainder');
                        if($pdf_generate==1){
                            try{
                                WeeklyRemainderJob::dispatch($details);
                            }catch(Exception $e){
                                Log::info($e->getMessage(). ' TASK-->weeklyRemainder'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                            }
                        }
                        $i=0;
                    }
                }
            }
            if($mail_dispatch==1 && $is_consolidated_mail==1){
                $details['filename'] = "WeeklyTaskRemainder(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                $details['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details['filename'];
                $pdf_generate = EmailForUserSettings::create_mail_pdf($details,'weeklyRemainder');
                if($pdf_generate==1){
                    try{
                        WeeklyRemainderJob::dispatch($details);
                    }catch(Exception $e){
                        Log::info($e->getMessage(). ' TASK-->weeklyRemainder'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                    }
                }
            }
        }
    }

    /* Function For sending Email Notification For Task Delay Day Wise */
    public static function tasksDelayNotification(){
        $theNotification = EmailNotificationSettings::get();
        foreach($theNotification as $notification) {
            $whereConditions = [
                ['company_id',"=", $notification->company_id],
                ['workspace_id','=',$notification->workspace_id]
            ];
            $noOfDaysDelay_arr=json_decode($notification->no_of_delays,true);
           // dd($notification);
            $is_consolidated_mail = $notification->is_consolidated_mail ?? 1;
            //Staff mail start
            if(is_array($noOfDaysDelay_arr) && !empty($noOfDaysDelay_arr)){
                foreach($noOfDaysDelay_arr as $roles){
                    $users = Staff::where($whereConditions)->where('role_id',$roles['id'])
                    ->select(DB::raw('CONCAT(first_name," ",last_name) as name'),'email','id')->get();
                    foreach($users as $user){
                        if(!empty($user) && $roles['no_of_days']> 0){
                            $language = GetUserLanguage::getLanguageOfUserWithId($notification->company_id,$notification->workspace_id,"Staff",$user->id);
                            $dateFormat = (UserPreferences::where('staff_id',$user->id)->first());

                            if(empty($dateFormat) || $dateFormat->date_format == NUll || $dateFormat->date_format == "" || empty($dateFormat->date_format)){
                                $dateFormat = "d M Y";
                            }else{
                                $dates =$dateFormat->date_format;
                                $dateFormat = $dates;
                            }
                            $userLogo = CompanyCompanySettings::getUserLogoStatus($notification->company_id);
                            $companyLogo = $userLogo['useLogo'] == 1 ? $userLogo['userLogo'] : "";
                            $companyLogo_url =$companyLogo !="" ?  Storage::disk('s3')->temporaryUrl($companyLogo, '+15 minutes') : "";
                            $details=[];
                            $details['to'] = $user->email;
                            $details['userName'] = trim($user->name);
                            $details['language']=$language;
                            $details['dateFormat']=$dateFormat;
                            $details['companyLogo_url']=$companyLogo_url;

                            $orders = Order::where('orders.company_id',$notification->company_id)->where('orders.workspace_id',$notification->workspace_id)
                            ->where('orders.status',"1")->where('order_contacts.staff_id',$user->id)
                            ->join('order_contacts','order_contacts.order_id','orders.id')
                            ->leftjoin('order_factory','order_factory.id','orders.factory_id')
                            ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
                            ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
                            ->select('orders.id','order_buyer.name as buyer','order_pcu.name as pcu','order_factory.name as factory','orders.order_no','orders.style_no')
                            ->get();

                            $i=0;$mail_dispatch=0;
                            foreach($orders as $order) {
                                $orderTasksDueToday = OrderTask::where('order_task_data.order_id', $order->id)->where('order_task_data.task_accomplished_date',NULL)
                                ->where('task_schedule_end_date',date('Y-m-d',strtotime("-".$roles['no_of_days']." day")))
                                ->leftjoin('staff','staff.id','order_task_data.task_pic')
                                ->select('order_task_data.task_title','order_task_data.task_schedule_start_date','order_task_data.task_schedule_end_date',
                                    'order_task_data.task_accomplished_date',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'),'staff.first_name','staff.last_name')
                                ->get();
                                if(count($orderTasksDueToday)>0){
                                    $taskDetails=[];
                                    foreach($orderTasksDueToday as $task){
                                        $detailed = [];
                                        $detailed['taskName'] = $task->task_title;
                                        $detailed['start_date'] = date($dateFormat,strtotime($task->task_schedule_start_date));
                                        $detailed['end_date'] = date($dateFormat,strtotime($task->task_schedule_end_date));
                                        $detailed['pic']=$task->first_name." ".$task->last_name;
                                        $detailed['noOfDays']=$task->noOfDays;
                                        $detailed['status']='Delay';
                                        $taskDetails[]= $detailed;
                                    }
                                    $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
                                    $details['delivery_date'][$i] = $delivery_date;
                                    $details['orderNo'][$i] = $order->order_no;
                                    $details['styleNo'][$i] = $order->style_no;
                                    $details['buyer'][$i] = $order->buyer;
                                    $details['pcu'][$i] = $order->pcu;
                                    $details['factory'][$i] = $order->factory;
                                    $details['taskDetails'][$i] = $taskDetails;
                                    $details['count'] = $i+1;
                                    $mail_dispatch=1;
                                    $i++;
                                    if($is_consolidated_mail==0){
                                        $details['filename'] = $order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                                        $details['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details['filename'];
                                        $pdf_generate = EmailForUserSettings::create_mail_pdf($details,'Escalation');
                                        if($pdf_generate==1){
                                            try{
                                                EmailNotificationDelayTaskJob::dispatch($details);;
                                            }catch(Exception $e){
                                                Log::info($e->getMessage(). ' TASK-->OrderEscalation'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                                            }
                                        }
                                        //EmailNotificationDelayTaskJob::dispatch($details);
                                        $i=0;
                                    }
                                }
                            }
                           // dd($details);
                            if($mail_dispatch==1 && $is_consolidated_mail==1 && !empty($details)){
                                $details['filename'] = $order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                                $details['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details['filename'];
                                $pdf_generate = EmailForUserSettings::create_mail_pdf($details,'Escalation');
                                if($pdf_generate==1){
                                    try{
                                        EmailNotificationDelayTaskJob::dispatch($details);;
                                    }catch(Exception $e){
                                        Log::info($e->getMessage(). ' TASK-->OrderEscalation'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                                    }
                                }
                                //EmailNotificationDelayTaskJob::dispatch($details);
                            }
                        }
                    }
                }
            }
            //Staff mail end
            //Admin mail start
            if($notification->user_id > 0 && $notification->staff_id ==0 && $notification->notify_admin > 0){
                $user = User::where('id',$notification->user_id)->first();
                $language = GetUserLanguage::getLanguageOfUserWithId($notification->company_id,$notification->workspace_id,"User",$user->id);
                $dateFormat = (UserPreferences::where('user_id',$user->id)->first());

                if(empty($dateFormat) || $dateFormat->date_format == NUll || $dateFormat->date_format == "" || empty($dateFormat->date_format)){
                    $dateFormat = "d M Y";
                }else{
                    $dates =$dateFormat->date_format;
                    $dateFormat = $dates;
                }
                $userLogo = CompanyCompanySettings::getUserLogoStatus($notification->company_id);
                $companyLogo = $userLogo['useLogo'] == 1 ? $userLogo['userLogo'] : "";
                $companyLogo_url =$companyLogo !="" ?  Storage::disk('s3')->temporaryUrl($companyLogo, '+15 minutes') : "";
                $details=[];
                $details['to'] = $user->email;
                $details['userName'] = trim($user->name);
                $details['language']=$language;
                $details['dateFormat']=$dateFormat;
                $details['companyLogo_url']=$companyLogo_url;

                $orders = Order::where('orders.company_id',$notification->company_id)->where('orders.workspace_id',$notification->workspace_id)
                ->where('orders.status',"1")
                ->leftjoin('order_factory','order_factory.id','orders.factory_id')
                ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
                ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
                ->select('orders.id','order_buyer.name as buyer','order_pcu.name as pcu','order_factory.name as factory','orders.order_no','orders.style_no')
                ->get();

                $i=0;$mail_dispatch=0;
                foreach($orders as $order) {
                    $orderTasksDueToday = OrderTask::where('order_task_data.order_id', $order->id)->where('order_task_data.task_accomplished_date',NULL)
                    ->where('task_schedule_end_date',date('Y-m-d',strtotime("-".$notification->notify_admin." day")))
                    ->leftjoin('staff','staff.id','order_task_data.task_pic')
                    ->select('order_task_data.task_title','order_task_data.task_schedule_start_date','order_task_data.task_schedule_end_date',
                        'order_task_data.task_accomplished_date',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'),'staff.first_name','staff.last_name')
                    ->get();
                    if(count($orderTasksDueToday)>0){
                        $taskDetails=[];
                        foreach($orderTasksDueToday as $task){
                            $detailed = [];
                            $detailed['taskName'] = $task->task_title;
                            $detailed['pic']=$task->first_name." ".$task->last_name;
                            $detailed['start_date'] = $task->task_schedule_start_date;
                            $detailed['end_date'] = $task->task_schedule_end_date;
                            $detailed['noOfDays']=$task->noOfDays;
                            $detailed['status']='Delay';
                            $taskDetails[]= $detailed;
                        }
                        $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
                        $details['delivery_date'][$i] = $delivery_date;
                        $details['orderNo'][$i] = $order->order_no;
                        $details['styleNo'][$i] = $order->style_no;
                        $details['buyer'][$i] = $order->buyer;
                        $details['pcu'][$i] = $order->pcu;
                        $details['factory'][$i] = $order->factory;
                        $details['taskDetails'][$i] = $taskDetails;
                        $details['count'] = $i+1;
                        $mail_dispatch=1;
                        $i++;
                        if($is_consolidated_mail==0){
                            $details['filename'] = "TaskDelay(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";//$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                            $details['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details['filename'];
                            $pdf_generate = EmailForUserSettings::create_mail_pdf($details,'Escalation');
                            if($pdf_generate==1){
                                try{
                                    EmailNotificationDelayTaskJob::dispatch($details);;
                                }catch(Exception $e){
                                    Log::info($e->getMessage(). ' TASK-->OrderEscalation'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                                }
                            }
                            //EmailNotificationDelayTaskJob::dispatch($details);
                            $i=0;
                        }
                    }
                }
                if($mail_dispatch==1 && $is_consolidated_mail==1){
                    $details['filename'] = "TaskDelay(".date('Y-m-d').")".$order->id."_".rand(100000,999999)."_".$user->id.".pdf";
                    $details['pdf_path']= public_path() . '/Notifications/'.date('Y_m_d').'/'.$details['filename'];
                    $pdf_generate = EmailForUserSettings::create_mail_pdf($details,'Escalation');
                    if($pdf_generate==1){
                        try{
                            EmailNotificationDelayTaskJob::dispatch($details);
                        }catch(Exception $e){
                            Log::info($e->getMessage(). ' TASK-->OrderEscalation'." EMAIL-->".$user->email." NAME-->".$user->name." ID-->".$user->id);
                        }
                    }
                    //EmailNotificationDelayTaskJob::dispatch($details);
                }
            }
            //Admin mail end
            //Guest mail start
            // if($notification->email_ids != '' && $notification->email_no_of_delays > 0){
            //     $user = User::where('id',$notification->user_id)->first();
            //     $language = GetUserLanguage::getLanguageOfUserWithId($notification->company_id,$notification->workspace_id,"User",$user->id);
            //     $dateFormat = (UserPreferences::where('user_id',$user->id)->first());

            //     if(empty($dateFormat) || $dateFormat->date_format == NUll || $dateFormat->date_format == "" || empty($dateFormat->date_format)){
            //         $dateFormat = "d M Y";
            //     }else{
            //         $dates =$dateFormat->date_format;
            //         $dateFormat = $dates;
            //     }
            //     $details=[];
            //     $details['to'] = $notification->email_ids;
            //     $details['userName'] = 'User';
            //     $details['language']=$language;
            //     $details['dateFormat']=$dateFormat;

            //     $orders = Order::where('orders.company_id',$notification->company_id)->where('orders.workspace_id',$notification->workspace_id)
            //     ->where('orders.status',"1")
            //     ->leftjoin('order_factory','order_factory.id','orders.factory_id')
            //     ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
            //     ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
            //     ->select('orders.id','order_buyer.name as buyer','order_pcu.name as pcu','order_factory.name as factory','orders.order_no','orders.style_no')
            //     ->get();

            //     $i=0;$mail_dispatch=0;
            //     foreach($orders as $order) {
            //         $orderTasksDueToday = OrderTask::where('order_task_data.order_id', $order->id)->where('order_task_data.task_accomplished_date',NULL)
            //         ->where('task_schedule_end_date',date('Y-m-d',strtotime("-".$notification->email_no_of_delays." day")))
            //         ->leftjoin('staff','staff.id','order_task_data.task_pic')
            //         ->select('order_task_data.task_title','order_task_data.task_schedule_start_date','order_task_data.task_schedule_end_date',
            //             'order_task_data.task_accomplished_date','staff.first_name','staff.last_name')
            //         ->get();
            //         if(count($orderTasksDueToday)>0){
            //             $taskDetails=[];
            //             foreach($orderTasksDueToday as $task){
            //                 $detailed = [];
            //                 $detailed['taskName'] = $task->task_title;
            //                 $detailed['pic']=$task->first_name." ".$task->last_name;
            //                 $detailed['start_date'] = $task->task_schedule_start_date;
            //                 $detailed['end_date'] = $task->task_schedule_end_date;
            //                 $taskDetails[]= $detailed;
            //             }
            //             $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
            //             $details['delivery_date'][$i] = $delivery_date;
            //             $details['orderNo'][$i] = $order->order_no;
            //             $details['styleNo'][$i] = $order->style_no;
            //             $details['buyer'][$i] = $order->buyer;
            //             $details['pcu'][$i] = $order->pcu;
            //             $details['factory'][$i] = $order->factory;
            //             $details['taskDetails'][$i] = $taskDetails;
            //             $details['count'] = $i+1;
            //             $mail_dispatch=1;
            //             $i++;
            //             if($is_consolidated_mail==0){
            //                 EmailNotificationDelayTaskJob::dispatch($details);
            //                 $i=0;
            //             }
            //         }
            //     }
            //     if($mail_dispatch==1 && $is_consolidated_mail==1){
            //         //print_r($details);
            //         EmailNotificationDelayTaskJob::dispatch($details);
            //     }
            // }
            //Guest mail end

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
}
