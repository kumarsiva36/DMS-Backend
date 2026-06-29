<?php

namespace App\Http\Controllers\WebSite\Order\EditOrders;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduction;
use App\Models\OrderSku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EditSKU extends Controller
{
    /****************** Edit the SKU's ********************/
    public static function editSku (Request $request)
    {
        $request = CommonApp::webDecrypt($request->getContent());
        $request->sku = json_decode(json_encode($request->sku), true);
        $request->qty_arr = json_decode(json_encode($request->qty_arr), true);
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
            'sku' => 'required|array',
            'qty_arr' => 'array'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to Edit order
            $per = CommonApp::checkStaffPermission($request,'20');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id]
        ];
        $aldreadyExists = OrderSku::where($whereConditions)->get();
        if(!empty($aldreadyExists)){
            OrderSku::where($whereConditions)->delete();
        }
        $orderSkuArr = [];
        $sku_act_tot_qty=0;
        $orderSkuArr['user_id']= $companyDetails->user_id;
        $orderSkuArr['company_id']= $request->company_id;
        $orderSkuArr['workspace_id']= $request->workspace_id;
        $orderSkuArr['staff_id']= $request->staff_id ?? 0;
        $orderSkuArr['order_id']= $request->order_id;
        foreach ($request->sku as $sku){
            // dd($sku);
            $orderSkuArr['sku_color_id']=$sku['color_id'];
            $orderSkuArr['sku_size_id']=$sku['size_id'];
            $orderSkuArr['sku_quantity']=$sku['quantity'];
            $orderSkuArr['created_at']=date('Y-m-d H:i:s');
            $orderSkuArr['updated_at']=date('Y-m-d H:i:s');
            OrderSku::insert($orderSkuArr);
            $sku_act_tot_qty+= (int)$sku['quantity'];
        }

        if(isset($request->qty_arr) && !empty($request->qty_arr)){
            $addOrderArr=[];
            $addOrderArr['total_quantity']=$request->total_qty;
            $addOrderArr['tolerance_volume']=$request->qty_arr['tolerance_volume'];
            $addOrderArr['tolerance_perc']=$request->qty_arr['tolerance_perc'];
            $addOrderArr['is_tolerance_req']=(string)$request->qty_arr['is_tolerance_req'];
            Order::where('id',$request->order_id)->update($addOrderArr);

            //Production Update
            //Cut Data
            $cut_whereConditions =[
                ['workspace_id','=',$request->workspace_id],
                ['company_id', '=', $request->company_id],
                ['order_id','=',$request->order_id],
                ['type_of_production','=','Cut'],
                ['holiday_flag','=','0'],
            ];
            $cut_count = OrderProduction::where($cut_whereConditions)->count();
            $cut_per_day_qty = floor($sku_act_tot_qty / $cut_count);
            $cut_excess_qty = $sku_act_tot_qty % $cut_count;
            $cut_arr=[];
            $cut_arr['target_value']=$cut_per_day_qty;
            OrderProduction::where($cut_whereConditions)->update($cut_arr);
            if($cut_excess_qty > 0){
                $cut_arr['target_value']=$cut_per_day_qty+1;
                OrderProduction::where($cut_whereConditions)->limit($cut_excess_qty)->update($cut_arr);
            }


            //Sew Data
            $sew_whereConditions =[
                ['workspace_id','=',$request->workspace_id],
                ['company_id', '=', $request->company_id],
                ['order_id','=',$request->order_id],
                ['type_of_production','=','Sew'],
                ['holiday_flag','=','0'],
            ];
            $sew_count = OrderProduction::where($sew_whereConditions)->count();
            $sew_per_day_qty = floor($sku_act_tot_qty / $sew_count);
            $sew_excess_qty = $sku_act_tot_qty % $sew_count;
            $sew_arr=[];
            $sew_arr['target_value']=$sew_per_day_qty;
            OrderProduction::where($sew_whereConditions)->update($sew_arr);
            if($sew_excess_qty>0){
                $sew_arr['target_value']=$sew_per_day_qty+1;
                OrderProduction::where($sew_whereConditions)->limit($sew_excess_qty)->update($sew_arr);
            }

            //Pack Data
            $pack_whereConditions =[
                ['workspace_id','=',$request->workspace_id],
                ['company_id', '=', $request->company_id],
                ['order_id','=',$request->order_id],
                ['type_of_production','=','Pack'],
                ['holiday_flag','=','0'],
            ];
            $pack_count = OrderProduction::where($pack_whereConditions)->count();
            $pack_per_day_qty = floor($sku_act_tot_qty / $pack_count);
            $pack_excess_qty = $sku_act_tot_qty % $pack_count;
            $pack_arr=[];
            $pack_arr['target_value']=$pack_per_day_qty;
            OrderProduction::where($pack_whereConditions)->update($pack_arr);
            if($pack_excess_qty>0){
                $pack_arr['target_value']=$pack_per_day_qty+1;
                OrderProduction::where($pack_whereConditions)->limit($pack_excess_qty)->update($pack_arr);
            }

        }


        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"SKU's Updated Successfully"]);
        return CommonApp::webEncrypt($res);
    }

}
