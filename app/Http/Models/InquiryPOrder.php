<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use App\Models\InquiryPOrderTranslate;
use App\Models\TechPack;
use App\Models\Inquiries;
class InquiryPOrder extends Model
{
    use HasFactory;

    protected $table = "inquiry_new_po";

    /* Get All the Inquiry PO */
    static function get_all_po($whereConditions,$request,$pdf=0){

        if(isset($request->factory_id) && $request->factory_id!=''){
            $whereConditions[]=['inquiry_contact.id',"=",$request->factory_id];
        }
        if(isset($request->po_id) && $request->po_id!=''){
            $whereConditions[]=['inquiry_new_po.id',"=",$request->po_id];
        }
        if(isset($request->fabric_type_id) && $request->fabric_type_id!=''){
            $whereConditions[]=['inquiry_new_po.fabric_type_id',"=",$request->fabric_type_id];
        }
        if(isset($request->incoterms_id) && $request->incoterms_id!=''){
            $whereConditions[]=['inquiry_new_po.incoterms',"=",$request->incoterms_id];
        }

        if($pdf==0){
            $allpo = InquiryPOrder::where($whereConditions)
            ->leftjoin('order_article_name','order_article_name.id','inquiry_new_po.article_id')
            ->leftjoin('fabric_type','fabric_type.id','inquiry_new_po.fabric_type_id')
            ->leftjoin('income_terms','income_terms.id','inquiry_new_po.incoterms')
            ->select('inquiry_new_po.id as po_id','inquiry_new_po.style_no','order_article_name.name','inquiry_new_po.status','inquiry_new_po.article_id',
            'fabric_type_id','fabric_type.name as fabric','inquiry_new_po.incoterms as incoterms_id','income_terms.name as incoterms_name',
            DB::raw('DATE_FORMAT(inquiry_new_po.created_at,"%Y-%m-%d") as created_date'),'po_number')
            ->orderBy('inquiry_new_po.id','DESC')
            ->paginate(20, ['*'], 'page', $request->page);
            // ->get();
        }else{
            $allpo = InquiryPOrder::where($whereConditions)
            ->leftjoin('order_article_name','order_article_name.id','inquiry_new_po.article_id')
            ->leftjoin('fabric_type','fabric_type.id','inquiry_new_po.fabric_type_id')
            ->leftjoin('income_terms','income_terms.id','inquiry_new_po.incoterms')
            ->select('inquiry_new_po.id as po_id','inquiry_new_po.style_no','order_article_name.name','inquiry_new_po.status','inquiry_new_po.article_id',
            'fabric_type_id','fabric_type.name as fabric','inquiry_new_po.incoterms as incoterms_id','income_terms.name as incoterms_name',
            DB::raw('DATE_FORMAT(inquiry_new_po.created_at,"%Y-%m-%d") as created_date'),'po_number')
            ->orderBy('inquiry_new_po.id','DESC')->get();
        }
        return $allpo;
    }
    static function get_all_po_articles($whereConditions){

            $allpo = InquiryPOrder::where($whereConditions)
            ->join('order_article_name','order_article_name.id','inquiry_new_po.article_id')
            ->select(DB::raw('DISTINCT(inquiry_new_po.article_id) as article_id'),'order_article_name.name')
            ->orderBy('order_article_name.name','ASC')->get();

        return $allpo;
    }

