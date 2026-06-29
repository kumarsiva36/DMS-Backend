<?php

namespace App\Http\Controllers\Mobile\Order\EditOrders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EditOrder extends Controller
{
    /* To View the added order */
    public static function getOrderDetails(Request $request){
        $validated = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $whereConditions = [
            ['id','=',$request->order_id],
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id]
        ];
        $order = Order::where($whereConditions)
                ->select('order_no','style_no','buyer_id','factory_id','pcu_id','category_id','article_id','fabric_id','order_price','income_terms','total_quantity','no_of_deliverys','quantity_wise','tolerance_perc','tolerance_volume','currency_type','is_tolerance_req','status')
                ->first();

        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$order]);
    }

    public function updateOrderDetails( Request $request)
    {
        $validator = Validator::make($request->all(),[
        'order_id' => 'required',
        'company_id' => 'required',
        'workspace_id' => 'required',
        //'order_no' => 'required',
        //'style_no' => 'required',
       // 'category_id' => 'required',
        'fabric_id' => 'required',
        'article_id' => 'required',
        'total_quantity' => 'required',
        'no_of_deliverys' => 'required',
        'order_price' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(["status_code"=>401,
            "error"=>$validator->errors()]);
        }
        $whereCondition = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['id','=',$request->order_id],
            ];
        $updateOrder = Order::where($whereCondition)->first();
        //$updateOrder->order_no = $request->order_no;
        //$updateOrder->style_no = $request->style_no;
        $updateOrder->category_id = $request->category_id;
        $updateOrder->fabric_id = $request->fabric_id;
        $updateOrder->article_id = $request->article_id;
        $updateOrder->total_quantity = $request->total_quantity;
        $updateOrder->no_of_deliverys = $request->no_of_deliverys;
        $updateOrder->order_price = $request->order_price;
        $updateOrder->tolerance_volume = $request->tolerance_volume;
        $updateOrder->tolerance_perc = $request->tolerance_perc;
        $updateOrder->quantity_wise = $request->quantity_wise;
        $updateOrder->income_terms = $request->income_terms;
        if($request->is_tolerance_req==1){
            $is_tol_req="1";
        }else{
            $is_tol_req="0";
        }
        $updateOrder->is_tolerance_req = $is_tol_req;
        $updateOrder->currency_type = $request->currency_type;
        $updateOrder->save();
        return response()->json([ "status_code"=>200, "status"=>"Success",
        "message"=>"Order updated successfully",  "data"=>$updateOrder]);
    }
}
