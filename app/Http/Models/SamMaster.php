<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class SamMaster extends Model
{
    use HasFactory;

    protected $table = 'sam_master';

    public static function save_master_data($request){
        $data=[];
        $data['company_id'] = $request->company_id??0;
        $data['workspace_id'] = $request->workspace_id??0;
        $data['value'] = $request->value??NULL;
        $data['type'] = $request->type??NULL;
        $data['shift_from_time'] = $request->shift_from_time??NULL;
        $data['shift_end_time'] = $request->shift_end_time??NULL;
        $data['line_unit'] = $request->line_unit??0;
        $data['line_no_of_machines'] = $request->line_no_of_machines??0;
        $data['line_machine_type'] = $request->line_machine_type??NULL;
        $data['supervisor_id'] = $request->supervisor_id??NULL;
        $data['user_id'] = $request->user_id??0;
        $data['staff_id'] = $request->staff_id??0;
        SamMaster::insert($data);
    }

    public static function check_shift_exists($request){
        return SamMaster::where('workspace_id',$request->workspace_id)->where('company_id',$request->company_id)->where('type','Shift')->where('value',$request->value)->count();
    }
    public static function check_unit_exists($request){
        return SamMaster::where('workspace_id',$request->workspace_id)->where('company_id',$request->company_id)->where('type','Unit')->where('value',$request->value)->count();
    }
    public static function check_line_exists($request){
        return SamMaster::where('workspace_id',$request->workspace_id)->where('company_id',$request->company_id)->where('type','Line')->where('value',$request->value)
        ->where('line_unit',$request->line_unit)->count();
    }
    public static function check_supervisor_exists($request){
        return SamMaster::where('workspace_id',$request->workspace_id)->where('company_id',$request->company_id)->where('type','Supervisor')->where('value',$request->value)
        ->where('supervisor_id',$request->supervisor_id)->count();
    }

}
