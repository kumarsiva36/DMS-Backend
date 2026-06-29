<?php

namespace App\Http\Controllers\Mobile\v1\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddOrderProduction extends Controller
{

   /* Add/overwrite production data for order */

    public static function addProductionData(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
            'prod_datas' => 'required|array',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to Add Data Input
            $per = CommonApp::checkStaffPermission($request,'27');
            if($per===0){
                return CommonApp::checkStaffPermissionResponseMobile();
            }
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
                  //  $order->cutting_start_date = date('Y-m-d',strtotime($productionData['start_date']));
                   // $order->cutting_end_date = date('Y-m-d',strtotime($productionData['end_date']));

                    $order->cutting_start_date =$productionData['start_date'];
                    $order->cutting_end_date =$productionData['end_date'];
                    $order->save();
                }
                else if($productionData['type_of_production'] == 'Sew'){
                   // $order->sewing_start_date = date('Y-m-d',strtotime($productionData['start_date']));
                   // $order->sewing_end_date = date('Y-m-d',strtotime($productionData['end_date']));

                    $order->sewing_start_date = $productionData['start_date'];
                    $order->sewing_end_date =$productionData['end_date'];
                    $order->save();
                }
                else if($productionData['type_of_production'] == 'Pack'){
                   // $order->packing_start_date = date('Y-m-d',strtotime($productionData['start_date']));
                   // $order->packing_end_date = date('Y-m-d',strtotime($productionData['end_date']));

                    $order->packing_start_date = $productionData['start_date'];
                    $order->packing_end_date = $productionData['end_date'];
                    $order->save();
                   /*Update Order Step Status*/
          $addOrderArr=[];
          $addOrderArr['step_level'] = '6';
           Order::where('id',$orderId)->update($addOrderArr);
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

        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Production Data Added Successfully"]);
    }
    /* Get The Production Data */
    public static function getProductionData(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
            'type_of_production' => 'required'
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id],
            ['type_of_production','=',$request->type_of_production]
        ];

        $prodDetails = OrderProduction::where($whereConditions)->get();
		$arr=array();$i=0;
    	foreach ($prodDetails as $value) {
    		$arr[$i]['date_of_production']=$value->date_of_production;
    		$arr[$i]['target_value']=$value->target_value;
    		$arr[$i]['holiday_flag']=$value->holiday_flag;
    		$arr[$i]['holiday_detail']=$value->holiday_detail;
    		$i++;

		}
        return response()->json(["status_code"=>200,"status" =>"Success","Production Type"=>$request->type_of_production,"data"=>$arr],200);
    }
}
