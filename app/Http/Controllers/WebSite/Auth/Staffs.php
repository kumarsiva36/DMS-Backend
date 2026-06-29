<?php

namespace App\Http\Controllers\WebSite\Auth;

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
use App\Models\Stafflog;
use Exception;
use App\Common\Uploads;
use Illuminate\Support\Facades\Storage;
use App\Enums\ConfidentionalContact;
use App\Models\RolesPermissionsChanges;

class Staffs extends Controller
{
    /* To Register a New Staff */
    public function register(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
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
            //'mobile' => 'numeric',
            'role_id'=>'required'
        ]);

        if($validated->fails()) {
            $res = json_encode(["status_code"=>401,"status"=>"Failed","message"=>"Validation Errors","errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $project = isset($request->project_type)?$request->project_type:'DMS';
        if(isset($request->login_staff_id) && $request->login_staff_id > 0 && $project=='DMS'){ //Check if staff have permission to add staff
            $per = CommonApp::checkStaffPermission($request,'14');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['email','=',$request->email]
        ];
        $whereConditions2 = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
        ];
        $staffCount = Staff::where($whereConditions2)->count();
        if($staffCount >= $companyDetails->no_of_user){
            $res = json_encode(["status_code"=>600,"message"=>"Please upgrade Your Plan To Add Staffs"]);
            return CommonApp::webEncrypt($res);
        }
        $ifStaffExistsInSameCompany = Staff::checkIfUserExistsInSameCompany($request);
        if(!empty($ifStaffExistsInSameCompany)){
            $res = json_encode(["status_code"=>400,"message"=>"Staff Already Exists"]);
            return CommonApp::webEncrypt($res);
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
                "mobile" => $request->mobile ?? '',
                // "ip_address"=>$request->ip_address,
                "address1" => $request->address1 ?? '',
                "address2" => $request->address2 ?? '',
                "city" => $request->city ?? '',
                "state" => $request->state ?? '',
                "country" => $request->country ?? 0,
                "zipcode" => $request->zipcode ?? '',
                "user_type" => $request->user_type ?? '',
                "status" =>$request->status ?? "1",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            );
            $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"User",$request->user_id);
            $staff = Staff::create($userDataArray);
            $staff_id = DB::getPdo()->lastInsertId();

            /**Staff log insert */
            $logArry['company_id'] = $request->company_id;
            $logArry['workspace_id'] = $request->workspace_id;
            $logArry['staff_id'] =$staff_id;
            $logArry['updated_by_user_id'] = $request->user_id ?? 0;
            $logArry['action'] = 'Create';
            Stafflog::insert($logArry);
            /**Staff log insert */

            if(!is_null($staff)) {
                $details=[];
                $details['to']=$staff->email;
                $details['userName'] = $staff->first_name.' '.$staff->last_name;
                $details['workspaceName'] = (Workspace::getWorkspaceDetails($request))->name;
                $details['language']=$language;
                staffRegisterJob::dispatch($details);
                $res = json_encode(["status_code" =>200,"status"=>"success",
                "message" => "Staff Added successfully", "data" => $staff]);
                return CommonApp::webEncrypt($res);
            }

            else{
                $res = json_encode(["status_code"=>400,"status"=>"failed", "message"=>"Failed to register"]);
                return CommonApp::webEncrypt($res);
            }
        }
    }

