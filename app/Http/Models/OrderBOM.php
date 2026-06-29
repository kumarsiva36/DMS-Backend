<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class OrderBOM extends Model
{
    use HasFactory;

    protected $table = 'order_bom';

    public static function get_materials_details($request){
        $whereConditions = [
            ['order_id','=',$request->order_id]
        ] ;
        $data = OrderBOM::where($whereConditions)->first();
        // $data->sewing_accessories = $data->sewing_accessories ? json_decode($data->sewing_accessories,true):[];
        // $data->packing_accessories = $data->packing_accessories ? json_decode($data->packing_accessories,true):[];
        // $data->miscellaneous = $data->miscellaneous ? json_decode($data->miscellaneous,true):[];
        //dd($data);
        return $data;
    }

    public static function get_order_materials_label($request){
        $data = OrderBOM::where('order_bom.order_id',$request->order_id)
        ->join('orders','orders.id','order_bom.order_id')
        ->leftjoin('users','users.id','order_bom.created_user_id')
        ->leftjoin('staff','staff.id','order_bom.created_staff_id')
        ->leftjoin('order_bom_approval_log','order_bom_approval_log.order_id','order_bom.order_id')
        //->leftjoin('order_media','order_media.order_id','order_bom.order_id')
        ->select('order_bom.*','orders.currency_type','orders.order_no','orders.style_no','users.name as user_name',DB::raw('CONCAT(first_name," ",last_name) as staffName'),'order_bom_approval_log.approval_date','order_bom_approval_log.approved_by')
        ->orderBy('order_bom_approval_log.id','DESC')
        ->get()->toArray();
        return $data;
    }



}
