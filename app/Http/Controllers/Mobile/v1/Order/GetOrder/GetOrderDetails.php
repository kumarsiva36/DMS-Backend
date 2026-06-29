<?php

namespace App\Http\Controllers\Mobile\v1\Order\GetOrder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderSku;
use App\Models\OrderTask;
use App\Models\User;
use App\Common\CommonApp;
use Carbon\Carbon;
use App\Models\OrderContacts;
use App\Models\UpdateSkuQuantity;
use App\Models\OrderProduction;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;

class GetOrderDetails extends Controller
{
    /**
     * Handle the incoming request.
     * get order details
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'=>'required',
            'workspace_id'=>'required',
            //'user_id'=>'required',
            //'staff_id'=>'required',
            'order_id'=>'required',
        ]);
        if($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereCondition =[
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id],
            ['orders.id','=',$request->order_id],
           // ['orders.user_id','=',$request->user_id],
        ];
        $whereConditionsku =[
            ['order_sku.company_id','=',$request->company_id],
            ['order_sku.workspace_id','=',$request->workspace_id],
            ['order_sku.order_id','=',$request->order_id],
          //  ['order_sku.user_id','=',$request->user_id],
        ];
        $whereConditioncontact =[
            ['order_contacts.company_id','=',$request->company_id],
            ['order_contacts.workspace_id','=',$request->workspace_id],
            ['order_contacts.order_id','=',$request->order_id],
          //  ['order_contacts.user_id','=',$request->user_id],
        ];
        $whereConditionskuqty =[
            ['update_sku_quantities.company_id','=',$request->company_id],
            ['update_sku_quantities.workspace_id','=',$request->workspace_id],
            ['update_sku_quantities.order_id','=',$request->order_id],
           // ['update_sku_quantities.user_id','=',$request->user_id],
        ];

        $whereConditions =[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->order_id],
           // ['user_id','=',$request->user_id],
        ];
        $whereConditionsku =[
            ['order_sku.company_id','=',$request->company_id],
            ['order_sku.workspace_id','=',$request->workspace_id],
            ['order_sku.order_id','=',$request->order_id],
           // ['order_sku.user_id','=',$request->user_id],
        ];

        $basicInfo = Order::where($whereCondition)
        ->join('workspace','workspace.id','orders.workspace_id')
        ->leftjoin('order_factory','order_factory.id','orders.factory_id')
        ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
        ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
        ->select('orders.order_no as order','orders.factory_id','orders.pcu_id','orders.buyer_id','orders.style_no as style','orders.total_quantity as quantity',
        'workspace.name as workspace','order_pcu.name as pcu','order_factory.name as factory','order_buyer.name as buyer','orders.order_task_template','orders.cutting_start_date','orders.cutting_end_date','orders.sewing_start_date','orders.sewing_end_date','orders.packing_start_date','orders.packing_end_date','orders.is_tolerance_req','orders.tolerance_volume','orders.tolerance_perc','orders.status')
        ->get();

        $orderSkuDetails = OrderSku::select('order_sku.sku_color_id','color.name as colorname','order_sku.sku_size_id','size.name as sizename','sku_quantity')->where($whereConditionsku)->leftjoin('color','color.id','order_sku.sku_color_id')->leftjoin('size','size.id','order_sku.sku_size_id')->get();

        $orderContacts = OrderContacts::select('order_contacts.staff_id','staff.first_name','staff.last_name')->where($whereConditioncontact)->leftjoin('staff','staff.id','order_contacts.staff_id')->get();

        $taskDetails = OrderTask::select('id','cat_title','task_title','task_schedule_start_date','task_schedule_end_date','task_accomplished_date')->where($whereConditions)->get();

        $dayByDaySKUupdates = UpdateSkuQuantity::select('update_sku_quantities.color_id','color.name','update_sku_quantities.size_id','size.name','update_sku_quantities.type_of_production','update_sku_quantities.updated_quantity','update_sku_quantities.sku_date')->where($whereConditionskuqty)->leftjoin('color','color.id','update_sku_quantities.color_id')->leftjoin('size','size.id','update_sku_quantities.size_id')->get();
       // $prodDetails = OrderProduction::where($whereConditions)->get();

       // $getColorDetails=$this->getSKUDetails($whereConditionsku,'color');
       // $getSizeDetails=$this->getSKUDetails($whereConditionsku,'size');
        $getProductionData=$this->getProductionData($whereConditions);
        $getPercentage=$this->getProductionDataPercentage($whereConditions);
        $dataArr=[];
        $dataArr['basicInfo'] =$basicInfo;
        $dataArr['skuDetails'] =$orderSkuDetails;
        $dataArr['orderContact'] =$orderContacts;
        $dataArr['taskDetails']= $taskDetails;
       // $dataArr['skucolor'] =$getColorDetails;
       // $dataArr['skusize'] =$getSizeDetails;
        $dataArr['updateSkuDetails']= $dayByDaySKUupdates;
        $dataArr['productionDetails']= $getProductionData;
        $dataArr['percentage']= $getPercentage;
        $dataArr['statusName']= array(0=>"Default","1"=>"Activated","2"=>"Deactivated","3"=>"Deleted","10"=>"Cancelled","11"=>"Closed","12"=>"Complete");

        if(count($basicInfo)>0){
            return response()->json(["status_code"=>200,"status" =>"Success","data"=>$dataArr],200);
        }else{
            return response()->json(["status_code"=>201,"status" =>"error","msg"=>"Data Not Found"],201);
        }

    }

    /* Get the SKU Details */
    public function getSKUDetails($whereConditionsku,$type){
        if($type=='color'){
        $orderSkuDetails = OrderSku::select('color.id','color.name')->where($whereConditionsku)->leftjoin('color','color.id','order_sku.sku_color_id')->groupBy('color.id')->get();
        }else{
            $orderSkuDetails = OrderSku::select('size.id','size.name')->where($whereConditionsku)->leftjoin('size','size.id','order_sku.sku_size_id')->groupBy('order_sku.sku_size_id')->get();
        }
        return $orderSkuDetails;
    }

