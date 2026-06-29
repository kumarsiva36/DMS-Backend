<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class OrderMaterialsLabel extends Model
{
    use HasFactory;

    protected $table = 'order_material_label';

    public static function get_materials_details($request){
        $whereConditions = [
            ['order_id','=',$request->order_id]
        ] ;
        $data = OrderMaterialsLabel::where($whereConditions)->first();
        return $data;
    }

    public static function get_order_materials_label($request){
        $data = OrderMaterialsLabel::where('order_material_label.order_id',$request->order_id)
        ->leftjoin('order_media','order_media.order_id','order_material_label.order_id')
        ->get()->toArray();
        return $data;
    }



}
