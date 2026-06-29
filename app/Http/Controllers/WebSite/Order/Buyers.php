<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buyer;
use Illuminate\Support\Facades\Validator;

class Buyers extends Controller
{
    /* Create New Buyer */
    public function createBuyer(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated =Validator::make((array)$request,[
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
        $aldradyExists = Buyer::where($whereConditions)->first();

        if(!empty($aldradyExists)){
            $res = json_encode(["status_code"=>400,"status" =>"Failure","message"=>"Buyer Already Exists"]);
            return CommonApp::webEncrypt($res);
        }
        else{
            $buyerArr = [];
            $buyerArr['name']=$request->name;
            $buyerArr['company_id']=$request->company_id;
            $buyerArr['user_id']=$companyDetails->user_id;
            $buyerArr['workspace_id']= $request->workspace_id;
            $buyerArr['staff_id']= '0';
            $buyerArr['created_by']= $companyDetails->user_id;
            $buyerArr['created_at']= date('Y-m-d H:i:s');
            $buyerArr['updated_at']= date("Y-m-d H:i:s");
            Buyer::insert($buyerArr);
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Buyer Added Succesfully"]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Get the created Buyers */
    public function getBuyers(Request $request){
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
        $buyers = Buyer::select('id','name')->where($whereConditions)->get();

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$buyers]);
        return CommonApp::webEncrypt($res);
    }
}
