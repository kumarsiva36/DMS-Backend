<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Factories extends Controller
{
    /* Add a new Factory */
    public function createFactory(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
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
        $aldradyExists = Factory::where($whereConditions)->first();

        if(!empty($aldradyExists)){
            $res = json_encode(["status_code"=>400,"status" =>"Failure","message"=>"Factory Already Exists"]);
            return CommonApp::webEncrypt($res);
        }
        else{
            $factoryArr = [];
            $factoryArr['name']=$request->name;
            $factoryArr['company_id']=$request->company_id;
            $factoryArr['user_id']=$companyDetails->user_id;
            $factoryArr['workspace_id']= $request->workspace_id;
            $factoryArr['staff_id']= '0';
            $factoryArr['created_by']= $companyDetails->user_id;
            $factoryArr['created_at']= date('Y-m-d H:i:s');
            $factoryArr['updated_at']= date("Y-m-d H:i:s");
            Factory::insert($factoryArr);
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Factory Added Succesfully"]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* View the added Factories */
    public function getFactory(Request $request){
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
        $factories = Factory::select('id','name')->where($whereConditions)->get();

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$factories]);
        return CommonApp::webEncrypt($res);
    }

    /* Create a new Factory */
    public function createStaffFactory(Request $request){
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
        $aldradyExists = Factory::where($whereConditions)->first();

        if(!empty($aldradyExists)){
            return response()->json(["status_code"=>400,"status" =>"Failure","message"=>"Factory Already Exists"]);
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
            Factory::insert($factoryArr);

            return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Factory Added Succesfully"]);
        }
    }

    /* See the added Factories */
    public function getStaffFactory(Request $request){
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
        $factories = Factory::select('id','name')->where($whereConditions)->get();

        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$factories]);
    }
}
