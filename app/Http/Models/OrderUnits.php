<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class OrderUnits extends Model
{
    use HasFactory;

    protected $table = 'order_units';

    public static function get_order_units($request,$type=''){
        $whereCondition=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['status','!=',"3"]
            ];
        // if($type=='bom_units'){
        //     $whereCondition[]=['bom_unit','=',1];
        // }else{
        //     $whereCondition[]=['bom_unit','=',0];
        // }

        $getUnits = OrderUnits::where($whereCondition)->orWhere('is_default', '=', '0')->select('id','name','bom_unit')->orderBy('name','asc')->get()->toArray();

        if($type=='bom_units'){
            $getUnits = (array_filter($getUnits, function ($var) {
                return ($var['bom_unit'] == 1);
            }));
        }else{
            $getUnits = (array_filter($getUnits, function ($var) {
                return ($var['bom_unit'] == 0);
            }));
        }
        return $getUnits;
    }





}
