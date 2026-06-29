<?php

namespace App\Http\Controllers\WebSite\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Common\CommonApp;
use App\Common\GetUserLanguage;
use App\Common\Logs;
use App\Common\Mailconfig;
use App\Jobs\UserLoginConfirmationJob;
use App\Jobs\UserRegistrationJob;
use App\Models\CompanySettings;
use App\Models\UserPreferences;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class Users extends Controller
{
    /*
        This function is used for the user Sign Up action
    */
    public function register(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'first_name'=>'required|max:60',
            'last_name'=>'required|max:60',
            'email' => 'required|email:dns,rfc|unique:users',
            'countryId' => 'required',
            // 'ip_address'=>'ip',
            'language'=>'required',
            'mobile_number' => 'required|numeric',
            'user_type' => 'required'
        ]);

        if($validated->fails()) {
            $res = json_encode(["status_code"=>401,"status"=>"Failed",
            "message"=>"Validation Errors","errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $username = $request->first_name;
        $name = $request->first_name.' '.$request->last_name;
        $userDataArray = array(
            "name" => $name,
            "email" => $request->email,
            "username" => $username,
            "mobile_number" => $request->mobile_number,
            "ip_address"=>$header->ip(),
            "user_type" => $request->user_type,
            "country_id" => $request->countryId,
            "lang_code"=> $request->language,
            "status" =>'0',
        );

        $user = User::create($userDataArray);
        if(!is_null($user)) {
            $details=[];
            $details['to']=$request->email;
            $details['userName']=$name;
            $details['language']=$request->language;
            UserRegistrationJob::dispatch($details);
            $res = json_encode(["status_code" =>200,"status"=>"success",
            "message" => "Registration completed successfully", "data" => $user]);
            return CommonApp::webEncrypt($res);
        }

        else{
            $res = json_encode(["status_code"=>400,"status"=>"failed",
            "message"=>"Failed to register"]);
            return CommonApp::webEncrypt($res);
        }
    }

    /*
        This function is to get the otp and send it through mail
    */
    public function getOtp(Request $request){
        $header = $request;
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
        $user_email = User::getUserByEmail($request);
        if(!is_null($user_email))
        {
            if(env('ALLOW_USER_LOGIN_VALIDATE')=='yes'){
            $token_count = DB::table("oauth_access_tokens")->where('user_id','=',$user_email->id)->where('name','=','APItoken')
            ->where('revoked','=','0')->count();

            if($token_count>0 && $confirmation==0){
                $res = json_encode(["status_code"=>409, "message"=>"Already Logged in another device. Are sure logged out other devices?"]);
                return CommonApp::webEncrypt($res);
            }
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
                }catch(Exception $e){
                    return $e->getMessage();
                }
                $res = json_encode(["status_code"=>200, "message"=>"OTP sent to your email"]);
                return CommonApp::webEncrypt($res);
            }
            else if ($user_email->status == 2){
                $res = json_encode(["status_code"=>4001,"email"=>$request->email ,"message"=>"Please Contact Super Admin to Re-activate your Account"]);
                return CommonApp::webEncrypt($res);
            }
            else {
                $res = json_encode(["status_code"=>4000,"email"=>$request->email ,"message"=>"Please Purchase a Plan"]);
                return CommonApp::webEncrypt($res);
            }
        }
        else{
            $res = json_encode(["status_code"=>400, "message"=>"User Not Found"]);
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
        $user_email = $request->email;
        $user = User::getUserByEmail($request);
        $otp = $user->otp;
        $user_entered_otp = $request->otp;
        $browserDetails = $header->header('User-Agent');
        $ipAddress = $header->ip();
        $logType = "Login";
        $logUserType = "User";
        $language = GetUserLanguage::getUserLanguageWithEmail($request->email,"User");
        App::setLocale($language);
        $userArr = [];
        $userArr['userID'] = $user->id;
        $userArr['userName'] = $user->name;
        $userArr['browserDetails'] = $browserDetails;
        $userArr['ipAddress'] = $ipAddress;
        $userArr['logType'] = $logType;
        $userArr['logUserType'] = $logUserType;
        if($user->company_id != null || $user->company_id !=0){
            $userArr['companyID'] = $user->company_id;
        }
        $workspace = CommonApp::getWorkspaceDetails($user->id);
        // $company = CompanySettings::where('user_id',$user->id)->where('id',$user->company_id)->first();
        $userPreference =  UserPreferences::getUserPreference($user);;
        // $planExpiryDate="";
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
        }else{
            $dateFormat=$userPreference->date_format;
        }
        $role = "SuperUser";
        $workspace_id = 0;
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
            $user->tokens()->where('name', 'APItoken')->delete(); //Delete all previous tokens
            $token = $user->createToken('APItoken')->accessToken;
            $user->last_loggedin_time = date('Y-m-d H:i:s');
            $user->save();
            $loginStatus = "Success";
            $userArr['loginStatus'] = $loginStatus;
            $details=[];
            $details['to']=$request->email;
            $details['userName']=$user->name;
            $details['language']=$language;
            UserLoginConfirmationJob::dispatch($details);
            Logs::userLogs($userArr);
            $res = json_encode(["status_code"=>200,"email"=>$request->email,"user_id"=>$user->id,"user_name"=> $user->name,
            "country"=>$user->country,"company_id"=>$user->company_id,"workspace_id"=>$workspace_id,"plan"=>$plan,"language"=>$language,
            "workspaceName"=>$workspace_name,"workspaceType"=>$workspace_type,"role"=>$role,"dateformat"=>$dateFormat,"token"=>$token,"message"=>"OTP Verified Successfully"],200);
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

    /* User Logout function*/
    public function userLogout(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        User::logout($request,$header);
        $res = json_encode(["status_code"=>200,"message"=>"User Logged Out Successfully"]);
        return CommonApp::webEncrypt($res);
    }

}
