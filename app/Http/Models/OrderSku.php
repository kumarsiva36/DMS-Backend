<?php

namespace App\Models;

use App\Common\CommonApp;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderSku extends Model
{
    use HasFactory;

    protected $table = "order_sku";

    public static function addSKU($request){
        DB::beginTransaction();
        try{
            $orderId= $request->order_id;
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
            /*Update Total Qty*/
            $total_quantity=$request->total_qty;
            if($total_quantity>0){
                Order::where($whereConditionsOrd)->update(["total_quantity" =>$total_quantity]);
            }
            $orderSkuArr = [];
            $orderSkuArr['user_id']= $companyDetails->user_id;
            $orderSkuArr['company_id']= $request->company_id;
            $orderSkuArr['workspace_id']= $request->workspace_id;
            $orderSkuArr['staff_id']= $request->staff_id??0;
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
            $addOrderArr['step_level'] = '2';
            if((isset($request->tolPerc) && isset($request->tolVol)) && $request->tolPerc > 0 && $request->tolVol >0){
                $addOrderArr['tolerance_perc'] = $request->tolPerc;
                $addOrderArr['tolerance_volume'] = $request->tolVol;
            }
            Order::where('id',$orderId)->update($addOrderArr);
        }catch(Exception $e){
            DB::rollback();
            throw new InvalidArgumentException("Unable to Post Data");
        }
        DB::commit();
    }

    public static function getSKU($whereConditions){
        $skuDetails = OrderSku::where($whereConditions)->get();
		$arr=array();$i=0;
    	foreach ($skuDetails as $value) {
            $getColorId=$value->sku_color_id;
            $getColorName=Color::getColorNameUsingId($getColorId);
            $getSizeId=$value->sku_size_id;
            $getSizeName=Size::getSizeNameUsingId($getSizeId);
    		$arr[$i]['color_id']=$getColorId;
            $arr[$i]['color_name']=$getColorName['name'];
    		$arr[$i]['size_id']=$getSizeId;
            $arr[$i]['size_name']=$getSizeName['name'];
            $arr[$i]['category']=$getSizeName['category'];
    		$arr[$i]['quantity']=$value->sku_quantity;
    		$i++;

		}
        return $arr;
    }

    public static function order_sku($request){
        $whereConditions=[
            ['order_sku.order_id','=',$request->order_id],

        ];
        $sku = OrderSku::where($whereConditions)
        ->leftjoin('color','order_sku.sku_color_id','color.id')
        ->leftjoin('size','order_sku.sku_size_id','size.id')
        ->select('order_sku.sku_quantity as quantity','color.id as color_id','color.name as color','size.id as size_id','size.name as size','size.category as category')
        ->get()->toArray();

        return $sku;
    }
}
