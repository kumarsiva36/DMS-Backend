<?php
namespace App\Common;

use App\Models\CompanySettings;
use App\Models\Staff;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Country;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Common\Encryption;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use App\Http\Controllers\WebSite\Company\CompanySettings as CompanyCompanySettings;
use App\Models\Buyer;
use App\Models\Factory;
use App\Models\InquiryContact;
use App\Models\PCU;
use App\Models\RolesAndPermissions;
use EasyAES;
require 'EasyAES.php';

class CommonApp
{
    public static function generate_Login_OTP(){
        $otp = mt_rand(100001,999999);
        return $otp;
    }

    public static function getUserDetailsByEmail($email){
        $getUserDetails = User::where('email',$email)->first();
        return $getUserDetails;
    }

    public static function getStaffDetailsByEmail($email){
        $getUserDetails = Staff::where('email',$email)->first();
        return $getUserDetails;
    }

    public static function getStaffDetailsByID($id){
        $getUserDetails = Staff::where('id',$id)->first();
        return $getUserDetails;
    }
    public static function getUserDetailsById($id){
        $getUserDetails = User::where('id',$id)->first();
        return $getUserDetails;
    }

    public static function getCompanyDetails($userID){
        $getCompanyDetails = CompanySettings::where('user_id',$userID)->first();
        return $getCompanyDetails;
    }

    public static function getCompanyDetailsbyID($companyID){
        $getCompanyDetails = CompanySettings::where('id',$companyID)->first();
        return $getCompanyDetails;
    }

    public static function getWorkspaceDetails($userID){
        $getWorkspaceDetails = Workspace::where('user_id',$userID)->first();
        return $getWorkspaceDetails;
    }
    public static function getCountryDetails($ID){
        $getCountryDetails = Country::where('id',$ID)->first();
        return $getCountryDetails;
    }

    public static function webDecrypt($str){
        $nonceValue = 'ITOHENDMS';
        $decrypted = Encryption::decrypt($str, $nonceValue);
        $res = json_decode($decrypted, True);
        $res = json_decode(json_encode((object) $res), FALSE);
        return $res;
    }

    public static function webEncrypt($str){
        $nonceValue = 'ITOHENDMS';
        $encrypted = Encryption::encrypt($str, $nonceValue);
        return $encrypted;
    }

    public static function apiEncrypt($str){
        // $secretyKey = 'ITOHENDMS';
        // $encryption = new \MrShan0\CryptoLib\CryptoLib();
        // $encrypted = $encryption->encryptPlainTextWithRandomIV($str, $secretyKey);
        $pwd = "QwertyUIOP!2#4";
        $iv ='ITOHENDMS';
        $aes = new EasyAES($pwd, 256, $iv);
        $encrypted = $aes->encrypt($str);
        return $encrypted;
    }
    public static function apiDecrypt($str){
        // $secretyKey = 'ITOHENDMS';
        // $encryption = new \MrShan0\CryptoLib\CryptoLib();
        // $decrypted = $encryption->decryptCipherTextWithRandomIV($str, $secretyKey);
        $pwd = "QwertyUIOP!2#4";
        $iv ='ITOHENDMS';
        $aes = new EasyAES($pwd, 256, $iv);
        $decrypted = $aes->decrypt($str);
        $res = json_decode($decrypted, True);
        $res = json_decode(json_encode((object) $res), FALSE);
        return $res;
       // return json_decode($decrypted,true);
    }

    public static function getAllCompanyDetails(){
        $getCompanyDetails = CompanySettings::where('id','>','0')
        ->select('id','company_name')
        ->orderBy('company_name','ASC')
        ->get();
        return $getCompanyDetails;
    }
    public static function getAllUsers($resquest){
        $where = [['id','>','0']];
        if($resquest->company_id && $resquest->company_id!=''){
            $where[] =['company_id','=',$resquest->company_id];
        }
        $getUserDetails = User::where($where)
        ->select('id','name')
        ->orderBy('name','ASC')
        ->get();
        return $getUserDetails;
    }
    public static function getAllStaffs($resquest){
        $where = [['id','>','0']];
        if($resquest->company_id && $resquest->company_id!=''){
            $where[] =['company_id','=',$resquest->company_id];
        }
        if($resquest->workspace_id && $resquest->workspace_id!=''){
            $where[] =['workspace_id','=',$resquest->workspace_id];
        }
        $getUserDetails = Staff::where($where)
        ->select('id','first_name','last_name')
        ->orderBy('first_name','ASC')
        ->get();
        return $getUserDetails;
    }

    public static function getAllOrders($resquest){
        $orwhere = $where = [['orders.id','>','0']];
        if($resquest->company_id && $resquest->company_id!=''){
            $orwhere[] = $where[] =['orders.company_id','=',$resquest->company_id];
        }
        if($resquest->workspace_id && $resquest->workspace_id!=''){
            $orwhere[] = $where[] =['orders.workspace_id','=',$resquest->workspace_id];
        }
        if($resquest->user_id && $resquest->user_id!=''){
            $where[] =['orders.user_id','=',$resquest->user_id];
            $orwhere[] =['order_contacts.user_id','=',$resquest->user_id];
        }
        if($resquest->staff_id && $resquest->staff_id!=''){
            $where[] =['orders.staff_id','=',$resquest->staff_id];
            $orwhere[] =['order_contacts.staff_id','=',$resquest->staff_id];
        }
        $Order = Order::where($where)->orWhere($orwhere)
        ->join('order_contacts','orders.id','order_contacts.order_id')
        ->select(DB::RAW('DISTINCT(orders.id) as id'),'orders.style_no')
        ->orderBy('orders.style_no','ASC')
        ->get();
        return $Order;
    }

