<?php

namespace App\Http\Controllers\WebSite\Reports;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\OrderSku;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\UpdateSkuQuantity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Common\CommonApp;

class SKUDailyProdReports extends Controller
{
    /* SKU Daily Prod Reports */
    public static function skuDailyProdData(Request $request){
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
        $size   = isset($request->sizes)?$request->sizes:[];
        $color   = isset($request->colors)?$request->colors:[];

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
            $whereCondition1[]=['company_id','=',$request->company_id];
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
            ['update_sku_quantities.company_id',"=",$request->company_id],
            ['update_sku_quantities.workspace_id','=',$request->workspace_id],
            ['update_sku_quantities.order_id','=',$request->order_id]
        ];
        // if($request->has("production_type")){
        //     $whereCondition1[]=["type_of_production",$request->production_type];
        // }
        $dailyUpdatesArr=$dailyUpdate=[];
        $colors = OrderSku::where('order_id',$request->order_id)
        ->leftjoin('color','color.id','order_sku.sku_color_id')
        ->select('color.id','color.name')
        ->groupBy('sku_color_id')->get();
        $sizes = OrderSku::where('order_id',$request->order_id)
        ->leftjoin('size','size.id','order_sku.sku_size_id')
        ->select('size.id','size.name')
        ->groupBy('sku_size_id')->get();
        $dailyUpdatesArr['colors']=$colors;
        $dailyUpdatesArr['size']=$sizes;
        $page = (isset($request->page) && $request->page!='')?$request->page:1;
        if(!empty($orders)){
            $startDate= $orders->cutting_start_date;
            $endDate= $orders->packing_end_date;
            if(isset($request->start_date) && isset($request->end_date)){
                if(!empty($size) || !empty($color)){
                    if(!empty($size) && !empty($color)){
                            $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                            ->whereBetween('update_sku_quantities.sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                            ->whereIn('update_sku_quantities.size_id',$size)
                            ->whereIn('update_sku_quantities.color_id',$color)
                            ->leftjoin('color','color.id','update_sku_quantities.color_id')
                            ->leftjoin('size','size.id','update_sku_quantities.size_id')
                            ->select('update_sku_quantities.type_of_production',"updated_quantity",
                                'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                            ->orderBy('update_sku_quantities.sku_date','ASC')->paginate(50, ['*'], 'page', $page);
                    }
                    else if(!empty($size)){
                            $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                            ->whereBetween('update_sku_quantities.sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                            ->whereIn('update_sku_quantities.size_id',$size)
                            ->leftjoin('color','color.id','update_sku_quantities.color_id')
                            ->leftjoin('size','size.id','update_sku_quantities.size_id')
                            ->select('update_sku_quantities.type_of_production',"updated_quantity",
                                'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                            ->orderBy('update_sku_quantities.sku_date','ASC')->paginate(50, ['*'], 'page', $page);
                    }
                    else if(!empty($color)){
                            $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                            ->whereBetween('update_sku_quantities.sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                            ->whereIn("update_sku_quantities.color_id",$color)
                            ->leftjoin('color','color.id','update_sku_quantities.color_id')
                            ->leftjoin('size','size.id','update_sku_quantities.size_id')
                            ->select('update_sku_quantities.type_of_production',"update_sku_quantities.updated_quantity",
                                'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                            ->orderBy('update_sku_quantities.sku_date','ASC')->paginate(50, ['*'], 'page', $page);
                    }
                }
                else{
                    $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                    ->whereBetween('update_sku_quantities.sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                    ->leftjoin('color','color.id','update_sku_quantities.color_id')
                    ->leftjoin('size','size.id','update_sku_quantities.size_id')
                    ->select('update_sku_quantities.type_of_production',"update_sku_quantities.updated_quantity",
                        'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                    ->orderBy('update_sku_quantities.sku_date','ASC')->paginate(50, ['*'], 'page', $page);
                }
            }else{
                if(!empty($size) || !empty($color)){
                        if(!empty($size) && !empty($color)){
                            $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                            ->whereIn('update_sku_quantities.size_id',$size)
                            ->whereIn('update_sku_quantities.color_id',$color)
                            ->leftjoin('color','color.id','update_sku_quantities.color_id')
                            ->leftjoin('size','size.id','update_sku_quantities.size_id')
                            ->select('update_sku_quantities.type_of_production','update_sku_quantities.updated_quantity',
                                'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                            // ->groupBy('update_sku_quantities.type_of_production')->groupBy('update_sku_quantities.sku_date')
                            ->orderBy('update_sku_quantities.sku_date','ASC')->paginate(50, ['*'], 'page', $page);
                        }
                        else if(!empty($size)){
                            $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                            ->whereIn('update_sku_quantities.size_id',$size)
                            ->leftjoin('color','color.id','update_sku_quantities.color_id')
                            ->leftjoin('size','size.id','update_sku_quantities.size_id')
                            ->select('update_sku_quantities.type_of_production',"update_sku_quantities.updated_quantity",
                                'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                            ->orderBy('update_sku_quantities.sku_date','ASC')->paginate(50, ['*'], 'page', $page);
                        }
                        else if(!empty($color)){
                                $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                                ->whereIn('update_sku_quantities.color_id',$color)
                                ->leftjoin('color','color.id','update_sku_quantities.color_id')
                                ->leftjoin('size','size.id','update_sku_quantities.size_id')
                                ->select('update_sku_quantities.type_of_production',"update_sku_quantities.updated_quantity",
                                    'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                                // ->groupBy('update_sku_quantities.type_of_production')->groupBy('update_sku_quantities.sku_date')
                                ->orderBy('update_sku_quantities.sku_date','ASC')->paginate(50, ['*'], 'page', $page);
                        }
                }
                else{
                    $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                    ->leftjoin('color','color.id','update_sku_quantities.color_id')
                    ->leftjoin('size','size.id','update_sku_quantities.size_id')
                    ->select('update_sku_quantities.type_of_production',"updated_quantity",
                        'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                    // ->groupBy('update_sku_quantities.type_of_production')->groupBy('update_sku_quantities.sku_date')
                    // ->groupBy('update_sku_quantities.color_id')->groupBy('update_sku_quantities.size_id')
                    ->orderBy('update_sku_quantities.sku_date','ASC')->paginate(50, ['*'], 'page', $page);
                }
            }
            // dd($dailyUpdates);
            //return response()->json(["status_code"=>200,"status" =>"success","data"=>$dailyUpdates]);
            $i=0;
            $dailyUpdatesArr['necessaryDetails']['orderNo']=$orders->order_no;
            $dailyUpdatesArr['necessaryDetails']['styleNo']=$orders->style_no;
            $dailyUpdatesArr['necessaryDetails']['pages']=$dailyUpdates->lastPage();
            $dailyUpdatesArr['necessaryDetails']['currentPage']=$dailyUpdates->currentPage();
            foreach($dailyUpdates as $updates){
                if(isset($updates->updated_quantity) && (int)$updates->updated_quantity>0){
                    $updatesArr=[];
                    $updatesArr['sku_date']=$updates->sku_date;
                    $updatesArr[$updates->type_of_production."_actual"]=(int)$updates->updated_quantity;
                    $updatesArr[$updates->type_of_production."_target"]=(int)$updates->target_value;
                    // $updatesArr[$updates->type_of_production."_diff"]=(int)$updates->diff;
                    $updatesArr[$updates->type_of_production]
                    [$updates->colorName." - ".$updates->sizeName]=(int)$updates->updated_quantity;
                    $updatesArr['total_qty']=(int)$orders->total_quantity;
                    /* To place the prod datas in their respective dates */
                    $res=array_search($updates->sku_date, array_column($dailyUpdate, 'sku_date')) ?? -1;

                    if($res>=0 && strlen($res)>0){
                        $dailyUpdate[$res][$updates->type_of_production."_actual"]=(int)$updates->updated_quantity ;
                        $dailyUpdate[$res][$updates->type_of_production."_target"]=(int)$updates->target_value;
                        // $dailyUpdate[$res][$updates->type_of_production."_diff"]=(int)$updates->diff;
                        $dailyUpdate[$res][$updates->type_of_production]
                        [$updates->colorName." - ".$updates->sizeName]=(int)$updates->updated_quantity;
                    }else{
                        $dailyUpdate[$i]=$updatesArr;
                        $i++;
                    }
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
}
