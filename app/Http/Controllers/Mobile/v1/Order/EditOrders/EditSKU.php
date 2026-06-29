<?php

namespace App\Http\Controllers\Mobile\v1\Order\EditOrders;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\OrderSku;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EditSKU extends Controller
{
    /****************** Edit the SKU's ********************/
    public static function editSku (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
            'sku' => 'required|array'
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }

        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id]
        ];
        $whereConditionsOrd =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['id','=',$request->order_id]
        ];
        $aldreadyExists = OrderSku::where($whereConditions)->get();
        if(!empty($aldreadyExists)){
            OrderSku::where($whereConditions)->delete();
        }
        $total_quantity=$request->total_qty;
        if($total_quantity>0){
            Order::where($whereConditionsOrd)->update(["total_quantity" =>$total_quantity]);
        }
        $orderSkuArr = [];
        $orderSkuArr['user_id']= $companyDetails->user_id;
        $orderSkuArr['company_id']= $request->company_id;
        $orderSkuArr['workspace_id']= $request->workspace_id;
        $orderSkuArr['staff_id']= $request->input('staff_id','0');
        $orderSkuArr['order_id']= $request->order_id;
        foreach ($request->sku as $sku){
            // dd($sku);
            $orderSkuArr['sku_color_id']=$sku['color_id'];
            $orderSkuArr['sku_size_id']=$sku['size_id'];
            $orderSkuArr['sku_quantity']=$sku['quantity'];
            $orderSkuArr['created_at']=date('Y-m-d H:i:s');
            $orderSkuArr['updated_at']=date('Y-m-d H:i:s');
            OrderSku::insert($orderSkuArr);
        }

        $addOrderArr=[];

        if($request->tolPerc > 0 && $request->tolVol >0){
            $addOrderArr['tolerance_perc'] = $request->tolPerc;
            $addOrderArr['tolerance_volume'] = $request->tolVol;
            Order::where('id',$request->order_id)->update($addOrderArr);
        }

        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"SKU's Updated Successfully"]);
    }

}
