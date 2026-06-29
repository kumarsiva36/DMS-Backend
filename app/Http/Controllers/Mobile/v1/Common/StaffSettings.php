<?php

namespace App\Http\Controllers\Mobile\v1\Common;

use App\Common\CommonApp;
use App\Enums\ConsolidatedEmail;
use App\Http\Controllers\Controller;
use App\Models\EmailScheduleSettings;
use App\Models\Language;
use App\Models\NotificationSettings;
use App\Models\UserPreferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TimeZoneFormat;
use App\Models\EmailScheduleTask;

class StaffSettings extends Controller
{
    /* To update the staff preference of language and timezone */
    public function staffPreference(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            //'languageId' => 'required',
            //'timezoneId' => 'required',
            'workspaceId' => 'required',
            //'date_format'=>'required'
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        // $staffDetails = CommonApp::getCompanyDetailsbyID($request->companyId);
        $whereConditions = [
            ['staff_id','=',$request->staffId],
            ['workspace_id','=',$request->workspaceId],
            ['company_id','=',$request->companyId]
        ];
        $checkEntry = UserPreferences::where($whereConditions)->first();
        if(empty($checkEntry)){
            $userPreferencesArr = [];
            // $userPreferencesArr['name'] = $staffDetails->name;
            $userPreferencesArr['company_id'] = $request->companyId;
            $userPreferencesArr['workspace_id'] =$request->workspaceId;
            $userPreferencesArr['user_id'] = '0';
            if(isset($request->staffId) && $request->staffId>0){
            $userPreferencesArr['staff_id'] = $request->staffId;
            }
            if(isset($request->date_format) && $request->date_format!=''){
            $userPreferencesArr['date_format'] = $request->date_format;
            }
            if(isset($request->languageId) && $request->languageId!=''){
            $userPreferencesArr['language_id'] = $request->languageId;
            }
            if(isset($request->timezoneId) && $request->timezoneId!=''){
            $userPreferencesArr['time_zone_format'] = $request->timezoneId;
            }
            $userPreferencesArr['created_at'] = date('Y-m-d H:i:s');
            $userPreferencesArr['updated_at'] = date('Y-m-d H:i:s');
            UserPreferences::insert($userPreferencesArr);
            /* This is to update the language and time zone in staff table*/
            // $language = Language::where('id',$request->language_id)->first();
            // $staffDetails->language = $language->lang_code;
            // $staffDetails->timezone = $request->time_zone_format;
            // $staffDetails->save();

            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"User Preference Updated Successfully"]);
            return CommonApp::apiEncrypt($res);
        }
        else{
            // $checkEntry['name'] = $staffDetails->name;
            $checkEntry['company_id'] = $request->companyId;
            $checkEntry['workspace_id'] =$request->workspaceId ;
            $checkEntry['user_id'] = '0';
            if(isset($request->staffId) && $request->staffId>0){
            $checkEntry['staff_id'] = $request->staffId;
            }
            if(isset($request->date_format) && $request->date_format!=''){
            $checkEntry['date_format'] = $request->date_format;
            }
            if(isset($request->languageId) && $request->languageId>0){
            $checkEntry['language_id'] = $request->languageId;
            }
            if(isset($request->timezoneId) && $request->timezoneId!=''){
            $checkEntry['time_zone_format'] = $request->timezoneId;
            }
            $checkEntry['created_at'] = date('Y-m-d H:i:s');
            $checkEntry['updated_at'] = date('Y-m-d H:i:s');
            $checkEntry->save();
            /* This is to update the language and time zone in staff table*/
            // $language = Language::where('id',$request->language_id)->first();
            // $staffDetails->language = $language->lang_code;
            // $staffDetails->timezone = $request->time_zone_format;
            // $staffDetails->save();

            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"User Preference Updated Successfully"]);
            return CommonApp::apiEncrypt($res);
        }
    }
    /* Give the stored data */
    public function getStaffPreferences(Request $request){
        $validated = Validator::make($request->all(),[
            'email' => 'required',
            'workspace_id' => 'required',
            'company_id' => 'required',
            'staff_id' => 'required'
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $staffDetails = CommonApp::getStaffDetailsByEmail($request->email);
        $whereConditions = [
            ['staff_id','=',$request->staff_id],
            ['workspace_id','=',$request->workspace_id],
            ['name','=',$staffDetails->name],
            ['company_id','=',$request->company_id]
        ];
        $userPreference = UserPreferences::where($whereConditions)->first();
        return response()->json(["status_code"=>200,"data"=>$userPreference]);
    }

    /*
        Add User Notification Settings
    */

    public function notificationSettings(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'workspace_id' => 'required',
            'company_id' => 'required',
            'staff_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereConditions = [
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$request->company_id],
            ['staff_id','=',$request->staff_id],
        ];
        $notificationALdreadyExists = NotificationSettings::where($whereConditions)->first();
        if(empty($notificationALdreadyExists)){
            $notificationSettingsArr=[];
            $notificationSettingsArr['company_id'] = $request->company_id;
            $notificationSettingsArr['workspace_id'] = $request->workspace_id;
            $notificationSettingsArr['user_id'] = '0';
            $notificationSettingsArr['staff_id'] = $request->staff_id;
            $notificationSettingsArr['email_daily_reminder'] = $request->email_daily_reminder ?? '7' ;
            $notificationSettingsArr['email_weekly_reminder'] = $request->email_weekly_reminder ?? '7' ;
            $notificationSettingsArr['email_task_accomplishment'] = $request->email_task_accomplishment ?? '7';
            $notificationSettingsArr['email_task_reschedule'] = $request->email_task_reschedule ?? '7';
            $notificationSettingsArr['email_due_today'] = $request->email_due_today ?? '7';
            $notificationSettingsArr['email_due_tomorrow'] = $request->email_due_tomorrow ?? '7';
            $notificationSettingsArr['email_daily_schedule'] = $request->email_daily_schedule ?? '7';
            $notificationSettingsArr['whatsapp'] =  $request->whatsapp ?? '7';
            $notificationSettingsArr['linemessenger'] = $request->linemessenger ?? '7';
            $notificationSettingsArr['sms'] = $request->sms ?? '7';
            $notificationSettingsArr['backup'] =  $request->backup ?? '7';
            $notificationSettingsArr['created_at'] = date('Y-m-d H:i:s');
            $notificationSettingsArr['updated_at'] = date('Y-m-d H:i:s');
            $notificationSettingsArr['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
            NotificationSettings::insert($notificationSettingsArr);

            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Notification Settings Updated Successfully"]);
            return CommonApp::apiEncrypt($res);
        }
        else{
            $notificationALdreadyExists['company_id'] = $request->company_id;
            $notificationALdreadyExists['workspace_id'] = $request->workspace_id;
            $notificationALdreadyExists['user_id'] = '0';
            $notificationALdreadyExists['staff_id'] = $request->staff_id;
            $notificationALdreadyExists['email_daily_reminder'] = $request->email_daily_reminder ?? '7' ;
            $notificationALdreadyExists['email_weekly_reminder'] = $request->email_weekly_reminder ?? '7' ;
            $notificationALdreadyExists['email_task_accomplishment'] = $request->email_task_accomplishment ?? '7';
            $notificationALdreadyExists['email_task_reschedule'] = $request->email_task_reschedule ?? '7';
            $notificationALdreadyExists['email_due_today'] = $request->email_due_today ?? '7';
            $notificationALdreadyExists['email_due_tomorrow'] = $request->email_due_tomorrow ?? '7';
            $notificationALdreadyExists['email_daily_schedule'] = $request->email_daily_schedule ?? '7';
            $notificationALdreadyExists['whatsapp'] =  $request->whatsapp ?? '7';
            $notificationALdreadyExists['linemessenger'] = $request->linemessenger ?? '7';
            $notificationALdreadyExists['sms'] = $request->sms ?? '7';
            $notificationALdreadyExists['backup'] =  $request->backup ?? '7';
            $notificationALdreadyExists['created_at'] = date('Y-m-d H:i:s');
            $notificationALdreadyExists['updated_at'] = date('Y-m-d H:i:s');
            $notificationALdreadyExists['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
            $notificationALdreadyExists->save();

            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Notification Settings Updated Successfully"]);
            return CommonApp::apiEncrypt($res);
        }
    }

    public function getNotificationSettings(Request $request){
        $validated = Validator::make($request->all(),[
            'email' => 'required',
            'workspace_id' => 'required',
            'company_id' => 'required',
            'staff_id' => 'required'
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $userDetails = CommonApp::getUserDetailsByEmail($request->email);
        $whereConditions = [
            ['user_id','=',$userDetails->id],
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$request->company_id],
            ['staff_id','=',$request->staff]
        ];
        $notificationSettings = NotificationSettings::where($whereConditions)->first();
        return response()->json(["status_code"=>200,"data"=>$notificationSettings]);
    }

    public function emailScheduleNotification(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $request->emailSchedule = json_decode(json_encode($request->emailSchedule), true);
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            'workspaceId' => 'required',
            'staffId' => 'required',
           // 'staffId' => 'required',
            'emailSchedule' => 'required',
           // 'task_id'=> 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $getUserId=0;
        $getworkspaceId=$request->workspaceId ?? 0;
        $getcompanyId=$request->companyId ?? 0;
        $getstaffId=$request->staffId ?? 0;
        $getTaskDetails=$request->emailSchedule;
        //$companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);

        //$getTaskDetails=$request->emailSchedule;

        $whereConditionsDel = [
           // ['user_id','=',$getUserId],
            ['workspace_id','=',$getworkspaceId],
            ['staff_id','=',$getstaffId],
            ['company_id','=',$getcompanyId]
        ];
        EmailScheduleSettings::where($whereConditionsDel)->delete();
        foreach($getTaskDetails as $taskDetails) {
            $getEmailScheduleTaskId=$taskDetails['id'];
            $getEmailScheduleTaskDay=implode(",",$taskDetails['days']);
        $whereConditions = [
            ['user_id',$getUserId],
            ['workspace_id','=',$getworkspaceId],
            ['staff_id','=',$getstaffId],
          //  ['name','=',$request->task_name],
           ['email_schedule_task_id','=',$getEmailScheduleTaskId],
           ['company_id','=',$getcompanyId]
        ];

        $aldreadyExists = EmailScheduleSettings::where($whereConditions)->first();
        //return response()->json($aldreadyExists);
        if(empty($aldreadyExists)){
            $emailScheduleNotificationArr=[];
            $emailScheduleNotificationArr['company_id']= $getcompanyId;
            $emailScheduleNotificationArr['workspace_id']= $getworkspaceId;
            $emailScheduleNotificationArr['user_id']= $getUserId;
            $emailScheduleNotificationArr['staff_id']= $getstaffId ;
            $emailScheduleNotificationArr['email_schedule_task_id']=$getEmailScheduleTaskId ;
            $emailScheduleNotificationArr['name']= '';
            $emailScheduleNotificationArr['email_to_user_id']= $getUserId;
            $emailScheduleNotificationArr['email_to_staff_id']=$getstaffId;
            $emailScheduleNotificationArr['days']= $getEmailScheduleTaskDay;
            $emailScheduleNotificationArr['created_at']=  date('Y-m-d H:i:s');
            $emailScheduleNotificationArr['updated_at']=  date('Y-m-d H:i:s');
            $emailScheduleNotificationArr['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
            EmailScheduleSettings::insert($emailScheduleNotificationArr);

           // return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Email Schedule Settings Updated Successfully"]);
        }
        else{
            $emailScheduleNotificationUpd=[];
            $emailScheduleNotificationUpd['company_id']= $getcompanyId;
            $emailScheduleNotificationUpd['workspace_id']= $getworkspaceId;
            $emailScheduleNotificationUpd['user_id']= $getUserId;
            $emailScheduleNotificationUpd['staff_id']= $getstaffId ;
            $emailScheduleNotificationUpd['email_schedule_task_id']=$getEmailScheduleTaskId ;
            $emailScheduleNotificationUpd['name']= '';
            $emailScheduleNotificationUpd['email_to_user_id']= $getUserId;
            $emailScheduleNotificationUpd['email_to_staff_id']=$getstaffId;
            $emailScheduleNotificationUpd['days']= $getEmailScheduleTaskDay;
            $emailScheduleNotificationUpd['updated_at']=  date('Y-m-d H:i:s');
            $emailScheduleNotificationArr['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
            EmailScheduleSettings::where($whereConditions)->update($emailScheduleNotificationUpd);

           // return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Email Schedule Settings Updated Successfully"]);
        }
    }
    $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Email Schedule Settings Updated Successfully"]);
    return CommonApp::apiEncrypt($res);
    }

    public function getemailScheduleNotification(Request $request){
        $validated = Validator::make($request->all(),[
            'email' => 'required',
            'workspace_id' => 'required',
            'company_id' => 'required',
            'staff_id' => 'required',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $whereConditions = [
            ['staff_id','=',$request->staff_id],
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$request->company_id]
        ];
        $emailSettings = EmailScheduleSettings::where($whereConditions)->first();
        return response()->json(["status_code"=>200,"data"=>$emailSettings]);
    }

       /* To List ALl Staff Settings in Single API For Mobile */
       public function getAllStaffsettings(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            'workspaceId' => 'required',
            'userId' => 'required',
            'staffId' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }

        $responseArray=[];
        $getGeneralSettingsDetails=$this->getGeneralSettings();
        $responseArray['general_settings']=$getGeneralSettingsDetails;
        $responseArray['selected_general_settings']=$this->getGeneralSettingsSelected($request->companyId,$request->workspaceId,$request->userId,$request->staffId);
        $getNotificationTasks=$this->getNotificationSettingsDetails();
        $responseArray['notification_settings']=$getNotificationTasks;
        $responseArray['selected_notification_settings']=$this->getNotificationSelected($request->companyId,$request->workspaceId,$request->userId,$request->staffId);

        $getEmailScheduleTasks=$this->getEmailScheduleTaskDetails();
        $responseArray['email_schedule_settings']=$getEmailScheduleTasks;
        $responseArray['selected_email_schedule_settings']=$this->getEmailScheduleTaskSelected($request->companyId,$request->workspaceId,$request->userId,$request->staffId);

        $whereConditions=[
            ['company_id','=',$request->companyId],
            ['workspace_id','=',$request->workspaceId],
        ];
        if((isset($request->userId) && isset($request->staffId)) && $request->userId > 0 && $request->staffId == 0){
            $whereConditions[]=['user_id','=',$request->userId];
        }
        if(isset($request->staffId) && $request->staffId > 0){
            $whereConditions[]=['staff_id','=',$request->staffId];
        }
        $responseArray['dashboardSettings']=$this->getDashboardWidgets($whereConditions);

        $res = json_encode(["status_code"=>200,"data"=>$responseArray]);
        return CommonApp::apiEncrypt($res);
    }
    public static function  getGeneralSettings(){
       // $dateFormat=array("d M Y"=>"01 Jan 2022","d-m-Y"=>"DD-MM-YYYY","Y-m-d"=>"YYYY-MM-DD","Y M d"=>"2022 Jan 01");
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
    public static function  getGeneralSettingsSelected($companyId,$workspaceId,$userId,$staffId){
      $whereConditions = [
           // ['user_id','=',$userId],
            ['staff_id','=',$staffId],
            ['workspace_id','=',$workspaceId],
            ['company_id','=',$companyId]
        ];
      $getSetting= UserPreferences::select("date_format","language_id","time_zone_format","dashboard_widget_ids")->where($whereConditions)->first();
      if($getSetting!=''){
      return array("date_format"=>$getSetting['date_format'],"language_id"=>$getSetting['language_id'],"time_zone_format"=>$getSetting['time_zone_format']);
      }else{
       return array("date_format"=>"","language_id"=>0,"time_zone_format"=>"");
       }
     }
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
     public function getNotificationSelected($companyId,$workspaceId,$userId,$staffId){
        $whereConditions = [
            // ['user_id','=',$userId],
             ['staff_id','=',$staffId],
             ['workspace_id','=',$workspaceId],
             ['company_id','=',$companyId]
         ];
        $notificationSettings = NotificationSettings::where($whereConditions)->first();
        if($notificationSettings!=''){
            $notifyArray=array("email_daily_reminder"=>$notificationSettings['email_daily_reminder'],"email_weekly_reminder"=>$notificationSettings['email_weekly_reminder'],"email_task_accomplishment"=>$notificationSettings['email_task_accomplishment'],"email_task_reschedule"=>$notificationSettings['email_task_reschedule'],"email_due_today"=>$notificationSettings['email_due_today'],"email_due_tomorrow"=>$notificationSettings['email_due_tomorrow'],"email_daily_schedule"=>$notificationSettings['email_daily_schedule'],"whatsapp"=>$notificationSettings['whatsapp'],"linemessenger"=>$notificationSettings['linemessenger'],"is_consolidated_mail"=>$notificationSettings['is_consolidated_mail']);
        }else{
            $notifyArray=array("email_daily_reminder"=>"","email_weekly_reminder"=>"","email_task_accomplishment"=>"","email_task_reschedule"=>"","email_due_today"=>"","email_due_tomorrow"=>"","email_daily_schedule"=>"","whatsapp"=>"","linemessenger"=>"","is_consolidated_mail"=>'1');
        }
        return  $notifyArray;
     }
     public function getEmailScheduleTaskSelected($companyId,$workspaceId,$userId,$staffId){
        $whereConditions = [
            // ['user_id','=',$userId],
             ['staff_id','=',$staffId],
             ['workspace_id','=',$workspaceId],
             ['company_id','=',$companyId]
         ];
          $emailSettings = EmailScheduleSettings::where($whereConditions)
        ->select('email_schedule_task_id','days','is_consolidated_mail')
        ->get();
       return array("emailsettings" => $emailSettings);
     }
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

    /* Update Language Code Staff Settings in Single API For Mobile */
    public function updateLanguageStaffsettings(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            'workspaceId' => 'required',
             'staffId' => 'required',
            'languageId' =>  'required|numeric',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $langArray=[];
        $langArray['language_id']=$request->languageId;

        $whereConditions = [
             ['user_id','=',0],
             ['staff_id','=',$request->staffId],
             ['workspace_id','=',$request->workspaceId],
             ['company_id','=',$request->companyId]
         ];
        $count =  UserPreferences::where($whereConditions)->count();
        if($count > 0){
            UserPreferences::where($whereConditions)->update($langArray);
        }else{
            $arr=[];
            $arr['company_id'] = $request->companyId;
            $arr['workspace_id'] = $request->workspaceId;
            $arr['user_id'] = 0;
            $arr['staff_id'] = $request->staffId;
            $arr['language_id'] = $request->languageId;
            UserPreferences::insert($arr);
        }
        $res = json_encode(["status_code"=>200,"message"=>"Success"]);
        return CommonApp::apiEncrypt($res);
    }

}
