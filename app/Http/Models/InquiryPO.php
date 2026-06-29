<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class InquiryPO extends Model
{
    use HasFactory;

    protected $table = "inquiry_po";

    /* Get All the Inquiry PO */
    static function get_all_po($whereConditions,$request){

        if(isset($request->factory_id) && $request->factory_id!=''){
            $whereConditions[]=['inquiry_contact.id',"=",$request->factory_id];
        }
        if(isset($request->po_id) && $request->po_id!=''){
            $whereConditions[]=['inquiry_po.id',"=",$request->po_id];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date==''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['inquiry_po.created_at','>=',$from];
            $whereConditions[]=['inquiry_po.created_at','<=',$to];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date!=''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->to_date));
            $whereConditions[]=['inquiry_po.created_at','>=',$from];
            $whereConditions[]=['inquiry_po.created_at','<=',$to];
        }

        $allpo = InquiryPO::where($whereConditions)
        ->leftjoin('inquiry_contact','inquiry_contact.id','inquiry_po.factory_id')
        ->select('inquiry_po.id as po_id','inquiry_po.factory_id','inquiry_po.inquiry_id','inquiry_contact.factory',
        'inquiry_po.po_status  as po_status')
        ->orderBy('inquiry_po.id','DESC')
        ->paginate(20, ['*'], 'page', $request->page);
        // ->get();

        return $allpo;
    }

    /* Get PO Details */
    static function get_po_details($request){
        $whereConditions=[
            ['inquiry_po.id','=',$request->po_id],
        ];

        $inquiries = InquiryPO::where($whereConditions)
        ->leftjoin('order_article_name','order_article_name.id','inquiry_po.article_id')
        ->leftjoin('fabric_type','fabric_type.id','inquiry_po.fabric_type_id')
        ->leftjoin('income_terms','income_terms.id','inquiry_po.incoterms')
        ->select('inquiry_po.*','order_article_name.name as article_name','fabric_type.name as fabric_composition','income_terms.name as income_terms',
        DB::raw('DATE_FORMAT(inquiry_po.created_at,"%Y-%m-%d") as created_date'))
        ->get();
       // Log::info('Showing the get_po_details: '.$inquiries);
       // dd($inquiries);
        return $inquiries;
    }

    /* Cancel PO */
        static function cancel_po($request){
        $whereConditions=[
            ['id','=',$request->po_id]
        ];
        $poToCancel = InquiryPO::where($whereConditions)->first();
        $factory = InquiryFactoryResponse::where("inquiry_id",$poToCancel->inquiry_id)
        ->where('factory_contact_id',$poToCancel->factory_id)
        ->first();

        $inquiry = Inquiries::where("id",$poToCancel->inquiry_id)->first();
        $poID = $poToCancel->id;
        try{
            if(!empty($factory)){
            $factory->is_po_generated =2;
            $factory->save();
            }
            $poToCancel->po_status = "2";
            $poToCancel->save();
            $inquiry->is_po_generated = 0;
            $inquiry->save();
            /* Mail Cancelled Status */
            if(!empty($factory)){
            $details['poID']=$poID;
            $details['created_by'] = $factory->contact_person;
            $details["email"] = $factory->contact_email;
            $details["title"] = 'PO Cancellation Notice';

            if($factory->contact_email){
                Mail::send('InquiryPOCancelMail', ['details'=>$details], function($message)use($details) {
                $message->to($details["email"])
                        ->subject($details["title"]);
                });
            }
        }
        }catch(Exception $e){
            throw new InvalidArgumentException($e);
        }
        return true;
    }
    public static function get_inquiry_label($request){
        $res = InquiryPO::where('inquiry_po.id',$request->inquiry_id)
            ->leftjoin('inquiry_po_media','inquiry_po.id','inquiry_po_media.po_id')
            ->leftjoin('order_article_name','order_article_name.id','inquiry_po.article_id')
            ->leftjoin('order_category','order_category.id','inquiry_po.category_id')
            ->leftjoin('fabric_type','fabric_type.id','inquiry_po.fabric_type_id')
            ->select('inquiry_po.id as po_id','inquiry_po.inquiry_id as id','print_type','print_no_of_colors','print_size','main_lable','main_lable_info','washcare_lable','washcare_lable_info','hangtag_lable',
            'hangtag_lable_info','barcode_lable','barcode_lable_info','poly_bag_size','poly_bag_material','poly_bag_price','poly_bag_print','carton_bag_dimensions',
            'carton_color','carton_material','carton_edge_finish','carton_mark','media_type','filepath','inquiry_po.style_no','order_article_name.name as article',
            'fabric_type.name as fabric_composition','order_category.name as category','inquiry_po.created_at as inq_date','inquiry_po.inquiry_id')
            ->get();

        return $res;
    }
}
