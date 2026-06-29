<?php

namespace App\Http\Controllers\WebSite\Order\PendingTasks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\UpdateSkuQuantity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DateTime;
use App\Common\CommonApp;

class PendingProduction extends Controller
{
    //
    /* To Get the Pending Production Details for the Order */
    public static function pendingProduction(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id'=>'required',
            'workspace_id'=>'required',
            'orderNo'=>'required'
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition =[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_no','=',$request->orderNo],
            ['step_level','=','6'],
            ['status','=','1']
        ];
        if(isset($request->staff_id) && $request->staff_id > 0){
            $whereCondition1= $whereCondition2 = [
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
                    $theOrder = Order::where("id", $order->order_id)->first();
                    if($request->orderNo == $theOrder->order_no) {
                        $theOrders[]=$theOrder;
                    }
                }
                $orders=$theOrders;
            }else{
                $orders = Order::where($whereCondition)->get();
            }
        }else{
            $orders = Order::where($whereCondition)->get();
        }
        $pendingProductionArr=[];
        $ltFiveDays = $btwnFiveToTenDays = $gtTenDays = 0;
        foreach ($orders as $order) {
            // dd($order);
            $productionDetails=$prodData=$cutArr=$sewArr=$packArr=[];
            $pendingProductionArr['orderNo']=$order->order_no;
            $productionDetails['styleNo']=$order->style_no;
            /* Cut */
            $cutArr['title']="Cutting";
            $cutArr['startDate']=$order->cutting_start_date;
            $cutArr['endDate']=$order->cutting_end_date;
            $cutArr['totalQuantity']=$order->total_quantity;
            $cutArr['updatedQuantity']=PendingProduction::getUpdatedProductionSum($order,"Cut");
            $cutArr['pendingQuantity']=$cutArr['totalQuantity'] - $cutArr['updatedQuantity'];
            $cuttingInterval = PendingProduction::dateDifference($order->cutting_start_date,$order->cutting_end_date);
            $cutArr['delay']= $cuttingInterval['delay'];
            $cutArr['type']= $cuttingInterval['type'];
            if($cutArr['pendingQuantity'] >0){
                $prodData[]=$cutArr;
            }
            /* Sew */
            $sewArr['title']="Sewing";
            $sewArr['startDate']=$order->sewing_start_date;
            $sewArr['endDate']=$order->sewing_end_date;
            $sewArr['totalQuantity']=$order->total_quantity;
            $sewArr['updatedQuantity']=PendingProduction::getUpdatedProductionSum($order,"Sew");
            $sewArr['pendingQuantity']=$sewArr['totalQuantity'] - $sewArr['updatedQuantity'];
            $sewingInterval=PendingProduction::dateDifference($order->sewing_start_date,$order->sewing_end_date);
            $sewArr['delay']=$sewingInterval['delay'];
            $sewArr['type']=$sewingInterval['type'];
            if($sewArr['pendingQuantity'] >0){
                $prodData[]=$sewArr;
            }
            /* Pack */
            $packArr['title']="Packing";
            $packArr['startDate']=$order->packing_start_date;
            $packArr['endDate']=$order->packing_end_date;
            $packArr['totalQuantity']=$order->total_quantity;
            $packArr['updatedQuantity']=PendingProduction::getUpdatedProductionSum($order,"Pack");
            $packArr['pendingQuantity']=$packArr['totalQuantity'] - $packArr['updatedQuantity'];
            $packingInterval = PendingProduction::dateDifference($order->packing_start_date,$order->packing_end_date);
            $packArr['delay']= $packingInterval['delay'];
            $packArr['type']= $packingInterval['type'];
            if($packArr['pendingQuantity'] >0){
                $prodData[]=$packArr;
            }
            /* Delay Conditions */
            $delays = PendingProduction::calculateDelayRangesCount($cutArr['delay'],$sewArr['delay'],$packArr['delay']);
            $productionDetails['ltFiveDays'] =$delays['ltFiveDays'];
            $productionDetails['btwnFiveToTenDays'] =$delays['btwnFiveToTenDays'];
            $productionDetails['gtTenDays'] =$delays['gtTenDays'];
            /* Data append to array */
            $productionDetails['prodData']=$prodData;
            /* For Top level data */
            $ltFiveDays += $delays['ltFiveDays'];
            $btwnFiveToTenDays += $delays['btwnFiveToTenDays'];
            $gtTenDays += $delays['gtTenDays'];
            $pendingProductionArr['ltFiveDays'] =$ltFiveDays;
            $pendingProductionArr['btwnFiveToTenDays'] =$btwnFiveToTenDays;
            $pendingProductionArr['gtTenDays'] =$gtTenDays;
            !empty($productionDetails['prodData']) ? $pendingProductionArr['productionData'][]=$productionDetails
            : $pendingProductionArr['productionData']=[];
        }
        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$pendingProductionArr]);
        return CommonApp::webEncrypt($res);
    }

    /* To Get The Sum of the updated quantities */
    public static function getUpdatedProductionSum($order,$type){
            $whereCondition=[
                ['company_id','=',$order->company_id],
                ['workspace_id','=',$order->workspace_id],
                ['order_id','=',$order->id],
                ['type_of_production','=',$type]
            ];
            $total = UpdateSkuQuantity::where($whereCondition)->sum('updated_quantity');
            return $total;
    }

    /* To Get the day difference */
    public static function dateDifference($startdate,$endDate)
    {
        $lastDate = new DateTime($endDate);
        $startDate = new DateTime($startdate);
        $today = new DateTime(date("Y-m-d"));
        $interval = [];
        if($startDate > $today){
            $interval['delay'] = (int)$today->diff($startDate)->format("%r%a");
            $interval['type']="YetToBeStarted";
        }
        else if($startDate >= $today){
            $interval['delay'] = (int) 0;
            $interval['type']="StartsToday";
        }
        else{
            $interval['delay'] = (int)$today->diff($lastDate)->format("%r%a");
            $interval['type']="Progress";
        }

        return $interval;
    }

    /* To Calculate the Delay Ranges count */
    public static function calculateDelayRangesCount($cut,$sew,$pack){
        $ltFiveDay = $btwnFiveToTenDays = $gtTenDays=0;
        /* For Cut */
        if($cut<0){
            if(abs($cut)<5 ){
                $ltFiveDay+=1;
            }
            else if(abs($cut) >=5 && abs($cut)<10){
                $btwnFiveToTenDays+=1;
            }
            else if(abs($cut)>10){
                $gtTenDays+=1;
            }
        }
        if($sew<0){
            if(abs($sew)<5 ){
                $ltFiveDay+=1;
            }
            else if(abs($sew) >=5 && abs($sew)<10){
                $btwnFiveToTenDays+=1;
            }
            else if(abs($sew)>10){
                $gtTenDays+=1;
            }
        }
        if($pack<0){
            if(abs($pack)<5 ){
                $ltFiveDay+=1;
            }
            else if(abs($pack) >=5 && abs($pack)<10){
                $btwnFiveToTenDays+=1;
            }
            else if(abs($pack)>10){
                $gtTenDays+=1;
            }
        }
        $details=[];
        $details['ltFiveDays']=$ltFiveDay;
        $details['btwnFiveToTenDays']=$btwnFiveToTenDays;
        $details['gtTenDays']=$gtTenDays;
        return $details;
    }
}
