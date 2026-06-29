<?php

namespace App\Http\Controllers\Mobile\v1\Order\GetOrder;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Factory;
use App\Models\Order;
use App\Models\OrderTask;
use App\Models\PCU;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\OrderContacts;
use App\Common\CommonApp;

class GetStyle extends Controller
{
    /****** Get Styles for the Filter  *********/
    public static function getStyles(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id'=>'required',
            'workspace_id'=>'required'
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $pagetype=$request->page_type ?? "";
        if($pagetype == 'orderstatus'){
        $whereCondition =[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],

        ];
    }else{
        $whereCondition =[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_task_template','!=',0],
            ['status','=','1']
        ];
    }
        if (isset($request->factory_id) && $request->factory_id>0){
            $whereCondition['factory_id'] = $request->factory_id;
        }
        if (isset($request->pcu_id) && $request->pcu_id>0){
            $whereCondition['pcu_id'] = $request->pcu_id;
        }
        if (isset($request->buyer_id) && $request->buyer_id>0){
            $whereCondition['buyer_id'] = $request->buyer_id;
        }
        if(isset($request->staff_id) && $request->staff_id > 0){
            $whereCondition1= $whereCondition2 =[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $staffRoleHasPermission = Staff::where('id',$request->staff_id)->select('role_id','company_id')->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
            $whereCondition1[]=['permission_id','=','19'];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $theOrders = [];
                $whereCondition3 = [];
                if (isset($request->factory_id) && $request->factory_id!=""){
                    $whereCondition3['factory_id'] = $request->factory_id;
                }
                if (isset($request->pcu_id) && $request->pcu_id!=""){
                    $whereCondition3['pcu_id'] = $request->pcu_id;
                }
                if (isset($request->buyer_id) && $request->buyer_id!=""){
                    $whereCondition3['buyer_id'] = $request->buyer_id;
                }
                foreach($involedOrders as $order){
                    $whereCondition3['id']=$order->order_id;
                    $theOrder = Order::where($whereCondition3)->select('id','style_no','order_no','step_level')->first();
                    if(!empty($theOrder)){
                        $theOrders[]=$theOrder;
                    }
                }
                $orders = $theOrders;
            }else{
                $orders = Order::where($whereCondition)
                        ->select('id','style_no','order_no','step_level')
                        ->orderBy("id","DESC")
                        ->get();
            }
        }else{
            $orders = Order::where($whereCondition)
                    ->select('id','style_no','order_no','step_level')
                    ->orderBy("id","DESC")
                    ->get();
        }

        $res = json_encode(['status_code' => 200,'status'=>"success" ,'data' => $orders]);
        return CommonApp::apiEncrypt($res);
    }

    /********************* Get the particular style **********************/
    public static function getTheStyle(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id'=>'required',
            'workspace_id'=>'required',
            'orderId'=>'required',
        ]);
        if($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $order = Order::where('id',$request->orderId)->first();
        $whereCondition = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->orderId],
            ['template_id','=',$order->order_task_template]
        ];
        $factory = Factory::where('id',$order->factory_id)->select('name')->first();
        $pcu = PCU::where('id',$order->pcu_id)->select('name')->first();
        $buyer = Buyer::where('id',$order->buyer_id)->select('name')->first();
        $templateData = OrderTask::where($whereCondition)->get();
        $taskDataArr = array();
        $totalTask = count($templateData);
        $scheduledTasks = count(OrderTask::where($whereCondition)->where('task_schedule_start_date','!=',NULL)
        ->where('task_schedule_end_date','!=',NULL)->get());
        $accomplishedTasks = count(OrderTask::where($whereCondition)->where('task_accomplished_date','!=',NULL)->get());
        $taskDataArr['totalTask'] = $totalTask;
        $taskDataArr['scheduledTasks'] = $scheduledTasks;
        $taskDataArr['accomplishedTasks'] = $accomplishedTasks;
        $arr = [];
        if($order->factory_id >0){
            $arr['factoryName'] = $factory->name;
        }
        if($order->pcu_id >0){
            $arr['pcuName'] = $pcu->name;
        }
        if($order->buyer_id >0){
            $arr['buyerName'] = $buyer->name;
        }
        return response()->json(["status_code"=>200,"status" =>"Success",
        "name"=>$arr,"taskInfoData"=>$taskDataArr,"data"=>$order]);
    }
    public static function getFilters(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id'=>'required',
            'workspace_id'=>'required',

        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }

        $whereCondition =[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
        ];
        $factory = Factory::where($whereCondition)->select('id','name')->get();
        $pcu = PCU::where($whereCondition)->select('id','name')->get();
        $buyer = Buyer::where($whereCondition)->select('id','name')->get();
        if(isset($request->staff_id) && $request->staff_id > 0){
            $whereCondition1= $whereCondition2 =[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $staffRoleHasPermission =  Staff::where('id',$request->staff_id)->select('role_id','company_id')->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
            $whereCondition1[]=['permission_id','=','19'];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $theOrders = [];
                $whereCondition3 = [];
                if (isset($request->factory_id) && $request->factory_id>0){
                    $whereCondition3['factory_id'] = $request->factory_id;
                }
                if (isset($request->pcu_id) && $request->pcu_id>0){
                    $whereCondition3['pcu_id'] = $request->pcu_id;
                }
                if (isset($request->buyer_id) && $request->buyer_id>0){
                    $whereCondition3['buyer_id'] = $request->buyer_id;
                }
                foreach($involedOrders as $order){
                    $whereCondition3['id']=$order->order_id;
                    $theOrder = Order::where($whereCondition3)->select('id','style_no','order_no','step_level','status')->first();
                    if(!empty($theOrder)){
                        $theOrders[]=$theOrder;
                    }
                }
                $orders = $theOrders;
            }else{
                $orders = Order::where($whereCondition)
                        ->select('id','style_no','order_no','step_level','status')
                        ->orderBy('id',"DESC")
                        ->get();
            }
        }else{
            $orders = Order::where($whereCondition)
                    ->select('id','style_no','order_no','step_level','status')
                    ->orderBy('id',"DESC")
                    ->get();
        }

        $filterArray=[];
        $filterArray['factory'] = $factory;
        $filterArray['buyer'] = $buyer;
        $filterArray['pcu'] = $pcu;
        $filterArray['style'] = $orders;


        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$filterArray]);
        return CommonApp::apiEncrypt($res);

    }
}
