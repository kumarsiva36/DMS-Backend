<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\CompanySettings;
use App\Models\HolidaySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HolidaySettings extends Controller
{
    /**************  To Create a New Holiday  ************/
    public static function createHolidaySettings(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'holiday_start_date' => 'required',
            'holiday_end_date' => 'required',
            'name' => 'required'
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission for Add/Edit Calendar Configuration
            $per = CommonApp::checkStaffPermission($request,'31');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        $companySettings = CompanySettings::getCompanyInfoUsingID($request->company_id);
        $whereConditions = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['holiday_start_date','=',date('Y-m-d',strtotime($request->holiday_start_date))],
            ['holiday_end_date','=',date('Y-m-d',strtotime($request->holiday_end_date))]
        ];
        $ifHolidayExists = HolidaySetting::getHoliday($whereConditions);
        if(!empty($ifHolidayExists) && $ifHolidayExists->status == "1"){
            $res = json_encode(["status_code"=>400,"status" =>"Failure","message"=>"Holiday Exists"]);
            return CommonApp::webEncrypt($res);
        }
        if(!empty($ifHolidayExists) && $ifHolidayExists->status == "2"){
            $ifHolidayExists->status = "1";
            $ifHolidayExists->name = $request->name;
            $ifHolidayExists->save();
            $holidayId = $ifHolidayExists->id;
            $res = json_encode(["status_code"=>200,"holiday_id"=>$holidayId,"status" =>"Success","message"=>"Holiday Added Successfully"]);
            return CommonApp::webEncrypt($res);
        }
        try{
            $holidayId = HolidaySetting::createHoliday($request,$companySettings);
            $res = json_encode(["status_code"=>200,"status" =>"Success","holiday_id"=>$holidayId,"message"=>"Holiday Added Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }

    }

    /*************** Get Holiday Settings for the Company *************/
    public static function getHolidaySettings(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $holidays = HolidaySetting::getHolidays($request);

        $res = json_encode(['status_code'=>200,"status"=>"success","data"=>$holidays]);
        return CommonApp::webEncrypt($res);
    }

    /********************Delete Holiday Settings***********************************/

    public static function deleteHolidaySettings(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['status','=','1'],
            ['id','=',$request->id],
            // ['holiday_start_date','=',date('Y-m-d',strtotime($request->startDate))],
            // ['holiday_end_date','=',date('Y-m-d',strtotime($request->endDate))]
        ];

        try{
            HolidaySetting::deleteHoliday($whereConditions);
            $res = json_encode(['status_code'=>200,"status"=>"success","message"=>"Holiday removed successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }
}
