<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class PartialDelivery extends Model
{
    use HasFactory;

    protected $table="partial_deliveries";

    public static function addPartialDeliveries($request){
        $deliveryDate = date('Y-m-d',strtotime($request->delivery_date));
        $totalQuantity = 0;
        DB::beginTransaction();
        try{
            $whereConditions=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['order_id','=',$request->order_id],
                ['delivery_date','=',$deliveryDate],
            ];
            /* To change the status of the date to delivered */
            $theDate = MultipleDeliveryDates::where($whereConditions)->first();
            if($theDate->is_delivered == 1){
                throw new InvalidArgumentException("Updated");
            }
            $arr['order_id']=$request->order_id;
            $arr['company_id']=$request->company_id;
            $arr['workspace_id']=$request->workspace_id;
            $arr['user_type']=ucfirst($request->user_type); /* User or Staff */
            $arr['user_id']=$request->user_id;
            $arr['staff_id']=$request->staff_id;
            $arr['delivery_date']=$deliveryDate;
            $arr['delivery_comments']=$request->comments != ""?$request->comments :null;
            foreach($request->sku_data as $sku){
                $arr['color_id']=$sku->color_id;
                $arr['size_id']=$sku->size_id;
                $arr['quantity']=$sku->quantity;
                $arr['created_at']=date('Y-m-d H:i:s');
                $arr['updated_at']=date('Y-m-d H:i:s');
                PartialDelivery::insert($arr);
                $totalQuantity += $sku->quantity;
            }
            $theDate->is_delivered = 1;
            $theDate->total_delivered_quantity = $totalQuantity;
            $theDate->delivery_comments = $request->comments != ""?$request->comments :null;
            $theDate->save();
        }catch(Exception $e){
            DB::rollBack();
            Log::info($e->getMessage());
            if($e->getMessage() === "Updated"){
                throw new InvalidArgumentException("Delivery Already Updated");
            }else{
                throw new InvalidArgumentException("Unable to Post Data");
            }
        }
        DB::commit();
    }
}
