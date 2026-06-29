<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechPackComments extends Model
{
    use HasFactory;
    protected $table = 'techpack_comments';
    protected $fillable = [
        'company_id','workspace_id','user_id','staff_id','techpack_id','techpack_type','comment_type','comment_data','created_at','updated_at'
    ];

    public static function updateAdminReadStatus($techpack_id,$techpack_type){
        if($techpack_type!=''){
            $updateArr['admin_read'] = 1;
            TechPackComments::where('techpack_id','=',$techpack_id)->where('techpack_type','=',$techpack_type)->update($updateArr);
        }else{
            $updateArr['admin_read'] = 1;
            TechPackComments::where('techpack_id','=',$techpack_id)->update($updateArr);
        }
    }

    public static function updateStaffReadStatus($techpack_id,$techpack_type,$staff_id){
        if($techpack_type!=''){
            $res = TechPackComments::select('staff_read','id')
            ->where('techpack_id','=',$techpack_id)->where('techpack_type','=',$techpack_type)
            ->whereRaw("NOT FIND_IN_SET(?, techpack_comments.staff_read)",[$staff_id])
            ->get();
            foreach($res as $r){
                $staff_read = $r->staff_read.",".$staff_id;
                $updateArr['staff_read'] = $staff_read;
                TechPackComments::where('id','=',$r->id)->update($updateArr);
            }

        }else{
            $res = TechPackComments::select('staff_read','id')
            ->where('techpack_id','=',$techpack_id)
            ->whereRaw("NOT FIND_IN_SET(?, techpack_comments.staff_read)",[$staff_id])
            ->get();
            foreach($res as $r){
                $staff_read = $r->staff_read.",".$staff_id;
                $updateArr['staff_read'] = $staff_read;
                TechPackComments::where('id','=',$r->id)->update($updateArr);
            }
        }
    }
}
