<?php

namespace App\Http\Controllers\Mobile\v1\Order\EditOrders;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EditProduction extends Controller
{
    public static function editProductionData(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
            'prod_datas' => 'required|array',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $companyId = $request->company_id;
        $workspaceId = $request->workspace_id;
        $orderId = $request->order_id;
        $order = Order::where('id',$orderId)->first();
        foreach ($request->prod_datas as  $productionData) {
            if(!empty($productionData)){

            $whereConditions =[
                ['workspace_id','=',$request->workspace_id],
                ['company_id', '=', $request->company_id],
                ['order_id','=',$request->order_id],
                ['type_of_production','=',$productionData['type_of_production']]
            ];
            $aldreadyExists = OrderProduction::where($whereConditions)->get();
            if(!empty($aldreadyExists)){
                OrderProduction::where($whereConditions)->delete();
            }

            if($productionData['start_date'] != "" && $productionData['end_date'] != ""){
                if($productionData['type_of_production'] == 'Cut'){
                    $order->cutting_start_date = date('Y-m-d',strtotime($productionData['start_date']));
                    $order->cutting_end_date = date('Y-m-d',strtotime($productionData['end_date']));
                    $order->save();
                }
                else if($productionData['type_of_production'] == 'Sew'){
                    $order->sewing_start_date = date('Y-m-d',strtotime($productionData['start_date']));
                    $order->sewing_end_date = date('Y-m-d',strtotime($productionData['end_date']));
                    $order->save();
                }
                else if($productionData['type_of_production'] == 'Pack'){
                    $order->packing_start_date = date('Y-m-d',strtotime($productionData['start_date']));
                    $order->packing_end_date = date('Y-m-d',strtotime($productionData['end_date']));
                    $order->save();
                }
                $orderProductionArr = [];
                $orderProductionArr['user_id']= $companyDetails->user_id;
                $orderProductionArr['company_id']= $companyId;
                $orderProductionArr['workspace_id']= $workspaceId;
                $orderProductionArr['order_id']= $orderId;
                $orderProductionArr['type_of_production']= $productionData['type_of_production'];
                if(!empty($productionData['prod_data'])){
                foreach ($productionData['prod_data'] as $prodData){
                    $orderProductionArr['date_of_production']= date('Y-m-d',strtotime($prodData['date_of_production']));
                    $orderProductionArr['target_value']= $prodData['target_value'];
                    $orderProductionArr['holiday_flag']= $prodData['holiday_flag'];
                    $orderProductionArr['holiday_detail']= $prodData['holiday_detail'];
                    $orderProductionArr['created_at']=date('Y-m-d H:i:s');
                    $orderProductionArr['updated_at']=date('Y-m-d H:i:s');
                    OrderProduction::insert($orderProductionArr);
                }
            }
            }
        }
            else{
               // return response()->json(["status_code"=>400,"status" =>"Failure","message"=>"Please enter the Dates"]);
            }
        }
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Production Data Updated Successfully"]);
    }
}
