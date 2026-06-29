<?php

namespace App\Http\Controllers\WebSite\Order\DataInput;

use App\Common\Logs;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduction;
use App\Models\OrderSku;
use App\Models\UpdateSkuQuantity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Common\CommonApp;

class AddInputMultipleData extends Controller
{
    //  Same Add Input Data but with multiple color, size and quantity
    public static function addInputData(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $request->data = json_decode(json_encode($request->data), true);
        $validator = Validator::make((array)$request,[
            "company_id"=>"required",
            "workspace_id"=>"required",
            "order_id"=>"required",
            "data"=>"array",
            "data.*.quantity"=>"integer",
            "date"=>"required",
            "type_of_production"=>"required",
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to add Data Input
            $per = CommonApp::checkStaffPermission($request,'27');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        $orderValid=OrderProduction::select("id")->where('order_id',$request->order_id)->where('type_of_production',$request->type_of_production)
        ->where('is_accomplished',1)->count();
        if($orderValid>0){
            $res = json_encode(["status_code"=>201,"status" =>"Failure","message"=>$request->type_of_production." Already Accomplished."]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ["company_id","=",$request->company_id],
            ["workspace_id","=",$request->workspace_id],
            ["order_id","=",$request->order_id]
        ];
        /* To insert the quantity in order production table */
        $prodData = OrderProduction::where($whereConditions)
        ->where("date_of_production",date('Y-m-d',strtotime($request->date)))
        ->where("type_of_production",$request->type_of_production)
        ->first();
        if(empty($prodData)){
            $res = json_encode(['status_code'=>204,"status" =>"Failure","message"=>"Date Exceeded"]);
            return CommonApp::webEncrypt($res);
        }
        elseif($prodData->holiday_flag === 1){
            $res = json_encode(['status_code'=>203,"status" =>"Failure","message"=>"It appears to be a holiday"]);
            return CommonApp::webEncrypt($res);
        }
        $prodData->actual_value = isset($request->quantity)?$request->quantity:NULL;
        $prodData->save();
        /* To Check the quantity exceeds the total quantity*/
        foreach($request->data as $sku){
            $sku = (array)$sku;
            if($sku['quantity']<0){
                $res = json_encode(["status_code"=>201,"status" =>"Failure","message"=>"Negative Quantity is not Allowed."]);
                return CommonApp::webEncrypt($res);
            }
            $conditionsToSend = [
                ["order_id","=",$request->order_id],
                ["color_id","=",$sku['color_id']],
                ["size_id","=",$sku['size_id']],
                ["type_of_production","=",$request->type_of_production],
                ["sku_date","!=",date('Y-m-d',strtotime($request->date))]
            ];
            $whereConditions2 =[
                ["order_sku.company_id","=",$request->company_id],
                ["order_sku.workspace_id","=",$request->workspace_id],
                ["order_sku.order_id","=",$request->order_id],
                ["order_sku.sku_color_id","=",$sku['color_id']],
                ["order_sku.sku_size_id","=",$sku['size_id']],
            ];
            $sumOfSku = UpdateSkuQuantity::where($conditionsToSend)->sum('updated_quantity');
            $skuData = OrderSku::where($whereConditions2)
            ->join("color","color.id","order_sku.sku_color_id")
            ->join("size","size.id","order_sku.sku_size_id")
            ->select('order_sku.id as id','order_sku.sku_quantity','size.name as size','color.name as color')
            ->first();
            $conditionsForDataAnalysis = [
                ["order_id","=",$request->order_id],
                ["color_id","=",$sku['color_id']],
                ["size_id","=",$sku['size_id']],
            ];
            /* To Check if the Sew value exceeds the total cut value of the particular SKU */
            if($request->type_of_production === "Sew"){
                $conditionsForDataAnalysis[] = ["type_of_production","=","Cut"];
                $sumOfCutSku = UpdateSkuQuantity::where($conditionsForDataAnalysis)->sum('updated_quantity');
                if(($sumOfSku + $sku['quantity']) > $sumOfCutSku){
                    $res = json_encode(["status_code"=>202,"status" =>"Failure","message"=>"Actual quantity for Color - '".$skuData->color."' and Size - '".$skuData->size."' exceeded total Cut quantity.","color"=>$skuData->color,"size"=>$skuData->size,"production_type"=>"sew"]);
                    return CommonApp::webEncrypt($res);
                }
            }
            /* To Check if the Pack value exceeds the total sew value of the particular SKU */
            if($request->type_of_production === "Pack"){
                $conditionsForDataAnalysis[] = ["type_of_production","=","Sew"];
                $sumOfSewSku = UpdateSkuQuantity::where($conditionsForDataAnalysis)->sum('updated_quantity');
                if(($sumOfSku + $sku['quantity']) > $sumOfSewSku){
                    $res = json_encode(["status_code"=>202,"status" =>"Failure","message"=>"Actual quantity for Color - '".$skuData->color."' and Size - '".$skuData->size."' exceeded total Sew quantity.","color"=>$skuData->color,"size"=>$skuData->size,"production_type"=>"pack"]);
                    return CommonApp::webEncrypt($res);
                }
            }
            /* To check if the updated quantity exceeds total quantity*/
            if(($sku['quantity']) > ($skuData->sku_quantity - $sumOfSku)){
                $res = json_encode(["status_code"=>202,"status" =>"Failure","message"=>"Actual quantity for Color - '".$skuData->color."' and Size - '".$skuData->size."' exceeded total quantity.","color"=>$skuData->color,"size"=>$skuData->size,"production_type"=>"cut"]);
                return CommonApp::webEncrypt($res);
            }
        }
        /* To Insert Accomplished Date */
        if($request->isOrderAccomplished === "1"){
            $order = Order::where('id',$request->order_id)->where('workspace_id',$request->workspace_id)
                          ->where('company_id',$request->company_id)->first();
            if($request->type_of_production === "Cut"){
                $order->cutting_accomplished_date = date('Y-m-d');
                $order->cutting_completion = 1;
            }
            else if($request->type_of_production === "Sew"){
                $order->sewing_accomplished_date = date('Y-m-d');
                $order->sewing_completion = 1;
            }
            else if($request->type_of_production === "Pack"){
                $order->packing_accomplished_date = date('Y-m-d');
                $order->packing_completion = 1;
            }
            $order->save();
            OrderProduction::where('order_id',$request->order_id)->where('type_of_production',$request->type_of_production)
            ->update(['is_accomplished'=>1]);
        }
        /* To insert the values into the table*/
        foreach($request->data as $skus){
            $skus = (array)$skus;
            $skusData = OrderSku::where($whereConditions)
            ->where('sku_color_id',$skus['color_id'])
            ->where('sku_size_id',$skus['size_id'])
            ->first();
            $whereConditions1 =[
                ["company_id","=",$request->company_id],
                ["workspace_id","=",$request->workspace_id],
                ["order_id","=",$request->order_id],
                ["color_id","=",$skus['color_id']],
                ["size_id","=",$skus['size_id']],
                ["sku_date","=",date('Y-m-d',strtotime($request->date))],
                ["sku_id","=",$skusData->id],
                ["type_of_production","=",$request->type_of_production]
            ];
            $ifValueAlreadyExists = UpdateSkuQuantity::where($whereConditions1)->first();
            if(empty($ifValueAlreadyExists)){
                $updateSKUArr=[];
                $updateSKUArr['company_id']=$request->company_id;
                $updateSKUArr['workspace_id']=$request->workspace_id;
                $updateSKUArr['user_id']=$request->user_id ?? 0;
                $updateSKUArr['staff_id']=$request->staff_id ?? 0;
                $updateSKUArr['order_id']=$request->order_id;
                $updateSKUArr['color_id']=$skus['color_id'];
                $updateSKUArr['size_id']=$skus['size_id'];
                $updateSKUArr['updated_quantity']=$skus['quantity'];
                $updateSKUArr['target_value']=$request->target_value;
                $updateSKUArr['sku_date']=date('Y-m-d',strtotime($request->date));
                $updateSKUArr['type_of_production']=$request->type_of_production;
                $updateSKUArr['sku_id']=$skusData->id;
                $updateSKUArr['created_at']=date('Y-m-d H:i:s');
                $updateSKUArr['updated_at']=date('Y-m-d H:i:s');
                UpdateSkuQuantity::insert($updateSKUArr);
            }
            else{
                $ifValueAlreadyExists->updated_quantity = $skus['quantity'] ;
                $ifValueAlreadyExists->user_id = $request->user_id ?? 0;
                $ifValueAlreadyExists->staff_id = $request->staff_id ?? 0;
                $ifValueAlreadyExists->updated_at = date('Y-m-d H:i:s');
                $ifValueAlreadyExists->save();
            }
            Logs::insertSKUDataInputHistory($request,$skus,$skusData,"Web",$header);
        }
        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Data Updated Successfully"]);
        return CommonApp::webEncrypt($res);
    }

    /* Add Data Input after the date has exceeded */
    public static function addInputDataAfterDateExceeded(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        //$requestdata = $request->data= (array)$request->data;
        $requestdata = $request->data = json_decode(json_encode($request->data), true);
        $validator = Validator::make((array)$request,[
            "company_id"=>"required",
            "workspace_id"=>"required",
            "order_id"=>"required",
            "data"=>"array",
            "data.*.quantity"=>"integer",
            "date"=>"required",
            "type_of_production"=>"required",
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to add Data Input
            $per = CommonApp::checkStaffPermission($request,'27');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        $orderValid=OrderProduction::select("id")->where('order_id',$request->order_id)->where('type_of_production',$request->type_of_production)
        ->where('is_accomplished',1)->count();
        if($orderValid>0){
            $res = json_encode(["status_code"=>201,"status" =>"Failure","message"=>$request->type_of_production." Already Accomplished."]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ["company_id","=",$request->company_id],
            ["workspace_id","=",$request->workspace_id],
            ["order_id","=",$request->order_id]
        ];
        /* To Check the quantity exceeds the total quantity*/
        foreach($requestdata as $sku){

            $sku = (array)$sku;
            if($sku['quantity']<0){
                $res = json_encode(["status_code"=>201,"status" =>"Failure","message"=>"Negative Quantity is not Allowed."]);
                return CommonApp::webEncrypt($res);
            }
            $conditionsToSend = [
                ["order_id","=",$request->order_id],
                ["color_id","=",$sku['color_id']],
                ["size_id","=",$sku['size_id']],
                ["type_of_production","=",$request->type_of_production],
                ["sku_date","!=",date('Y-m-d',strtotime($request->date))]
            ];
            $whereConditions2 =[
                ["order_sku.company_id","=",$request->company_id],
                ["order_sku.workspace_id","=",$request->workspace_id],
                ["order_sku.order_id","=",$request->order_id],
                ["order_sku.sku_color_id","=",$sku['color_id']],
                ["order_sku.sku_size_id","=",$sku['size_id']],
            ];
            $sumOfSku = UpdateSkuQuantity::where($conditionsToSend)->sum('updated_quantity');
            $skuData = OrderSku::where($whereConditions2)
            ->join("color","color.id","order_sku.sku_color_id")
            ->join("size","size.id","order_sku.sku_size_id")
            ->select('order_sku.id as id','order_sku.sku_quantity','size.name as size','color.name as color')
            ->first();
            $conditionsForDataAnalysis = [
                ["order_id","=",$request->order_id],
                ["color_id","=",$sku['color_id']],
                ["size_id","=",$sku['size_id']],
            ];
            /* To Check if the Sew value exceeds the total cut value of the particular SKU */
            if($request->type_of_production === "Sew"){
                $conditionsForDataAnalysis[] = ["type_of_production","=","Cut"];
                $sumOfCutSku = UpdateSkuQuantity::where($conditionsForDataAnalysis)->sum('updated_quantity');
                if(($sumOfSku + $sku['quantity']) > $sumOfCutSku){
                    $res = json_encode(["status_code"=>202,"status" =>"Failure","message"=>"Actual quantity for Color - '".$skuData->color."' and Size - '".$skuData->size."' exceeded total Cut quantity.","color"=>$skuData->color,"size"=>$skuData->size,"production_type"=>"sew"]);
                    return CommonApp::webEncrypt($res);
                }
            }
            /* To Check if the Pack value exceeds the total sew value of the particular SKU */
            if($request->type_of_production === "Pack"){
                $conditionsForDataAnalysis[] = ["type_of_production","=","Sew"];
                $sumOfSewSku = UpdateSkuQuantity::where($conditionsForDataAnalysis)->sum('updated_quantity');
                if(($sumOfSku + $sku['quantity']) > $sumOfSewSku){
                    $res = json_encode(["status_code"=>202,"status" =>"Failure","message"=>"Actual quantity for Color - '".$skuData->color."' and Size - '".$skuData->size."' exceeded total Sew quantity.","color"=>$skuData->color,"size"=>$skuData->size,"production_type"=>"pack"]);
                    return CommonApp::webEncrypt($res);
                }
            }
            /* To check if the updated quantity exceeds total quantity*/
            if(($sku['quantity']) > ($skuData->sku_quantity - $sumOfSku)){
                $res = json_encode(["status_code"=>202,"status" =>"Failure","message"=>"Actual quantity for Color - '".$skuData->color."' and Size - '".$skuData->size."' exceeded total quantity.","color"=>$skuData->color,"size"=>$skuData->size,"production_type"=>"cut"]);
                return CommonApp::webEncrypt($res);
            }
        }
        /* To Insert Accomplished Date */
        $order = Order::where('id',$request->order_id)->where('workspace_id',$request->workspace_id)
                      ->where('company_id',$request->company_id)->first();
        /* To Check if the Type of production is already accomplished */
        if($request->type_of_production === "Cut" && $order->cutting_completion === 1){
            $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"Already Updated."]);
            return CommonApp::webEncrypt($res);
        }
        if($request->type_of_production === "Sew" && $order->sewing_completion === 1){
            $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"Already Updated."]);
            return CommonApp::webEncrypt($res);
        }
        if($request->type_of_production === "Pack" && $order->packing_completion === 1){
            $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"Already Updated."]);
            return CommonApp::webEncrypt($res);
        }
        if($request->isOrderAccomplished === "1"){
            if($request->type_of_production === "Cut"){
                $order->cutting_accomplished_date = date('Y-m-d');
                $order->cutting_completion = 1;
            }
            else if($request->type_of_production === "Sew"){
                $order->sewing_accomplished_date = date('Y-m-d');
                $order->sewing_completion = 1;
            }
            else if($request->type_of_production === "Pack"){
                $order->packing_accomplished_date = date('Y-m-d');
                $order->packing_completion = 1;
            }
            $order->save();
            OrderProduction::where('order_id',$request->order_id)->where('type_of_production',$request->type_of_production)
            ->update(['is_accomplished'=>1]);
        }
        /* To insert the values into the table*/
        foreach($requestdata as $skus){
            $skus = (array)$skus;
            $skusData = OrderSku::where($whereConditions)
            ->where('sku_color_id',$skus['color_id'])
            ->where('sku_size_id',$skus['size_id'])
            ->first();
            $whereConditions1 =[
                ["company_id","=",$request->company_id],
                ["workspace_id","=",$request->workspace_id],
                ["order_id","=",$request->order_id],
                ["color_id","=",$skus['color_id']],
                ["size_id","=",$skus['size_id']],
                ["sku_date","=",date('Y-m-d',strtotime($request->date))],
                ["sku_id","=",$skusData->id],
                ["type_of_production","=",$request->type_of_production]
            ];
            $ifValueAlreadyExists = UpdateSkuQuantity::where($whereConditions1)->first();
            if(empty($ifValueAlreadyExists)){
                $updateSKUArr=[];
                $updateSKUArr['company_id']=$request->company_id;
                $updateSKUArr['workspace_id']=$request->workspace_id;
                $updateSKUArr['user_id']=$request->user_id ?? 0;
                $updateSKUArr['staff_id']=$request->staff_id ?? 0;
                $updateSKUArr['order_id']=$request->order_id;
                $updateSKUArr['color_id']=$skus['color_id'];
                $updateSKUArr['size_id']=$skus['size_id'];
                $updateSKUArr['updated_quantity']=$skus['quantity'];
                $updateSKUArr['target_value']=$request->target_value;
                $updateSKUArr['sku_date']=date('Y-m-d',strtotime($request->date));
                $updateSKUArr['type_of_production']=$request->type_of_production;
                $updateSKUArr['sku_id']=$skusData->id;
                $updateSKUArr['created_at']=date('Y-m-d H:i:s');
                $updateSKUArr['updated_at']=date('Y-m-d H:i:s');
                UpdateSkuQuantity::insert($updateSKUArr);
            }
            else{
                $ifValueAlreadyExists->updated_quantity = $skus['quantity'] ;
                $ifValueAlreadyExists->user_id = $request->user_id ?? 0;
                $ifValueAlreadyExists->staff_id = $request->staff_id ?? 0;
                $ifValueAlreadyExists->updated_at = date('Y-m-d H:i:s');
                $ifValueAlreadyExists->save();
            }
            Logs::insertSKUDataInputHistory($request,$skus,$skusData,"Web",$header);
        }
        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Data Updated Successfully"]);
        return CommonApp::webEncrypt($res);
    }
}