    /* Get PO Details */
    static function get_po_details($request){
        $whereConditions=[
            ['inquiry_new_po.id','=',$request->po_id],
        ];

        $inquiries = InquiryPOrder::where($whereConditions)
        ->leftjoin('order_article_name','order_article_name.id','inquiry_new_po.article_id')
        ->leftjoin('fabric_type','fabric_type.id','inquiry_new_po.fabric_type_id')
        ->leftjoin('fabric_composition','fabric_composition.id','inquiry_new_po.fabric_composition_id')
        ->leftjoin('currencies','currencies.name','inquiry_new_po.currency')
        ->leftjoin('income_terms','income_terms.id','inquiry_new_po.incoterms')
        ->leftjoin('order_units','order_units.id','inquiry_new_po.units')
        ->leftjoin('order_units as price_unit','price_unit.id','inquiry_new_po.price_units')
        ->select('inquiry_new_po.*','order_article_name.name as article_name','fabric_type.name as fabric_type','fabric_composition.name as fabric_composition_name','income_terms.name as income_terms',
        'order_units.name as unit_name','currencies.short_code as currency_short_code','price_unit.name as price_unit_name',
        DB::raw('DATE_FORMAT(inquiry_new_po.created_at,"%Y-%m-%d") as created_date'))
        ->get();
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
        $res = InquiryPO::where('inquiry_new_po.id',$request->inquiry_id)
            ->leftjoin('inquiry_new_po_media','inquiry_new_po.id','inquiry_new_po_media.po_id')
            ->leftjoin('order_article_name','order_article_name.id','inquiry_new_po.article_id')
            ->leftjoin('order_category','order_category.id','inquiry_new_po.category_id')
            ->leftjoin('fabric_type','fabric_type.id','inquiry_new_po.fabric_type_id')
            ->select('inquiry_new_po.id as po_id','inquiry_new_po.inquiry_id as id','print_type','print_no_of_colors','print_size','main_lable','main_lable_info','washcare_lable','washcare_lable_info','hangtag_lable',
            'hangtag_lable_info','barcode_lable','barcode_lable_info','poly_bag_size','poly_bag_material','poly_bag_price','poly_bag_print','carton_bag_dimensions',
            'carton_color','carton_material','carton_edge_finish','carton_mark','media_type','filepath','inquiry_new_po.style_no','order_article_name.name as article',
            'fabric_type.name as fabric_composition','order_category.name as category','inquiry_new_po.created_at as inq_date','inquiry_new_po.inquiry_id')
            ->get();

        return $res;
    }

