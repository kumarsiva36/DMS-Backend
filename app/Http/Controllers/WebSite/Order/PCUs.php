<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\PCU;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PCUs extends Controller
{
    /* Create PCU  */
    public function createPCU(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'name' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions = [
            ['name','=',$request->name],
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$request->company_id]
        ];
        $aldradyExists = PCU::where($whereConditions)->first();

        if(!empty($aldradyExists)){
            $res = json_encode(["status_code"=>400,"status" =>"Failure","message"=>"PCU Already Exists"]);
            return CommonApp::webEncrypt($res);
        }
        else{
            $pcuArr = [];
            $pcuArr['name']=$request->name;
            $pcuArr['company_id']=$request->company_id;
            $pcuArr['user_id']=$companyDetails->user_id;
            $pcuArr['workspace_id']= $request->workspace_id;
            $pcuArr['staff_id']= '0';
            $pcuArr['created_by']= $companyDetails->user_id;
            $pcuArr['created_at']= date('Y-m-d H:i:s');
            $pcuArr['updated_at']= date("Y-m-d H:i:s");
            PCU::insert($pcuArr);

            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"PCU Added Succesfully"]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* View the PCU's */
    public function getPCU(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions = [
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$request->company_id]
        ];
        $PCU = PCU::select('id','name')->where($whereConditions)->get();

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$PCU]);
        return CommonApp::webEncrypt($res);
    }

    /* Add a new PCU */
    public function createStaffPCU(Request $request){
        $validated = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $staffDetails = CommonApp::getStaffDetailsByEmail($request->email);
        $whereConditions = [
            ['name','=',$request->name],
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$staffDetails->company_id]
        ];
        $aldradyExists = PCU::where($whereConditions)->first();

        if(!empty($aldradyExists)){
            return response()->json(["status_code"=>400,"status" =>"Failure","message"=>"PCU Already Exists"]);
        }
        else{
            $factoryArr = [];
            $factoryArr['name']=$request->name;
            $factoryArr['company_id']=$staffDetails->company_id;
            $factoryArr['user_id']='0';
            $factoryArr['workspace_id']= $request->workspace_id;
            $factoryArr['staff_id']= $staffDetails->id;
            $factoryArr['created_by']= $staffDetails->id;
            $factoryArr['created_at']= date('Y-m-d H:i:s');
            $factoryArr['updated_at']= date("Y-m-d H:i:s");
            PCU::insert($factoryArr);

            return response()->json(["status_code"=>200,"status" =>"Success","message"=>"PCU Added Succesfully"]);
        }
    }

    /* View the added PCU's */
    public function getStaffPCU(Request $request){
        $validated = Validator::make($request->all(),[
            'email' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $staffDetails = CommonApp::getStaffDetailsByEmail($request->email);
        $whereConditions = [
            ['user_id'=>$staffDetails->id],
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$staffDetails->company_id]
        ];
        $factories = PCU::select('id','name')->where($whereConditions)->get();

        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$factories]);
    }
}
