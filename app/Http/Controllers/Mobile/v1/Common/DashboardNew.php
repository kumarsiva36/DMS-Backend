<?php

namespace App\Http\Controllers\Mobile\v1\Common;

use App\Http\Controllers\Controller;
use App\Models\DashboardSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\UserPreferences;
use App\Common\CommonApp;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardNew extends Controller
{

    /* Top 5 Delayed Task */
    public static function getTopDelayTask(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }

        $delayedTasks = DashboardSettings::top5TaskDelay($request);
        // dd($delayedTasks);
        $res = json_encode(['status_code' => 200, 'status'=>"success",'data' => $delayedTasks]);
        return CommonApp::apiEncrypt($res);
    }

    /* Top 5 Production Delay */
    public static function getTopDelayProduction(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }

        $orders = DashboardSettings::top5ProdDelayMob($request);
        // dd($orders);
        $res = json_encode(['status_code' => 200, 'status'=>"success",'data' => $orders]);
        return CommonApp::apiEncrypt($res);
    }
    /* Top 5 Delayed Task & Production Delay */
    public static function getTopDelayTaskandProduction(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id>0){
            $whereConditions = [

                ['staff_id','=',$request->staff_id],
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
        }else{
        $whereConditions = [
            ['user_id','=',$request->user_id],
            ['staff_id','=',0],
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id]
        ];
        }

        $selectedWidgets = UserPreferences::where($whereConditions)->first();
        if($selectedWidgets!==null){

             if($selectedWidgets->dashboard_widget_ids!=''){
                $dashBoardwidg = explode(",",$selectedWidgets->dashboard_widget_ids);
                if(in_array("4",$dashBoardwidg)){
                    $top5taskdelayed = DashboardSettings::top5TaskDelay($request);
                }else{
                    $top5taskdelayed =[];
                }
                if(in_array("2",$dashBoardwidg)){
                    $top5ProdDelay = DashboardSettings::top5ProdDelayMob($request);
                }else{
                    $top5ProdDelay=[];
                }
             }else{
                $top5taskdelayed =[];
                $top5ProdDelay=[];
             }
             }else{
                $top5taskdelayed =[];
                $top5ProdDelay=[];
             }

        $res = json_encode(['status_code' => 200, 'status'=>"success",'data' => array("top5proddelayed"=>$top5ProdDelay,"top5taskdelayed"=>$top5taskdelayed)]);
        return CommonApp::apiEncrypt($res);
    }

    /* Get the Dashboard Widgets selected list  */
    public static function newDashboardWidgets(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $dashwig=[];
        $configArry=config('constant.dashboard_modules');
        foreach($configArry as $key => $value){
            $dashwigv=[];
            $dashwigv['id']=$key;
            $dashwigv['name']=$value;
            $dashwig[]=$dashwigv;
        }
        $dashBoardArr=[];
      //  $dashBoardArr['0widgetNames'] = config('constant.dashboard_modules');
        $dashBoardArr['widgetNames'] =  $dashwig;

        if(isset($request->staff_id) && $request->staff_id>0){
            $whereConditions = [

                ['staff_id','=',$request->staff_id],
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
        }else{
        $whereConditions = [
            ['user_id','=',$request->user_id],
            ['staff_id','=',0],
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id]
        ];
        }
        $selectedWidgets = UserPreferences::where($whereConditions)->first();
        if($selectedWidgets===null){
            $dashBoardArr['dashboardWidgets'] = [];
        }else{
        $dashBoardArr['dashboardWidgets'] = explode(",",$selectedWidgets->dashboard_widget_ids);
        }

        $res = json_encode(["status_code"=>200,"data"=>$dashBoardArr]);
        return CommonApp::apiEncrypt($res);
    }

    /* Order Status in Dashboard */
    public static function orderStatus(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
        ];
        $dataArr=[];
        $tasks = DashboardSettings::getTaskDetails($request);
        $prodData = DashboardSettings::getProdDetails($request);
        $dataArr['taskCounts'] = $tasks['taskCount'];
        $dataArr['taskChart'] = $tasks['tasksChart'];
        $dataArr['prodData'] = $prodData;

        $res = json_encode(["status_code"=>200,"taskCount"=>$tasks['taskCount'],"taskChart"=>$tasks['tasksChart'],
        "prodData"=>$prodData]);
        return CommonApp::apiEncrypt($res);
    }

    /* Dashboard Widgets */
    public static function dashboardWidgets(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id'=>'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
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
        return CommonApp::apiEncrypt($res);
    }

    /* To Get Task and Production Status for the Recent orders */
    public static function getOrderTaskProductionStatus(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $prodArr = DashboardSettings::getMobileOrderTaskProductionStatus($request);
        $res = json_encode(['status_code' => 200, 'status'=>"success",'data' => $prodArr['forTableView']]);
        return CommonApp::apiEncrypt($res);
    }
}
