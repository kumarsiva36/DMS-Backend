<?php

namespace App\Http\Controllers\Mobile\Common;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\WeekOff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WeekOffs extends Controller
{
    /* To Create a new WeekOff*/
    public static function createWeekOffs(Request $request){
        $validated = Validator::make($request->all(),[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'days' => 'required'
        ]);
        if($validated->fails()){
            return response()->json(["status_code" => 404, "error" => $validated->errors()]);
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
            $weekOffArr['staff_id']=$request->input('staff_id','0');
            $weekOffArr['days'] = $request->days;
            $weekOffArr['status']="1";
            $weekOffArr['created_at'] = date('Y-m-d H:i:s');
            $weekOffArr['updated_at'] = date('Y-m-d H:i:s');
            WeekOff::insert($weekOffArr);
            $getWeekOffDetails=WeekOffs::getAllWeekOffs($request->company_id,$request->workspace_id);
            return response()->json(["status_code"=>200,"status"=>"success","message"=>"WeekOff Added Successfully","data"=>$getWeekOffDetails]);
        }
        else{
            WeekOff::where($whereConditions)->delete();
            return response()->json(["status_code"=>201,"status"=>"success","message"=>"Week Off Deleted Successfully"]);
        }

    }

    /* To get the weekoffs*/
    public static function getWeekOffs(Request $request){
        $validated = Validator::make($request->all(),[
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if($validated->fails()){
            return response()->json(["status_code" => 404, "error" => $validated->errors()]);
        }
        $whereConditions = [
            ["company_id","=",$request->company_id],
            ["workspace_id","=",$request->workspace_id]
        ];
        $weekOffs = WeekOff::where($whereConditions)->select('days as day')->get();

        return response()->json(["status_code" =>200, "status" =>"Success","data" => $weekOffs]);
    }
    public static function getAllWeekOffs($company_id,$workspace_id){
     
        $whereConditions = [
            ["company_id","=",$company_id],
            ["workspace_id","=",$workspace_id]
        ];
        $weekOffs = WeekOff::where($whereConditions)->select('days as day')->get();
        return  $weekOffs;
        //return response()->json(["status_code" =>200, "status" =>"Success","data" => $weekOffs]);
    }
}
