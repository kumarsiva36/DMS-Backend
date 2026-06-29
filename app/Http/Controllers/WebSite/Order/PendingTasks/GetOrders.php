<?php

namespace App\Http\Controllers\WebSite\Order\PendingTasks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Common\CommonApp;

class GetOrders extends Controller
{
    /******************To get the list of the orders **********************/
    public function getOrdersList(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id'=>'required',
            'workspace_id'=>'required'
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition =[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['status','=','1'],
            ['step_level','=','6']
        ];
        if(isset($request->type) && $request->type === "pendingProduction"){
            $whereCondition[]=["step_level","=","6"];
        }
        if (isset($request->factory_id) && $request->factory_id!=""){
            $whereCondition['factory_id'] = $request->factory_id;
        }
        if (isset($request->pcu_id) && $request->pcu_id!=""){
            $whereCondition['pcu_id'] = $request->pcu_id;
        }
        if (isset($request->buyer_id) && $request->buyer_id!=""){
            $whereCondition['buyer_id'] = $request->buyer_id;
        }
        if( isset($request->staff_id) && $request->staff_id > 0){
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
                $whereCondition3['step_level']="6";
                foreach($involedOrders as $order) {
                    $whereCondition3['id']=$order->order_id;
                    $theOrder = Order::where($whereCondition3)->select('order_no')->first();
                    if(!empty($theOrder)){
                        $theOrders[]=$theOrder;
                    }
                }
                $getUniqueOrders = isset($theOrders)?
                array_map("unserialize",array_unique(array_map("serialize",$theOrders))):[];
                $orders = array_values($getUniqueOrders);
            }else{
                $orders = Order::where($whereCondition)
                        ->select('order_no')
                        ->groupBy('order_no')
                        ->get();
            }
        }
        else{
            $orders = Order::where($whereCondition)
                    ->select('order_no')
                    ->groupBy('order_no')
                    ->get();
        }
        $res = json_encode(['status_code' => 200,'status'=>"success" ,'data' => $orders]);
        return CommonApp::webEncrypt($res);
    }

    /*****************************To get THE ORDER and their respective styles *******************************/
    // public static function getTheOrder(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'company_id'=>'required',
    //         'workspace_id'=>'required',
    //         'orderNo'=>'required'
    //     ]);
    //     if($validator->fails()){
    //         return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
    //     }

    // }
}