    /* Get the Production Data */
    public function getProductionData($whereConditions){

        $cutProdDetails = OrderProduction::where($whereConditions)->where('type_of_production','Cut')->get();
        $sewProdDetails = OrderProduction::where($whereConditions)->where('type_of_production','Sew')->get();
        $packProdDetails = OrderProduction::where($whereConditions)->where('type_of_production','Pack')->get();
        return array("cut"=>$cutProdDetails, "sew"=>$sewProdDetails, "pack"=>$packProdDetails);

    }

    /* Get the Production Data Percentage */
    public function getProductionDataPercentage($whereConditions) {

        $cutProdDetailsCountv = OrderProduction::select(DB::raw("SUM(target_value) as skucount"))->where($whereConditions)->where('type_of_production','Cut')->where('holiday_flag',0)->get();
        $cutProdDetailsCount=$cutProdDetailsCountv[0]['skucount']?$cutProdDetailsCountv[0]['skucount']:0;
        $sewProdDetailsCountv = OrderProduction::select(DB::raw("SUM(target_value) as skucount"))->where($whereConditions)->where('type_of_production','Sew')->where('holiday_flag',0)->get();
        $sewProdDetailsCount=$sewProdDetailsCountv[0]['skucount']?$sewProdDetailsCountv[0]['skucount']:0;
        $packProdDetailsCountv = OrderProduction::select(DB::raw("SUM(target_value) as skucount"))->where($whereConditions)->where('type_of_production','Pack')->get();
        $packProdDetailsCount=$packProdDetailsCountv[0]['skucount']?$packProdDetailsCountv[0]['skucount']:0;

        $cutUpdateQtyv=UpdateSkuQuantity::select(DB::raw("SUM(updated_quantity) as skucount"))->where($whereConditions)->where('type_of_production','Cut')->get();
        $cutUpdateQty=$cutUpdateQtyv[0]['skucount']?$cutUpdateQtyv[0]['skucount']:0;
        $sewUpdateQtyv=UpdateSkuQuantity::select(DB::raw("SUM(updated_quantity) as skucount"))->where($whereConditions)->where('type_of_production','Sew')->get();
        $sewUpdateQty=$sewUpdateQtyv[0]['skucount']?$sewUpdateQtyv[0]['skucount']:0;
        $packUpdateQtyv=UpdateSkuQuantity::select(DB::raw("SUM(updated_quantity) as skucount"))->where($whereConditions)->where('type_of_production','Pack')->get();
        $packUpdateQty=$packUpdateQtyv[0]['skucount']?$packUpdateQtyv[0]['skucount']:0;

        $cutPercentage=$cutProdDetailsCount>0?($cutUpdateQty/$cutProdDetailsCount)*100:0;
        $sewPercentage=$sewProdDetailsCount>0?($sewUpdateQty/$sewProdDetailsCount)*100:0;
        $packPercentage=$packProdDetailsCount>0?($packUpdateQty/$packProdDetailsCount)*100:0;
       return array("totalCutQty"=>$cutProdDetailsCount, "totalSewQty"=>$sewProdDetailsCount, "totalPackQty"=>$packProdDetailsCount,"cutUpdatedQty"=>$cutUpdateQty,"sewUpdatedQty"=>$sewUpdateQty,"packUpdatedQty"=>$packUpdateQty,"cutPercentage"=>$cutPercentage,"sewPercentage"=>$sewPercentage,"packPercentage"=>$packPercentage);


      }

}
