<?php

namespace App\Http\Controllers\Mobile\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BasicInfo extends Controller
{
    /* Get the basic Information */
    public static function getBasicInfo(Request $request){
        $validated = Validator::make($request->all(),[
            'order_id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $whereConditions=[
            ['orders.id','=',$request->order_id],
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id]
        ];
        $basicInfo = Order::where($whereConditions)
                    ->join('workspace','workspace.id','orders.workspace_id')
                    ->leftjoin('order_factory','order_factory.id','orders.factory_id')
                    ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
                    ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
                    ->select('orders.order_no as order','orders.factory_id','orders.pcu_id','orders.buyer_id','orders.style_no as style','orders.total_quantity as quantity',
                    'workspace.name as workspace','order_pcu.name as pcu','order_factory.name as factory','order_buyer.name as buyer','orders.is_tolerance_req','orders.tolerance_volume','orders.tolerance_perc')
                    ->get();

        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$basicInfo]);
    }
}
