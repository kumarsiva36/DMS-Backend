<?php

namespace App\Http\Controllers\Website\Common;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailScheduleSettings;
use App\Models\EmailScheduleTask;
use App\Models\NotificationSettings;
use App\Models\UserPreferences;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Models\EmailScheduleReport;
use App\Models\EmailNotificationSettings;

class UserSettings extends Controller
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

    /* To update the user preference of language and timezone */
    public function userPreference(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            'workspaceId' => 'required',
            // 'timezoneId' => 'required',
            // 'workspaceId' => 'required',
            // 'date_format'=>'required'
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
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
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Get the user preferences */
    public function getUserPreferences(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            'workspaceId' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->companyId);
        $whereConditions = [
            ['user_id','=',$companyDetails->user_id],
            ['workspace_id','=',$request->workspaceId],
            ['company_id','=',$companyDetails->id]
        ];
        $userPreference = UserPreferences::getUserSettingPreferences($whereConditions);
        $res = json_encode(["status_code"=>200,"data"=>$userPreference]);
        return CommonApp::webEncrypt($res);
    }

    /*
        Add User Notification Settings
    */

    public function notificationSettings(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
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
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Get Notification Settings */
    public function getNotificationSettings(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions = [
            ['user_id','=',$companyDetails->user_id],
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$companyDetails->id]
        ];
        $notificationSettings = NotificationSettings::getNotificationSettings($whereConditions);
        $res = json_encode(["status_code"=>200,"data"=>$notificationSettings]);
        return CommonApp::webEncrypt($res);
    }

    /*
        To add email notification-settings
    */
    public function emailScheduleNotification(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            'workspaceId' => 'required',
            'userId' => 'required',
           // 'staffId' => 'required',
           // 'emailSchedule' => 'required',
           // 'task_id'=> 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            EmailScheduleSettings::addEmailScheduleNotification($request);
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Email Schedule Settings Updated Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Get the email schedule notification settings */
    public function getemailScheduleNotification(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            'workspaceId' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->companyId);
        $whereConditions = [
            ['user_id','=',$companyDetails->user_id],
            ['workspace_id','=',$request->workspaceId],
            ['company_id','=',$companyDetails->id]
        ];
        $emailSettings = EmailScheduleSettings::getEmailScheduleSettings($whereConditions);
        $res = json_encode(["status_code"=>200,"data"=>$emailSettings]);
        return CommonApp::webEncrypt($res);
    }

    /* Get The selected Dashboard Notifications  */
    public function dashboardNotifications(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $dashBoardArr=[];
        $dashBoardArr['widgetNames'] = config('constant.dashboard_modules');
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
        ];
        if($request->user_id > 0 && $request->staff_id == 0){
            $whereConditions[]=['user_id','=',$request->user_id];
        }
        if($request->staff_id > 0){
            $whereConditions[]=['staff_id','=',$request->staff_id];
        }
        $dashBoardArr['dashboardWidgets'] = UserPreferences::getSelectDashboardWidgets($whereConditions);

        $res = json_encode(["status_code"=>200,"data"=>$dashBoardArr]);
        return CommonApp::webEncrypt($res);
    }

    /* Add the dashboard notification widgets to be shown on the dashboard */
    public function addDashboardNotifications(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            // 'data'=>"required"
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
        ];
        if($request->user_id > 0 && $request->staff_id == 0)
            $whereConditions[]=['user_id','=',$request->user_id];
        if($request->staff_id > 0)
            $whereConditions[]=['staff_id','=',$request->staff_id];

        try{
            UserPreferences::addDashboardWidget($request,$whereConditions);
            $res = json_encode(["status_code"=>200,"message"=>"Added Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"status" =>"Failure",
            "error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /*
        To add email notification order Id settings
    */
    public function emailScheduleReportsOrderIds(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            'workspaceId' => 'required',
            'userId' => 'required',
            'staffId' => 'required',
            'task_id' => 'required',
            'order_ids'=>'required|array',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            EmailScheduleReport::addEmailScheduleReportNotification($request);
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Email Schedule Settings Updated Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }
    /*
        To get email notification order Id settings
    */
    public function get_emailScheduleReportsOrderIds(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'companyId' => 'required',
            'workspaceId' => 'required',
            'userId' => 'required',
            'staffId' => 'required',
            'task_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            $data = EmailScheduleReport::getEmailScheduleReportSettings($request);
            $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$data]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }
/*
        Add / Edit User Email Notification Settings
    */

    public function emailNotificationSetting(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
           try{
            EmailNotificationSettings::addEmailNotinficationSettings($request);
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Email Notification Settings Updated Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /*
        View User Email Notification Settings
    */

    public function viewEmailNotificationSetting(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
           try{
           $getEmailset= EmailNotificationSettings::ViewEmailNotificationSettings($request);
            $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$getEmailset]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

}
