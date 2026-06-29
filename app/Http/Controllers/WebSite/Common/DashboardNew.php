<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Http\Controllers\Controller;
use App\Models\DashboardSettings;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\OrderProduction;
use App\Models\OrderTask;
use App\Models\Plan;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\UpdateSkuQuantity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Common\CommonApp;
use App\Models\HolidaySetting;
use App\Models\MultipleDeliveryDates;
use App\Models\WeekOff;
use Carbon\Carbon;
use App\Models\Buyer;
use App\Models\Factory;
use App\Models\PCU;
use Illuminate\Support\Facades\Storage;

class DashboardNew extends Controller
{
    /* To Get Task Status for the selected orders */
    public static function getTaskStatus(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['widget_id','=',2]
        ];
        if($request->staff_id == "0"){
            $dashboardArr['user_id'] = $request->user_id;
            $whereConditions[] = ['user_id','=',$request->user_id];
        }
        else if($request->staff_id > "0"){
            $dashboardArr['staff_id'] = $request->staff_id;
            $whereConditions[] = ['staff_id','=',$request->staff_id];
        }
        $taskOrders = DashboardSettings::where($whereConditions)->first();
        $tasksArr=$forTableView=[];
        $orderArr=$styleArr=$totalArr=$delayedArr=$delayCompletedArr=$completedArr=
        $inProgressArr=$delayedStartArr=$yetToStartArr=$notScheduledArr=$orderIdArr=[];
        if(!empty($taskOrders)){
            $orders = explode(",",$taskOrders->order_ids);
            $orders = Order::whereIn('id',$orders)->where('status',"1")->get();
            foreach($orders as $order){
                $dataArr=[];
                $tasks = OrderTask::where('order_id',$order->id)->where('is_subtask',0)->get();
                $counts = DashboardSettings::getTasksCount($tasks);
                $dataArr['orderNo']=$order->order_no;
                $dataArr['styleNo']=$order->style_no;
                $dataArr['total'] = $counts['total'];
                $dataArr['completed'] = $counts['completed'];
                $dataArr['delayedCompleted'] = $counts['delayedCompleted'];
                $dataArr['delay']= $counts['delay'];
                $dataArr['inProgress'] = $counts['inProgress'];
                $dataArr['delayedStart'] = $counts['delayedStart'];
                $dataArr['yetToStart']= $counts['yetToStart'];
                $forTableView[]=$dataArr;
                /* For Chart View */
                $orderArr[]=$order->order_no;
                $styleArr[]=$order->style_no;
                $orderIdArr[]= $order->id;
                $totalArr[] = $counts['total'];
                $completedArr[] = $counts['completed'];
                $delayCompletedArr[] = $counts['delayedCompleted'];
                $delayedArr[]= $counts['delay'];
                $inProgressArr[] = $counts['inProgress'];
                $delayedStartArr[] = $counts['delayedStart'];
                $yetToStartArr[]= $counts['yetToStart'];
                $notScheduledArr[]= $counts['notScheduled'];
            }
        }else{
            $whereCondition1= $whereCondition2 = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $whereCondition3= [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['step_level','=',6],
                ['status','=',"1"]
            ];
            if($request->staff_id > 0){
                $staffRoleHasPermission = Staff::where('id',$request->staff_id)->first();
                $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
                $whereCondition1[]=['permission_id','=','19'];
                $whereCondition1[]=['company_id','=',$request->company_id];
                $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
                if(empty($isPermissionGiven)){
                    $whereCondition2[]=['staff_id','=',$request->staff_id];
                    $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                    $theOrders = [];
                    $i=1;
                    foreach($involedOrders as $order) {
                        $theOrder = Order::where("id", $order->order_id)->where("step_level","6")
                        ->where('status',"1")->first();
                        if(!empty($theOrder)){
                            if($i<=5) {
                                $theOrders[]=$theOrder;
                            }
                            $i++;
                        }
                    }
                    $orders=$theOrders;
                }else{
                    $orders = Order::where($whereCondition3)->orderByRaw('FIELD(order_priority, "Low","Medium","High")DESC')
                    ->limit(5)->get();
                }
            }else{
                $orders = Order::where($whereCondition3)->orderByRaw('FIELD(order_priority, "Low","Medium","High")DESC')
                    ->limit(5)->get();
            }
            foreach($orders as $order){
                $dataArr=[];
                $tasks = OrderTask::where('order_id',$order->id)->where('is_subtask',0)->get();
                $counts = DashboardSettings::getTasksCount($tasks);
                $dataArr['orderNo']=$order->order_no;
                $dataArr['styleNo']=$order->style_no;
                $dataArr['total'] = $counts['total'];
                $dataArr['completed'] = $counts['completed'];
                $dataArr['delayedCompleted'] = $counts['delayedCompleted'];
                $dataArr['delay']= $counts['delay'];
                $dataArr['inProgress'] = $counts['inProgress'];
                $dataArr['delayedStart'] = $counts['delayedStart'];
                $dataArr['yetToStart']= $counts['yetToStart'];
                $forTableView[]=$dataArr;
                /* For Chart View */
                $orderArr[]=$order->order_no;
                $styleArr[]=$order->style_no;
                $orderIdArr[]= $order->id;
                $totalArr[] = $counts['total'];
                $completedArr[] = $counts['completed'];
                $delayCompletedArr[] = $counts['delayedCompleted'];
                $delayedArr[]= $counts['delay'];
                $inProgressArr[] = $counts['inProgress'];
                $delayedStartArr[] = $counts['delayedStart'];
                $yetToStartArr[]= $counts['yetToStart'];
                $notScheduledArr[]= $counts['notScheduled'];
            }
        }
        $tasksArr['orderNo']=$orderArr;
        $tasksArr['styleNo']=$styleArr;
        $tasksArr['total'] = $totalArr;
        $tasksArr['orderId'] = $orderIdArr;
        $tasksArr['completed'] = $completedArr;
        $tasksArr['delayedCompleted'] = $delayCompletedArr;
        $tasksArr['delay']= $delayedArr;
        $tasksArr['inProgress'] = $inProgressArr;
        $tasksArr['delayedStart'] = $delayedStartArr;
        $tasksArr['yetToStart']= $yetToStartArr;
        $tasksArr['notScheduled']= $notScheduledArr;
        $res = json_encode(['status_code' => 200, 'status'=>"success",'data' => [$tasksArr],'tableView' => $forTableView]);
        return CommonApp::webEncrypt($res);
    }

    /* Top 5 Delayed Task */
    public static function getTopDelayTask(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $delayedTasks = DashboardSettings::top5TaskDelay($request);
        // dd($delayedTasks);
        $res = json_encode(['status_code' => 200, 'status'=>"success",'data' => $delayedTasks]);
        return CommonApp::webEncrypt($res);
    }

    /* To Get Production Status for the selected orders */
    public static function getProductionStatus(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $prodArr = DashboardSettings::getProductionStatus($request);
        $res = json_encode(['status_code' => 200, 'status'=>"success",'data' => [$prodArr['prodArr']],
        'forTableView'=>$prodArr['forTableView']]);
        return CommonApp::webEncrypt($res);
    }

    /* Top 5 Production Delay */
    public static function getTopDelayProduction(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $orders = DashboardSettings::top5ProdDelay($request);
        // dd($orders);
        $res = json_encode(['status_code' => 200, 'status'=>"success",'data' => [$orders['prodArr']],
        'forTableView'=>$orders['forTableView']]);
        return CommonApp::webEncrypt($res);
    }

    /* The Order status show-casing the task and production status */
    public static function orderStatus(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
        ];
        $dataArr=[];
        $tasks = DashboardSettings::getTaskDetails($request);
        $prodData = DashboardSettings::getProdDetails($request);
        $orderDetails = Order::getOrderDetailUsingID($request->order_id);
        $holidayDetails = HolidaySetting::getHolidays($request);
        $weekOffs = WeekOff::where($whereConditions )->select(DB::raw('GROUP_CONCAT(days) as days'))->get();
        $status = "";
        if($tasks['taskCount'][0]['delay']>0 || $prodData[0]['cutStatus'] === "Delayed" || $prodData[0]['sewStatus'] === "Delayed"
        || $prodData[0]['packStatus'] === "Delayed"){
            $status = "Delayed";
        }
        else if($tasks['taskCount'][0]['total'] === $tasks['taskCount'][0]['completed']
        || $prodData[0]['cutStatus'] === "Completed" || $prodData[0]['sewStatus'] === "Completed"
        || $prodData[0]['packStatus'] === "Completed"){
            $status = "Completed";
        }
        else if($tasks['taskCount'][0]['total'] === ($tasks['taskCount'][0]['completed'] + $tasks['taskCount'][0]['delayedCompleted'])
        || $prodData[0]['cutStatus'] === "Delayed Completion" || $prodData[0]['sewStatus'] === "Delayed Completion"
        || $prodData[0]['packStatus'] === "Delayed Completion"){
            $status = "Delayed Completion";
        }
        $dataArr['status'] = $status;
        $dataArr['taskCounts'] = $tasks['taskCount'];
        $dataArr['taskChart'] = $tasks['tasksChart'];
        $dataArr['startDate'] = $orderDetails->cutting_start_date;
        $dataArr['prodData'] = $prodData;

        //Delivery Dates
        /*$delivery_date_arr=[];
        $delivery_dates = MultipleDeliveryDates::where('delivery_date','>=',date('Y-m-d'))->where('order_id','=',$request->order_id)->select('delivery_date')->get();
        $last_delivery_date = MultipleDeliveryDates::where('order_id','=',$request->order_id)->orderBy('delivery_date',"DESC")->pluck('delivery_date')->first();
        $delivery_date_arr['delivery_dates'] = $delivery_dates;
        $delivery_date_arr['last_delivery_date'] = $last_delivery_date; */
        $delivery_date = MultipleDeliveryDates::where('order_id','=',$request->order_id)->where('is_delivered','=','0')
                        ->orderBy('delivery_date',"ASC")->pluck('delivery_date')->first();
        $delivery_date_exceed=0;
        if($delivery_date!="" && $delivery_date!=null){
            if($delivery_date < date('Y-m-d')){
                $delivery_date_exceed=1;
            }
        }

        $res = json_encode(["status_code"=>200,"status"=>$status,"taskCount"=>$tasks['taskCount'],"taskChart"=>$tasks['tasksChart'],
        "prodData"=>$prodData,"startDate"=>$dataArr['startDate'],"holidayDetails"=>$holidayDetails,"weekOffs"=>$weekOffs,"delivery_date"=>$delivery_date,
        "delivery_date_exceed"=>$delivery_date_exceed]);
        return CommonApp::webEncrypt($res);
    }

    /* To get plan validity days */
    public static function getPlanValidityDays(Request $request){
        $validated = Validator::make($request->all(), [
            'company_id' => 'required',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $whereCondition = [
            ['id','=',$request->company_id]
        ];

        $planValidity = Plan::getPlanRemainingDays($whereCondition);

        return response()->json(["status_code"=>200,"data"=>$planValidity]);
    }

    /* Dashboard Widgets */
    public static function dashboardWidgets(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id'=>'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition=[
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id],
            ['orders.status','=','1'],
        ];
        $activeOrders = Order::where($whereCondition)->count();
        $onTimeOrders = Order::where($whereCondition)
        ->where('cutting_start_date',"<=",date('Y-m-d'))
        ->where('packing_end_date',">=",date('Y-m-d'))
        ->count();
        $riskyOrders = Order::where($whereCondition)
        ->where('packing_end_date',"<",date('Y-m-d'))
        // ->where('delivery_date',"!=",NULL)
        // ->where('delivery_date',"<",date('Y-m-d'))
        ->count();
        /* Check Task and Production delay */
        $whereCondition1=[
            ['order_task_data.task_schedule_start_date',"!=",null],
            ['order_task_data.task_schedule_end_date',"!=",null],
            ['order_task_data.task_schedule_end_date',"<",date('Y-m-d')],
        ];
        $delayedOrder = Order::where($whereCondition)
        ->leftjoin('order_task_data','order_task_data.order_id','orders.id')
        ->where($whereCondition1)
        ->select('orders.id',DB::raw('COUNT(order_task_data.id) AS delayedTask'))
        ->having('delayedTask','>',0)
        ->groupBy('order_task_data.order_id')
        ->count();

        $res = json_encode(["status_code"=>200,"status"=>"success","activeOrder"=>$activeOrders,"onTimeOrders"=>$onTimeOrders,
        "riskyOrders"=>$riskyOrders,"delayedOrder"=>$delayedOrder]);
        return CommonApp::webEncrypt($res);
    }

    /*============================================Start New Dashboard For staff v2 on 13-12-2023 by saravanan */
       /* To Get staff Production Status for the selected orders limit 2*/
       public static function getStaffProductionStatus(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $prodArr = DashboardSettings::getStaffProductionStatus($request);
         $res = json_encode(['status_code' => 200, 'status'=>"success",'data' => [$prodArr['prodArr']],
        'forTableView'=>$prodArr['forTableView']]);
        return CommonApp::webEncrypt($res);
    }

        /* To Get Task Status for the selected orders limit 2*/
        public static function getStaffTaskStatus(Request $request){
            $request= CommonApp::webDecrypt($request->getContent());
            $validated = Validator::make((array)$request, [
                'company_id' => 'required',
                'workspace_id' => 'required',
            ]);
            if($validated->fails()){
                $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
                return CommonApp::webEncrypt($res);
            }
            $whereConditions=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['widget_id','=',2]
            ];
            if($request->staff_id == "0"){
                $dashboardArr['user_id'] = $request->user_id;
                $whereConditions[] = ['user_id','=',$request->user_id];
            }
            else if($request->staff_id > 0){
                $dashboardArr['staff_id'] = $request->staff_id;
                $whereConditions[] = ['staff_id','=',$request->staff_id];
            }
           // try{
            $taskOrders = DashboardSettings::where($whereConditions)->first();
            $tasksArr=$forTableView=[];
            $orderArr=$styleArr=$totalArr=$delayedArr=$delayCompletedArr=$completedArr=
            $inProgressArr=$delayedStartArr=$yetToStartArr=$notScheduledArr=$orderIdArr=[];
if(isset($request->staff_id) && $request->staff_id>0){
            $staffRoleHasPermission = Staff::where('id',$request->staff_id)->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['company_id','=',$request->company_id];
            $whereCondition1[]=['permission_id','=','19'];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
          $is_staff=0;
            if(empty($isPermissionGiven)){$is_staff=1;}
}
            if(!empty($taskOrders) && $taskOrders->order_ids!=''){
                $orders = explode(",",$taskOrders->order_ids);
                $orders = Order::whereIn('id',$orders)->where('status',"1")->limit(2)->get();
              //  dd($orders);
                $jk=1;
                foreach($orders as $order){
                    if($jk<=2){
                        $jk++;
                    $dataArr=[];
                    $tasks = OrderTask::where('order_id',$order->id)->where('is_subtask',0)->get();
                    $counts = DashboardSettings::getTasksCount($tasks);
                    $dataArr['orderNo']=$order->order_no;
                    $dataArr['styleNo']=$order->style_no;
                    $dataArr['total'] = $counts['total'];
                    $dataArr['completed'] = $counts['completed'];
                    $dataArr['delayedCompleted'] = $counts['delayedCompleted'];
                    $dataArr['delay']= $counts['delay'];
                    $dataArr['inProgress'] = $counts['inProgress'];
                    $dataArr['delayedStart'] = $counts['delayedStart'];
                    $dataArr['yetToStart']= $counts['yetToStart'];
                    $forTableView[]=$dataArr;
                    /* For Chart View */
                    $orderArr[]=$order->order_no;
                    $styleArr[]=$order->style_no;
                    $orderIdArr[]= $order->id;
                    $totalArr[] = $counts['total'];
                    $completedArr[] = $counts['completed'];
                    $delayCompletedArr[] = $counts['delayedCompleted'];
                    $delayedArr[]= $counts['delay'];
                    $inProgressArr[] = $counts['inProgress'];
                    $delayedStartArr[] = $counts['delayedStart'];
                    $yetToStartArr[]= $counts['yetToStart'];
                    $notScheduledArr[]= $counts['notScheduled'];
                    }
                }
            }else{

                $whereCondition1= $whereCondition2 = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id]
                ];
                $whereCondition3= [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['step_level','=',6],
                    ['status','=',"1"]
                ];
                if($request->staff_id > 0){


                    if($is_staff==1){
                        $whereCondition2[]=['staff_id','=',$request->staff_id];
                        //$involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                        $involedOrders = OrderTask::select('order_id')->where('is_subtask',0)->where('task_pic',$request->staff_id)->groupBy("order_id")->get();
                        $theOrders = [];
                        $i=1;
                        foreach($involedOrders as $order) {
                            $theOrder = Order::select("order_no","style_no","id")->where("id", $order->order_id)->where("step_level","6")
                            ->where('status',"1")->first();
                            if(!empty($theOrder)){
                                if($i<=2) {
                                    $theOrders[]=$theOrder;
                                }
                                $i++;
                            }
                        }

                        $orders=$theOrders;

                    }else{
                        $orders = Order::where($whereCondition3)->orderByRaw('FIELD(order_priority, "Low","Medium","High")DESC')
                        ->limit(2)->get();
                    }
                }else{
                    $orders = Order::where($whereCondition3)->orderByRaw('FIELD(order_priority, "Low","Medium","High")DESC')
                        ->limit(2)->get();
                }

                foreach($orders as $order){
                    $dataArr=[];
                    if($is_staff==1){
                        $tasks = OrderTask::where('order_id',$order->id)->where('is_subtask',0)->where('task_pic',$request->staff_id)->get();
                    }else{
                        $tasks = OrderTask::where('order_id',$order->id)->where('is_subtask',0)->get();
                    }
                  if(!empty($tasks)){
                    $counts = DashboardSettings::getTasksCount($tasks);
                    $dataArr['orderNo']=$order->order_no;
                    $dataArr['styleNo']=$order->style_no;
                    $dataArr['total'] = $counts['total'];
                    $dataArr['completed'] = $counts['completed'];
                    $dataArr['delayedCompleted'] = $counts['delayedCompleted'];
                    $dataArr['delay']= $counts['delay'];
                    $dataArr['inProgress'] = $counts['inProgress'];
                    $dataArr['delayedStart'] = $counts['delayedStart'];
                    $dataArr['yetToStart']= $counts['yetToStart'];
                    $forTableView[]=$dataArr;
                    /* For Chart View */
                    $orderArr[]=$order->order_no;
                    $styleArr[]=$order->style_no;
                    $orderIdArr[]= $order->id;
                    $totalArr[] = $counts['total'];
                    $completedArr[] = $counts['completed'];
                    $delayCompletedArr[] = $counts['delayedCompleted'];
                    $delayedArr[]= $counts['delay'];
                    $inProgressArr[] = $counts['inProgress'];
                    $delayedStartArr[] = $counts['delayedStart'];
                    $yetToStartArr[]= $counts['yetToStart'];
                    $notScheduledArr[]= $counts['notScheduled'];
                  }
                }
            }

            $tasksArr['orderNo']=$orderArr;
            $tasksArr['styleNo']=$styleArr;
            $tasksArr['total'] = $totalArr;
            $tasksArr['orderId'] = $orderIdArr;
            $tasksArr['completed'] = $completedArr;
            $tasksArr['delayedCompleted'] = $delayCompletedArr;
            $tasksArr['delay']= $delayedArr;
            $tasksArr['inProgress'] = $inProgressArr;
            $tasksArr['delayedStart'] = $delayedStartArr;
            $tasksArr['yetToStart']= $yetToStartArr;
            $tasksArr['notScheduled']= $notScheduledArr;
            $res = json_encode(['status_code' => 200, 'status'=>"success",'data' => [$tasksArr],'tableView' => $forTableView]);
            return CommonApp::webEncrypt($res);
       // }catch (Exception $e) {
         //   return response()->json(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
       // }
        }
    /* To Get Staff onGoing Order with Workspace List */
    public function onGoingStaffList(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            //'workspaceType' => 'required',
            'staff_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $whereCondition = [
            ['order_contacts.staff_id','=',$request->staff_id]
        ];
        $getStaffIDS=CommonApp::getStaffIDS($request->staff_id);
       // dd($getStaffIDS);
        if(isset($request->staff_id) && $request->staff_id > 0){
            $orders = Order::where('orders.status','1')->where('order_contacts.status','1')->whereIn('order_contacts.staff_id',$getStaffIDS)
                        ->leftjoin('order_contacts','order_contacts.order_id','orders.id')
                        ->leftjoin('workspace','workspace.id','orders.workspace_id')
                        ->select('orders.id','orders.style_no','orders.order_no','workspace.name as workspaceName')
                        ->get();
                        $deliveryD=[];
                        foreach($orders as $orderdev){
                            $getOrderId=$orderdev['id'];
                            $is_date_assign=0;
                            $is_date_assign2=0;
                            $getDeliveryDate='';
                            $getFinDeliveryDate='';
                            $diffInDays=0;
                            $del_status='';
                          $getDelv= MultipleDeliveryDates::select("delivery_date","is_delivered")->where('order_id',$getOrderId)->orderBy("delivery_date","ASC")->get();

                            foreach($getDelv as $delDate){
                                $del_date=$delDate['delivery_date'];
                                $del_status=$delDate['is_delivered'];
                          $endDate = Carbon::parse($del_date);
                          $currentDate = Carbon::now();
                          $diffInDays = $currentDate->diffInDays($endDate, false);

                          if($diffInDays<=0 && $del_status==0 && $is_date_assign==0){
                            $getDeliveryDate=$del_date;
                            $is_date_assign=1;
                            $is_date_assign2=1;

                          }
                          else  if($diffInDays>0 && $del_status==0 && $is_date_assign==0){
                            $getDeliveryDate=$del_date;
                            $is_date_assign=1;
                            $is_date_assign2=1;
                          }
                          else  if($del_status==1){
                            $getFinDeliveryDate=$del_date;

                          }
                          else{

                        }
                            }

                           if($getFinDeliveryDate!='' && $getDeliveryDate==''){
                            $getDeliveryDate=$getFinDeliveryDate;
                           }
                           // dd($getOrderId,$getDeliveryDate);
                            $deliveryD[]=array("dDate"=>$getDeliveryDate,"dateDiFF"=>$diffInDays,"deliveryStatus"=>$del_status,"getOrderId"=>$getOrderId);
                        }

                       // dd($orders);
        }else{
            $res = json_encode(["status_code"=>401,"status"=>'error']);
        }

        $sortedData = collect($deliveryD)->sortBy('deliveryStatus')->sortBy('dDate')->values()->all();
