<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\EmailScheduleSettings;
use App\Models\EmailScheduleTask;
use App\Models\Language;
use App\Models\NotificationSettings;
use App\Models\UserPreferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Enums\ConsolidatedEmail;

class StaffSettings extends Controller
{
    /* Get the email schedule task names */
    public function getEmailScheduleTask(){
        $getEmailScheduleTask = EmailScheduleTask::getEmailSchedule();
        if(!empty($getEmailScheduleTask)){
           $res = json_encode(['status_code'=>200,'status'=>'success','data'=>$getEmailScheduleTask]);
           return CommonApp::webEncrypt($res);
        }
        else{
           $res = json_encode(['status_code'=>400,'status'=>'Failure']);
           return CommonApp::webEncrypt($res);
        }
    }
    /* To update the staff preference of language and timezone */
    public function staffPreference(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            //'languageId' => 'required',
            //'timezoneId' => 'required',
            'workspaceId' => 'required',
            //'date_format'=>'required'
        ]);
        if($validated->fails()){
           $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
           return CommonApp::webEncrypt($res);
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
            if(isset($request->date_format) && $request->date_format != ""){
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
        }
        else{
            // $checkEntry['name'] = $staffDetails->name;
            $checkEntry['company_id'] = $request->companyId;
            $checkEntry['workspace_id'] =$request->workspaceId ;
            $checkEntry['user_id'] = '0';
            if(isset($request->staffId) && $request->staffId>0){
            $checkEntry['staff_id'] = $request->staffId;
            }
            if(isset($request->date_format) && $request->date_format != ""){
            $checkEntry['date_format'] = $request->date_format;
            }
            if(isset($request->languageId) && $request->languageId!=''){
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

        }
        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"User Preference Updated Successfully"]);
        return CommonApp::webEncrypt($res);
    }
    /* Give the stored data */
    public function getStaffPreferences(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'workspaceId' => 'required',
            'companyId' => 'required',
            'staffId' => 'required'
        ]);
        if($validated->fails()){
           $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
           return CommonApp::webEncrypt($res);
        }
        // $staffDetails = CommonApp::getStaffDetailsByEmail($request->email);
        $whereConditions = [
            ['staff_id','=',$request->staffId],
            ['workspace_id','=',$request->workspaceId],
            ['company_id','=',$request->companyId]
        ];
        $userPreference = UserPreferences::where($whereConditions)->first();
        $res = json_encode(["status_code"=>200,"data"=>$userPreference]);
        return CommonApp::webEncrypt($res);
    }

    /*
        Add User Notification Settings
    */

    public function notificationSettings(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'workspace_id' => 'required',
            'company_id' => 'required',
            'staff_id' => 'required',
        ]);
        if($validated->fails()){
           $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
           return CommonApp::webEncrypt($res);
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
            $notificationSettingsArr['email_daily_reminder'] = $request->email_daily_reminder??'7';
            $notificationSettingsArr['email_weekly_reminder'] = $request->email_weekly_reminder??'7';
            $notificationSettingsArr['email_task_accomplishment'] = $request->email_task_accomplishment??'7';
            $notificationSettingsArr['email_task_reschedule'] = $request->email_task_reschedule??'7';
            $notificationSettingsArr['email_due_today'] = $request->email_due_today??'7';
            $notificationSettingsArr['email_due_tomorrow'] = $request->email_due_tomorrow??'7';
            $notificationSettingsArr['email_daily_schedule'] = $request->email_daily_schedule??'7';
            $notificationSettingsArr['whatsapp'] =  $request->whatsapp??'7';
            $notificationSettingsArr['linemessenger'] = $request->linemessenger??'7';
            $notificationSettingsArr['sms'] = $request->sms??'7';
            $notificationSettingsArr['backup'] =  $request->backup??'7';
            $notificationSettingsArr['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
            $notificationSettingsArr['created_at'] = date('Y-m-d H:i:s');
            $notificationSettingsArr['updated_at'] = date('Y-m-d H:i:s');
            NotificationSettings::insert($notificationSettingsArr);
        }
        else{
            $notificationALdreadyExists['company_id'] = $request->company_id;
            $notificationALdreadyExists['workspace_id'] = $request->workspace_id;
            $notificationALdreadyExists['user_id'] = '0';
            $notificationALdreadyExists['staff_id'] = $request->staff_id;
            $notificationALdreadyExists['email_daily_reminder'] = $request->email_daily_reminder??'7';
            $notificationALdreadyExists['email_weekly_reminder'] = $request->email_weekly_reminder??'7';
            $notificationALdreadyExists['email_task_accomplishment'] = $request->email_task_accomplishment??'7';
            $notificationALdreadyExists['email_task_reschedule'] = $request->email_task_reschedule??'7';
            $notificationALdreadyExists['email_due_today'] = $request->email_due_today??'7';
            $notificationALdreadyExists['email_due_tomorrow'] = $request->email_due_tomorrow??'7';
            $notificationALdreadyExists['email_daily_schedule'] = $request->email_daily_schedule??'7';
            $notificationALdreadyExists['whatsapp'] =  $request->whatsapp??'7';
            $notificationALdreadyExists['linemessenger'] = $request->linemessenger??'7';
            $notificationALdreadyExists['sms'] = $request->sms??'7';
            $notificationALdreadyExists['backup'] =  $request->backup??'7';
            $notificationALdreadyExists['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
            $notificationALdreadyExists['created_at'] = date('Y-m-d H:i:s');
            $notificationALdreadyExists['updated_at'] = date('Y-m-d H:i:s');
            $notificationALdreadyExists->save();
        }
        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Notification Settings Updated Successfully"]);
        return CommonApp::webEncrypt($res);
    }

    /* Get Notification Settings */
    public function getNotificationSettings(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'workspace_id' => 'required',
            'company_id' => 'required',
            'staff_id' => 'required'
        ]);
        if($validated->fails()){
           $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
           return CommonApp::webEncrypt($res);
        }
        // $userDetails = CommonApp::getUserDetailsByEmail($request->email);
        $whereConditions = [
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$request->company_id],
            ['staff_id','=',$request->staff_id]
        ];
        $notificationSettings = NotificationSettings::where($whereConditions)->first();
       $res = json_encode(["status_code"=>200,"data"=>$notificationSettings]);
       return CommonApp::webEncrypt($res);
    }

    /* Add the email notification settings */
    public function emailScheduleNotification(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            'workspaceId' => 'required',
            'staffId' => 'required',
           // 'staffId' => 'required',
           // 'emailSchedule' => 'required',
           // 'task_id'=> 'required',
        ]);
        if($validated->fails()){
           $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $getUserId=0;
        $getworkspaceId=$request->workspaceId;
        $getcompanyId=$request->companyId;
        $getstaffId=$request->staffId;
        $getTaskDetails=$request->emailSchedule;
        //$companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);

        //$getTaskDetails=$request->emailSchedule;

        $whereConditionsDel = [
            //['user_id','=',$getUserId],
            ['workspace_id','=',$getworkspaceId],
            ['staff_id','=',$getstaffId],
            ['company_id','=',$getcompanyId]
        ];
        EmailScheduleSettings::where($whereConditionsDel)->delete();
        foreach($getTaskDetails as $taskDetails) {
            $getEmailScheduleTaskId=$taskDetails[0];
            $getEmailScheduleTaskDay=implode(",",$taskDetails[1]);
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
            $emailScheduleNotificationArr['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
            $emailScheduleNotificationArr['created_at']=  date('Y-m-d H:i:s');
            $emailScheduleNotificationArr['updated_at']=  date('Y-m-d H:i:s');
            EmailScheduleSettings::insert($emailScheduleNotificationArr);

           //$res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Email Schedule Settings Updated Successfully"]);
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
            $emailScheduleNotificationUpd['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
            $emailScheduleNotificationUpd['updated_at']=  date('Y-m-d H:i:s');
            EmailScheduleSettings::where($whereConditions)->update($emailScheduleNotificationUpd);

           //$res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Email Schedule Settings Updated Successfully"]);
        }
    }
    $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Email Schedule Settings Updated Successfully"]);
    return CommonApp::webEncrypt($res);
    }

    /* Get the email schedule notification settings */
    public function getemailScheduleNotification(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'workspaceId' => 'required',
            'companyId' => 'required',
            'staffId' => 'required',
        ]);
        if($validated->fails()){
           $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
           return CommonApp::webEncrypt($res);
        }
        $whereConditions = [
            ['staff_id','=',$request->staffId],
            ['workspace_id','=',$request->workspaceId],
            ['company_id','=',$request->companyId]
        ];
        $emailSettings = EmailScheduleSettings::where($whereConditions)
        ->select('company_id','workspace_id','staff_id','email_schedule_task_id','name','email_to_user_id','days','is_consolidated_mail')
        ->get();
       $res = json_encode(["status_code"=>200,"data"=>$emailSettings]);
       return CommonApp::webEncrypt($res);
    }

}
