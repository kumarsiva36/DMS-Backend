<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduction;
use App\Models\UpdateSkuQuantity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Orderlog;
use Exception;

class AddOrderProduction extends Controller
{

   /* Add/overwrite production data for order */

    public static function addProductionData(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $request->prod_datas = json_decode(json_encode($request->prod_datas), true);

        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
            'prod_datas' => 'required|array',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to add orders
            if(isset($request->type))
            {
                $per_id = $request->type=='Add' ? 18 : 20;
                $per = CommonApp::checkStaffPermission($request,$per_id);
                if($per===0){
                    return CommonApp::checkStaffPermissionResponse();
                }
            }
        }
        $prodType=$request->prod_datas[0]['type_of_production'];
        /*start Validation cut,sew,pack */
if(strtolower($prodType)=='pack'){
    $whereConditionsvalid =[
        ['workspace_id','=',$request->workspace_id],
        ['company_id', '=', $request->company_id],
        ['order_id','=',$request->order_id],
        ['type_of_production','=','Sew']
    ];
    $validCount = OrderProduction::where($whereConditionsvalid)->count();
    if($validCount==0){

        $res = json_encode(["status_code"=>401,"status" =>"Falied","message"=>"Please Save Sewing Data to Continue"]);

        return CommonApp::webEncrypt($res);
    }
}
else if(strtolower($prodType)=='sew'){
    $whereConditionsvalid =[
        ['workspace_id','=',$request->workspace_id],
        ['company_id', '=', $request->company_id],
        ['order_id','=',$request->order_id],
        ['type_of_production','=','Cut']
    ];
    $validCount = OrderProduction::where($whereConditionsvalid)->count();
    if($validCount==0){

        $res = json_encode(["status_code"=>401,"status" =>"Falied","message"=>"Please Save Cutting Data to Continue"]);
        return CommonApp::webEncrypt($res);

    }

}
else{}
$whereConditionsProd=[
    ['workspace_id','=',$request->workspace_id],
    ['company_id', '=', $request->company_id],
    ['order_id','=',$request->order_id]
  ];
$upQtyCount=UpdateSkuQuantity::where($whereConditionsProd)->count();
if($upQtyCount>0){
    $res = json_encode(["status_code"=>401,"status" =>"Falied","message"=>"Production Started.Unable to Update Data"]);
    return CommonApp::webEncrypt($res);
}
  /*end Validation cut,sew,pack */


        try{
            $res = OrderProduction::addProductionData($request);
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Production Data Added Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e ){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }

    }

    /* Get the order production Data */
    public static function getProductionData(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
            'type_of_production' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            $arr = OrderProduction::getProductionData($request);
            $res = json_encode(["status_code"=>200,"status" =>"Success","Production Type"=>$request->type_of_production,"data"=>$arr],200);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }
}
