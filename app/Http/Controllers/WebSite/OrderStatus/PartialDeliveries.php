<?php

namespace App\Http\Controllers\WebSite\OrderStatus;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\MultipleDeliveryDates;
use App\Models\Order;
use App\Models\OrderSku;
use App\Models\PartialDelivery;
use App\Models\Size;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PartialDeliveries extends Controller
{
    /* Add Partial Deliveries */
    public static function addPartialDelivery(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'user_type' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'sku_data' => 'required|array',
            'delivery_date'=>'required|date',
            // 'sku_data.*.color_id' => 'required',
            // 'sku_data.*.size_id' => 'required',
            // 'sku_data.*.quantity' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            PartialDelivery::addPartialDeliveries($request);
            $res = json_encode(['status_code' => 200, 'status'=>"success",'message' =>"Data Added successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"errors"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Get Details For partial Deliveries */
    public static function getDetailsForPartialDelivery(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'user_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->order_id]
        ];
        $whereConditions1=[
            ['order_sku.company_id','=',$request->company_id],
            ['order_sku.workspace_id','=',$request->workspace_id],
            ['order_sku.order_id','=',$request->order_id]
        ];
        $whereConditions2=[
            ['partial_deliveries.company_id','=',$request->company_id],
            ['partial_deliveries.workspace_id','=',$request->workspace_id],
            ['partial_deliveries.order_id','=',$request->order_id]
        ];
        $arr=[];
        $arr['deliveryDates'] = MultipleDeliveryDates::where($whereConditions)
        ->select('id','delivery_date','is_delivered','delivery_comments','total_delivered_quantity')
        ->get();
        $arr['delivery_count'] = count($arr['deliveryDates']);
        $arr['delivered_count'] = count(array_filter($arr['deliveryDates']->toArray(), function ($var) {
            return ($var['is_delivered'] == 1);
        }));
        $arr['total_delivered_quantity'] =array_sum(array_column($arr['deliveryDates']->toArray(),'total_delivered_quantity'));
        foreach($arr['deliveryDates'] as $date){
            $dates['delivery_date']=$date->delivery_date;
            $dates['is_delivered']=$date->is_delivered;
            $dates['delivery_comments']=$date->delivery_comments;
            $dates['total_delivered_quantity']=$date->total_delivered_quantity;
            $whereConditions2[]=['delivery_date','=',$date->delivery_date];
            $dates['sku_data']= PartialDelivery::where($whereConditions2)
            ->join('color','color.id','partial_deliveries.color_id')
            ->join('size','size.id','partial_deliveries.size_id')
            ->select('partial_deliveries.color_id','partial_deliveries.size_id',DB::raw('SUM(quantity) as total_quantity')
            ,'delivery_comments','delivery_date','color.name as color_name','size.name as size_name')
            ->orderBy('delivery_date','asc')
            ->groupBy('partial_deliveries.color_id')
            ->groupBy('partial_deliveries.size_id')
            ->get();
            $arr['addedDeliveryDates'][]=$dates;
            array_pop($whereConditions2);
        }
        // dd( $arr['addedDeliveryDates']);
        $arr['sku_data']=OrderSku::where($whereConditions1)
        ->join('color','color.id','order_sku.sku_color_id')
        ->join('size','size.id','order_sku.sku_size_id')
        ->leftjoin('update_sku_quantities', function($join) {
            $join->on('update_sku_quantities.order_id','order_sku.order_id');
            $join->on('update_sku_quantities.color_id','order_sku.sku_color_id');
            $join->on('update_sku_quantities.size_id','order_sku.sku_size_id')
                ->where('update_sku_quantities.type_of_production','=','Pack');
        })
        ->select('color.name as color_name','color.id as color_id','size.name as size_name',
        'size.id as size_id','sku_quantity',
        DB::raw('(CASE WHEN update_sku_quantities.type_of_production="Pack" THEN update_sku_quantities.updated_quantity ELSE 0 END)as updated_quantity'))
        ->get();
        $arr['total_pack_quantity']=array_sum(array_column($arr['sku_data']->toArray(),'updated_quantity'));
        // dd($arr['total_pack_quantity'],$arr['sku_data']);
        $colorArr=$sizeArr=[];
        foreach($arr['sku_data'] as $skuData){
            $array = $Array =[];
            $array['color_id']=$skuData['color_id'];
            $array['colorName']=$skuData['color_name'];
            $Array['size_id']=$skuData['size_id'];
            $Array['sizeName']=$skuData['size_name'];
            $colorArr[]=$array;
            $sizeArr[]=$Array;
        }
        $arr['colors']=isset($colorArr)?
        array_map("unserialize",array_values(array_unique(array_map("serialize",$colorArr)))):[];
        $arr['sizes']=isset($sizeArr)?
        array_map("unserialize",array_values(array_unique(array_map("serialize",$sizeArr)))):[];
        $arr['order'] = Order::where('id',$request->order_id)
        ->select('total_quantity','no_of_deliverys')
        ->first();

        $res = json_encode(['status_code' => 200, 'status'=>"success",'data' =>$arr]);
        return CommonApp::webEncrypt($res);
    }
}
