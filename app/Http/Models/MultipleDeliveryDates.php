<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class MultipleDeliveryDates extends Model
{
    use HasFactory;

    protected $table="multiple_delivery_dates";


    public static function addMultipleDeliveryDates($request,$orderID,$dates){
        try{
            $datesArr['company_id']=$request->company_id;
            $datesArr['workspace_id']=$request->workspace_id;
            $datesArr['order_id']=$orderID;
            foreach($dates as $date){
                $datesArr['delivery_date']=$date;
                $datesArr['created_at']=date('Y-m-d H:i:s');
                $datesArr['updated_at']=date('Y-m-d H:i:s');
                MultipleDeliveryDates::insert($datesArr);
            }
        }catch(Exception $e){
            Log::info($e->getMessage());
            throw new InvalidArgumentException("unable to Insert Dates");
        }
    }
    public static function updateMultipleDeliveryDates($request,$orderID,$dates){
        if(count($dates)>0){
        try{
          
            MultipleDeliveryDates::where('order_id',$request->order_id)->delete();
            $datesArr['company_id']=$request->company_id;
            $datesArr['workspace_id']=$request->workspace_id;
            $datesArr['order_id']=$orderID;
            foreach($dates as $date){
                $datesArr['delivery_date']=$date;
                $datesArr['created_at']=date('Y-m-d H:i:s');
                $datesArr['updated_at']=date('Y-m-d H:i:s');
                MultipleDeliveryDates::insert($datesArr);
            }
        }catch(Exception $e){
            Log::info($e->getMessage());
            throw new InvalidArgumentException("unable to Insert Dates");
        }
    }
    }
}
