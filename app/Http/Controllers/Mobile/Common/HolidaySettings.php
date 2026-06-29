<?php

namespace App\Http\Controllers\Mobile\Common;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\HolidaySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Redis;

class HolidaySettings extends Controller
{
    /**************  To Create a New Holiday  ************/
    public static function createHolidaySettings(Request $request){
        $validated = Validator::make($request->all(),[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'holiday_start_date' => 'required',
            'holiday_end_date' => 'required',
            'name' => 'required'
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $companySettings = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['holiday_start_date','=',date('Y-m-d',strtotime($request->holiday_start_date))],
            ['holiday_end_date','=',date('Y-m-d',strtotime($request->holiday_end_date))]
        ];
        $ifHolidayExists = HolidaySetting::getHoliday($whereConditions);
        if(!empty($ifHolidayExists) && $ifHolidayExists->status == "1"){
            return response()->json(["status_code"=>400,"status" =>"Failure","message"=>"Holiday Exists"]);
        }
        if(!empty($ifHolidayExists) && $ifHolidayExists->status == "2"){
            $ifHolidayExists->status = "1";
            $ifHolidayExists->save();
            return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Holiday Added Successfully"]);
        }
        try{
            $holidayId = HolidaySetting::createHoliday($request,$companySettings);
            return response()->json(["status_code"=>200,"status" =>"Success","holiday_id"=>$holidayId,"message"=>"Holiday Added Successfully"]);
        }catch(Exception $e){
            return response()->json(["status_code"=>401,"error"=>$e->getMessage()]);
        }
    }

    /*************** Get Holiday Settings for the Company *************/
    public static function getHolidaySettings(Request $request){
        $validated = Validator::make($request->all(),[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }

        $holidays = HolidaySetting::getHolidays($request);

        return response()->json(['status_code'=>200,"status"=>"success","data"=>$holidays]);
    }

    /********************Delete Holiday Settings***********************************/

    public static function deleteHolidaySettings(Request $request){
        $validated = Validator::make($request->all(),[
            'company_id' => 'required',
            'id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
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
            return response()->json(['status_code'=>200,"status"=>"success","message"=>"Holiday removed successfully"]);
        }catch(Exception $e){
            return response()->json(["status_code"=>401,"error"=>$e->getMessage()]);
        }
    }
}
