<?php

namespace App\Http\Controllers\Mobile\v1\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\OrderSku;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Validation\Rule;

class AddOrderSKU extends Controller
{

    /* Add/overwrite SKU's for the order */


    public static function addSku (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
            'sku' => 'required|array',
            'sku.*.color_id'=>['integer','min:0',Rule::exists('color','id')],
            'sku.*.size_id'=>['integer','min:0',Rule::exists('size','id')],
        ],$messages=['sku.*.color_id.exists'=>"Color Not Found",'sku.*.size_id.exists'=>"Size Not Found"]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $orderId= $request->order_id;
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id]
        ];
        $whereConditionsOrd =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['id','=',$request->order_id]
        ];
        $aldreadyExists = OrderSku::where($whereConditions)->get();
        if(!empty($aldreadyExists)){
            OrderSku::where($whereConditions)->delete();
        }
        /*Update Total Qty*/
        $total_quantity=$request->total_qty;
        if($total_quantity>0){
            Order::where($whereConditionsOrd)->update(["total_quantity" =>$total_quantity]);
        }
        $orderSkuArr = [];
        $orderSkuArr['user_id']= $companyDetails->user_id;
        $orderSkuArr['company_id']= $request->company_id;
        $orderSkuArr['workspace_id']= $request->workspace_id;
        $orderSkuArr['staff_id']= $request->input('staff_id','0');
        $orderSkuArr['order_id']= $request->order_id;
        foreach ($request->sku as $sku){
            // dd($sku);
            $orderSkuArr['sku_color_id']=$sku['color_id'];
            $orderSkuArr['sku_size_id']=$sku['size_id'];
            $orderSkuArr['sku_quantity']=$sku['quantity'];
            $orderSkuArr['created_at']=date('Y-m-d H:i:s');
            $orderSkuArr['updated_at']=date('Y-m-d H:i:s');
            OrderSku::insert($orderSkuArr);
        }

        $addOrderArr=[];
        $addOrderArr['step_level'] = '2';
        if($request->tolPerc > 0 && $request->tolVol >0){
            $addOrderArr['tolerance_perc'] = $request->tolPerc;
            $addOrderArr['tolerance_volume'] = $request->tolVol;
        }
        Order::where('id',$orderId)->update($addOrderArr);
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"SKU's Added Successfully"]);
    }

    /* List the updated sku's for the order */

    public static function getSku(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id]
        ];

        $skuDetails = OrderSku::where($whereConditions)->get();
		$arr=array();$i=0;
    	foreach ($skuDetails as $value) {
            $getColorId=$value->sku_color_id;
            $getColorName=Color::getColorNameUsingId($getColorId);
            $getSizeId=$value->sku_size_id;
            $getSizeName=Size::getSizeNameUsingId($getSizeId);
    		$arr[$i]['color_id']=$getColorId;
            $arr[$i]['color_name']=$getColorName['name'];
    		$arr[$i]['size_id']=$getSizeId;
            $arr[$i]['size_name']=$getSizeName['name'];
    		$arr[$i]['quantity']=$value->sku_quantity;
    		$i++;

		}
        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$arr]);
    }
}