//dd($sortedData );
        $res = json_encode(["status_code"=>200,"status"=>'success',"data"=>$orders,"deliveryDate"=>$sortedData],200);
        return CommonApp::webEncrypt($res);
    }
     /* To Get Staff onGoing Order with Workspace List */
     public function getStafOvelallTaskList(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            //'workspaceType' => 'required',
            'staff_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $tasks=[];
        $is_staff=0;
        $staff_name='';
        $profile_img='';
        $max_count=0;
        $total_quantity=0;
        $factoryId=0;
        $buyerId=0;
        $buyingHouseId=0;
        $delyPercentage=0;
        if($request->staff_id==0){
            $res = OrderTask::where('order_task_data.company_id',$request->company_id)
            ->join('staff','staff.id','order_task_data.task_pic')
            ->join('orders','orders.id','order_task_data.order_id')
            ->select('order_task_data.id','order_task_data.task_pic as pic','staff.first_name','staff.last_name',DB::raw('COUNT(order_task_data.id) as total_count'))
            ->selectRaw('(SELECT COUNT(order_task_data.id) from order_task_data
                join orders on orders.id = order_task_data.order_id
                where order_task_data.company_id="'.$request->company_id.'" and task_pic = pic and order_task_data.is_subtask = 0
                and orders.status = "1" and orders.step_level >= 5
                and task_accomplished_date IS NOT NULL )as complete_count')
            ->selectRaw('(SELECT COUNT(order_task_data.id) from order_task_data
                join orders on orders.id = order_task_data.order_id
                where order_task_data.company_id="'.$request->company_id.'" and task_pic = pic and order_task_data.is_subtask = 0
                and orders.status = "1" and orders.step_level >= 5
                and task_accomplished_date is NULL and task_schedule_end_date < "'.date('Y-m-d').'" )as delayed_count')
            ->selectRaw('(SELECT COUNT(order_task_data.id) from order_task_data
                join orders on orders.id = order_task_data.order_id
                where order_task_data.company_id="'.$request->company_id.'" and task_pic = pic and order_task_data.is_subtask = 0
                and orders.status = "1" and orders.step_level >= 5
                and task_accomplished_date is NULL and task_schedule_end_date >= "'.date('Y-m-d').'" and task_schedule_start_date <= "'.date('Y-m-d').'" )as inprogress_count')
            ->selectRaw('(SELECT COUNT(order_task_data.id) from order_task_data
                join orders on orders.id = order_task_data.order_id
                where order_task_data.company_id="'.$request->company_id.'" and task_pic = pic and order_task_data.is_subtask = 0
                and orders.status = "1" and orders.step_level >= 5
                and task_accomplished_date is NULL and task_schedule_start_date > "'.date('Y-m-d').'" )as yettostart_count')
            ->where('orders.status', "1")
            ->where('orders.step_level','>=', "5")
            ->where('order_task_data.is_subtask', 0)
            ->groupBy('order_task_data.task_pic')
            ->get();
            //dd($res);
            $tasks['count_details'] = $res;
        }

        if($request->staff_id>0){
            $whereCondition = [
                ['order_task_data.company_id','=',$request->company_id],
                ['order_task_data.workspace_id','=',$request->workspace_id],
                //['order_task_data.order_id','=',$request['orderId']],
                ['order_task_data.task_schedule_end_date','!=',NULL],
                ['order_task_data.task_schedule_start_date','!=',NULL],
             // ['order_task_data.task_accomplished_date','=',NULL],
              //['order_task_data.task_schedule_end_date','<',date("Y-m-d")],
              ['orders.status','=','1'],
              ['orders.step_level','>=','5']

            ];
            $staffRoleHasPermission = Staff::select('id','role_id','first_name','last_name','profile_img')->where('id',$request->staff_id)->first();
                    $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
                    $whereCondition1[]=['permission_id','=','19'];
                    $whereCondition1[]=['company_id','=',$request->company_id];
                  $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
                  if(empty($isPermissionGiven)){
                    $is_staff=1;
                    $staff_name=$staffRoleHasPermission->first_name." ".$staffRoleHasPermission->last_name;
                    $profile_img=$staffRoleHasPermission->profile_img;
                    $whereCondition[]=['order_task_data.task_pic','=',$request->staff_id];
                      }else{
                        $staff_name=$staffRoleHasPermission->first_name." ".$staffRoleHasPermission->last_name;
                        $profile_img=$staffRoleHasPermission->profile_img;
                       }




                    if(isset($request->filter_staff_id)){
                        if($request->filter_staff_id>0 && strtolower($request->filter_staff_id)!='all' && $request->filter_staff_id!=null){
                            $whereCondition[]=['order_task_data.task_pic','=',$request->filter_staff_id];
                        }
                        }
                    $currentDate = now()->toDateString();
                    $taskDet = OrderTask::select('orders.id as order_id','orders.order_no','orders.style_no','orders.total_quantity','order_task_data.cat_title','order_task_data.task_title','order_task_data.subtask_title',
                    'order_task_data.actual_start_date','order_task_data.task_schedule_end_date','order_task_data.task_schedule_start_date',
                    'order_task_data.task_accomplished_date','order_task_data.task_pic','staff.id','staff.first_name','staff.last_name','staff.profile_img', 'orders.factory_id','orders.buyer_id','orders.pcu_id as buyinghouse_id',
                      DB::raw('(SUM(CASE WHEN order_task_data.task_accomplished_date IS NULL AND order_task_data.task_schedule_end_date < CURRENT_DATE() THEN 1 ELSE 0 END) / COUNT(orders.id) * 100) as percentage_accomplished')
                        )
                    ->selectRaw('COUNT(orders.id) as max_task_count')
                    ->leftjoin('staff','staff.id','order_task_data.task_pic')
                    ->leftjoin('orders','orders.id','order_task_data.order_id')
                    ->where('order_task_data.is_subtask',0)
                    ->where($whereCondition)
                    ->where('order_task_data.task_pic','!=',0)
                    ->groupBy('staff.id')
                    ->orderBy('percentage_accomplished','desc')
                    ->first();
                       if(!empty($taskDet)){
                        $staff_name=$taskDet['first_name']." ".$taskDet['last_name'];
                        $max_count=$taskDet['max_task_count'];
                        $total_quantity=$taskDet['total_quantity'];

                        // $factoryId=$taskDet['factory_id'];
                        // $buyerId=$taskDet['buyer_id'];
                        // $buyingHouseId=$taskDet['buyinghouse_id'];
                        $delyPercentage=$taskDet['percentage_accomplished'];
                        $profile_img=$taskDet['profile_img'];
                    }

        }
        $Complete_task=$this->getSatffTaskDetailList($request,'complete',$is_staff);
        $delay_task=$this->getSatffTaskDetailList($request,'delay',$is_staff);
        $yettostart_task=$this->getSatffTaskDetailList($request,'yettostart',$is_staff);
        $inprogress_task=$this->getSatffTaskDetailList($request,'inprogress',$is_staff);
        $factoryDET=[];
        $buyerDET=[];
        $buyinghouseDET=[];
        if(isset($request->order_id) && ($request->order_id>0)){
            $delaycomplete=$this->getSatffTaskDetailList($request,'delaycomplete',$is_staff);
            $factoryDET=Factory::select("id",'name')->where("company_id",$request->company_id)->get();
            $buyerDET=Buyer::select("id",'name')->where("company_id",$request->company_id)->get();
            $buyinghouseDET=PCU::select("id",'name')->where("company_id",$request->company_id)->get();
            $taskDete=Order::select("id",'factory_id',"buyer_id","pcu_id as buyinghouse_id")->where("id",$request->order_id)->first();
            $factoryId=$taskDete['factory_id'];
            $buyerId=$taskDete['buyer_id'];
            $buyingHouseId=$taskDete['buyinghouse_id'];
        }else{
            $delaycomplete=array('taskDet'=>[],'taskDetStaff' => []);
        }
        //$taskPercentage=$this->getSatffTaskDetailCount(count($yettostart_task),count($inprogress_task),count($delay_task),count($Complete_task),count($delaycomplete));
        $taskPercentage=$this->getSatffTaskDetailCount(count($yettostart_task['taskDet']),count($inprogress_task['taskDet']),count($delay_task['taskDet']),count($Complete_task['taskDet']),count($delaycomplete['taskDet']));

        $tottaslPer=0;
        if($is_staff==0 && $max_count>0 && $taskPercentage['total']>0){
            $tottaslPer=round(($max_count/$taskPercentage['total'])*100);
            $tottaslPer=number_format($tottaslPer,1,".","");
        }
        $tasks['complete']=$Complete_task['taskDet'];
        $tasks['complete_staff']=$Complete_task['taskDetStaff'];
        $tasks['delay']=$delay_task['taskDet'];
        $tasks['delay_staff']=$delay_task['taskDetStaff'];
        $tasks['yettostart']=$yettostart_task['taskDet'];
        $tasks['yettostart_staff']=$yettostart_task['taskDetStaff'];
        $tasks['inprogress']=$inprogress_task['taskDet'];
        $tasks['inprogress_staff']=$inprogress_task['taskDetStaff'];
        $tasks['delaycomplete']=$delaycomplete['taskDet'];
        $tasks['delaycomplete_staff']=$delaycomplete['taskDetStaff'];
        $tasks['task_percentage']=$taskPercentage;
        $tasks['delay_person']=trim($staff_name);
        //$tasks['delay_percentage']=$is_staff==1?$taskPercentage['delay']:$tottaslPer;
        $tasks['delay_percentage']=$delyPercentage>0?number_format($delyPercentage,1,".",""):0;
        $tasks['factory']=$factoryDET;
        $tasks['buyer']=$buyerDET;
        $tasks['buyer_house']=$buyinghouseDET;
        $tasks['factory_id']= $factoryId;
        $tasks['buyer_id']= $buyerId;
        $tasks['buyinghouse_id']=$buyingHouseId;

        if($profile_img!=null){
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $awsCompanyPath = $companyDetails->aws_s3_path;
    $profile_img = Storage::disk('s3')->temporaryUrl($awsCompanyPath.'/Staff/'.$profile_img, '+20 minutes');
        }
        $tasks['profile_img']=$profile_img;
        if(isset($request->order_id)){
            if($request->order_id>0){
                $tasks['orderChart']=$this->getStaffGranttChart($request);
            }
        }else{
            $tasks['orderChart']='';
        }
        $res = json_encode(["status_code"=>200,"status"=>'success',"data"=>$tasks],200);
        return CommonApp::webEncrypt($res);
    }

    /*Get Staff MyTask Details in Dashboard */
    public function getSatffTaskDetailList($request,$type,$is_staff){
       // dd($request);

        if($type=='complete'){
            if(isset($request->order_id) && ($request->order_id>0)){

            $whereCondition = [
                ['order_task_data.company_id','=',$request->company_id],
                ['order_task_data.workspace_id','=',$request->workspace_id],
                //['order_id','=',$request['orderId']],
               // ['task_schedule_end_datet','>=','task_accomplished_date'],
                ['order_task_data.task_accomplished_date','!=',NULL],
                ['order_task_data.task_schedule_end_date','>=',DB::raw('order_task_data.task_accomplished_date')]
            ];}else{
                $whereCondition = [
                    ['order_task_data.company_id','=',$request->company_id],
                    ['order_task_data.workspace_id','=',$request->workspace_id],
                    //['order_id','=',$request['orderId']],
                   // ['task_schedule_end_datet','>=','task_accomplished_date'],
                    ['order_task_data.task_accomplished_date','!=',NULL],
                  //  ['order_task_data.task_schedule_end_date','>=',DB::raw('order_task_data.task_accomplished_date')]
                ];
            }
        }elseif($type=='delay'){
            $whereCondition = [
                ['order_task_data.company_id','=',$request->company_id],
                ['order_task_data.workspace_id','=',$request->workspace_id],
                //['order_task_data.order_id','=',$request['orderId']],
                ['order_task_data.task_schedule_end_date','!=',NULL],
                ['order_task_data.task_schedule_start_date','!=',NULL],
               ['order_task_data.task_schedule_end_date','<',date("Y-m-d")],
               ['order_task_data.task_accomplished_date','=',NULL],

            ];

        }
        elseif($type=='delaycomplete'){
            $whereCondition = [
                ['order_task_data.company_id','=',$request->company_id],
                ['order_task_data.workspace_id','=',$request->workspace_id],
                //['order_task_data.order_id','=',$request['orderId']],
                ['order_task_data.task_schedule_end_date','!=',NULL],
                ['order_task_data.task_schedule_start_date','!=',NULL],
               ['order_task_data.task_accomplished_date','>',DB::raw('order_task_data.task_schedule_end_date')],
               ['order_task_data.task_accomplished_date','!=',NULL],

            ];

        }
        elseif($type=='yettostart'){
            $whereCondition = [
                ['order_task_data.company_id','=',$request->company_id],
                ['order_task_data.workspace_id','=',$request->workspace_id],
                ['order_task_data.task_schedule_start_date','>',date("Y-m-d")],
               // ['order_task_data.order_id','=',$request['orderId']],
               //['task_schedule_end_date','<','task_accomplished_date'],
               ['task_accomplished_date','=',NULL],
            ];
            //dd($whereCondition);
        }elseif($type=='inprogress'){
            $whereCondition = [
                ['order_task_data.company_id','=',$request->company_id],
                ['order_task_data.workspace_id','=',$request->workspace_id],
                //['order_task_data.order_id','=',$request['orderId']],
                ['order_task_data.task_schedule_start_date','<=',date("Y-m-d")],
               // ['actual_start_date','<=',date("Y-m-d")],
                ['order_task_data.task_schedule_end_date','>=',date("Y-m-d")],
                ['order_task_data.task_accomplished_date','=',NULL],
                ['order_task_data.task_schedule_start_date','!=',NULL],
                ['order_task_data.task_schedule_end_date','!=',NULL],
            ];

        }


                if($is_staff==1){
                    $whereCondition[]=['order_task_data.task_pic','=',$request->staff_id];
                }

        $whereCondition[]=['orders.status','=','1'];
        $whereCondition[]=['orders.step_level','>=','5'];

        if(isset($request->order_id)){
        if($request->order_id>0){
            $whereCondition[]=['order_task_data.order_id','=',$request->order_id];
        }
        }
        $whereConditionStaff[]=$whereCondition;
       // dd($whereCondition,$whereConditionStaff);
        if(isset($request->filter_staff_id)){
            if($request->filter_staff_id>0 && strtolower($request->filter_staff_id)!='all' && $request->filter_staff_id!=null){
                $whereCondition[]=['order_task_data.task_pic','=',$request->filter_staff_id];
            }
            }

        $currentDate = now()->toDateString();
         $taskDet = OrderTask::select('orders.id as order_id','orders.order_no','orders.style_no','order_task_data.cat_title','order_task_data.task_title','order_task_data.subtask_title',
        'order_task_data.actual_start_date','order_task_data.task_schedule_end_date','order_task_data.task_schedule_start_date',
        'order_task_data.task_accomplished_date','order_task_data.task_pic','staff.id','staff.first_name','staff.last_name')
        ->selectRaw('DATEDIFF(order_task_data.task_schedule_end_date, ?) as days_difference', [$currentDate])
        ->leftjoin('staff','staff.id','order_task_data.task_pic')
        ->leftjoin('orders','orders.id','order_task_data.order_id')
        ->where('order_task_data.is_subtask',0)
        ->where($whereCondition)
        ->orderby('task_schedule_start_date','asc')
        ->get();

        $taskDetStaff = OrderTask::select('orders.id as order_id','staff.id','staff.first_name','staff.last_name')
       // ->selectRaw('DATEDIFF(order_task_data.task_schedule_end_date, ?) as days_difference', [$currentDate])
        ->leftjoin('staff','staff.id','order_task_data.task_pic')
        ->leftjoin('orders','orders.id','order_task_data.order_id')
        ->where('order_task_data.is_subtask',0)
        ->where('staff.id','!=',null)
        ->where($whereConditionStaff[0])
        ->orderby('staff.first_name','asc')
        ->groupBy('staff.id')
        ->get();
       // return  $taskDet;
       return array('taskDet'=>$taskDet,'taskDetStaff' => $taskDetStaff);
    }
    /*Get Staff MyTask Percentage in Dashboard */
    public function getSatffTaskDetailCount($yettostart,$inprogrss,$delay,$complete,$delaycomplete){

        $yet_percentage=0;
        $inpro_percentage=0;
        $delay_percentage=0;
        $complete_percentage=0;
        $delcomplete_percentage=0;
$total=(int)$yettostart+$inprogrss+$delay+$complete+$delaycomplete;

$percentage=[];
if($total>0){
$yet_percentage=(($yettostart/$total)*100);
$inpro_percentage=(($inprogrss/$total)*100);
$delay_percentage=(($delay/$total)*100);
$complete_percentage=(($complete/$total)*100);
$delcomplete_percentage=(($delaycomplete/$total)*100);
}
$percentage['yettostart']= $this->textReplaceDecimal($yet_percentage<=9?$yet_percentage:number_format($yet_percentage,1,".",""));
$percentage['inprogress']= $this->textReplaceDecimal($inpro_percentage<=9?$inpro_percentage:number_format($inpro_percentage,1,".",""));
$percentage['delay']= $this->textReplaceDecimal($delay_percentage<=9?$delay_percentage:number_format($delay_percentage,1,".",""));
$percentage['complete']= $this->textReplaceDecimal($complete_percentage<=9?$complete_percentage:number_format($complete_percentage,1,".",""));
$percentage['delaycomplete']= $this->textReplaceDecimal($delcomplete_percentage<=9?$delcomplete_percentage:number_format($delcomplete_percentage,1,".",""));
$percentage['total']= $total;
return $percentage;
    }
    public function textReplaceDecimal($a){
           // return str_replace(".0",'',$a);
          // return $a;
          if($a==0){
            return $a;
          }else{
          return number_format($a,1,".","");
          }
    }


    public function dashboardOrderList(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            //'workspaceType' => 'required',
           // 'staff_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition = [
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id],
            ['orders.status','=','1'],
              ];
        if($request->staff_id>0){


            $staffRoleHasPermission = Staff::select('id','role_id')->where('id',$request->staff_id)->first();
            $whereCondition1[]=['company_id','=',$request->company_id];
                    $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
                    $whereCondition1[]=['permission_id','=','19'];
                    $whereCondition1[]=['company_id','=',$request->company_id];
                    $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
                    if(empty($isPermissionGiven)){
                        $whereCondition[]=['order_task_data.task_pic','=',$request->staff_id];
                    }
    }
