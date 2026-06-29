<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Common\CommonApp;
use App\Common\GetUserLanguage;
use App\Common\Logs;
use App\Common\Mailconfig;
use App\Http\Controllers\Controller;
use App\Jobs\staffRegisterJob;
use App\Jobs\UserLoginConfirmationJob;
use App\Mail\staffOtpMail;
use App\Models\CompanySettings;
use App\Models\EmailScheduleSettings;
use App\Models\Language;
use App\Models\NotificationSettings;
use App\Models\OrderTask;
use App\Models\Roles;
use App\Models\Staff;
use App\Models\UserHistory;
use App\Models\UserPreferences;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class Staffs extends Controller
{
    /* To Register a New Staff */
    public function register(Request $request){
        // dd($request);
        $header = $request;
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id'=>'required',
            'workspace_id'=>'required',
            'first_name'=>'required|max:60',
            // 'last_name'=>'required|max:60',
            // 'ip_address'=>'ip',
            // 'address1' => 'required',
            // 'address2' => 'required',
            // 'city'=> 'required',
            // 'state'=> 'required',
            // 'country'=> 'required|numeric',
            // 'zipcode'=> 'required|numeric',
            'email' => 'required|email:dns,rfc',
            //'mobile' => 'required',
            'role_id'=>'required'
        ]);

        if($validated->fails()) {
            $res = json_encode(["status_code"=>401,"status"=>"Failed","message"=>"Validation Errors","errors"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['email','=',$request->email]
        ];
        $ifStaffExistsInSameCompany = Staff::checkIfUserExistsInSameCompany($request);
        if(!empty($ifStaffExistsInSameCompany)){
            $res = json_encode(["status_code"=>400,"message"=>"Staff already exists"]);
            return CommonApp::apiEncrypt($res);
        }
        else{
            $userDataArray = array(
                "company_id"=>$request->company_id,
                "workspace_id"=>$request->workspace_id,
                "user_id"=>$companyDetails->user_id,
                "role_id"=>$request->role_id,
                "first_name" => $request->first_name,
                "last_name" => trim($request->last_name),
                "email" => $request->email,
                "mobile" => $request->mobile,
                // "ip_address"=>$request->ip_address,
                "address1" => $request->address1 ?? '',
                "address2" => $request->address2 ?? '',
                "city" => $request->city ?? '',
                "state" => $request->state ?? '',
                "country" => $request->country ?? '0',
                "zipcode" => $request->zipcode ?? '',
                "status" =>$request->status ?? '1',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            );
            $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"User",$request->user_id);
            $staff = Staff::create($userDataArray);
            if(!is_null($staff)) {
                $details=[];
                $details['to']=$staff->email;
                $details['userName'] = $staff->first_name.' '.$staff->last_name;
                $details['workspaceName'] = (Workspace::getWorkspaceDetails($request))->name;
                $details['language']=$language;
                staffRegisterJob::dispatch($details);
                $res = json_encode(["status_code" =>200,"status"=>"success",
                "message" => "Staff Added successfully", "data" => $staff]);
                return CommonApp::apiEncrypt($res);
            }

            else{
                $res = json_encode(["status_code"=>400,"status"=>"failed", "message"=>"Failed to register"]);
                return CommonApp::apiEncrypt($res);
            }
        }
    }

    /*
        This function is to get the otp and send it through mail
    */
    public function getOtp(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "email" => 'required|email',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $confirmation = $request->confirmation ?? 0;
        $resend = $request->resend ?? 0;
        $staff_email = Staff::getStaffByEmailLogin($request->email);

        if(!is_null($staff_email)){
            $token_count = DB::table("oauth_access_tokens")->where('user_id','=',$staff_email->id)->where('name','=','StaffAPIToken')
            ->where('revoked','=','0')->count();

            if($token_count>0 && $confirmation==0){
                $res = json_encode(["status_code"=>409, "message"=>"Already Logged in another device. Are sure logged out other devices?"]);
                return CommonApp::apiEncrypt($res);
            }

            $language = GetUserLanguage::getUserLanguageWithEmail($request->email,"Staff");
            App::setLocale($language);
            $otp = CommonApp::generate_Login_OTP();
            $staff_email->otp = $otp;
            $staff_email->otp_generated_time = Carbon::now();
            $staff_email->save();
            Mailconfig::staffOtpSendMail($request->email,$staff_email,$language,$resend);
            $res = json_encode(["status_code"=>200 ,"message"=>"User Found & OTP Mail Sent"]);
            return CommonApp::apiEncrypt($res);
        }
        else{
            $res = json_encode(["status_code"=>400, "message"=>"Contact your DMS ADMIN for Login"]);
            return CommonApp::apiEncrypt($res);
        }
    }

    /* This function validates the OTP and gives access token */
    public function otpValidate(Request $request){
        $header = $request;
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "email" => 'required|email',
            "otp" => 'required|numeric|integer'
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $staff_email = $request->email;
        $staff = Staff::getStaffByEmailLogin($request->email);
        $language = GetUserLanguage::getUserLanguageWithEmail($request->email,"Staff");
        App::setLocale($language);
        $otp = $staff->otp;
        $user_entered_otp = $request->otp;
        // $browserDetails = $request->header('User-Agent');
        $detailsArr=[];
        $detailsArr['device_id'] = $header->header('device-id');
        $detailsArr['platform'] = $header->header('platform');
        $detailsArr['os_version'] = $header->header('os-version');
        $detailsArr['app_version'] = $header->header('app-version');
        $detailsArr['mobile_model'] = $header->header('mobile-model');
        $ipAddress = $header->ip();
        $logType = "Login";
        $logUserType = "Staff";
        $firstName=$staff->first_name;
        $lastName=$staff->last_name;
        $userArr = [];
        $userArr['userID'] = $staff->id;
        $userArr['userName'] = $staff->first_name." ".$staff->last_name;
        $userArr['companyID'] = $staff->company_id;
        $userArr['browserDetails'] = json_encode($detailsArr);
        $userArr['ipAddress'] = $ipAddress;
        $userArr['logType'] = $logType;
        $userArr['logUserType'] = $logUserType;
        if($otp == $user_entered_otp){
            $staff->tokens()->where('name', 'StaffAPIToken')->delete(); //Delete all previous tokens
            $staffPresentList = Staff::getStaffByEmailLogin($request->email,"validateOTP");
            $Preferencesetting = UserPreferences::getStaffPreference($staff);
            if(empty($Preferencesetting)){
                $slanguageId=0;
                $slanguage_code='en';
            }else{
                $slanguageId=$Preferencesetting->language_id;
                if($slanguageId>0){
                $slangc=Language::getLanguagesCodeUsingId($slanguageId);
                $slanguage_code=$slangc->lang_code;
                }else{
                    $slanguage_code='en';
                }
            }
            $workspaceList=[];
            foreach ($staffPresentList as $staffs){
                $staffPreferences = UserPreferences::getStaffPreference($staffs);
                $company = CompanySettings::getCompanyInfoUsingID($staffs->company_id);
                if(empty($staffPreferences)){
                    $dateFormat="";
                    $language_id=0;
                    $language_code='en';
                }else{
                    $dateFormat=$staffPreferences->date_format;
                    $language_id=$staffPreferences->language_id;
                    if($language_id>0){
                    $langc=Language::getLanguagesCodeUsingId($language_id);
                    $language_code=$langc->lang_code;
                    }else{
                    $language_code='en';
                    }
                }
                $workspaces = CommonApp::getWorkspaceDetails($staffs->user_id);
                $planExpiryDate = CompanySettings::getExpiryInfo($staffs);
                if(strtotime(date('Y-m-d')) >= strtotime(date('Y-m-d',strtotime($planExpiryDate->account_expire_at))) ){
                    $plan = "Expired";
                }else{
                    $plan = "Active";
                }
                if($plan == "Active"){
                    $staffWorkspace=[];
                    $staffWorkspace['company_id']=$staffs->company_id;
                    $staffWorkspace['user_id']=$staffs->user_id;
                    $staffWorkspace['staff_id']=$staffs->id;
                    $staffWorkspace['user_name']=$staffs->first_name." ".$staffs->last_name;
                    $staffWorkspace['workspace_id']=$workspaces->id;
                    $staffWorkspace['workspaceName']=$workspaces->name;
                    $staffWorkspace['workspaceType']=$workspaces->workspace_type;
                    $staffWorkspace['language']=$language_code;
                    $staffWorkspace['language_id']=$language_id;
                    $roles = Roles::getRoleName($staffs->role_id);
                    $permissions = Roles::getPermissionsForStaff($staffs);
                    $modules = Roles::getModulesForStaff($staffs);
                    $staffWorkspace['permissions']=$permissions;
                    $staffWorkspace['module']=isset($modules)?
                    array_values(array_unique($modules)):[];
                    $staffWorkspace['role']=$roles->name;
                    $staffWorkspace['roleId']=$roles->id;
                    $staffWorkspace['dateformat']=$dateFormat;
                    $user_email= DB::table("users")->where('company_id','=',$staffs->company_id)->pluck('email')->first();
                    $staffWorkspace['admin_email']=($staff_email==$user_email)?1:0;
                    $workspaceList[]=$staffWorkspace;
                }
            }
            if(empty($workspaceList)){
                $res = json_encode(["status_code"=>401,"message"=>"Plan Expired! Contact Admin."]);
                return CommonApp::apiEncrypt($res);
            }
            $permission = $workspaceList[0]['permission'] ?? [];
            $moduler = $workspaceList[0]['module'] ?? [];
            $module = isset($moduler)?array_values(array_unique($moduler)):[];
            $token = $staff->createToken('StaffAPIToken')->accessToken;
            $loginStatus = "Success";
            $userArr['loginStatus'] = $loginStatus;
            $details=[];
            $details['to']=$request->email;
            $details['userName']=$staff->first_name." ".$staff->last_name;
            $details['language']=$language;
            UserLoginConfirmationJob::dispatch($details);
            Logs::userLogs($userArr);

            //$staff->last_seen = date('Y-m-d H:i:s');
            $staff->device_details  = json_encode($detailsArr);
            $staff->fcm_token  = $request->fcm_token??Null;
            $staff->save();

            $res = json_encode(["status_code"=>200,"user_id"=>$workspaceList[0]['user_id'],"email"=>$staff_email,
            "staff_id"=>$workspaceList[0]['staff_id'],"user_name"=>$workspaceList[0]['user_name'],"login_type"=>$logUserType,
            "company_id"=>$workspaceList[0]['company_id'],"workspace_id"=>$workspaceList[0]['workspace_id'],"workspaceName"=>$workspaceList[0]['workspaceName'],
            "workspaceType"=>$workspaceList[0]['workspaceType'],"role"=>$workspaceList[0]['role'],"language"=>$slanguage_code,"language_id"=>$slanguageId,
            "roleId"=>$workspaceList[0]['roleId'],"workspacesList"=>$workspaceList,"permission"=>$permission,"module"=>$module,"dateformat"=>$workspaceList[0]['dateformat'],
            "token"=>$token,"message"=>"OTP Verified Successfully"],200);
            return CommonApp::apiEncrypt($res);
        }
        else{
            $loginStatus = "Failure";
            $userArr['loginStatus'] = $loginStatus;
            Logs::userLogs($userArr);
            $res = json_encode(["status_code"=>400,"message"=>"Incorrect OTP, Please Enter Correctly"]);
            return CommonApp::apiEncrypt($res);
        }
    }

    /* To Edit the staff */
    public static function editStaff(Request $request){
        $validated = Validator::make($request->all(),[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'staff_id' => 'required'
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        // dd($request);
        $staffDetails = Staff::getStaffDetailForEdit($request);

        return response()->json(["status_code"=>200,"data"=>$staffDetails]);
    }

    /* To get the staff list */
    public function getStaffList(Request $request){
        $validated = Validator::make($request->all(),[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $staffList = Staff::getStaffLists($request);

        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$staffList]);
    }

    /* To Update the staff */
    public function updateStaff(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'staff_id'=>'required',
            'company_id'=>'required',
            'workspace_id'=>'required',
            'first_name'=>'required|max:60',
          //  'last_name'=>'required|max:60',
           // 'email' => 'required|email:dns,rfc',
            'mobile' => 'required',
            'role_id'=>'required'
        ]);

        if($validated->fails()) {
            $res = json_encode(["status_code"=>401,"status"=>"Failed","message"=>"Validation Errors","errors"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $staff = Staff::getStaffByID($request->staff_id);
        $isStaffActiveInOrder = OrderTask::checkIfStaffIsPresentInActiveOrder($request->staff_id);

        if(count($isStaffActiveInOrder)>0 && ($request->status === "2")){
            $res = json_encode(["status_code" =>600,"status"=>"failure","message" => "Please Remove Staff from Order to Deactivate."]);
            return CommonApp::apiEncrypt($res);
        }
        // $staff->company_id=$request->company_id;
        // $staff->workspace_id=$request->workspace_id;
        // $staff->user_id=$companyDetails->user_id;
        $staff->role_id=$request->role_id ?? 0;
        $staff->first_name = $request->first_name ?? "";
        $staff->last_name = $request->last_name ? trim($request->last_name) : "";
       // $staff->email = $request->email;
        $staff->mobile = $request->mobile;
        $staff->address1 = $request->address1 ?? "";
        $staff->address2 = $request->address2 ?? "";
        $staff->city = $request->city ?? "";
        $staff->state= $request->state ?? "";
        $staff->country= $request->country ?? "0";
        $staff->zipcode= $request->zipcode ?? "";
        $staff->status=$request->status ?? '1';
       // $staff->created_at= Carbon::now();
        $staff->updated_at= Carbon::now();
        $staff->save();
        $res = json_encode(["status_code" =>200,"status"=>"success",
        "message" => "Staff Updated successfully", "data" => $staff]);
        return CommonApp::apiEncrypt($res);
    }

    /* For the staff logout */
    public function staffLogout(Request $request){
        $header = $request;
        $request= CommonApp::apiDecrypt($request->getContent());
        Staff::logout($request,$header);
        $res = json_encode(["status_code"=>200,"message"=>"User Logged Out Successfully"]);
        return CommonApp::apiEncrypt($res);
    }

    /* To get the staff Role */
    public function getStaffRole(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'staff_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $dataAry=[];
        $staff = Staff::where('id',$request->staff_id)->first();
        //dd($request);
        $role=Roles::getRoleName($staff->role_id);
        $permission = Roles::getPermissionsForStaff($staff);
        $module = Roles::getModulesForStaff($staff);
        $workspace = Workspace::where('company_id',$request->company_id)->where('id',$request->workspace_id)->first();

        $dataAry['role']=$role->name;
        $dataAry['roleId']=$role->id;
        $dataAry['company_id']=$request->company_id;
        $dataAry['workspace_id']=$request->workspace_id;
        $dataAry['workspaceName']=$workspace->name;
        $dataAry['workspaceType']=$workspace->workspace_type;
        $dataAry['permission']=$permission;
        $dataAry['module']=$module;
        $res = json_encode(["status_code"=>200,"data"=>$dataAry],200);
        return CommonApp::apiEncrypt($res);
    }
}
