<?php

namespace App\Http\Controllers\WebSite\Reports;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\UpdateSkuQuantity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\UpdateSkuQuantityLog;
use App\Common\CommonApp;

class DailyProdReports extends Controller
{
    //
    /* Daily Production Reports */
    public static function dailyProdReports(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            "company_id" =>"required",
            "workspace_id" =>"required",
            "order_id" =>"required",
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $type = isset($request->type)?$request->type:"";
        $no_of_excess   = isset($request->no_of_excess)?$request->no_of_excess:"";
        $no_of_short   = isset($request->no_of_short)?$request->no_of_short:"";
        $operator_symb = isset($request->operator_symb)?$request->operator_symb:"";
        $all = "All";
        if(!empty($type))
            $all ='';
        $whereCondition =[
            ['company_id',"=",$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['step_level','=','6'],
            ['id','=',$request->order_id]
        ];
        if(isset($request->factory_id)){
            $whereCondition[]=['factory_id','=',$request->factory_id];
        }
        if(isset($request->buyer_id)){
            $whereCondition[]=['buyer_id','=',$request->buyer_id];
        }
        if(isset($request->pcu_id)){
            $whereCondition[]=['pcu_id','=',$request->pcu_id];
        }
        $whereCondition[]=['status','=',"1"];
        if(isset($request->staff_id) && $request->staff_id > 0){
            $whereCondition1= $whereCondition2 = $whereCondition3=[
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
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $theOrders = [];
                foreach($involedOrders as $order) {
                    $theOrder = Order::where($whereCondition3)->where("id", $order->order_id)->first();
                    if($theOrder->id === $request->order_id) {
                        $theOrders[]=$theOrder;
                    }
                }
                $orders=$theOrders;
            }else{
                $orders = Order::where($whereCondition)->first();
            }
        }else{
            $orders = Order::where($whereCondition)->first();
        }
        $whereCondition1 =[
            ['company_id',"=",$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->order_id]
        ];
        if(isset($request->production_type)){
            $whereCondition1[]=["type_of_production",$request->production_type];
        }
        $dailyUpdatesArr=$dailyUpdate=[];
        $page = (isset($request->page) && $request->page!='')?$request->page:1;
        if(!empty($orders)){
            $startDate= $orders->cutting_start_date;
            $endDate= $orders->packing_end_date;
            if(isset($request->start_date) && isset($request->end_date)){
                if($type==="Excess"){
                    if($no_of_excess!='' && $operator_symb!='' ){
                        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                        ->whereBetween('sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                        ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                            DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                        ->having('diff','>',0)
                        ->having('diff',$operator_symb,$no_of_excess)
                        ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')
                        ->paginate(30, ['*'], 'page', $page);
                    }else{
                        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                        ->whereBetween('sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                        ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                            DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                        ->having('diff','>',0)
                        ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')
                        ->paginate(30, ['*'], 'page', $page);
                    }
                }
                else if($type==="Short"){
                    if($no_of_short!='' && $operator_symb!='' ){
                        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                        ->whereBetween('sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                        ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                            DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                        ->having('diff','<',0)
                        ->having('diff',$operator_symb,-($no_of_short))
                        ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')
                        ->paginate(30, ['*'], 'page', $page);
                    }else{
                        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                        ->whereBetween('sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                        ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                            DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                        ->having('diff','<',0)
                        ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')
                        ->paginate(30, ['*'], 'page', $page);
                    }
                }
                else{
                    $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                    ->whereBetween('sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                    ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                        DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                    ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')
                    ->paginate(30, ['*'], 'page', $page);
                }
            }else{
                if($type==="Excess"){
                    if($no_of_excess!='' && $operator_symb!='' ){
                        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                        ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                            DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                        ->having('diff','>',0)
                        ->having('diff',$operator_symb,$no_of_excess)
                        ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')
                        ->paginate(30, ['*'], 'page', $page);
                    }
                    else{
                        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                        ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                            DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                        ->having('diff','>',0)
                        ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')
                        ->paginate(30, ['*'], 'page', $page);
                    }
                }
                else if($type==="Short"){
                    if($no_of_short!='' && $operator_symb!='' ){
                        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                        ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                            DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                        ->having('diff','<',0)
                        ->having('diff',$operator_symb,-($no_of_short))
                        ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')
                        ->paginate(30, ['*'], 'page', $page);
                    }else{
                        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                        ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                            DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                        ->having('diff','<',0)
                        ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')
                        ->paginate(30, ['*'], 'page', $page);
                    }
                }else{
                    $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                    ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                    DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                    ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')
                    ->paginate(30, ['*'], 'page', $page);
                    // ->get();
                    // dd($dailyUpdates->currentPage());
                }
            }
            // dd($dailyUpdates);
            $i=0;
            $dailyUpdatesArr['necessaryDetails']['orderNo']=$orders->order_no;
            $dailyUpdatesArr['necessaryDetails']['styleNo']=$orders->style_no;
            $dailyUpdatesArr['necessaryDetails']['pages']=$dailyUpdates->lastPage();
            $dailyUpdatesArr['necessaryDetails']['currentPage']=$dailyUpdates->currentPage();
            foreach($dailyUpdates as $updates){
                $updatesArr=[];
                $updatesArr['sku_date']=$updates->sku_date;
                $updatesArr[$updates->type_of_production."_actual"]=(int)$updates->updated_quantity;
                $updatesArr[$updates->type_of_production."_target"]=(int)$updates->target_value;
                $updatesArr[$updates->type_of_production."_diff"]=(int)$updates->diff;
                $updatesArr['total_qty']=(int)$orders->total_quantity;
                /* To place the prod datas in their respective dates */
                $res=array_search($updates->sku_date, array_column($dailyUpdate, 'sku_date')) ?? -1;

                if($res>=0 && strlen($res)>0){
                    $dailyUpdate[$res][$updates->type_of_production."_actual"]=(int)$updates->updated_quantity;
                    $dailyUpdate[$res][$updates->type_of_production."_target"]=(int)$updates->target_value;
                    $dailyUpdate[$res][$updates->type_of_production."_diff"]=(int)$updates->diff;
                }else{
                    $dailyUpdate[$i]=$updatesArr;
                    $i++;
                }
            }
            $dailyUpdatesArr['prodData']=$dailyUpdate;
            // !empty($dailyUpdatesArr['prodData']) ? $dailyArr = $dailyUpdatesArr : $dailyArr=[];
            // echo '<pre>'; print_r($dailyUpdatesArr); exit;
            $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$dailyUpdatesArr]);
            return CommonApp::webEncrypt($res);
        }
        else{
            $res = json_encode(["status_code"=>200,"status" =>"success","data"=>[]]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Daily Production Logs */
    public static function dailyProdReportsLogs(Request $request){
        $page = (isset($request->page) && $request->page!='')?$request->page:1;
        $whereCondition =[
            ['update_sku_quantity_logs.workspace_id',">",'0'],
            ['update_sku_quantity_logs.company_id','>','0']
        ];
        if(isset($request->start_date)){
            $whereCondition[] =['update_sku_quantity_logs.sku_date',">=",$request->start_date];
        }
        if(isset($request->end_date)){
            $whereCondition[] =['update_sku_quantity_logs.sku_date',"<=",$request->end_date];
        }
        $result = UpdateSkuQuantityLog::where($whereCondition)
        ->join('company_settings','company_settings.id','update_sku_quantity_logs.company_id')
        ->join('workspace','workspace.id','update_sku_quantity_logs.workspace_id')
        ->leftjoin('users','users.id','update_sku_quantity_logs.user_id')
        ->leftjoin('staff','staff.id','update_sku_quantity_logs.staff_id')
        ->join('orders','orders.id','update_sku_quantity_logs.order_id')
        ->join('color','color.id','update_sku_quantity_logs.color_id')
        ->join('size','size.id','update_sku_quantity_logs.size_id')
        ->select('company_settings.company_name','workspace.name','users.name as username','staff.first_name as staffname','orders.style_no',
        'color.name as colorname','size.name as size','type_of_production','sku_date','updated_quantity',
        DB::raw('DATE_FORMAT(update_sku_quantity_logs.created_at,"%d %b %Y %H:%i") as created_date'))
        ->orderBy('update_sku_quantity_logs.id','DESC')
        ->paginate(30, ['*'], 'page', $page);
        //->limit(10)
        //->get();

        return response()->json(["status_code"=>200,"status" =>"success","data"=>$result]);


    }
}
