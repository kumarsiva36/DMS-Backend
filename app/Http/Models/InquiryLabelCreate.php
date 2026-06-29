<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class InquiryLabelCreate extends Model
{
    use HasFactory;

    protected $table = 'inquiry_label_pdf_generate';

    public static function get_label_create_info($request){
        $info = array();
        $res = InquiryLabelCreate::where('inquiry_id',$request->inquiry_id)->select('user_name','staff_name','created_at')->first();
        if(empty($res)){
            $data = [];
            $data['company_id']=$request->company_id ?? 0;
            $data['workspace_id']=$request->workspace_id ?? 0;
            $data['inquiry_id']=$request->inquiry_id ?? 0;
            $data['user_id']=$request->user_id ?? 0;
            $data['staff_id']=$request->staff_id ?? 0;
            $info['user_name']=$data['user_name']=InquiryLabelCreate::get_user_name($data['user_id'])??'';
            $info['staff_name']=$data['staff_name']=InquiryLabelCreate::get_staff_name($data['staff_id'])??'';
            $info['date_created']=date('d.m.Y');
            InquiryLabelCreate::insert($data);
        }else{
            $info['user_name']=$res->user_name;
            $info['staff_name']=$res->staff_name;
            $info['date_created']=date('d.m.Y',strtotime($res->created_at));
        }
        return $info;
    }

    public static function get_user_name($id){
        return User::where('id',$id)->pluck('name')->first();
    }
    public static function get_staff_name($id){
        return Staff::where('id',$id)->pluck('first_name')->first();
    }
}
