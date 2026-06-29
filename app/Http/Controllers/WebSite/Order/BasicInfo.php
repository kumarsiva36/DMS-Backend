<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\MultipleDeliveryDates;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BasicInfo extends Controller
{
    /* Get the data for the basic info in the order section */
    public static function getBasicInfo(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'order_id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
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
                    ->leftjoin('multiple_delivery_dates','multiple_delivery_dates.order_id','orders.id')
                    ->select('orders.order_no as order','orders.style_no as style','orders.total_quantity as quantity',
                    'workspace.name as workspace','order_pcu.name as pcu','order_factory.name as factory',
                    'order_buyer.name as buyer','orders.cutting_start_date','orders.packing_end_date','orders.is_tolerance_req'
                    ,'orders.tolerance_volume','orders.tolerance_perc','orders.status','orders.step_level','orders.inquiry_date',
                    'orders.delivery_date','orders.sewing_start_date','orders.packing_start_date',DB::raw('DATE_FORMAT(orders.created_at,"%Y-%m-%d") as created_date'),
                    DB::raw('GROUP_CONCAT(multiple_delivery_dates.delivery_date) as delivery_dates'))
                    ->get();
        //Delivery Dates
        $delivery_dates = MultipleDeliveryDates::where('order_id','=',$request->order_id)
                        ->select("delivery_date","is_delivered",DB::raw('DATE_FORMAT(updated_at,"%Y-%m-%d") as updated_date'))
                        ->orderBy('delivery_date',"ASC")->get();

                        $delivery_date = MultipleDeliveryDates::where('order_id','=',$request->order_id)->where('is_delivered','=','0')
                        ->orderBy('delivery_date',"ASC")->pluck('delivery_date')->first();
        $delivery_date_exceed=0;
        if($delivery_date!="" && $delivery_date!=null){
            if($delivery_date < date('Y-m-d')){
                $delivery_date_exceed=1;
            }
        }

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$basicInfo,"delivery_dates"=>$delivery_dates,"delivery_date"=>$delivery_date,"delivery_date_exceed"=>$delivery_date_exceed]);
        return CommonApp::webEncrypt($res);
    }
}
