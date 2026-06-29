<?php

namespace App\Http\Controllers\Mobile\Common;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailScheduleSettings;
use App\Models\EmailScheduleTask;
use App\Models\NotificationSettings;
use App\Models\UserPreferences;
use Illuminate\Support\Facades\Validator;
use App\Models\TimeZoneFormat;
use Exception;

class UserSettings extends Controller
{
    /* Get the Email Schedule Types */
    public function getEmailScheduleTask(){
        $getEmailScheduleTask = EmailScheduleTask::getEmailSchedule();
        if(!empty($getEmailScheduleTask)){
            return response()->json(['status_code'=>200,'status'=>'success','data'=>$getEmailScheduleTask]);
        }
        else{
            return response()->json(['status_code'=>400,'status'=>'Failure']);
        }
    }

    /* Add the user preference */
    public function userPreference(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            'workspaceId' => 'required',
            // 'timezoneId' => 'required',
            // 'workspaceId' => 'required',
            // 'date_format'=>'required'
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->companyId);
        $whereConditions = [
            ['user_id','=',$companyDetails->user_id],
            ['workspace_id','=',$request->workspaceId],
            ['company_id','=',$companyDetails->id]
        ];
        try{
            UserPreferences::addUserPreference($request,$companyDetails,$whereConditions);
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"User Preference Updated Successfully"]);
            return CommonApp::apiEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::apiEncrypt($res);
        }
    }

    /* Get the user's preference */
    public function getUserPreferences(Request $request){
        $validated = Validator::make($request->all(),[
            'companyId' => 'required',
            'workspaceId' => 'required',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->companyId);
        $whereConditions = [
            ['user_id','=',$companyDetails->user_id],
            ['workspace_id','=',$request->workspaceId],
            ['company_id','=',$companyDetails->id]
        ];
        $userPreference = UserPreferences::getUserSettingPreferences($whereConditions);
        return response()->json(["status_code"=>200,"data"=>$userPreference]);
    }

    /*
        Add User Notification Settings
    */

    public function notificationSettings(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions = [
            ['user_id','=',$companyDetails->user_id],
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$companyDetails->id]
        ];
        try{
            NotificationSettings::addNotificationSettings($request,$companyDetails,$whereConditions);
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Notification Settings Updated Successfully"]);
            return CommonApp::apiEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::apiEncrypt($res);
        }
    }

    /* Get the added notification Settings */
    public function getNotificationSettings(Request $request){
        $validated = Validator::make($request->all(),[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions = [
            ['user_id','=',$companyDetails->user_id],
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$companyDetails->id]
        ];
        $notificationSettings = NotificationSettings::getNotificationSettings($whereConditions);
        return response()->json(["status_code"=>200,"data"=>$notificationSettings]);
    }

    /*
        To add email notification-settings
    */
    public function emailScheduleNotification(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            'workspaceId' => 'required',
            'userId' => 'required',
           // 'staffId' => 'required',
            'emailSchedule' => 'required',
           // 'task_id'=> 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        try{
            EmailScheduleSettings::addEmailScheduleNotification($request,"Mobile");
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Email Schedule Settings Updated Successfully"]);
            return CommonApp::apiEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::apiEncrypt($res);
        }
    }

    /* To Get the email schedule notifications */
    public function getemailScheduleNotification(Request $request){
        $validated = Validator::make($request->all(),[
            'companyId' => 'required',
            'workspaceId' => 'required',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->companyId);
        $whereConditions = [
            ['user_id','=',$companyDetails->user_id],
            ['workspace_id','=',$request->workspaceId],
            ['company_id','=',$companyDetails->id]
        ];
        $emailSettings = EmailScheduleSettings::getEmailScheduleSettings($whereConditions);
        return response()->json(["status_code"=>200,"data"=>$emailSettings]);
    }
   /* To List ALl User Settings in Single API For Mobile */
    public function getAllUsersettings(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            'workspaceId' => 'required',
            'userId' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }

        $responseArray=[];
        $getGeneralSettingsDetails=$this->getGeneralSettings();
        $responseArray['general_settings']=$getGeneralSettingsDetails;
        $responseArray['selected_general_settings']=$this->getGeneralSettingsSelected($request->companyId,$request->workspaceId,$request->userId);
        $getNotificationTasks=$this->getNotificationSettingsDetails();
        $responseArray['notification_settings']=$getNotificationTasks;
        $responseArray['selected_notification_settings']=$this->getNotificationSelected($request->companyId,$request->workspaceId,$request->userId,$request->staffId);
        $getEmailScheduleTasks=$this->getEmailScheduleTaskDetails();
        $responseArray['email_schedule_settings']=$getEmailScheduleTasks;
        $responseArray['selected_email_schedule_settings']=$this->getEmailScheduleTaskSelected($request->companyId,$request->workspaceId,$request->userId,$request->staffId);

        $whereConditions=[
            ['company_id','=',$request->companyId],
            ['workspace_id','=',$request->workspaceId],
            ['user_id','=',$request->userId],
        ];
        $responseArray['dashboardSettings']=$this->getDashboardWidgets($whereConditions);

        $res = json_encode(["status_code"=>200,"data"=>$responseArray]);
        return CommonApp::apiEncrypt($res);

    }
    /* Get the General Settings */
    public static function  getGeneralSettings(){
      //  $dateFormat=array("d M Y"=>"01 Jan 2022","d-m-Y"=>"DD-MM-YYYY","Y-m-d"=>"YYYY-MM-DD","Y M d"=>"2022 Jan 01");
      $dateFormat=[
        [
        "id"=>1,
        "display_name"=> "01 Jan 2022",
        "name"=> "d M Y"
        ],
        [
            "id"=>2,
            "display_name"=> "DD-MM-YYYY",
            "name"=> "d-m-Y"
            ],
            [
                "id"=>3,
                "display_name"=> "YYYY-MM-DD",
                "name"=> "Y-m-d"
                ],
                [
                    "id"=>4,
                    "display_name"=> "YYYY/MM/DD",
                    "name"=> "Y/m/d"
                    ],
                [
                    "id"=>5,
                    "display_name"=> "2022 Jan 01",
                    "name"=> "Y M d"
                    ]
    ];
        $timeZoneFormat=TimeZoneFormat::select('id','name','timezone')->where('status','1')->get();
        return array("dateformat"=>$dateFormat,"timezoneformat"=>$timeZoneFormat);
    }
    /* Get the general selected setttings */
    public static function  getGeneralSettingsSelected($companyId,$workspaceId,$userId){
        $whereConditions = [
            ['user_id','=',$userId],
            ['workspace_id','=',$workspaceId],
            ['company_id','=',$companyId],
            ['staff_id','=',0]
        ];
        $getSetting= UserPreferences::select("date_format","language_id","time_zone_format","dashboard_widget_ids")->where($whereConditions)->first();
        if($getSetting!=''){
            return array("date_format"=>$getSetting['date_format'],"language_id"=>$getSetting['language_id'],"time_zone_format"=>$getSetting['time_zone_format']);
            }else{
             return array("date_format"=>"","language_id"=>0,"time_zone_format"=>"");
            }
     }
     /* Get the Notification Settings */
    public static function  getNotificationSettingsDetails(){
       return $assignData=[
         [
         "id"=>1,
         "display_name"=> "Task Due Today",
         "name"=> "email_due_today",
         "sort"=> "1"
         ],
         [
             "id"=>2,
             "display_name"=> "Task Due Tomorrow",
             "name"=> "email_due_tomorrow",
             "sort"=> "2"
         ],
         [
             "id"=>3,
             "display_name"=> "Task Rescheduled",
             "name"=> "email_task_reschedule",
             "sort"=> "3"
         ],
           [
             "id"=>4,
             "display_name"=> "Daily Reminder",
             "name"=> "email_daily_reminder",
             "sort"=> "4"
         ],
         [
             "id"=>5,
             "display_name"=> "Weekly Reminder",
             "name"=> "email_weekly_reminder",
             "sort"=> "5"
         ],
         [
             "id"=>6,
             "display_name"=> "Accomplished Tasks",
             "name"=> "email_task_accomplishment",
             "sort"=> "6"
         ],
         ];
     }
     /* Get the selected notification settings */
     public function getNotificationSelected($companyId,$workspaceId,$userId,$staffId){
        $whereConditions = [
             ['user_id','=',$userId],
             ['staff_id','=',0],
             ['workspace_id','=',$workspaceId],
             ['company_id','=',$companyId]
         ];
        $notificationSettings = NotificationSettings::where($whereConditions)->first();
        if($notificationSettings!=''){
            $notifyArray=array("email_daily_reminder"=>$notificationSettings['email_daily_reminder'],"email_weekly_reminder"=>$notificationSettings['email_weekly_reminder'],"email_task_accomplishment"=>$notificationSettings['email_task_accomplishment'],"email_task_reschedule"=>$notificationSettings['email_task_reschedule'],"email_due_today"=>$notificationSettings['email_due_today'],"email_due_tomorrow"=>$notificationSettings['email_due_tomorrow'],"email_daily_schedule"=>$notificationSettings['email_daily_schedule'],"whatsapp"=>$notificationSettings['whatsapp'],"linemessenger"=>$notificationSettings['linemessenger'],"is_consolidated_mail"=>$notificationSettings['is_consolidated_mail']);
        }else{
            $notifyArray=array("email_daily_reminder"=>"","email_weekly_reminder"=>"","email_task_accomplishment"=>"","email_task_reschedule"=>"","email_due_today"=>"","email_due_tomorrow"=>"","email_daily_schedule"=>"","whatsapp"=>"","linemessenger"=>"","is_consolidated_mail"=>"1");
        }
        return  $notifyArray;
     }
     /* Get the email schedule settings  */
     public function getEmailScheduleTaskSelected($companyId,$workspaceId,$userId,$staffId){
        $whereConditions = [
             ['user_id','=',$userId],
             ['staff_id','=',0],
             ['workspace_id','=',$workspaceId],
             ['company_id','=',$companyId]
         ];
         $emailSettings = EmailScheduleSettings::where($whereConditions)
         ->select('email_schedule_task_id','name','email_to_user_id','days','is_consolidated_mail')
         ->get();
       return array("emailsettings" => $emailSettings);
     }
     /* Get the email schedule tasks */
    public static function getEmailScheduleTaskDetails(){
       return $getEmailScheduleTask = EmailScheduleTask::select('id','name')->where("status","1")->get();
    }

    /* To Get Dashboard Widgets */
    public function getDashboardWidgets($whereCondition){
        $dashboardWidgets = config('constant.dashboard_modules_mobile');
        $selectedWidgets = UserPreferences::getSelectDashboardWidgets($whereCondition);
        $dashboardArr=[];
        foreach ($dashboardWidgets as $key=>$value){
            $widget=[];
            $widget['id']=$key;
            $widget['name'] =$value;
            $widget['isChecked'] = in_array($key,$selectedWidgets);
            $dashboardArr[]=$widget;
        }
        return $dashboardArr;
    }

    /* Add new dashboard widgets */
    public static function addDashboardWigets(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
        ];
        if((isset($request->user_id) && isset($request->staff_id)) && $request->user_id > 0 && $request->staff_id == 0){
            $whereConditions[]=['user_id','=',$request->user_id];
        }
        if(isset($request->staff_id) && $request->staff_id > 0){
            $whereConditions[]=['staff_id','=',$request->staff_id];
        }

        try{
            UserPreferences::addDashboardWidget($request,$whereConditions);
            $res = json_encode(["status_code"=>200,"message"=>"Added Successfully"]);
            return CommonApp::apiEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"status" =>"Failure",
            "error"=>$e->getMessage()]);
            return CommonApp::apiEncrypt($res);
        }
    }

           /* Update Language Code User Settings in Single API For Mobile */
           public function updateLanguageUsersettings(Request $request){
            $request= CommonApp::apiDecrypt($request->getContent());
            $validated = Validator::make((array)$request,[
                'companyId' => 'required',
                'workspaceId' => 'required',
                 'userId' => 'required',
                'languageId' => 'required|numeric',
            ]);
            if($validated->fails()){
                $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
                return CommonApp::apiEncrypt($res);
            }
            $langArray=[];
            $langArray['language_id']=$request->languageId;

            $whereConditions = [
                 ['user_id','=',$request->userId],
                 ['staff_id','=',0],
                 ['workspace_id','=',$request->workspaceId],
                 ['company_id','=',$request->companyId]
             ];
            UserPreferences::where($whereConditions)->update($langArray);
            $res = json_encode(["status_code"=>200,"message"=>"Success"]);
            return CommonApp::apiEncrypt($res);
        }
}
