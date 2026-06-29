<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\OrderSku;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Color;
use App\Models\RolesAndPermissions;
use App\Models\Size;
use App\Models\Staff as ModelsStaff;
use Exception;
use Illuminate\Validation\Rule;
use Staff;

class AddOrderSKU extends Controller
{

    /* Add/overwrite SKU's for the order */

    public static function addSku (Request $request)
    {
        $request = CommonApp::webDecrypt($request->getContent());
        $request->sku = json_decode(json_encode($request->sku), true);
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
            'sku' => 'required|array',
            'sku.*.color_id'=>['integer','min:0',Rule::exists('color','id')],
            'sku.*.size_id'=>['integer','min:0',Rule::exists('size','id')],
        ],$messages=['sku.*.color_id.exists'=>"Color Not Found",'sku.*.size_id.exists'=>"Size Not Found"]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to add orders
            $per = CommonApp::checkStaffPermission($request,'18');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        try{
            OrderSku::addSKU($request);
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"SKU's Added Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e ){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* List the updated sku's for the order */

    public static function getSku(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id]
        ];
        try{
            $arr = OrderSku::getSKU($whereConditions);
            $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$arr]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }
}
