<?php

namespace App\Http\Controllers\WebSite\Order\DataInput;

use App\Common\Logs;
use App\Http\Controllers\Controller;
use App\Models\OrderProduction;
use App\Models\OrderSku;
use App\Models\UpdateSkuQuantity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddInputData extends Controller
{
    /* To Add or Update the quantity of the SKU metrics */
    public static function addInputData(Request $request){
        $validator = Validator::make($request->all(),[
            "company_id"=>"required",
            "workspace_id"=>"required",
            "order_id"=>"required",
            "color_id" => "required",
            "size_id"=>"required",
            "quantity"=>"required",
            "date"=>"required",
            "type_of_production"=>"required",
        ]);
        if($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereConditions=[
            ["company_id","=",$request->company_id],
            ["workspace_id","=",$request->workspace_id],
            ["order_id","=",$request->order_id]
        ];
        $conditionsToSend = [
            ["order_id","=",$request->order_id],
            ["color_id","=",$request->color_id],
            ["size_id","=",$request->size_id],
            ["type_of_production","=",$request->type_of_production],
            ["sku_date","!=",date('Y-m-d',strtotime($request->date))]
        ];
        /* To insert the quantity in order production table */
        $prodData = OrderProduction::where($whereConditions)
                    ->where("date_of_production",date('Y-m-d',strtotime($request->date)))
                    ->where("type_of_production",$request->type_of_production)
                    ->first();
        if(empty($prodData)){
            return response()->json(['status_code'=>201,"status" =>"Failure","message"=>"Date Exceeded"]);
        }
        elseif($prodData->holiday_flag === 1){
            return response()->json(['status_code'=>201,"status" =>"Failure","message"=>"It appears to be a holiday"]);
        }
        $prodData->actual_value = $request->quantity;
        $prodData->save();
        $sumOfSku = UpdateSkuQuantity::where($conditionsToSend)->sum('updated_quantity');
        $skuData = OrderSku::where($whereConditions)
        ->where('sku_color_id',$request->color_id)
        ->where('sku_size_id',$request->size_id)
        ->first();
        $conditionsForDataAnalysis = [
            ["order_id","=",$request->order_id],
            ["color_id","=",$request->color_id],
            ["size_id","=",$request->size_id],
        ];
        /* To Check if the Sew value exceeds the total cut value of the particular SKU */
        if($request->type_of_production === "Sew"){
            $conditionsForDataAnalysis[] = ["type_of_production","=","Cut"];
            $sumOfCutSku = UpdateSkuQuantity::where($conditionsForDataAnalysis)->sum('updated_quantity');
            if(($sumOfSku + $request->quantity) > $sumOfCutSku){
                return response()->json(["status_code"=>201,"status" =>"Failure","message"=>"Actual quantity exceeded total Cut quantity."]);
            }
        }
        /* To Check if the Pack value exceeds the total sew value of the particular SKU */
        if($request->type_of_production === "Pack"){
            $conditionsForDataAnalysis[] = ["type_of_production","=","Sew"];
            $sumOfSewSku = UpdateSkuQuantity::where($conditionsForDataAnalysis)->sum('updated_quantity');
            if(($sumOfSku + $request->quantity) > $sumOfSewSku){
                return response()->json(["status_code"=>201,"status" =>"Failure","message"=>"Actual quantity exceeded total Sew quantity."]);
            }
        }
        /* To check if the updated quantity exceeds total quantity*/
        if(($request->quantity) > ($skuData->sku_quantity - $sumOfSku)){
            return response()->json(["status_code"=>201,"status" =>"Failure","message"=>"Actual quantity exceeded total quantity."]);
        }
        $whereConditions1 =[
            ["company_id","=",$request->company_id],
            ["workspace_id","=",$request->workspace_id],
            ["order_id","=",$request->order_id],
            ["color_id","=",$request->color_id],
            ["size_id","=",$request->size_id],
            ["sku_date","=",date('Y-m-d',strtotime($request->date))],
            ["sku_id","=",$skuData->id],
            ["type_of_production","=",$request->type_of_production]
        ];

        $ifValueAlreadyExists = UpdateSkuQuantity::where($whereConditions1)->first();
        if(empty($ifValueAlreadyExists)){
            $updateSKUArr=[];
            $updateSKUArr['company_id']=$request->company_id;
            $updateSKUArr['workspace_id']=$request->workspace_id;
            $updateSKUArr['user_id']=$request->input('user_id',0);
            $updateSKUArr['staff_id']=$request->input('staff_id',0);
            $updateSKUArr['order_id']=$request->order_id;
            $updateSKUArr['color_id']=$request->color_id;
            $updateSKUArr['size_id']=$request->size_id;
            $updateSKUArr['updated_quantity']=$request->quantity;
            $updateSKUArr['sku_date']=date('Y-m-d',strtotime($request->date));
            $updateSKUArr['type_of_production']=$request->type_of_production;
            $updateSKUArr['sku_id']=$skuData->id;
            $updateSKUArr['created_at']=date('Y-m-d H:i:s');
            $updateSKUArr['updated_at']=date('Y-m-d H:i:s');
            UpdateSkuQuantity::insert($updateSKUArr);
        }
        else{
            $ifValueAlreadyExists->updated_quantity = $request->quantity ;
            $ifValueAlreadyExists->user_id = $request->input('user_id',0);
            $ifValueAlreadyExists->staff_id = $request->input('staff_id',0);
            $ifValueAlreadyExists->updated_at = date('Y-m-d H:i:s');
            $ifValueAlreadyExists->save();
        }
        Logs::insertSKUDataInputHistory($request , $skuData);
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Data Updated Successfully"]);
    }
}
