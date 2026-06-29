<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Common\CommonApp;
use App\Common\GetUserLanguage;
use App\Common\Logs;
use App\Common\Mailconfig;
use App\Models\CompanySettings;
use App\Models\UserPreferences;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Models\Workspace;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Users extends Controller
{
    /*
        This function is used for the user Sign Up action
    */
    public function register(Request $request){
        $header = $request;
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'first_name'=>'required|max:60',
            'last_name'=>'required|max:60',
            'ip_address'=>'ip',
            'email' => 'required|email:dns,rfc|unique:users',
            'mobile_number' => 'nullable|unique:users',
            'user_type' => 'required',
            // 'company_name'=> 'required',
            'language'=>'required',
            'countryId' => 'required'
        ]);

        if($validated->fails()) {
            $res = json_encode(["status_code"=>401,"status"=>"Failed","message"=>"Validation Errors","errors"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $username = $request->first_name;
        $name = $request->first_name.' '.$request->last_name;
        $userDataArray = array(
            "name" => $name,
            "email" => $request->email,
            "username" => $username,
            "mobile_number" => $request->mobile_number,
            "ip_address"=>$header->ip_address,
            "user_type" => $request->user_type,
            // "company_name" => $request->company_name,
            "country_id" => $request->countryId,
            "lang_code"=> $request->language,
            "status" =>'0',
        );

        $user = User::create($userDataArray);
        // $token = $user->createToken('APItoken')->accessToken;
        if(!is_null($user)) {
            $res = json_encode(["status_code" =>200,"status"=>"success",
            "message" => "Registration completed successfully", "data" => $user]);
            return CommonApp::apiEncrypt($res);
        }

        else{
            $res = json_encode(["status_code"=>400,"status"=>"failed", "message"=>"Failed to register"]);
            return CommonApp::apiEncrypt($res);
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
        $user_email = User::getUserByEmail($request);
        if(!is_null($user_email))
        {
            if($user_email->company_id == 0 || $user_email->company_id ===null ){
                $res = json_encode(["status_code"=>400, "message"=>"Please complete the registration process on the website and try again"]);
                return CommonApp::apiEncrypt($res);
            }else{
               $datcount= Workspace::getWorkspaceCount($user_email->company_id);
               if(!$datcount>0){
                    $res = json_encode(["status_code"=>400, "message"=>"Please complete the registration process on the website and try again"]);
                    return CommonApp::apiEncrypt($res);
                }
            }

            $token_count = DB::table("oauth_access_tokens")->where('user_id','=',$user_email->id)->where('name','=','APItoken')
            ->where('revoked','=','0')->count();

            if($token_count>0 && $confirmation==0){
                $res = json_encode(["status_code"=>409, "message"=>"Already Logged in another device. Are sure logged out other devices?"]);
                return CommonApp::apiEncrypt($res);
            }

            if($user_email->status == 1)
            {
                $otp = CommonApp::generate_Login_OTP();
                $user_email->otp = $otp;
                $user_email->otp_generated_time = Carbon::now();
                $user_email->save();
                $language = GetUserLanguage::getUserLanguageWithEmail($request->email,"User");
                App::setLocale($language);
                try{
                    Mailconfig::userOtpSendMail($request->email,$user_email,$language,$resend);
                }
                catch(Exception $e){

                }

                $res = json_encode(["status_code"=>200, "message"=>"OTP sent to your email"]);
                return CommonApp::apiEncrypt($res);
            }
            else {
                $res = json_encode(["status_code"=>4000,"email"=>$request->email ,"message"=>"Please Purchase a Plan"]);
                return CommonApp::apiEncrypt($res);
            }
        }
        else{
            $res = json_encode(["status_code"=>400, "message"=>"User Not Found"]);
            return CommonApp::apiEncrypt($res);
        }
    }

    /* This function validates the OTP and gives access token */
    public function otpValidate(Request $request){
        // dd($request);
        $header=$request;
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "email" => 'required|email',
            "otp" => 'required|numeric'
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $user_email = $request->email;
        $user = User::getUserByEmail($request);
        $otp = $user->otp;
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
        $logUserType = "User";
        $userPreference = UserPreferences::getUserPreference($user);
        $language = GetUserLanguage::getUserLanguageWithEmail($request->email,"User");
        $userArr = [];
        $userArr['userID'] = $user->id;
        $userArr['userName'] = $user->name;
        $userArr['browserDetails'] = json_encode($detailsArr);
        $userArr['ipAddress'] = $ipAddress;
        $userArr['logType'] = $logType;
        $userArr['logUserType'] = $logUserType;
        if($user->company_id != null || $user->company_id !=0){
            $userArr['companyID'] = $user->company_id;
        }
        $workspace = CommonApp::getWorkspaceDetails($user->id);
        $role = "SuperAdmin";
        $workspace_id = 0;
        if($user->company_id != null || $user->company_id != "" ){
            $planExpiryDate = CompanySettings::getExpiryInfo($user);
            if(strtotime(date('Y-m-d')) >= strtotime(date('Y-m-d',strtotime($planExpiryDate->account_expire_at))) ){
                $plan = "Expired";
            }else{
                $plan = "Active";
            }
        }else{
            $plan = "New";
        }
        if(empty($userPreference)){
            $dateFormat="";
            $language_id=0;
        }else{
            $dateFormat=$userPreference->date_format;
            $language_id=$userPreference->language_id;
        }
        if(!empty($workspace)){
            $workspace_id = $workspace->id;
            $workspace_name = $workspace->name;
            $workspace_type = $workspace->workspace_type;
        }else{
            $workspace_id = 0;
            $workspace_name = '';
            $workspace_type = '';
        }
        if($otp == $user_entered_otp){

            $plan = CompanySettings::where('id', $user->company_id)->select('account_expire_at')->first();
            if($plan->account_expire_at != "" || $plan->account_expire_at != null){
                if($plan->account_expire_at < date("Y-m-d")){
                    $res = json_encode(["status_code"=>401,"status" =>"failure","message"=>"Plan Expired"]);
                    return CommonApp::apiEncrypt($res);
                }
            }

            $user->tokens()->where('name', 'APItoken')->delete(); //Delete all previous tokens
            $token = $user->createToken('APItoken')->accessToken;
            $user->last_loggedin_time = date('Y-m-d H:i:s');
            $user->device_details  = json_encode($detailsArr);
            $user->fcm_token  = $request->fcm_token??Null;
            $user->save();
            $loginStatus = "Success";
            $userArr['loginStatus'] = $loginStatus;
            Logs::userLogs($userArr);
            $res = json_encode(["status_code"=>200,"login_type"=>"User","email"=>$request->email,"user_id"=>$user->id,"user_name"=>$user->name,
            "country"=>$user->country,"company_id"=>$user->company_id,"workspace_id"=>$workspace_id,"dateformat"=>$dateFormat,"plan"=>$plan,"language"=>$language,"language_id"=>$language_id,
            "workspaceName"=>$workspace_name,"workspaceType"=>$workspace_type,"role"=>$role,"token"=>$token,"message"=>"OTP Verified Successfully"],200);
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

    /* User Logout function*/
    public function userLogout(Request $request){
        $header = $request;
        $request= CommonApp::apiDecrypt($request->getContent());
        User::logout($request,$header);
        $res = json_encode(["status_code"=>200,"message"=>"User Logged Out Successfully"]);
        return CommonApp::apiEncrypt($res);
    }

}