//dd($whereCondition);
    $taskDet = OrderTask::select('orders.id','orders.order_no','orders.style_no','orders.step_level','orders.status')
     ->leftjoin('orders','orders.id','order_task_data.order_id')
    ->where('order_task_data.is_subtask',0)
    ->where($whereCondition)
    ->orderby('orders.id','desc')
    ->groupBy('orders.id')
    ->get();
    $res = json_encode(["status_code"=>200,"status"=>'success',"data"=>$taskDet],200);
    return CommonApp::webEncrypt($res);
    }

    /*Get Dashboard Staff Grantt Chart */
    public function getStaffGranttChart($request){

        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
                ];

        $dataArr=[];
        $prodDatachart = [];
        $tasks = $this->getGranttChartTaskDetails($request);
        // if($request->order_id>0){
        // $prodDatachart =  OrderProduction::select("order_id","date_of_production","type_of_production","target_value","actual_value")->where('order_id',$request->order_id)->where('date_of_production','<=',date('Y-m-d'))->get();
        //  }else{
        //     $prodDatachart = [];
        // }
        $prodData= DashboardSettings::getProdDetails($request);
       // $prodDatachart = DashboardSettings::getProductionStatus($request);
       // $prodDatachart = $this->getStaffDashboardProductionStatus($request);
       $prodDatachart = $this->getStaffDashboardProductionStatusData($request);
       $prodDatachartMonth = $this->getProductionMonthView($request);

        $orderDetails = Order::getOrderDetailUsingID($request->order_id);
        $holidayDetails = HolidaySetting::getHolidays($request);
        $weekOffs = WeekOff::where($whereConditions )->select(DB::raw('GROUP_CONCAT(days) as days'))->get();
        $status = "";
        // if($tasks['taskCount'][0]['delay']>0 || $prodData[0]['cutStatus'] === "Delayed" || $prodData[0]['sewStatus'] === "Delayed"
        // || $prodData[0]['packStatus'] === "Delayed"){
        //     $status = "Delayed";
        // }
        // else if($tasks['taskCount'][0]['total'] === $tasks['taskCount'][0]['completed']
        // || $prodData[0]['cutStatus'] === "Completed" || $prodData[0]['sewStatus'] === "Completed"
        // || $prodData[0]['packStatus'] === "Completed"){
        //     $status = "Completed";
        // }
        // else if($tasks['taskCount'][0]['total'] === ($tasks['taskCount'][0]['completed'] + $tasks['taskCount'][0]['delayedCompleted'])
        // || $prodData[0]['cutStatus'] === "Delayed Completion" || $prodData[0]['sewStatus'] === "Delayed Completion"
        // || $prodData[0]['packStatus'] === "Delayed Completion"){
        //     $status = "Delayed Completion";
        // }
        $dataArr['status'] = $status;
       // $dataArr['taskCounts'] = $tasks['taskCount'];
       // $dataArr['taskChart'] = $tasks['tasksChart'];
        $dataArr['startDate'] = $orderDetails->cutting_start_date;
        $dataArr['prodData'] = $prodData;
       // $dataArr['prodDatachart'] = $prodDatachart;

        $delivery_date = MultipleDeliveryDates::where('order_id','=',$request->order_id)->where('is_delivered','=','0')
                        ->orderBy('delivery_date',"ASC")->pluck('delivery_date')->first();
        $delivery_date_exceed=0;
        if($delivery_date!="" && $delivery_date!=null){
            if($delivery_date < date('Y-m-d')){
                $delivery_date_exceed=1;
            }
        }

