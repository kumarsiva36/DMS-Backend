<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\WeekOff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WeekOffs extends Controller
{
    /* To Create a new WeekOff*/
    public static function createWeekOffs(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'days' => 'required'
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code" => 404, "error" => $validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission for Add/Edit Calendar Configuration
            $per = CommonApp::checkStaffPermission($request,'31');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions = [
            ["company_id","=",$request->company_id],
            ["workspace_id","=",$request->workspace_id],
            ["days","=",$request->days]
        ];
        $ifweekOffAlreadyExists = WeekOff::where($whereConditions)->get();
        if(count($ifweekOffAlreadyExists) === 0) {
            $weekOffArr=[];
            $weekOffArr['company_id'] = $request->company_id;
            $weekOffArr['workspace_id'] = $request->workspace_id;
            $weekOffArr['user_id']=$companyDetails->user_id;
            $weekOffArr['staff_id']=$request->staff_id ?? 0;
            $weekOffArr['days'] = $request->days;
            $weekOffArr['status']="1";
            $weekOffArr['created_at'] = date('Y-m-d H:i:s');
            $weekOffArr['updated_at'] = date('Y-m-d H:i:s');
            WeekOff::insert($weekOffArr);
            $res = json_encode(["status_code"=>200,"status"=>"success","message"=>"Week Off Added Successfully"]);
            return CommonApp::webEncrypt($res);
        }
        else{
            WeekOff::where($whereConditions)->delete();
            $res = json_encode(["status_code"=>201,"status"=>"success","message"=>"Week Off Deleted Successfully"]);
            return CommonApp::webEncrypt($res);
        }

    }

    /* To get the weekoffs*/
    public static function getWeekOffs(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code" => 404, "error" => $validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions = [
            ["company_id","=",$request->company_id],
            ["workspace_id","=",$request->workspace_id]
        ];
        $weekOffs = WeekOff::where($whereConditions)->select('days as day')->get();

        $res = json_encode(["status_code" =>200, "status" =>"Success","data" => $weekOffs]);
        return CommonApp::webEncrypt($res);
    }
}
