<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class SamQuantity extends Model
{
    use HasFactory;

    protected $table = 'sam_quantity_details';

    public static function save_quantity_data($request){
        $data=[];
        $data['report_id'] = $request->report_id??0;
        $data['time_slot'] = $request->time_slot??NULL;
        $data['quantity'] = $request->quantity??0;
        $data['comments'] = $request->comments??NULL;
        $data['user_id'] = $request->user_id??0;
        $data['staff_id'] = $request->staff_id??0;
        SamQuantity::insert($data);
    }
    public static function get_sam_quantity($request){
        return SamQuantity::where('report_id', '=', $request->report_id)
                ->select('time_slot','quantity','comments')
                ->get();
    }
    public static function get_sam_added_report_times($request){
        return SamQuantity::where('report_id', '=', $request->report_id)
                ->pluck('time_slot');
    }

}