/*Add staff with image start*/
public function registerWithImage(Request $request){
    $validated = Validator::make($request->all(),[
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
        //'mobile' => 'numeric',
        'role_id'=>'required',
        'image' => 'nullable|mimes:jpeg,png,jpg|max:50|dimensions:max_width=350,max_height=350'
    ]);

    if($validated->fails()) {
        $res = json_encode(["status_code"=>401,"status"=>"Failed","message"=>"Validation Errors","errors"=>$validated->errors()]);
        return CommonApp::webEncrypt($res);
    }
    $project = isset($request->project_type)?$request->project_type:'DMS';
    if(isset($request->login_staff_id) && $request->login_staff_id > 0 && $project=='DMS'){ //Check if staff have permission to add staff
        $per = CommonApp::checkStaffPermission($request,'14');
        if($per===0){
            return CommonApp::checkStaffPermissionResponse();
        }
    }
    $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
    $whereConditions = [
        ['company_id','=',$request->company_id],
        ['workspace_id','=',$request->workspace_id],
        ['email','=',$request->email]
    ];
    $whereConditions2 = [
        ['company_id','=',$request->company_id],
        ['workspace_id','=',$request->workspace_id],
    ];
    $staffCount = Staff::where($whereConditions2)->count();
    if($staffCount >= $companyDetails->no_of_user){
        $res = json_encode(["status_code"=>600,"message"=>"Please upgrade Your Plan To Add Staffs"]);
        return CommonApp::webEncrypt($res);
    }
    $ifStaffExistsInSameCompany = Staff::checkIfUserExistsInSameCompany($request);
    if(!empty($ifStaffExistsInSameCompany)){
        $res = json_encode(["status_code"=>400,"message"=>"Staff Already Exists"]);
        return CommonApp::webEncrypt($res);
    }
    else{
        if($request->hasfile('image')){
            $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
            $awsCompanyPath = $companyDetails->aws_s3_path;
            $image = $request->file('image');
           // $imageName = time().'_'.$image->getClientOriginalName();

            $string = str_replace(' ', '-', $image->getClientOriginalName()); // Replaces all spaces with hyphens.
            $nameOfFile = preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
            $imageName = time().'_'.$nameOfFile;


            $filepath = $awsCompanyPath.'/Staff/'.$imageName;
            Uploads::orderAddtionalSpec($image,$filepath);
            $profile_img=$imageName;
        }
        else{

            $profile_img='';
        }
        $userDataArray = array(
            "company_id"=>$request->company_id,
            "workspace_id"=>$request->workspace_id,
            "user_id"=>$companyDetails->user_id,
            "role_id"=>$request->role_id,
            "first_name" => $request->first_name,
            "last_name" => trim($request->last_name),
            "email" => $request->email,
            "mobile" => $request->mobile ?? '',
            // "ip_address"=>$request->ip_address,
            "address1" => $request->address1 ?? '',
            "address2" => $request->address2 ?? '',
            "city" => $request->city ?? '',
            "state" => $request->state ?? '',
            "country" => $request->country ?? 0,
            "zipcode" => $request->zipcode ?? '',
            "user_type" => $request->user_type ?? '',
            "profile_img" => $profile_img,
            "status" =>$request->status ?? "1",
            "staff_type" =>$request->staff_type>1?2:1,
            "is_confidentional"=> strtolower($request->isConfidentional)=='true'?ConfidentionalContact::Yes:ConfidentionalContact::No,
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        );
        $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"User",$request->user_id);
        $staff = Staff::create($userDataArray);
        $staff_id = DB::getPdo()->lastInsertId();

        /**Staff log insert */
        $logArry['company_id'] = $request->company_id;
        $logArry['workspace_id'] = $request->workspace_id;
        $logArry['staff_id'] =$staff_id;
        $logArry['updated_by_user_id'] = $request->user_id ?? 0;
        $logArry['action'] = 'Create';
        Stafflog::insert($logArry);
        /**Staff log insert */

        if(!is_null($staff)) {
            $details=[];
            $details['to']=$staff->email;
            $details['userName'] = $staff->first_name.' '.$staff->last_name;
            $details['workspaceName'] = (Workspace::getWorkspaceDetails($request))->name;
            $details['language']=$language;
            staffRegisterJob::dispatch($details);
            $res = json_encode(["status_code" =>200,"status"=>"success",
            "message" => "Staff Added successfully", "data" => $staff]);
            return CommonApp::webEncrypt($res);
        }

        else{
            $res = json_encode(["status_code"=>400,"status"=>"failed", "message"=>"Failed to register"]);
            return CommonApp::webEncrypt($res);
        }
    }
}
/*Add staff with image end*/

    /*
        This function is to get the otp and send it through mail
    */
    public function getOtp(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "email" => 'required|email',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $confirmation = $request->confirmation ?? 0;
        $resend = $request->resend ?? 0;
        $staff_email = Staff::getStaffByEmailLogin($request->email);
        if(!is_null($staff_email)){

            if(env('ALLOW_USER_LOGIN_VALIDATE')=='yes'){
            $token_count = DB::table("oauth_access_tokens")->where('user_id','=',$staff_email->id)->where('name','=','StaffAPIToken')
            ->where('revoked','=','0')->count();

            if($token_count>0 && $confirmation==0){
                $res = json_encode(["status_code"=>409, "message"=>"Already Logged in another device. Are sure logged out other devices?"]);
                return CommonApp::webEncrypt($res);
            }
        }
            $language = GetUserLanguage::getUserLanguageWithEmail($request->email,"Staff");
            App::setLocale($language);
            $otp = CommonApp::generate_Login_OTP();
            $staff_email->otp = $otp;
            $staff_email->otp_generated_time = Carbon::now();
            $staff_email->save();
            try{
                Mailconfig::staffOtpSendMail($request->email,$staff_email,$language,$resend);
            }
            catch(Exception $e){

            }

            $res = json_encode(["status_code"=>200 ,"message"=>"OTP sent to your email"]);
            return CommonApp::webEncrypt($res);
        }
        else{
            $res = json_encode(["status_code"=>400, "message"=>"Contact your DMS ADMIN for Login"]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* This function validates the OTP and gives access token */
    public function otpValidate(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "email" => 'required|email',
            "otp" => 'required|numeric'
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $staff_email = $request->email;
        $staff = Staff::getStaffByEmailLogin($request->email);
        $user_email= DB::table("users")->where('company_id','=',$staff->company_id)->pluck('email')->first();
        $admin_staff_same_email = ($staff_email==$user_email)?1:0;
        $language = GetUserLanguage::getUserLanguageWithEmail($request->email,"Staff");
        App::setLocale($language);
        $otp = $staff->otp;
        $user_entered_otp = $request->otp;
        $browserDetails = $header->header('User-Agent');
        $ipAddress = $header->ip();
        $logType = "Login";
        $logUserType = "Staff";
        $userArr = [];
        $userArr['userID'] = $staff->id;
        $userArr['userName'] = $staff->first_name." ".$staff->last_name;
        $userArr['companyID'] = $staff->company_id;
        $userArr['browserDetails'] = $browserDetails;
        $userArr['ipAddress'] = $ipAddress;
        $userArr['logType'] = $logType;
        $userArr['logUserType'] = $logUserType;
        if($otp == $user_entered_otp){
            $staff->tokens()->where('name', 'StaffAPIToken')->delete(); //Delete all previous tokens
            $staffPresentList = Staff::getStaffByEmailLogin($request->email,"validateOTP");
            $workspaceList=[];
            foreach ($staffPresentList as $staffs){
                $staffPreferences = UserPreferences::getStaffPreference($staffs);
                $company = CompanySettings::getCompanyInfoUsingID($staffs->company_id);
                if(empty($staffPreferences)){
                    $dateFormat="";
                }else{
                    $dateFormat=$staffPreferences->date_format;
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
                   // $staffWorkspace['workspaceType']=$workspaces->workspace_type;
                    $staffWorkspace['workspaceType']=$workspaces->workspace_type;
                    $staffWorkspace['staffWorkspaceType']=$staffs->user_type?$staffs->user_type:$workspaces->workspace_type;
                    $staffWorkspace['language']=$staffPreferences->language?? 'en';
                    $roles = Roles::getRoleName($staffs->role_id);
                    $permissions = Roles::getPermissionsForStaff($staffs);
                    $modules = Roles::getModulesForStaff($staffs);
                    $staffWorkspace['permissions']=$permissions;
                    $staffWorkspace['modules']=isset($modules)?
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
                $res = json_encode(["status_code"=>600,"message"=>"Plan Expired! Contact Admin."]);
                return CommonApp::webEncrypt($res);
            }
            $permission = $workspaceList[0]['permissions'];
            $moduler = $workspaceList[0]['modules'];
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
            $res = json_encode(["status_code"=>200,"user_id"=>$workspaceList[0]['user_id'],"email"=>$staff_email,
            "staff_id"=>$workspaceList[0]['staff_id'],"user_name"=>$workspaceList[0]['user_name'],
            "company_id"=>$workspaceList[0]['company_id'],"workspace_id"=>$workspaceList[0]['workspace_id'],"workspaceName"=>$workspaceList[0]['workspaceName'],
            "workspaceType"=>$workspaceList[0]['workspaceType'], "staffWorkspaceType"=>$workspaceList[0]['staffWorkspaceType'],"role"=>$workspaceList[0]['role'],"language"=>$workspaceList[0]['language'],
            "roleId"=>$workspaceList[0]['roleId'],"workspacesList"=>$workspaceList,"permissions"=>$permission,"module"=>$module,"dateformat"=>$workspaceList[0]['dateformat'],
            "token"=>$token,"admin_staff_same_email"=>$admin_staff_same_email,"message"=>"OTP Verified Successfully"],200);
            return CommonApp::webEncrypt($res);
        }
        else{
            $loginStatus = "Failure";
            $userArr['loginStatus'] = $loginStatus;
            Logs::userLogs($userArr);
            $res = json_encode(["status_code"=>400,"message"=>"Incorrect OTP, Please Enter Correctly"]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* To Edit Staff */
    public static function editStaff(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'staff_id' => 'required'
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        // dd($request);
        $staffDetails = Staff::getStaffDetailForEdit($request);

        $res = json_encode(["status_code"=>200,"data"=>$staffDetails]);
        return CommonApp::webEncrypt($res);
    }
 /* To Edit Staff start*/
 public static function editStaffProfile(Request $request){
    $request= CommonApp::webDecrypt($request->getContent());
    $validated = Validator::make((array)$request,[
        'company_id' => 'required',
        'workspace_id' => 'required',
        'staff_id' => 'required'
    ]);
    if($validated->fails()){
        $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
        return CommonApp::webEncrypt($res);
    }
    // dd($request);
    $img='';
    $staffDetails = Staff::getStaffDetailForEdit($request);

   // $getServerURL = config('filesystems.disks.s3.url');
    if($staffDetails->profile_img){
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
            $awsCompanyPath = $companyDetails->aws_s3_path;
        $img = Storage::disk('s3')->temporaryUrl($awsCompanyPath.'/Staff/'.$staffDetails->profile_img, '+5 minutes');
    }

    $res = json_encode(["status_code"=>200,"data"=>$staffDetails,"profile_img"=>$img]);
    return CommonApp::webEncrypt($res);
}
 /* To Edit Staff end*/
    /* To Get staff List */
    public function getStaffList(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $staffList = Staff::getStaffLists($request);

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$staffList]);
        return CommonApp::webEncrypt($res);
    }

    /* To Update the staff details */
    public function updateStaff(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'staff_id'=>'required',
            'company_id'=>'required',
            'workspace_id'=>'required',
            'first_name'=>'required|max:60',
          //  'last_name'=>'required|max:60',
           // 'email' => 'required|email:dns,rfc',
            // 'mobile' => 'required',
            'role_id'=>'required'
        ]);

        if($validated->fails()) {
            $res = json_encode(["status_code"=>401,"status"=>"Failed","message"=>"Validation Errors","errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $project = isset($request->project_type)?$request->project_type:'DMS';
        if(isset($request->login_staff_id) && $request->login_staff_id > 0 && $project=='DMS'){ //Check if staff have permission to Edit Staff details
            $per = CommonApp::checkStaffPermission($request,'15');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $staff = Staff::getStaffByID($request->staff_id);
        $isStaffActiveInOrder = OrderTask::checkIfStaffIsPresentInActiveOrder($request->staff_id);

        if(count($isStaffActiveInOrder)>0 && (isset($request->status) && $request->status === "2")){
            $res = json_encode(["status_code" =>600,"status"=>"failure","message" => "Please Remove Staff from Order to Deactivate."]);
            return CommonApp::webEncrypt($res);
        }
        // $staff->company_id=$request->company_id;
        // $staff->workspace_id=$request->workspace_id;
        // $staff->user_id=$companyDetails->user_id;
        if($staff->role_id!=$request->role_id){
            $dat['company_id'] = $request->company_id;
            $dat['workspace_id'] = $request->workspace_id;
            $dat['staff_id'] = $request->staff_id;
            $dat['type'] = 'Role';
            RolesPermissionsChanges::insert($dat);
            //$staff->tokens()->where('name', 'StaffAPIToken')->delete();
        }
        $staff->role_id=$request->role_id;
        $staff->first_name = $request->first_name;
        $staff->last_name = trim($request->last_name);
       // $staff->email = $request->email;
        $staff->mobile = $request->mobile?? '';
        $staff->address1 = $request->address1 ?? '';
        $staff->address2 = $request->address2 ?? '';
        $staff->city = $request->city ?? '';
        $staff->state= $request->state ?? '';
        $staff->country= $request->country ?? 0;
        $staff->zipcode= $request->zipcode ?? '';
        $staff->status=$request->status ?? 1;
        $staff->user_type= $request->user_type ?? '';
       // $staff->created_at= Carbon::now();
        $staff->updated_at= Carbon::now();
        $staff->save();
        if($request->status==2){
            $staff->tokens()->where('name', 'StaffAPIToken')->delete();
        }

        /**Staff log insert */
        $logArry['company_id'] = $request->company_id;
        $logArry['workspace_id'] = $request->workspace_id;
        $logArry['staff_id'] =$request->staff_id;
        $logArry['updated_by_user_id'] = $request->user_id ?? 0;
        $logArry['action'] = 'Edit';
        $logArry['before_values'] = json_encode($request->before_values) ?? '';
        $logArry['after_values'] = json_encode($request->after_values) ?? '';
        Stafflog::insert($logArry);
        /**Staff log insert */

        $res = json_encode(["status_code" =>200,"status"=>"success",
        "message" => "Staff Updated successfully", "data" => $staff]);
        return CommonApp::webEncrypt($res);
    }
      /* To Update the staff Profile details start */
      public function updateStaffProfile(Request $request){

        $validated = Validator::make($request->all(),[
            'staff_id'=>'required',
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
            //'email' => 'required|email:dns,rfc',
            //'mobile' => 'numeric',
            'role_id'=>'required',
            'image' => 'nullable|mimes:jpeg,png,jpg|max:50|dimensions:max_width=350,max_height=350'
        ]);

        if($validated->fails()) {
            $res = json_encode(["status_code"=>401,"status"=>"Failed","message"=>"Validation Errors","errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $project = isset($request->project_type)?$request->project_type:'DMS';
        if(isset($request->login_staff_id) && $request->login_staff_id > 0 && $project=='DMS'){ //Check if staff have permission to Edit Staff details
            $per = CommonApp::checkStaffPermission($request,'15');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $staff = Staff::getStaffByID($request->staff_id);
        $isStaffActiveInOrder = OrderTask::checkIfStaffIsPresentInActiveOrder($request->staff_id);

        if(count($isStaffActiveInOrder)>0 && (isset($request->status) && $request->status === "2")){
            $res = json_encode(["status_code" =>600,"status"=>"failure","message" => "Please Remove Staff from Order to Deactivate."]);
            return CommonApp::webEncrypt($res);
        }
        // $staff->company_id=$request->company_id;
        // $staff->workspace_id=$request->workspace_id;
        // $staff->user_id=$companyDetails->user_id;
        if($request->hasfile('image')){
            $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
            $awsCompanyPath = $companyDetails->aws_s3_path;
            $image = $request->file('image');
           // $imageName = time().'_'.$image->getClientOriginalName();

            $string = str_replace(' ', '-', $image->getClientOriginalName()); // Replaces all spaces with hyphens.
            $nameOfFile = preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
            $imageName = time().'_'.$nameOfFile;
            $filepath = $awsCompanyPath.'/Staff/'.$imageName;
            Uploads::orderAddtionalSpec($image,$filepath);
            $staff->profile_img= $imageName;
        }
        if($staff->role_id!=$request->role_id){
            $staff->tokens()->where('name', 'StaffAPIToken')->delete();
        }
        $staff->role_id=$request->role_id;
        $staff->first_name = $request->first_name;
        $staff->last_name = trim($request->last_name);
       // $staff->email = $request->email;
        $staff->mobile = $request->mobile?? '';
        $staff->address1 = $request->address1 ?? '';
        $staff->address2 = $request->address2 ?? '';
        $staff->city = $request->city ?? '';
        $staff->state= $request->state ?? '';
        $staff->country= $request->country ?? 0;
        $staff->zipcode= $request->zipcode ?? '';
        $staff->status=$request->status ?? 1;
        $staff->user_type= $request->user_type ?? '';
        $staff->staff_type= $request->staff_type>1?2:1;
        $staff->is_confidentional= strtolower($request->isConfidentional)=='true'?ConfidentionalContact::Yes:ConfidentionalContact::No;
       // $staff->created_at= Carbon::now();
        $staff->updated_at= Carbon::now();
        $staff->save();

        /*If Deactive user Automatically loggout */
        if($request->status==2){
            $staff->tokens()->where('name', 'StaffAPIToken')->delete();
        }

        /**Staff log insert */
        $logArry['company_id'] = $request->company_id;
        $logArry['workspace_id'] = $request->workspace_id;
        $logArry['staff_id'] =$request->staff_id;
        $logArry['updated_by_user_id'] = $request->user_id ?? 0;
        $logArry['action'] = 'Edit';
        $logArry['before_values'] = json_encode($request->before_values) ?? '';
        $logArry['after_values'] = json_encode($request->after_values) ?? '';
        Stafflog::insert($logArry);
        /**Staff log insert */

        $res = json_encode(["status_code" =>200,"status"=>"success",
        "message" => "Staff Updated successfully", "data" => $staff]);
        return CommonApp::webEncrypt($res);
    }
      /* To Update the staff Profile details end*/

    /* Staff Logout */
    public function staffLogout(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        Staff::logout($request,$header);
        $res = json_encode(["status_code"=>200,"message"=>"User Logged Out Successfully"]);
        return CommonApp::webEncrypt($res);
    }

    /* To Resend the Staff Registration Email */
    public static function resendRegisterEmail(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $staff = Staff::getStaffByID($request->staff_id);
        $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"User",$request->user_id);
        if(!empty($staff)){
            $details=[];
            $details['to']=$staff->email;
            $details['userName'] = $staff->first_name.' '.$staff->last_name;
            $details['workspaceName'] = (Workspace::where('company_id',$request->company_id)->where('id',$request->workspace_id)->first())->name;
            $details['language']=$language;
            staffRegisterJob::dispatch($details);
            $res = json_encode(["status_code" =>200,"status"=>"success","message" =>"Staff Invite Sent Successfully"]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Add Last Seen To Staff's Record */
    public static function addLastSeenToStaff(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'staff_id'=>'required',
            'company_id'=>'required',
            'workspace_id'=>'required',
        ]);
        if($validated->fails()) {
            $res = json_encode(["status_code"=>401,"status"=>"Failed","message"=>"Validation Errors","errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }

        try{
            Staff::addLastSeen($request);
            $res = json_encode(["status_code" =>200,"status"=>"success","message" =>"Success"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code" =>200,"status"=>"success","error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }
    /* To get the staff Role */
    public function getStaffRole(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'staff_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }


        //Delete Role paermission update records
        RolesPermissionsChanges::where('company_id',$request->company_id)->where('workspace_id',$request->workspace_id)->where('staff_id',$request->staff_id)->delete();


        $res = json_encode(["status_code"=>200,"message"=>"success"],200);
        return CommonApp::webEncrypt($res);
    }
    /* To get the staff General Update */
    public function getStaffGeneralUpdate(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'staff_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $count = RolesPermissionsChanges::where('company_id',$request->company_id)->where('workspace_id',$request->workspace_id)->where('staff_id',$request->staff_id)->count();
        $count = (int)$count > 0 ? 1 : 0;

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

        $res = json_encode(["status_code"=>200,"update"=>$count,"data"=>$dataAry],200);
        return CommonApp::webEncrypt($res);
    }
}