    /* Get PO Details */
    static function get_po_details_multi($request,$table=''){
        $language = isset($request->language) ? $request->language : 'en';
        //$language = 'en';
        $count = InquiryPOrder::where('parent_id',$request->po_parent_id)->where('language',$language)->count();
        $count = ($table=='main')?1:$count;
        if($count > 0){
            $whereConditions[]=['inquiry_new_po.parent_id','=',$request->po_parent_id];
            if(isset($request->po_id) && $request->po_id > 0){
                $whereConditions[]=['inquiry_new_po.id','=',$request->po_id];
            }
            $inquiries = InquiryPOrder::where($whereConditions)
            ->leftjoin('order_article_name','order_article_name.id','inquiry_new_po.article_id')
            ->leftjoin('fabric_type','fabric_type.id','inquiry_new_po.fabric_type_id')
            ->leftjoin('fabric_composition','fabric_composition.id','inquiry_new_po.fabric_composition_id')
            ->leftjoin('currencies','currencies.name','inquiry_new_po.currency')
            ->leftjoin('income_terms','income_terms.id','inquiry_new_po.incoterms')
            ->leftjoin('order_units','order_units.id','inquiry_new_po.units')
            ->leftjoin('order_units as price_unit','price_unit.id','inquiry_new_po.price_units')
            ->leftjoin('inquiry_master','inquiry_master.id','inquiry_new_po.testing_cost')
            ->leftjoin('inquiry_po_forwarder','inquiry_po_forwarder.id','inquiry_new_po.forwarder_id')
            ->leftjoin('techpack','techpack.po_id','inquiry_new_po.id')
            ->select('inquiry_new_po.*','order_article_name.name as article_name','fabric_type.name as fabric_type','fabric_composition.name as fabric_composition_name','income_terms.name as income_terms',
            'order_units.name as unit_name','currencies.short_code as currency_short_code','price_unit.name as price_unit_name','inquiry_master.content as testing_costs','inquiry_po_forwarder.company_name',
            'inquiry_po_forwarder.address','inquiry_po_forwarder.contact_person','inquiry_po_forwarder.contact_phone','inquiry_po_forwarder.contact_email',
            DB::raw('DATE_FORMAT(inquiry_new_po.created_at,"%Y-%m-%d") as created_date'),
            DB::raw('FORMAT(inquiry_new_po.price,2) as price'),'inquiry_new_po.updated_at as updated_date','techpack.id as techpack_id')
            ->orderBy('inquiry_new_po.id','ASC')
            ->get();
        }else{
            $whereConditions=[
                ['inquiry_new_po_translate.parent_id','=',$request->po_parent_id],
            ];
            $inquiries = InquiryPOrderTranslate::where($whereConditions)
            ->leftjoin('order_article_name','order_article_name.id','inquiry_new_po_translate.article_id')
            ->leftjoin('fabric_type','fabric_type.id','inquiry_new_po_translate.fabric_type_id')
            ->leftjoin('fabric_composition','fabric_composition.id','inquiry_new_po_translate.fabric_composition_id')
            ->leftjoin('currencies','currencies.name','inquiry_new_po_translate.currency')
            ->leftjoin('income_terms','income_terms.id','inquiry_new_po_translate.incoterms')
            ->leftjoin('order_units','order_units.id','inquiry_new_po_translate.units')
            ->leftjoin('order_units as price_unit','price_unit.id','inquiry_new_po_translate.price_units')
            ->leftjoin('inquiry_master','inquiry_master.id','inquiry_new_po_translate.testing_cost')
            ->leftjoin('inquiry_po_forwarder','inquiry_po_forwarder.id','inquiry_new_po_translate.forwarder_id')
            ->leftjoin('techpack','techpack.po_id','inquiry_new_po_translate.id')
            ->select('inquiry_new_po_translate.*','order_article_name.name as article_name','fabric_type.name as fabric_type','fabric_composition.name as fabric_composition_name','income_terms.name as income_terms',
            'order_units.name as unit_name','currencies.short_code as currency_short_code','price_unit.name as price_unit_name','inquiry_master.content as testing_costs','inquiry_po_forwarder.company_name',
            'inquiry_po_forwarder.address','inquiry_po_forwarder.contact_person','inquiry_po_forwarder.contact_phone','inquiry_po_forwarder.contact_email',
            DB::raw('DATE_FORMAT(inquiry_new_po_translate.created_at,"%Y-%m-%d") as created_date'),
            DB::raw('FORMAT(inquiry_new_po.price,2) as price'),'inquiry_new_po_translate.updated_at as updated_date','techpack.id as techpack_id')
            ->orderBy('inquiry_new_po_translate.id','ASC')
            ->get();
        }

        return $inquiries;
    }

