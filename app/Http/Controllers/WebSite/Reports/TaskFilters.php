<?php

namespace App\Http\Controllers\WebSite\Reports;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Common\CommonApp;

class TaskFilters extends Controller
{
    /*Get PCU Based on Factory*/
    public static function getSecondBasedOnFirst(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'type' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code" =>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition = [
            ['orders.company_id',"=",$request->company_id],
            ['orders.workspace_id',"=",$request->workspace_id],
        ];
        if(((isset($request->type) && isset($request->factory_id)) && ($request->type === "Buyer" )) || ((isset($request->type) && isset($request->buyer_id)) && ($request->type === "Factory") ))
        {
            if(isset($request->factory_id)){
                $whereCondition[] = ['orders.factory_id',"=",$request->factory_id];
            }
            else if(isset($request->buyer_id)){
                $whereCondition []= ['orders.buyer_id',"=",$request->buyer_id];
            }
            $second = Order::where($whereCondition)
                      ->join('order_pcu','order_pcu.id','orders.pcu_id')
                      ->select('order_pcu.id','order_pcu.name')
                      ->distinct('orders.pcu_id')
                      ->get();
        }
        else if(((isset($request->type) && isset($request->factory_id)) && ($request->type === "PCU" )) || ((isset($request->type) && isset($request->buyer_id)) && ($request->type === "PCU" ) )){
           // $whereCondition[] = ['orders.factory_id',"=",$request->factory_id];
            if(isset($request->factory_id)){
            $whereCondition[] = ['orders.factory_id',"=",$request->factory_id];
            }
            else if(isset($request->buyer_id)){
                $whereCondition []= ['orders.buyer_id',"=",$request->buyer_id];
            }
            $second = Order::where($whereCondition)
                      ->join('order_buyer','order_buyer.id','orders.buyer_id')
                      ->select('order_buyer.id','order_buyer.name')
                      ->distinct('orders.buyer_id')
                      ->get();
        }
        $data=$second;
        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$data]);
        return CommonApp::webEncrypt($res);
    }
}