    public static function getOrderEssentialDetails($data, $type){
        if($type === "Factory"){
            $name = Factory::where($data)->select('name')->first();
        }
        if($type === "PCU"){
            $name = PCU::where($data)->select('name')->first();
        }
        if($type === "Buyer"){
            $name = Buyer::where($data)->select('name')->first();
        }
        return $name;
    }

    public static function getPIC($data){
        if($data === 0){
            return "";
        }
        else{
            $pic = Staff::where('id',$data)->first();
            return ($pic->first_name." ".$pic->last_name);
        }
    }

    public static function getDateAndLanguage($request){
        if(isset($request->user_id) && $request->user_id>0){
            $whereConditionToSend=[
                ['company_id','=',$request->company_id],
                ['id','=',$request->user_id]
            ];
            $dateFormat=GetUserSettings::getPeopleDateFormat("User",$whereConditionToSend);
            $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"User",$request->user_id);
        }
        if(isset($request->staff_id) && $request->staff_id>0){
            $whereConditionToSend=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['id','=',$request->staff_id]
            ];
            $dateFormat=GetUserSettings::getPeopleDateFormat("Staff",$whereConditionToSend);
            $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"Staff",$request->staff_id);
        }
        $userLogo = CompanyCompanySettings::getUserLogoStatus($request->company_id);
        $data['language'] = $language;
        $data['dateFormat'] = $dateFormat;
        $data['useLogo'] = $userLogo['useLogo'];
        $data['userLogo'] = $userLogo['useLogo'] == 1 ? $userLogo['userLogo'] : "";

        return $data;
    }

    public static function get_inquiry_article_name($id){
        $article = DB::table('order_article_name')->where('id', $id)->first();
        return $article->name;
    }

    public static function get_fabric_name($id){
        $fabric = DB::table('fabric_type')->where('id', $id)->first();
        return $fabric->name;
    }
    public static function get_incoterms_name($id){
        $incoterms = DB::table('income_terms')->where('id', $id)->first();
        return $incoterms->name;
    }

    public static function get_inquiry_factory_name($id){
        $factory = InquiryContact::where('id', $id)->first();
        return $factory->factory;
    }

    public static function get_inquiry_buyer_name($id){
        $buyer = User::where('id', $id)->first();
        return $buyer->name;
    }
    public static function compareArrayFieldValue($oldArray, $newArray){
        $changes = [];
        if(is_array($oldArray) && is_array($newArray)){
            foreach ($oldArray as $rowIndex => $oldRow) {
                foreach ($oldRow as $colIndex => $oldValue) {
                   $newValue = $newArray[$rowIndex][$colIndex];
                    if ($oldValue !== $newValue) {
                        $changes[] = [
                           // 'row' => $rowIndex,
                            'Field' => $colIndex,
                            'old Value' => $oldValue,
                            'new Value' => $newValue,
                        ];
                    }
                }
            }
        }
            return $changes;
    }

    public static function getStaffIDS($id){
        $getstaffEmail = Staff::select('email')->where('id', $id)->first();
        $getUserDetails = Staff::where('email',$getstaffEmail['email'])->pluck('id')->toArray();
        return $getUserDetails;
    }

    public static function checkStaffPermission($request,$id){
        $whereCondition1=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id]
        ];
        if(isset($request->login_staff_id) && $request->login_staff_id > 0)
            $staff_id = $request->login_staff_id;
        else
            $staff_id = $request->staff_id;

        $staffRoleHasPermission = Staff::select('role_id','company_id')->where('id',$staff_id)->first();
        $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
        $whereCondition1[]=['permission_id','=',$id];
        $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
        $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
        if(empty($isPermissionGiven)){
            return 0;
        }else{
            return 1;
        }
    }
    public static function checkStaffPermissionResponse(){
        $res = json_encode(["status_code"=>40005,"message"=>"Permission denied"]);
        return CommonApp::webEncrypt($res);
    }
    public static function checkStaffPermissionResponseMobile(){
        $res = json_encode(["status_code"=>40005,"message"=>"Permission denied"]);
        return CommonApp::apiEncrypt($res);
    }

    public static function calculateFreeStorage($maxStorage,$usedStorage){
        if(config('constant.plan_storage_free_mb_type')==2)
            return ($maxStorage - ($usedStorage + (int)config('constant.plan_storage_free_mb')))*1024*1024;
        else{
            $cachedStorage = $maxStorage * ((int)config('constant.plan_storage_free_mb')/100);
            return ($maxStorage - ($usedStorage + $cachedStorage))*1024*1024;
        }
    }
}
