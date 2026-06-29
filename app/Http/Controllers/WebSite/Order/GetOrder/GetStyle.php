<?php

namespace App\Http\Controllers\WebSite\Order\GetOrder;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Factory;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\OrderTask;
use App\Models\PCU;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Common\CommonApp;
use App\Models\CompanySettings;
use App\Models\OrderTemplate;
use Illuminate\Support\Facades\DB;

class GetStyle extends Controller

{
    /****** Get Styles for the Filter  *********/
    public static function getStyles(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id'=>'required',
            'workspace_id'=>'required'
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $pagetype=isset($request->page_type)?$request->page_type:"";
        if($pagetype == 'orderstatus'){
            $whereCondition =[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],

            ];
        }
        else if($pagetype == 'dashboard'){
            $whereCondition =[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['status','=',"1"],
                ['step_level','=',"6"]
            ];
        }
        else{
            $whereCondition =[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['order_task_template','!=','0'],
                ['status','=','1'],
                ['step_level','=',"6"]
            ];
        }
        if (isset($request->factory_id) && $request->factory_id!=""){
            $whereCondition['factory_id'] = $request->factory_id;
        }
        if (isset($request->pcu_id) && $request->pcu_id!="" && $request->pcu_id!="0"){
            $whereCondition['pcu_id'] = $request->pcu_id;
        }
        if ( isset($request->buyer_id) && $request->buyer_id!=""){
            $whereCondition['buyer_id'] = $request->buyer_id;
        }
        if (isset($request->type) && ($request->type === "DataInput" || $request->type === "ReportDataInput")){
            $whereCondition['step_level'] = 6;
        }
        if(isset($request->staff_id) && $request->staff_id > 0){
            $whereCondition1= $whereCondition2 =[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $staffRoleHasPermission = Staff::select('role_id','company_id')->where('id',$request->staff_id)->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['permission_id','=','19'];
            $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];

                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->orderBy("id","DESC")->get();
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
                    if (isset($request->type) && ($request->type === "DataInput" || $request->type === "ReportDataInput")){
                        $whereCondition3['step_level'] = 6;
                            }
                            if($pagetype == 'dashboard'){
                                $whereCondition3['step_level']="6";
                                $whereCondition3['status']="1";
                            }else  if($pagetype == 'taskupdate'){
                                $whereCondition3[]=['status','=',"1"];
                                $whereCondition3[]=['step_level','>',"3"];
                            }
                    $theOrder = Order::where($whereCondition3)->select('id','style_no','order_no','step_level','status')->first();
                    if(!empty($theOrder)){
                        $theOrders[]=$theOrder;
                    }
                }
                $orders = $theOrders;

            }else{

                  $orders = Order::where($whereCondition)
                        ->select('id','style_no','order_no','step_level','status')
                        ->orderByRaw("FIELD(status, '1','12','11','10','3')  ASC")
                        ->orderBy("id","DESC")
                        ->get();
            }
        }else{

            $orders = Order::where($whereCondition)
                    ->select('id','style_no','order_no','step_level','status')
                    ->orderByRaw("FIELD(status, '1','12','11','10','3')  ASC")
                    ->orderBy("id","DESC")
                    ->get();
        }

        /** Video upload based on plan details */
        $video_upload = 0;
        if(config('constant.order_comments_enable') == 1){
            $plandet = CompanySettings::where('company_settings.id','=',$request->company_id)
                            ->join('plan_price_details','company_settings.purchased_plan_id','plan_price_details.id')
                            ->select('plan_price_details.video_upload')->first();
            $video_upload = $plandet->video_upload;
        }

        $res = json_encode(['status_code' => 200,'status'=>"success" ,'data' => $orders,'inprogress_per_show'=>config('constant.task_inprogress_percentage'),'order_comments_enable'=>config('constant.order_comments_enable'),'video_upload'=>$video_upload]);
        return CommonApp::webEncrypt($res);
    }

    /********************* Get the particular style **********************/
    public static function getTheStyle(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id'=>'required',
            'workspace_id'=>'required',
            'orderId'=>'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $order = Order::where('id',$request->orderId)->first();
        $whereCondition = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->orderId],
            ['template_id','=',$order->order_task_template]
        ];
        // $factory = Factory::where('id',$order->factory_id)->select('name')->first();
        // $pcu = PCU::where('id',$order->pcu_id)->select('name')->first();
        // $buyer = Buyer::where('id',$order->buyer_id)->select('name')->first();
        $templateData = OrderTask::where($whereCondition)->get();
        $taskDataArr = array();
        $totalTask = count($templateData);
        $scheduledTasks = count(OrderTask::where($whereCondition)->where('task_schedule_start_date','!=',NULL)
        ->where('task_schedule_end_date','!=',NULL)->get());
        $accomplishedTasks = count(OrderTask::where($whereCondition)->where('task_accomplished_date','!=',NULL)->get());
        // $yetToStart = count(OrderTask::where($whereCondition)->where('task_schedule_start_date','=',NULL)
        // ->where('task_schedule_end_date','=',NULL)->where('task_accomplished_date','=',NULL)->get());
        $yetToStart =count(OrderTask::where($whereCondition)->where('task_schedule_start_date','>',date('Y-m-d'))->get());
        $taskDataArr['totalTask'] = $totalTask;
        $taskDataArr['scheduledTasks'] = $scheduledTasks;
        $taskDataArr['accomplishedTasks'] = $accomplishedTasks;
        $taskDataArr['yetToStart']=$yetToStart;
        $getOrderTemp=OrderTemplate::where("id",$order->order_task_template)->get();
       // $taskDataArr['orderTemplate']=$getOrderTemp;
        // if($order->factory_id != 0){
        //     $arr['factoryName'] = $factory->name;
        // }
        // if($order->pcu_id != 0){
        //     $arr['pcuName'] = $pcu->name;
        // }
        // if($order->buyer_id != 0){
        //     $arr['buyerName'] = $buyer->name;
        // }
        $res = json_encode(["status_code"=>200,"status" =>"Success","taskInfoData"=>$taskDataArr,"data"=>$order,"orderTemplate"=>$getOrderTemp]);
        return CommonApp::webEncrypt($res);
    }
}