    /* Get All the Inquiry PO */
    static function get_all_po_multiple($whereConditions,$request,$pdf=0){

        if(isset($request->factory_id) && $request->factory_id!=''){
            $whereConditions[]=['inquiry_contact.id',"=",$request->factory_id];
        }
        if(isset($request->po_id) && $request->po_id!=''){
            $whereConditions[]=['inquiry_new_po.id',"=",$request->po_id];
        }
        if(isset($request->fabric_type_id) && $request->fabric_type_id!=''){
            $whereConditions[]=['inquiry_new_po.fabric_type_id',"=",$request->fabric_type_id];
        }
        if(isset($request->incoterms_id) && $request->incoterms_id!=''){
            $whereConditions[]=['inquiry_new_po.incoterms',"=",$request->incoterms_id];
        }

        $whereConditions[]=['inquiry_new_po.parent_id',">","0"];

        if($pdf==0){
            $allpo = InquiryPOrder::where($whereConditions)
            ->leftjoin('order_article_name','order_article_name.id','inquiry_new_po.article_id')
            ->leftjoin('fabric_type','fabric_type.id','inquiry_new_po.fabric_type_id')
            ->leftjoin('income_terms','income_terms.id','inquiry_new_po.incoterms')
            ->leftjoin('inquiry_new_po_translate','inquiry_new_po_translate.parent_id','inquiry_new_po.parent_id')
            ->select('inquiry_new_po.id as po_id','inquiry_new_po.style_no','order_article_name.name','inquiry_new_po.status','inquiry_new_po.article_id',
            'inquiry_new_po.fabric_type_id','fabric_type.name as fabric','inquiry_new_po.incoterms as incoterms_id','income_terms.name as incoterms_name',
            DB::raw('DATE_FORMAT(inquiry_new_po.created_at,"%Y-%m-%d") as created_date'),'inquiry_new_po.po_number','inquiry_new_po.parent_id',DB::raw('GROUP_CONCAT( DISTINCT inquiry_new_po.style_no ) as styles'),DB::raw('GROUP_CONCAT( DISTINCT inquiry_new_po.id ) as style_ids'),
            DB::raw('GROUP_CONCAT( DISTINCT order_article_name.name ORDER BY order_article_name.id) as articles'),DB::raw('GROUP_CONCAT( DISTINCT fabric_type.name ORDER BY fabric_type.id) as fabrics'),
            DB::raw('GROUP_CONCAT( DISTINCT order_article_name.id ) as article_ids'),DB::raw('GROUP_CONCAT( DISTINCT fabric_type.id ) as fabric_ids'),DB::raw("SUBSTRING_INDEX(inquiry_new_po.buyer, '\n', 1) as buyer"),DB::raw("SUBSTRING_INDEX(inquiry_new_po.seller, '\n', 1) as seller"),DB::raw("SUBSTRING_INDEX(inquiry_new_po.maker, '\n', 1) as maker"),'inquiry_new_po.delivery_date',
            DB::raw('COUNT( inquiry_new_po_translate.id ) as translate'))
            ->orderBy('inquiry_new_po.id','DESC')
            ->groupBy('inquiry_new_po.parent_id')
            ->paginate(20, ['*'], 'page', $request->page);
            // ->get();
        }else{
            $allpo = InquiryPOrder::where($whereConditions)
            ->leftjoin('order_article_name','order_article_name.id','inquiry_new_po.article_id')
            ->leftjoin('fabric_type','fabric_type.id','inquiry_new_po.fabric_type_id')
            ->leftjoin('income_terms','income_terms.id','inquiry_new_po.incoterms')
            ->select('inquiry_new_po.id as po_id','inquiry_new_po.style_no','order_article_name.name','inquiry_new_po.status','inquiry_new_po.article_id',
            'fabric_type_id','fabric_type.name as fabric','inquiry_new_po.incoterms as incoterms_id','income_terms.name as incoterms_name',
            DB::raw('DATE_FORMAT(inquiry_new_po.created_at,"%Y-%m-%d") as created_date'),'po_number','parent_id',DB::raw("SUBSTRING_INDEX(buyer, '\n', 1) as buyer"),DB::raw("SUBSTRING_INDEX(seller, '\n', 1) as seller"),DB::raw("SUBSTRING_INDEX(maker, '\n', 1) as maker"),'inquiry_new_po.delivery_date')
            ->orderBy('inquiry_new_po.id','DESC')->get();
        }
        return $allpo;
    }