$resp =array("taskChart"=>$tasks['tasksChart'],
        "prodData"=>$prodData,"prodDataChart"=>$prodDatachart,"ProdDataMonth"=> $prodDatachartMonth,"startDate"=>$dataArr['startDate'],"holidayDetails"=>$holidayDetails,"weekOffs"=>$weekOffs,"delivery_date"=>$delivery_date,
        "delivery_date_exceed"=>$delivery_date_exceed);
        return $resp;
    }
    public function getGranttChartTaskDetails($request){

        $dataArr=[];
        $taskChartConditions=[
            ['order_task_data.order_id','=',$request->order_id],
            ['order_task_data.task_schedule_start_date','!=',NULL],
            ['order_task_data.task_schedule_end_date','!=',NULL],
            ['orders.status','=',"1"],
           // ['is_subtask','=',0]
        ];

        if($request->staff_id>0){


            $staffRoleHasPermission = Staff::select('id','role_id')->where('id',$request->staff_id)->first();

                    $whereCondition1[]=['company_id','=',$request->company_id];
                    $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
                    $whereCondition1[]=['permission_id','=','19'];
                    $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
                    if(empty($isPermissionGiven)){
                        $taskChartConditions[]=['order_task_data.task_pic','=',$request->staff_id];
                    }
    }


        //$tasks = OrderTask::where('order_id',$request->order_id)->where('is_subtask',0)->get();
       // $tasks = OrderTask::select("task_accomplished_date","task_schedule_start_date","task_schedule_end_date")->where($taskChartConditions)->get();

        $tasksChart = OrderTask::where($taskChartConditions)
        ->leftjoin('staff','staff.id','order_task_data.task_pic')
        ->leftjoin('orders','orders.id','order_task_data.order_id')
        ->where('order_task_data.is_subtask',0)
        ->select('order_task_data.cat_title','order_task_data.task_title','order_task_data.subtask_title',
        'order_task_data.actual_start_date','order_task_data.task_schedule_start_date','order_task_data.task_schedule_end_date',
        'order_task_data.task_accomplished_date','order_task_data.task_pic','staff.first_name','staff.last_name')
        ->get();
        // $counts = DashboardSettings::getTasksCount($tasks);
        // $dataArr['total'] = $counts['total'];
        // $dataArr['completed'] = $counts['completed'];
        // $dataArr['delayedCompleted'] = $counts['delayedCompleted'];
        // $dataArr['delay']= $counts['delay'];
        // $dataArr['inProgress'] = $counts['inProgress'];
        // $dataArr['yetToStart']= $counts['yetToStart'];

       // $tasksArr['taskCount'][]=$dataArr;
        $tasksArr['tasksChart']=$tasksChart;

        return $tasksArr;
    }


    public function getStaffDashboardProductionStatus($request){
        $arr=[];
        $cutArr=[];
        $sewArr=[];
        $packArr=[];
        $arydata=[];
        $prodDatav = OrderProduction::where('order_id',$request->order_id)->where('date_of_production','>=',date('Y-m-d', strtotime('-7 days')))->orderBy("id","asc")->get();
       // dd($prodDatav);
        $orderData = Order::where('id',$request->order_id)->where("status","1")->first();
        if(!empty($orderData)){
         //foreach($prodDatav as $prodData){
        $dataInputData = UpdateSkuQuantity::where('order_id',$request->order_id)->where('sku_date','>=',date('Y-m-d', strtotime('-7 days')))->orderBy("id","asc")->get();
        $counts = $this->dashboardprodCounts($prodDatav,$dataInputData,$orderData,'');
       // $counts = $this->getCountDayandMonthProduction($prodDatav,$dataInputData,$orderData,'');
        $arr['orderNo']= $orderData->order_no;
        $arr['styleNo']= $orderData->style_no;
        $arr['total'] = $orderData->total_quantity;
        $arr['cutPercentage']=round($counts['cutPercentage'],2);
        $arr['sewPercentage']=round($counts['sewPercentage'],2);
        $arr['packPercentage']=round($counts['packPercentage'],2);
        $arr['cutPendingPercentage']=$counts['cutPendingPercentage'];
        $arr['sewPendingPercentage']=$counts['sewPendingPercentage'];
        $arr['packPendingPercentage']=$counts['packPendingPercentage'];
        $arr['cutTargets']=$counts['cutTargetPercentage'];
        $arr['sewTargets']=$counts['sewTargetPercentage'];
        $arr['packTargets']=$counts['packTargetPercentage'];
        $forTableView[]=$arr;
        /* For Chart View */
        $orderArr[]= $orderData->order_no;
        $styleArr[]= $orderData->style_no;
        $orderIdArr[]= $orderData->id;
        $cutArr[]= round($counts['cutPercentage']);
        $sewArr[]= round($counts['sewPercentage']);
        $packArr[]= round($counts['packPercentage']);


       // }
        $arydata['cut']= $cutArr;
        $arydata['sew']= $sewArr;
        $arydata['pack']= $packArr;
        $arydata['cutEstDate'] = $counts['cutEstDate'];
        $arydata['sewEstDate'] = $counts['sewEstDate'];
        $arydata['packEstDate'] =$counts['packEstDate'];
        $arydata['cutActualDate'] = $counts['cutActualDate'];
        $arydata['sewActualDate'] =  $counts['sewActualDate'];
        $arydata['packActualDate'] =  $counts['packActualDate'];
        $arydata['productStartDate'] =  $dataInputData[0]['sku_date'];
    }

        return $arydata;
    }

      /* To Calculate the Staff Dashboard Production Data */
      public function dashboardprodCounts($production,$dataInput,$order,$type){
        // dd($production);
        $cutTargetValue = $sewTargetValue = $packTargetValue = 0;
        $cutActualTargetValue = $sewActualTargetValue = $packActualTargetValue = 0;
        $cutIsHoliday = $sewIsHoliday = $packIsHoliday = 2;
        $cutUpdatedValue = $sewUpdatedValue = $packUpdatedValue = 0;
        $cutTodayUpdatedValue = $sewTodayUpdatedValue = $packTodayUpdatedValue = 0;
        $cutCount = $sewCount = $packCount = 1;
        $cutPerDay = $sewPerDay = $packPerDay =0;
        $cutActualDate = $sewActualDate = $packActualDate ="";
        $whereCondition = [
            ['workspace_id','=',$order->workspace_id],
            ['company_id', '=', $order->company_id],
            ['order_id','=',$order->id],
            ['holiday_flag','!=',1],
            ['is_accomplished','!=',1]
        ];

        if(count($production)>0){
             foreach($production as $prodData){

                if($prodData->type_of_production === "Cut"){
                    $cutTargetValue += $prodData->target_value;
                    $cutIsHoliday = $prodData->holiday_flag;
                   // if(date('Y-m-d') === $prodData->date_of_production){
                        $cutActualTargetValue = $prodData->target_value;
                  //  }
                    // if(date('Y-m-d') === $prodData->date_of_production && $prodData->is_accomplished === 0)
                    //     $cutPerDay = $prodData->target_value;
                }
                else if($prodData->type_of_production === "Sew"){
                    $sewTargetValue += $prodData->target_value;
                    $sewIsHoliday = $prodData->holiday_flag;
                   // if(date('Y-m-d') === $prodData->date_of_production){
                        $sewActualTargetValue = $prodData->target_value;
                   // }
                    // if(date('Y-m-d') === $prodData->date_of_production && $prodData->is_accomplished === 0)
                    //     $sewPerDay = $prodData->target_value;
                }
                else if($prodData->type_of_production === "Pack"){
                    $packTargetValue += $prodData->target_value;
                    $packIsHoliday = $prodData->holiday_flag;
                   // if(date('Y-m-d') === $prodData->date_of_production){
                        $packActualTargetValue = $prodData->target_value;
                    //}
                    // if(date('Y-m-d') === $prodData->date_of_production && $prodData->is_accomplished === 0)
                    //     $packPerDay = $prodData->target_value;
                }
            }
        }
        if(count($dataInput)>0){
            foreach($dataInput as $data){
                if($data->type_of_production == "Cut"){
                    $cutUpdatedValue += $data->updated_quantity;
                    if(date('Y-m-d') === $data->sku_date){
                        $cutTodayUpdatedValue += $data->updated_quantity;
                    }
                    // $cutTargetValue += $data->target_value;
                }
                else if($data->type_of_production == "Sew"){
                    $sewUpdatedValue += $data->updated_quantity;
                    if(date('Y-m-d') === $data->sku_date){
                        $sewTodayUpdatedValue += $data->updated_quantity;
                    }
                    // $sewTargetValue += $data->target_value;
                }
                else if($data->type_of_production == "Pack"){
                    $packUpdatedValue += $data->updated_quantity;
                    if(date('Y-m-d') === $data->sku_date){
                        $packTodayUpdatedValue += $data->updated_quantity;
                    }
                    // $packTargetValue += $data->target_value;
                }
            }
        }
        /* To Get the prod Updated Dates */
        $updatedDates=UpdateSkuQuantity::select("type_of_production","sku_date")->where('order_id',$order->id)
        ->groupBy('type_of_production')
        ->groupBy('sku_date')
        ->orderBy('id','asc')
        ->where('sku_date','>=',date('Y-m-d', strtotime('-7 days')))
        ->get();
        $cutDays = $packDays = $sewDays = 0;
        $cutDate= $sewDate=$PackDate=0;
        foreach($updatedDates as $dates){
            if($dates->type_of_production === "Cut"){
                $cutDays+=1;
                $cutDate=$cutDate.",".$dates->sku_date;
            }
            if($dates->type_of_production === "Sew"){
                $sewDays+=1;
                $sewDate=$sewDate.",".$dates->sku_date;
            }
            if($dates->type_of_production === "Pack"){
                $packDays+=1;
                $PackDate=$PackDate.",".$dates->sku_date;
            }
        }
        if($type == "orderStatus"){
            $cutCount = OrderProduction::where($whereCondition)
            ->where('type_of_production',"cut")
            ->where("date_of_production",">=",date('Y-m-d'))->count();
            $sewCount = OrderProduction::where($whereCondition)
            ->where('type_of_production',"sew")
            ->where("date_of_production",">=",date('Y-m-d'))->count();
            $packCount = OrderProduction::where($whereCondition)
            ->where('type_of_production',"pack")
            ->where("date_of_production",">=",date('Y-m-d'))->count();
        }
        $total = $order->total_quantity;
        // dd($total,$cutTargetValue,$sewTargetValue,$packTargetValue,$cutUpdatedValue,$sewUpdatedValue,$packUpdatedValue);
        $cutPercentage = $sewPercentage = $packPercentage =0;
        $cutPercentage = ($cutUpdatedValue / $total)*100;
        $sewPercentage = ($sewUpdatedValue / $total)*100;
        $packPercentage = ($packUpdatedValue / $total)*100;
        $cutTargetPercentage = $sewTargetPercentage = $packTargetPercentage =0;
        $cutTargetPercentage = (($cutUpdatedValue-$cutTargetValue)/$total)*100;
        $sewTargetPercentage = (($sewUpdatedValue-$sewTargetValue)/$total)*100;
        $packTargetPercentage = (($packUpdatedValue-$packTargetValue)/$total)*100;
        $cutPendingPercentage = 100 - $cutPercentage;
        $sewPendingPercentage = 100 - $sewPercentage;
        $packPendingPercentage = 100 - $packPercentage;
        $cutStatus = $sewStatus = $packStatus = "";
        $cutPerDay = round(($total - $cutUpdatedValue)/($cutCount>0 ? $cutCount : 1));
        $sewPerDay = round(($total - $sewUpdatedValue)/($sewCount>0 ? $sewCount : 1));
        $packPerDay = round(($total - $packUpdatedValue)/($packCount>0 ? $packCount : 1));
        if(($total - $cutUpdatedValue)>0 && $order->cutting_accomplished_date == null){
            $cutAvgPerDay = round($cutUpdatedValue/($cutDays>0 ? $cutDays :1));
            $cutEstDate = date('Y-m-d',strtotime("+".round(($total-$cutUpdatedValue)/($cutAvgPerDay > 0 ? $cutAvgPerDay : 1))."days"));
        }else{
            $cutAvgPerDay = 0;
            $cutEstDate = "";
            $cutActualDate = $order->cutting_accomplished_date;
        }
        if(($total - $sewUpdatedValue)>0 && $order->sewing_accomplished_date == null){
            $sewAvgPerDay = round($sewUpdatedValue/($sewDays>0 ? $sewDays :1));
            $sewEstDate = date('Y-m-d',strtotime("+".round(($total-$sewUpdatedValue)/($sewAvgPerDay > 0 ? $sewAvgPerDay : 1))."days"));
        }else{
            $sewAvgPerDay = 0;
            $sewEstDate = "";
            $sewActualDate = $order->sewing_accomplished_date;
        }
        if(($total - $packUpdatedValue)>0 && $order->packing_accomplished_date == null){
            $packAvgPerDay = round($packUpdatedValue/($packDays>0 ? $packDays :1));
            $packEstDate = date('Y-m-d',strtotime("+".round(($total-$packUpdatedValue)/($packAvgPerDay > 0 ? $packAvgPerDay : 1))."days"));
        }else{
            $packAvgPerDay = 0;
            $packEstDate = "";
            $packActualDate = $order->packing_accomplished_date;
        }
        /* Cut Status */
        if($cutPercentage === 100){
            if($order->cutting_end_date >= $order->cutting_accomplished_date )
                $cutStatus = "Completed";
            else if ($order->cutting_end_date < $order->cutting_accomplished_date )
                $cutStatus = "Delayed Completion";
        }
        else if($cutPercentage != 100 && date('Y-m-d') > $order->cutting_end_date)
            $cutStatus = "Delayed";
        else if($cutPercentage != 100 && date('Y-m-d') <= $order->cutting_end_date && date('Y-m-d') >= $order->cutting_start_date)
            $cutStatus = "In Progress";
        else if($cutPercentage != 100 && date('Y-m-d') < $order->cutting_start_date)
            $cutStatus = "Yet To Start";
        /* Sew */
        if($sewPercentage === 100){
            if($order->sewing_end_date >= $order->sewing_accomplished_date )
                $sewStatus = "Completed";
            else if ($order->sewing_end_date < $order->sewing_accomplished_date )
                $sewStatus = "Delayed Completion";
        }
        else if($sewPercentage != 100 && date('Y-m-d')>$order->sewing_end_date)
            $sewStatus = "Delayed";
        else if($sewPercentage != 100 && date('Y-m-d') <= $order->sewing_end_date && date('Y-m-d') >= $order->sewing_start_date)
            $sewStatus = "In Progress";
        else if($sewPercentage != 100 && date('Y-m-d') < $order->sewing_start_date)
            $sewStatus = "Yet To Start";
        /* Pack */
        if($packPercentage === 100){
            if($order->packing_end_date >= $order->packing_accomplished_date )
                $packStatus = "Completed";
            else if ($order->packing_end_date < $order->packing_accomplished_date )
                $packStatus = "Delayed Completion";
        }
        else if($packPercentage != 100 && date('Y-m-d')>$order->packing_end_date)
            $packStatus = "Delayed";
        else if($packPercentage != 100 && date('Y-m-d') <= $order->packing_end_date && date('Y-m-d') >= $order->packing_start_date)
            $packStatus = "In Progress";
        else if($packPercentage != 100 && date('Y-m-d') < $order->packing_start_date)
            $packStatus = "Yet To Start";
        $arr=[];
        $arr['cutPercentage'] = $cutPercentage;
        $arr['sewPercentage'] = $sewPercentage;
        $arr['packPercentage'] = $packPercentage;
        $arr['cutPendingPercentage']= $cutPendingPercentage;
        $arr['sewPendingPercentage'] = $sewPendingPercentage;
        $arr['packPendingPercentage'] = $packPendingPercentage;
        $arr['cutTargetPercentage'] = $cutTargetPercentage;
        $arr['sewTargetPercentage'] = $sewTargetPercentage;
        $arr['packTargetPercentage'] = $packTargetPercentage;
        $arr['cutPerDay'] = $cutPerDay;
        $arr['sewPerDay'] = $sewPerDay;
        $arr['packPerDay'] = $packPerDay;
        $arr['cutActualTargetValue'] = $cutActualTargetValue;
        $arr['sewActualTargetValue'] = $sewActualTargetValue;
        $arr['packActualTargetValue'] = $packActualTargetValue;
        $arr['cutCompleted'] = $cutUpdatedValue;
        $arr['sewCompleted'] = $sewUpdatedValue;
        $arr['packCompleted'] = $packUpdatedValue;
        $arr['cutTodayUpdatedValue'] = $cutTodayUpdatedValue;
        $arr['sewTodayUpdatedValue'] = $sewTodayUpdatedValue;
        $arr['packTodayUpdatedValue'] = $packTodayUpdatedValue;
        $arr['cutStatus'] = $cutStatus;
        $arr['sewStatus'] = $sewStatus;
        $arr['packStatus'] = $packStatus;
        $arr['cutHoliday'] = $cutIsHoliday;
        $arr['sewHoliday'] = $sewIsHoliday;
        $arr['packHoliday'] = $packIsHoliday;
        $arr['cutAvgPerDay'] = $cutAvgPerDay;
        $arr['sewAvgPerDay'] = $sewAvgPerDay;
        $arr['packAvgPerDay'] = $packAvgPerDay;
        $arr['cutEstDate'] = $cutEstDate;
        $arr['sewEstDate'] = $sewEstDate;
        $arr['packEstDate'] = $packEstDate;
        $arr['cutActualDate'] = $cutActualDate;
        $arr['sewActualDate'] = $sewActualDate;
        $arr['packActualDate'] = $packActualDate;
        // dd($cutPercentage,$sewPercentage,$packPercentage);
        return $arr;
    }

    public function getStaffDashboardProductionStatusData($request){
        $ord=Order::select('total_quantity')->where("id",$request->order_id)->first();
        $total_quantity=$ord->total_quantity;
       // $dataInputData = UpdateSkuQuantity::select("sku_date")->where('order_id',$request->order_id)->groupBy("sku_date")->orderBy("id","asc")->take(1)->pluck("sku_date")->toArray();
       // $dataInputData = UpdateSkuQuantity::select("sku_date")->where('order_id',$request->order_id)->groupBy("sku_date")->orderBy("id","desc")->first();
        $dataInputData = UpdateSkuQuantity::select("sku_date")->where('order_id',$request->order_id)->groupBy("sku_date")->orderBy("sku_date","desc")->first();
        $dataDate = UpdateSkuQuantity::select(DB::raw("MAX(sku_date) as Max_date,MIN(sku_date) as Min_date"))->where('order_id',$request->order_id)->first();
        $start_day_no=0;
        $end_day_no=7;
        $weekNo=0;
        $cDays=1;

if(!empty($dataDate)){
        $maxDateInDays = Carbon::parse($dataDate['Min_date'])->diffInDays($dataDate['Max_date']);
      // dd($maxDateInDays);
      if($maxDateInDays>7){
        $cDays=round($maxDateInDays/7);
        if(isset($request->week_no) && $request->week_no>1){
            $weekNo=$request->week_no;

            $end_day_no=$weekNo*7;
            $start_day_no=$end_day_no-7;
        }
        //$start_day_no=7;
       // $end_day_no=14;
      }

}
   if(!empty($dataInputData)){
        $pastDate = Carbon::parse($dataInputData->sku_date); // Replace '2023-01-01' with your desired past date
        $ecurrentDate = $pastDate->copy()->subDays(7)->format('Y-m-d');
      $startDate=$dataInputData->sku_date;
      $endDate=$ecurrentDate;
$cutArray=[];
$sewArray=[];
$packArray=[];
$dateArray=[];

      for ($i = $start_day_no; $i < $end_day_no; $i++) {
        $getDate = $pastDate->copy()->subDays($i)->format('Y-m-d');
              $dateArray[]=$getDate;
        // $dateQry=UpdateSkuQuantity::select("order_id","sku_date","type_of_production","updated_quantity","target_value")->where('order_id',$request->order_id)->where('sku_date', '=',$getDate)
        // ->get();
        $dateQry=UpdateSkuQuantity::select("order_id","sku_date","type_of_production","target_value",DB::raw('SUM(updated_quantity) as total_qty'))
        ->groupBy('type_of_production')
        ->where('order_id',$request->order_id)->where('sku_date', '<=',$getDate)
        ->get();
       // dd($getDate, $dateQry);
        if(!empty($dateQry)){
            $cutChk=0;
            $sewChk=0;
            $packChk=0;






        foreach($dateQry as $prodDat){
           $totv=($prodDat['total_qty']/$total_quantity)*100;
           $totv= number_format($totv,1,".","");
            if(strtolower($prodDat['type_of_production'])=='cut'){
                $cutArray[]=$totv;

                $cutChk=1;
            }else if(strtolower($prodDat['type_of_production'])=='sew'){
                $sewArray[]=$totv;
                $sewChk=1;
        }else if(strtolower($prodDat['type_of_production'])=='pack'){
            $packArray[]=$totv;
            $packChk=1;
        }
    }

    if($cutChk==0){
        $cutArray[]=0;
    }
    if($sewChk==0){
        $sewArray[]=0;
    }
    if($packChk==0){
        $packArray[]=0;
    }

}else{
    $cutArray[]=0;
$sewArray[]=0;
$packArray[]=0;
}



    }
    $cutArray[]='';
$sewArray[]='';
$packArray[]='';
$dateArray[]='';
//dd($maxDateInDays/7);
    return array("cut"=>array_reverse($cutArray),"sew"=>array_reverse($sewArray),"pack"=>array_reverse($packArray),"dates"=>array_reverse($dateArray),"totalDays"=>$cDays);
}else{
    $cutArray=["",0,0,0,0,0,0,0];
    $sewArray=["",0,0,0,0,0,0,0];
    $packArray=["",0,0,0,0,0,0,0];
    $pastDate = Carbon::now();
    $dateArray=[];
    for ($i = 0; $i < 7; $i++) {
        $getDate = $pastDate->copy()->subDays($i)->format('Y-m-d');
        $dateArray[]=$getDate;
    }
    $dateArray[]='';

    return array("cut"=>$cutArray,"sew"=>($sewArray),"pack"=>($packArray),"dates"=>array_reverse($dateArray));
}
    }
    public function getProductionMonthView($request){

        $ord=Order::select('total_quantity')->where("id",$request->order_id)->first();
        $total_quantity=$ord->total_quantity;
       // dd($total_quantity);
        $dataInputData = UpdateSkuQuantity::select("sku_date")->where('order_id',$request->order_id)->groupBy("sku_date")->orderBy("sku_date","desc")->first();
        // dd($dataInputData);
     if(!empty($dataInputData)){
          $pastDate = Carbon::parse($dataInputData->sku_date)->firstOfMonth(); // Replace '2023-01-01' with your desired past date
          $ecurrentDate = $pastDate->copy()->subMonths(12)->format('M');

        $startDate=$dataInputData->sku_date;
        $endDate=$ecurrentDate;
  $cutArray=[];
  $sewArray=[];
  $packArray=[];
  $dateArray=[];
  $cutProdQty=0;
  $sewProdQty=0;
  $packProdQty=0;
  $cutArray[]='';
  $sewArray[]='';
  $packArray[]='';
  $dateArray[]='';
  $totcv=0;
  $totsv=0;
  $totpv=0;
        for ($i = 11; $i >=0; $i--) {
          $getMonth = $pastDate->copy()->subMonths($i)->format('m');
          $getMonthF = $pastDate->copy()->subMonths($i)->format('M');
          $getYear = $pastDate->copy()->subMonths($i)->format('Y');
         // dd($getMonthF." ".$getYear);
                $dateArray[]=$getMonthF." ".$getYear;
       /*   $dateQry=UpdateSkuQuantity::select("order_id","sku_date","type_of_production","updated_quantity","target_value")->where('order_id',$request->order_id)
          ->where(DB::raw('MONTH(sku_date)'), '=',$getMonth)
          ->where(DB::raw('YEAR(sku_date)'), '=',$getYear)
          ->get(); */
          $dateQry= UpdateSkuQuantity::select("order_id","sku_date","type_of_production",DB::raw('MONTH(sku_date) as month'), DB::raw('SUM(updated_quantity) as total_qty'),DB::raw('SUM(target_value) as target_qty'))->where('order_id',$request->order_id)
          ->groupBy('type_of_production')
          ->where(DB::raw('MONTH(sku_date)'), '=',$getMonth)
          ->where(DB::raw('YEAR(sku_date)'), '=',$getYear)

          ->groupBy('type_of_production')
          ->get();
          if(!empty($dateQry)){
            //dd($dateQry);
              $cutChk=0;
              $sewChk=0;
              $packChk=0;
          foreach($dateQry as $prodDat){
            // if($prodDat['target_qty']>0){

            // $totv=($prodDat['total_qty']/$total_quantity)*100;
            // if($totv>100){
            //     $totv=100;
            // }
            // }

              if(strtolower($prodDat['type_of_production'])=='cut'){
                $cutProdQty+=$prodDat['total_qty'];
                $totcv=($cutProdQty/$total_quantity)*100;
                $totcv= number_format($totcv,1,".","");
                  $cutArray[]= $totcv;
                  $cutChk=1;

              }else if(strtolower($prodDat['type_of_production'])=='sew'){

                $sewProdQty+=$prodDat['total_qty'];
                $totsv=($sewProdQty/$total_quantity)*100;
                $totsv= number_format($totsv,1,".","");
                  $sewArray[]= $totsv;
                  $sewChk=1;

          }else if(strtolower($prodDat['type_of_production'])=='pack'){
            $packProdQty+=$prodDat['total_qty'];
            $totpv=($packProdQty/$total_quantity)*100;
            $totpv= number_format($totpv,1,".","");
              $packArray[]= $totpv;
              $packChk=1;
          }
      }

      if($cutChk==0){

          $cutArray[]=$totcv>0?$totcv:0;
      }
      if($sewChk==0){
          $sewArray[]=$totsv>0?$totsv:0;
      }
      if($packChk==0){
          $packArray[]=$totpv>0?$totpv:0;
      }

  }else{
      $cutArray[]=$cutProdQty;
  $sewArray[]=$sewProdQty;
  $packArray[]=$packProdQty;
  }



      }


      return array("cut"=>($cutArray),"sew"=>($sewArray),"pack"=>($packArray),"dates"=>($dateArray));
  }else{
      $cutArray=["",0,0,0,0,0,0,0,0,0,0,0,0];
      $sewArray=["",0,0,0,0,0,0,0,0,0,0,0,0];
      $packArray=["",0,0,0,0,0,0,0,0,0,0,0,0];
      $pastDate = Carbon::now()->firstOfMonth();
      $dateArray=[];
      for ($i = 0; $i < 12; $i++) {
       // $getMonth = $pastDate->copy()->subMonths($i)->format('m');
        $getMonthF = $pastDate->copy()->subMonths($i)->format('M');
        $getYear = $pastDate->copy()->subMonths($i)->format('Y');
        //dd($getDate);
              $dateArray[]=$getMonthF." ".$getYear;
      }
    //   for ($i = 0; $i < 7; $i++) {
    //       $getDate = $pastDate->copy()->subDays($i)->format('Y-m-d');
    //       $dateArray[]=$getDate;
    //   }
      $dateArray[]='';
      return array("cut"=>$cutArray,"sew"=>($sewArray),"pack"=>($packArray),"dates"=>array_reverse($dateArray));
  }
    }

    public function getProductionWeekByWeekView(Request $request) {
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id'=>'required',
            'order_id'=>'required',
            'week_no'=>'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition=[
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id],
            ['orders.status','=','1'],
        ];
        $orders=$this->getStaffDashboardProductionStatusData($request);
        $res = json_encode(["status_code"=>200,"status"=>'success',"data"=>$orders],200);
        return CommonApp::webEncrypt($res);
    }

      /*============================================End New Dashboard For staff v2 on 13-12-2023 by saravanan */

    public function dashboardRecentOrderDetails(Request $request) {
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id'=>'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition=[
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id],
            ['orders.status','=','1'],
        ];

        $orders = DashboardSettings::getRecentOrderDetails($whereCondition);
        $res = json_encode(["status_code"=>200,"status"=>'success',"data"=>$orders],200);
        return CommonApp::webEncrypt($res);
    }
    public function staffInviteDetails(Request $request) {
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id'=>'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition=[
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id],
            ['orders.status','=','1'],
        ];

        $orders = DashboardSettings::getStaffInviteDetails($whereCondition);
        $res = json_encode(["status_code"=>200,"status"=>'success',"data"=>$orders],200);
        return CommonApp::webEncrypt($res);
    }
}
