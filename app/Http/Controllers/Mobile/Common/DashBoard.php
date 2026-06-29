<?php

namespace App\Http\Controllers\Mobile\Common;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Factory;
use App\Models\NotificationDashboard;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\PCU;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderTask;
use App\Common\CommonApp;

class DashBoard extends Controller
{
    /* Data for the dashboard */
    public static function dashboardWidgets(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereCondition = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id]
        ];
        $staffCount = Staff::where(['company_id'=>$request->company_id,'status'=>'1'])->count();
        $lastupdatedStaff = Staff::where(['company_id'=>$request->company_id,'status'=>'1'])
                            ->orderBy('created_at', 'DESC')->value('created_at');
        $orderCount = Order::where($whereCondition)->count();
        $lastupdatedOrder = Order::where('company_id',$request->company_id)
                            ->latest('updated_at')->value('updated_at');
        $factoryCount = Factory::where($whereCondition)->count();
        $lastupdatedFactory = Factory::where('company_id',$request->company_id)
                            ->latest('updated_at')->value('updated_at');
        $pcuCount = PCU::where($whereCondition)->count();
        $lastupdatedPCU = PCU::where('company_id',$request->company_id)
                            ->latest('updated_at')->value('updated_at');
        $buyerCount = Buyer::where($whereCondition)->count();
        $lastUpdatedBuyer = Buyer::where('company_id',$request->company_id)
                            ->latest('updated_at')->value('updated_at');
        $dataArr = ["Staff"=>$staffCount,
                    "updateStaffDate"=>($lastupdatedStaff!=null)?date('d M Y', strtotime($lastupdatedStaff)):'-',
                    "Order"=>$orderCount,
                    "updateOrderDate"=>($lastupdatedOrder!=null)?date('d M Y', strtotime($lastupdatedOrder)):'-',
                    "Factory"=>$factoryCount,
                    "updateFactoryDate"=>($lastupdatedFactory)?date('d M Y', strtotime($lastupdatedFactory)):'-',
                    "PCU"=>$pcuCount,
                    "updatePCUDate"=>($lastupdatedPCU)?date('d M Y', strtotime($lastupdatedPCU)):'-',
                    "Buyer"=>$buyerCount,
                    "updateBuyerDate"=>($lastUpdatedBuyer)?date('d M Y', strtotime($lastUpdatedBuyer)):'-'
                ];
        $res = json_encode(["status_code"=>200,"status" =>"Success", "data"=>$dataArr ]);
        return CommonApp::apiEncrypt($res);
    }

    /* To Get onGoing List */
    public static function onGoingList(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'workspaceType' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereCondition1 = $whereCondition2= [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id]
        ];
        $whereCondition = [
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id]
        ];

        if(isset($request->staff_id) && $request->staff_id>0){
            $staffRoleHasPermission = Staff::where('id',$request->staff_id)->select('role_id','company_id')->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['permission_id','=','19'];
            $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            /* If the Staff has permission to view all orders */
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $ordersArr=[];
                foreach($involedOrders as $order){
                    $whereCondition[]=['orders.id','=',$order->order_id];
                    $ordersArr[] = DashBoard::getOnGoingOrdersFunction($whereCondition,$request,true);
                    array_pop($whereCondition);
                }
                $orders = $ordersArr;
            }
            else{
                $orders = DashBoard::getOnGoingOrdersFunction($whereCondition,$request,false);
            }
        }else{
            $orders = DashBoard::getOnGoingOrdersFunction($whereCondition,$request,false);
        }

        $res = json_encode(["status_code"=>200,"status"=>'success',"data"=>$orders],200);
        return CommonApp::apiEncrypt($res);
    }

    /* For Notificatios in dashboard */
    public static function notifications(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereCondition = [
            ['dashboard_notification.company_id','=',$request->company_id],
            ['dashboard_notification.workspace_id','=',$request->workspace_id]
        ];
        $whereCondition1= $whereCondition2 = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id]
        ];
        if(isset($request->staff_id) && $request->staff_id>0){
            $staffRoleHasPermission = Staff::where('id',$request->staff_id)->select('role_id','company_id')->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
            $whereCondition1[]=['permission_id','=','19'];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            /* If the Staff has permission to view all orders */
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $notificationsArr=[];
                foreach($involedOrders as $order){
                    $whereCondition[]=['order_id','=',$order->order_id];
                    $notification = NotificationDashboard::where($whereCondition)
                                            ->select('notification_title as title','notification_description as description',
                                                    'notification_type as type','notification_url as URL',
                                                    "dashboard_notification.created_at as date",'notification_details')
                                            ->limit(20)
                                            ->orderBy('created_at','DESC')
                                            ->get();
                    array_pop($whereCondition);
                    foreach ($notification as $noti){
                        $notificationsArr[] = $noti;
                    }
                }
                $notifications = $notificationsArr;
            }else{
                $notifications = NotificationDashboard::where($whereCondition)
                                    ->select('notification_title as title','notification_description as description',
                                            'notification_type as type','notification_url as URL',
                                            "dashboard_notification.created_at as date",'notification_details')
                                    ->limit(20)
                                    ->orderBy('created_at','DESC')
                                    ->get();
            }
        }else{
            $notifications = NotificationDashboard::where($whereCondition)
                                ->select('notification_title as title','notification_description as description',
                                        'notification_type as type','notification_url as URL',
                                        "dashboard_notification.created_at as date",'notification_details')
                                ->limit(20)
                                ->orderBy('created_at','DESC')
                                ->get();
        }
        $res = json_encode(["status_code"=>200,"status"=>"Success","data"=>$notifications]);
        return CommonApp::apiEncrypt($res);
    }

    /* To Get onGoing Functions */
    public static function getOnGoingOrdersFunction($whereCondition,$request,$condition){
        if($condition){
            if($request->workspaceType === "Buyer"){
                $orders = Order::where($whereCondition)->where('status','1')
                        ->leftjoin('order_factory','order_factory.id','orders.factory_id')
                        ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
                        ->select('orders.id','orders.order_no','orders.style_no','order_factory.name as factoryName','order_pcu.name as pcuName')
                        ->first();
            }
            else if($request->workspaceType === "Factory"){
                $orders = Order::where($whereCondition)->where('status','1')
                            ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
                            ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
                            ->select('orders.id','orders.order_no','orders.style_no','order_pcu.name as pcuName','order_buyer.name as buyerName')
                            ->first();
            }
            else if($request->workspaceType === "PCU"){
                $orders = Order::where($whereCondition)->where('status','1')
                            ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
                            ->leftjoin('order_factory','order_factory.id','orders.factory_id')
                            ->select('orders.id','orders.order_no','orders.style_no','order_buyer.name as buyerName','order_factory.name as factoryName')
                            ->first();
            }
        }else{
            if($request->workspaceType === "Buyer"){
                $orders = Order::where($whereCondition)->where('status','1')
                        ->leftjoin('order_factory','order_factory.id','orders.factory_id')
                        ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
                        ->select('orders.id','orders.order_no','orders.style_no','order_factory.name as factoryName','order_pcu.name as pcuName')
                        ->orderBy('orders.id','DESC')
                        ->get();
            }
            else if($request->workspaceType === "Factory"){
                $orders = Order::where($whereCondition)->where('status','1')
                            ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
                            ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
                            ->select('orders.id','orders.order_no','orders.style_no','order_pcu.name as pcuName','order_buyer.name as buyerName')
                            ->orderBy('orders.id','DESC')
                            ->get();
            }
            else if($request->workspaceType === "PCU"){
                $orders = Order::where($whereCondition)->where('status','1')
                            ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
                            ->leftjoin('order_factory','order_factory.id','orders.factory_id')
                            ->select('orders.id','orders.order_no','orders.style_no','order_buyer.name as buyerName','order_factory.name as factoryName')
                            ->orderBy('orders.id','DESC')
                            ->get();
            }
        }
        return $orders;
    }

    /* The Dashboard task Status */
    public static function DashboardTaskStatus(Request $request){
        $validated = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $companyId=$request->company_id;
        $workspaceId=$request->workspace_id;
        $staffId=$request->staff_id;
        $whereCondition = [
            ['company_id','=',$companyId],
            ['workspace_id','=',$workspaceId]
        ];

            $whereConditioncomplete = [
                ['order_task_data.company_id','=',$companyId],
                ['order_task_data.workspace_id','=',$workspaceId],
                ['order_task_data.task_accomplished_date','!=',NULL],
                ['order_task_data.task_schedule_end_date','>=','order_task_data.task_accomplished_date'],
            ];

            $whereCondition_delay = [
                ['order_task_data.company_id','=',$companyId],
                ['order_task_data.workspace_id','=',$workspaceId],
                ['order_task_data.task_schedule_end_date','<',date("Y-m-d")],
               ['order_task_data.task_accomplished_date','=',NULL],
            ];

            $whereCondition_delaycomplete = [
                ['order_task_data.company_id','=',$companyId],
                ['order_task_data.workspace_id','=',$workspaceId],
                ['order_task_data.task_accomplished_date','>',DB::raw('order_task_data.task_schedule_end_date')]
            ];



            $whereCondition_notstart = [
                ['order_task_data.company_id','=',$companyId],
                ['order_task_data.workspace_id','=',$workspaceId],
                ['order_task_data.task_schedule_start_date','>',date("Y-m-d")],
            ];

            $whereCondition_inprogress = [
                ['order_task_data.company_id','=',$companyId],
                ['order_task_data.workspace_id','=',$workspaceId],
                ['order_task_data.task_schedule_start_date','>=',date("Y-m-d")],
               // ['order_task_data.task_schedule_end_date','<=',date("Y-m-d")],
                ['order_task_data.task_accomplished_date','!=',NULL],
            ];

            $whereCondition_rescheduled = [
                ['order_task_data.company_id','=',$companyId],
                ['order_task_data.workspace_id','=',$workspaceId],
                ['order_task_data.rescheduled','!=',NULL],
            ];



            $taskDataArr = [];
            $taskDataArr['complete'] = DashBoard::getDashboardTaskCount($whereConditioncomplete,$staffId);
            $taskDataArr['delay'] = DashBoard::getDashboardTaskCount($whereCondition_delay,$staffId);
            $taskDataArr['delaycomplete'] = DashBoard::getDashboardTaskCount($whereCondition_delaycomplete,$staffId);
            $taskDataArr['notstart'] = DashBoard::getDashboardTaskCount($whereCondition_notstart,$staffId);
            $taskDataArr['rescheduled'] = DashBoard::getDashboardTaskCount($whereCondition_rescheduled,$staffId);
            $taskDataArr['inprogress'] = DashBoard::getDashboardTaskCount($whereCondition_inprogress,$staffId);
            return response()->json(['status_code' => 200, 'status'=>"success",'data' => $taskDataArr]);
    }

    /* Get the Task Count for the dashboard */
    public  static function getDashboardTaskCount($whereCondition,$staffId){
        if($staffId>0){
            $completeTaskCount = OrderTask::select('orders.id')
            ->where($whereCondition)
            ->where('order_contacts.staff_id','=',$staffId)
            ->leftjoin('orders','orders.id','order_task_data.order_id')
            ->leftjoin('order_contacts','order_contacts.order_id','orders.id')
            ->get();
        }else{
        $completeTaskCount = OrderTask::select('orders.id')->where($whereCondition)->leftjoin('orders','orders.id','order_task_data.order_id')->get();
        }
        return  count($completeTaskCount);
    }
}