    static function get_inq_dashboard_details($request){
        $result = array();
        $year = intval($request->year);

        //For Inquiry
        $where_IN[]=['inquiry.company_id',"=",$request->company_id];
        $where_IN[]=['inquiry.workspace_id',"=",$request->workspace_id];

        //For PO
        $where_PO[]=['inquiry_new_po.company_id',"=",$request->company_id];
        $where_PO[]=['inquiry_new_po.workspace_id',"=",$request->workspace_id];

        //For Techpack
        $where_TP[]=['techpack.company_id',"=",$request->company_id];
        $where_TP[]=['techpack.workspace_id',"=",$request->workspace_id];

        $in_arr = Inquiries::where($where_IN)->whereRAW('YEAR(created_at) =?', [$year])->select(DB::raw("MONTH(created_at) month"), DB::raw("count(id) count"))->groupBy('month')->get();
        $po_arr = InquiryPOrder::where($where_PO)->whereRAW('YEAR(created_at) =?', [$year])->select(DB::raw("MONTH(created_at) month"), DB::raw("count(id) count"))->groupBy('month')->get();
        $tp_arr = TechPack::where($where_TP)->whereRAW('YEAR(created_at) =?', [$year])->select(DB::raw("MONTH(created_at) month"), DB::raw("count(id) count"))->groupBy('month')->get();

        $data=array();
        for($i=1;$i<=12;$i++){
            $data[$i-1]['name'] = date("M", mktime(0, 0, 0, $i, 10));
            $data[$i-1]['PO'] = 0;
            $data[$i-1]['TechPack'] = 0;
            $data[$i-1]['Inquiry'] = 0;
            foreach($in_arr as $in){
                if($in->month == $i)
                    $data[$i-1]['Inquiry'] =  $in->count ;
            }
            foreach($po_arr as $po){
                if($po->month == $i)
                    $data[$i-1]['PO'] =  $po->count ;
            }
            foreach($tp_arr as $tp){
                if($tp->month == $i)
                    $data[$i-1]['TechPack'] =  $tp->count;
            }
        }
        $result['overview'] = $data;
        $result['in_count'] = Inquiries::where($where_IN)->count();
        $result['po_count'] = InquiryPOrder::where($where_PO)->count();
        $result['tp_count'] = TechPack::where($where_TP)->count();

        $po_arr5 =  InquiryPOrder::where($where_PO)
        ->leftjoin('order_article_name','order_article_name.id','inquiry_new_po.article_id')
        ->leftjoin('fabric_type','fabric_type.id','inquiry_new_po.fabric_type_id')
        ->leftjoin('income_terms','income_terms.id','inquiry_new_po.incoterms')
        ->select('inquiry_new_po.id as po_id','inquiry_new_po.style_no','order_article_name.name','inquiry_new_po.status','inquiry_new_po.article_id',
        'fabric_type_id','fabric_type.name as fabric','inquiry_new_po.incoterms as incoterms_id','income_terms.name as incoterms_name',
        DB::raw('DATE_FORMAT(inquiry_new_po.created_at,"%Y-%m-%d") as created_date'),'po_number','parent_id',DB::raw('GROUP_CONCAT( DISTINCT inquiry_new_po.style_no ) as styles'),
        DB::raw('GROUP_CONCAT( DISTINCT order_article_name.name ORDER BY order_article_name.id) as articles'),DB::raw('GROUP_CONCAT( DISTINCT fabric_type.name ORDER BY fabric_type.id) as fabrics'),
        DB::raw('GROUP_CONCAT( DISTINCT order_article_name.id ) as article_ids'),DB::raw('GROUP_CONCAT( DISTINCT fabric_type.id ) as fabric_ids'),DB::raw("SUBSTRING_INDEX(buyer, '\n', 1) as buyer"),DB::raw("SUBSTRING_INDEX(seller, '\n', 1) as seller"),DB::raw("SUBSTRING_INDEX(maker, '\n', 1) as maker"),'inquiry_new_po.delivery_date')
        ->orderBy('inquiry_new_po.id','DESC')
        ->groupBy('inquiry_new_po.parent_id')
        ->offset(0)->limit(4)->get();

        $tp_arr5 = TechPack::where($where_TP)->select('po_no','style_no','article_name','fabric_name','is_publish',DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d") as created_date'))
        ->orderBy('id','DESC')->offset(0)->limit(4)->get();
        $result['po_array'] =  $po_arr5;
        $result['tp_array'] =  $tp_arr5;
        $po_art = InquiryPOrder::where($where_PO)->whereRAW('YEAR(created_at) =?', [$year])->select('article_id','article_name', DB::raw("count(article_id) count"))->groupBy('article_id')->get();
        $po_count = InquiryPOrder::where($where_PO)->whereRAW('YEAR(created_at) =?', [$year])->count();

        $art_array = array();
        foreach ($po_art as $key => $pa){
            $art_array[$key]['article'] = $pa->article_name;
            $art_array[$key]['percentage'] = round(($pa->count / $po_count)*100,0);
        }
        $result['pie_chart'] =  $art_array;
        return $result;
    }
}
