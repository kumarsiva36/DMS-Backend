<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use App\Models\Inquiries;
use App\Models\InquiryContact;

class InquiryFactoryResponse extends Model
{
    use HasFactory;

    protected $table = 'inquiry_factory_response';

    public static function FactoryResponse($request){

        $request->user_id =(int)$request->user_id;
        $count = InquiryFactoryResponse::where('inquiry_id','=',$request->inquiry_id)
        ->whereRaw('FIND_IN_SET('.$request->user_id.', notification_read_by)')
        ->count();
        //if($count ==0){
            DB::table('inquiry_factory_response')
            ->where('inquiry_id', $request->inquiry_id)
           // ->limit(1)
            ->update(array('notification_read_by' => DB::raw("concat(ifnull(notification_read_by,','), ',".$request->user_id."')")));
        //}

        $whereConditions=[
            ['inquiry_factory_response.inquiry_id','=',$request->inquiry_id]
        ];
       $res = InquiryFactoryResponse::where($whereConditions)
              ->join('inquiry_contact','inquiry_contact.id','inquiry_factory_response.factory_contact_id')
              ->select('inquiry_factory_response.factory_id','inquiry_factory_response.price',
              'inquiry_factory_response.comments','factory','contact_person','contact_number',
              'contact_email','inquiry_factory_response.inquiry_id','inquiry_factory_response.factory_contact_id'
              ,'is_po_generated')
              ->orderBy('inquiry_factory_response.created_at','desc')
              ->get();
        return $res;
    }

    public static function get_factory_list_response($request){
        $whereConditions=[
            ['id','=',$request->inquiry_id]
        ];

        $res = Inquiries::where($whereConditions)->pluck('factory_ids')->first();

        $fact_ids = array_unique(explode('||',$res));

        $factories = InquiryContact::whereIn('inquiry_contact.id',$fact_ids)
                    ->whereNotIn('inquiry_contact.id',function($query) use($request)
                    {
                        $query->select('inquiry_factory_response.factory_contact_id')
                            ->from('inquiry_factory_response')
                            ->whereRaw('inquiry_id = '.$request->inquiry_id);
                    })
                    ->where('inquiry_contact.factory_id','0')
                    ->select('inquiry_contact.id','factory')->get();
        return $factories;
    }

    public static function download_factory_response($whereConditions){
        $res = InquiryFactoryResponse::where($whereConditions)
              ->join('inquiry_contact','inquiry_contact.id','inquiry_factory_response.factory_contact_id')
              ->join('inquiry','inquiry.id','inquiry_factory_response.inquiry_id')
              ->select('inquiry_factory_response.factory_id','inquiry_factory_response.price','inquiry_factory_response.comments',
              'factory','contact_person','contact_number','contact_email','inquiry_factory_response.inquiry_id','inquiry.currency')
              ->orderBy('inquiry_factory_response.created_at','desc')
              ->get();
        return $res;
    }
}
