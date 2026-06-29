<?php

namespace App\Models;

use App\Common\CommonApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\InquiryContact;
use App\Models\InquiryFactoryResponse;
use App\Models\InquiryMedia;
use App\Models\InquirySku;
use App\Models\InquiryAdditional;
use Illuminate\Support\Facades\Storage;
use App\Models\InquiryPO;
use Exception;


class Inquiries extends Model
{
    use HasFactory;

    protected $table = 'inquiry';

    public static function get_inquirys($whereConditions,$request){

        if(isset($request->article_id) && $request->article_id!=''){
            $whereConditions[]=['inquiry.article_id','=',$request->article_id];
        }
        if(isset($request->factory_id) && $request->factory_id!=''){
            $whereConditions[]=['inquiry.factory_ids','like','%||'.$request->factory_id.'||%'];
        }
        if(isset($request->po_factory_id) && $request->po_factory_id!=''){
            $whereConditions[]=['inquiry_factory_response.factory_contact_id',"=",$request->factory_id];
        }
        if(isset($request->po_generated) && $request->po_generated!=''){
            $whereConditions[]=['inquiry.is_po_generated',"=",$request->po_generated];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date==''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['inquiry.created_at','>=',$from];
            $whereConditions[]=['inquiry.created_at','<=',$to];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date!=''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->to_date));
            $whereConditions[]=['inquiry.created_at','>=',$from];
            $whereConditions[]=['inquiry.created_at','<=',$to];
        }

        $inquiries = Inquiries::where($whereConditions)
        ->leftjoin('order_article_name','order_article_name.id','inquiry.article_id')
        ->leftjoin('inquiry_factory_response', function($join) use ($request){
            $join->on('inquiry_factory_response.inquiry_id', '=', 'inquiry.id')
            ->where(function ($query) use ($request) {
                        $query->whereRaw('NOT FIND_IN_SET('.$request->user_id.', notification_read_by)')
                              ->orWhere('notification_read_by', '=', NULL);
                    });
        })
        ->select('inquiry.id','inquiry.style_no','order_article_name.name','order_article_name.id as article_id','inquiry.factory_ids',
        'inquiry_factory_response.inquiry_id as notification','inquiry.is_po_generated','fabric_type',
        DB::raw('DATE_FORMAT(inquiry.created_at,"%Y-%m-%d") as created_date'))
        ->orderBy('inquiry.created_at','desc')
        ->paginate(20, ['*'], 'page', $request->page);
        //->get();
        return $inquiries;
    }

    public static function inquiry_details($request){
        $whereConditions=[
            ['inquiry.id','=',$request->inquiry_id],
        ];

        if(isset($request->user_id)){
            $contact_id = InquiryContact::where('factory_id',$request->user_id)->pluck('id')->first();
            if($contact_id != null){
                $count = Inquiries::where($whereConditions)
                ->whereRaw('FIND_IN_SET('.$contact_id.', read_by_factories)')
                ->count();
                if($count == 0){
                    DB::table('inquiry')
                    ->where($whereConditions)
                    ->limit(1)
                    // ->update(array('read_by_factories' => DB::raw("concat(ifnull(read_by_factories,','), '".$contact_id."')")));
                    ->update(array('read_by_factories' => DB::raw("concat(if(isnull(read_by_factories),',',concat(read_by_factories,',')), '".$contact_id."')")));
                }
            }
        }

        $inquiries = Inquiries::where($whereConditions)
        ->leftjoin('order_article_name','order_article_name.id','inquiry.article_id')
        ->leftjoin('fabric_type','fabric_type.id','inquiry.fabric_type_id')
        ->leftjoin('income_terms','income_terms.id','inquiry.incoterms')
        ->leftjoin('order_category','order_category.id','inquiry.category_id')
        ->select('inquiry.*','order_article_name.name as article_name','fabric_type.name as fabric_composition','income_terms.name as income_terms',
        'order_category.name as category',
        DB::raw('DATE_FORMAT(inquiry.created_at,"%Y-%m-%d") as created_date'))
        ->get();

        return $inquiries;
    }
    public static function inquiry_sku($request){
        $whereConditions=[
            ['inquiry.id','=',$request->inquiry_id],

        ];
        $inquiries = Inquiries::where($whereConditions)
        ->join('inquiry_sku','inquiry_sku.inquiry_id','inquiry.id')
        ->leftjoin('color','inquiry_sku.color_id','color.id')
        ->leftjoin('size','inquiry_sku.size_id','size.id')
        ->select('inquiry.id as inquiry_id','inquiry_sku.quantity','color.id as color_id','color.name as color','size.id as size_id','size.name as size','size.category as category')
        ->get()->toArray();

        return $inquiries;
    }
    public static function inquiry_media($request){
        $whereConditions=[
            ['inquiry.id','=',$request->inquiry_id],

        ];
        $inquiries = Inquiries::where($whereConditions)
        ->join('inquiry_media','inquiry_media.inquiry_id','inquiry.id')
        ->select('inquiry_media.id','inquiry_media.filepath','inquiry_media.media_type','inquiry_media.orginalfilename','inquiry_media.datas as datasource','inquiry_media.id as media_id')
        ->get();
        foreach($inquiries as $key => $file){
            $inquiries[$key]->org_file_path = $file->filepath;
            $inquiries[$key]->filepath = Storage::disk('s3')->temporaryUrl($file->filepath, '+15 minutes');

        }
        return $inquiries;
    }

    public static function getFactories($request){
        $whereConditions=[
            ['inquiry.id','=',$request->inquiry_id],

        ];
        $inquiries = Inquiries::where($whereConditions)->select('factory_ids')->get();

        if(!empty($inquiries)){
            $ids = array_unique(explode('||',$inquiries[0]['factory_ids']));
            $factories =InquiryContact::whereIn('id',$ids)->select('id','factory','contact_person','contact_number','contact_email')->get();
            return $factories;
        }
        return array();

    }

    public static function deleteInquiry($request){
        $whereConditions=[
            ['inquiry.id','=',$request->inquiry_id],
            ['is_po_generated',"=",0]
        ];
        Inquiries::where($whereConditions)->delete();

        $whereConditions1=[
            ['inquiry_id','=',$request->inquiry_id]
        ];
        InquiryFactoryResponse::where($whereConditions1)->delete();
        InquirySku::where($whereConditions1)->delete();
        InquiryAdditional::where($whereConditions1)->delete();

        $tempid = InquiryMedia::where($whereConditions1)->select('temp_id')->limit(1)->get();
        if(!empty($tempid) && isset($tempid[0]['temp_id'])){
            $filesizes = InquiryMedia::where($whereConditions1)->sum('filesize');
            $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
            $storageUsed = $companyDetails->storage_used*1024*1024;
            $storageToBeFreed = $filesizes;
            $freedStorage = ($storageUsed - $storageToBeFreed)/(1024*1024);
            $companyDetails->storage_used = $freedStorage;
            $companyFolder = $companyDetails->aws_s3_path;
            $fold = $companyFolder.'/Inquiry/'.$tempid[0]['temp_id'];
            Storage::disk('s3')->deleteDirectory($fold);
            $companyDetails->save();
        }
        $path = public_path() . '/Inquiry/' .$request->inquiry_id.'.pdf';
        if(file_exists($path))
            unlink($path);

        $path = public_path() . '/Inquiry/' .$request->inquiry_id.'_jp.pdf';
            if(file_exists($path))
                unlink($path);

        InquiryMedia::where($whereConditions1)->delete();
        return true;
    }

    public static function get_factory_inquirys($contact_id,$fac_id,$page,$request=array()){
        $whereConditions=[
            ['inquiry.factory_ids','like','%||'.$contact_id.'||%']
        ];
        $whereConditions2=[
            ['inquiry.factory_ids','like','%||'.$contact_id.'||%'],
            ['inquiry_factory_response.factory_id','=',$fac_id]
        ];

        if(isset($request['article_id']) && $request['article_id']!=''){
            $whereConditions[]=['inquiry.article_id','=',$request['article_id']];
        }
        if(isset($request['user_id']) && $request['user_id']!=''){
            $whereConditions[]=['inquiry.user_id','=',$request['user_id']];
        }
        if(isset($request['from_date']) && isset($request['to_date']) && $request['from_date']!='' && $request['to_date']==''){
            $from = date('Y-m-d 00:00:00',strtotime($request['from_date']));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['inquiry.created_at','>=',$from];
            $whereConditions[]=['inquiry.created_at','<=',$to];
        }
        if(isset($request['from_date']) && isset($request['to_date']) && $request['from_date']!='' && $request['to_date']!=''){
            $from = date('Y-m-d 00:00:00',strtotime($request['from_date']));
            $to = date('Y-m-d 23:59:59',strtotime($request['to_date']));
            $whereConditions[]=['inquiry.created_at','>=',$from];
            $whereConditions[]=['inquiry.created_at','<=',$to];
        }
        $inquiries = Inquiries::where($whereConditions)
        ->leftjoin('order_article_name','order_article_name.id','inquiry.article_id')
        ->leftjoin('users','users.id','inquiry.user_id')
        ->leftjoin('staff','staff.id','inquiry.staff_id')
        ->leftjoin('inquiry_factory_response', function ($join) use ($whereConditions2) {
            $join->on('inquiry_factory_response.inquiry_id','inquiry.id')
                 ->where($whereConditions2);
        })
        ->select('inquiry.id','inquiry.style_no','order_article_name.name','inquiry.due_date','users.name as user','staff.first_name as staff',
        DB::raw('DATE_FORMAT(inquiry.created_at,"%Y-%m-%d") as created_date'),
        DB::raw('DATE_FORMAT(inquiry_factory_response.created_at,"%Y-%m-%d") as response_date'),
        DB::raw('(CASE WHEN FIND_IN_SET('.$contact_id.', read_by_factories)THEN 1 ELSE 0 END) AS is_read'),
        DB::raw('DATEDIFF(inquiry.due_date, NOW()) as due_days'),
        // DB::raw('DATEDIFF(inquiry_factory_response.created_at, inquiry.due_date) as days_after_quote_sent'),
        )
        ->orderBy('inquiry.created_at','desc')
        ->paginate(20, ['*'], 'page', $page);
        //->get();

        return $inquiries;
    }

    public static function get_buyer_email($id){
        $email = Inquiries::where('inquiry.id', $id)
        ->join('users','users.id','inquiry.user_id')
        ->select('email','name')
        ->first()->toArray();

        return $email;
    }

    public static function deleteInquiryMedia($request){
        $whereConditions1=[
            ['id','=',$request->media_id]
        ];
        $filepath = InquiryMedia::where($whereConditions1)->select('filepath','filesize','company_id')->limit(1)->get();
        if(!empty($filepath) && isset($filepath[0]['filepath'])){
            if($filepath[0]['company_id'] > 0){
                $companyDetails = CommonApp::getCompanyDetailsbyID($filepath[0]['company_id']);
                $storageUsed = $companyDetails->storage_used*1024*1024;
                $storageToBeFreed = $filepath[0]['file_size'];
                $freedStorage = ($storageUsed - $storageToBeFreed)/(1024*1024);
                $companyDetails->storage_used = $freedStorage;
                $companyDetails->save();
            }
            // $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
            // $companyFolder = $companyDetails->aws_s3_path;
            $file = $filepath[0]['filepath'];
            Storage::disk('s3')->delete($file);
        }
        InquiryMedia::where($whereConditions1)->delete();
        return true;
    }

    public static function check_buyer_notification($request){
        $whereConditions=[
            ['user_id','=',$request->user_id]
        ];

        $count = Inquiries::where($whereConditions)
            ->join('inquiry_factory_response','inquiry_factory_response.inquiry_id','inquiry.id')
            ->where(function ($query) use ($request) {
                $query->whereRaw('NOT FIND_IN_SET('.$request->user_id.', notification_read_by)')
                      ->orWhere('notification_read_by', '=', NULL);
            })
            ->count();
        return $count;
    }

    /* Check Factory Notification */
    public static function check_factory_notification($request){
        $contact_id = InquiryContact::where('factory_id',$request->user_id)->pluck('id')->first();
        if($contact_id > 0){
            $whereConditions=[
                ['inquiry.factory_ids','like','%||'.$contact_id.'||%']
            ];
            $count = Inquiries::where($whereConditions)
                ->where(function ($query) use ($contact_id) {
                    $query->whereRaw('NOT FIND_IN_SET('.$contact_id.', read_by_factories)')
                          ->orWhere('read_by_factories', '=', NULL);
                })
                ->count();
            return $count;
        }
        return 0;

    }

    /* Get Factory Inquiry for Mobile */
    public static function get_factory_inquirys_mobile($contact_id,$fac_id,$page,$request=array()){
        $whereConditions=[
            ['inquiry.factory_ids','like','%||'.$contact_id.'||%']
        ];
        $whereConditions2=[
            ['inquiry.factory_ids','like','%||'.$contact_id.'||%'],
            ['inquiry_factory_response.factory_id','=',$fac_id]
        ];
        if(isset($request['article_id']) && $request['article_id']!=''){
            $whereConditions[]=['inquiry.article_id','=',$request['article_id']];
        }
        if(isset($request['user_id']) && $request['user_id']!=''){
            $whereConditions[]=['inquiry.user_id','=',$request['user_id']];
        }
        if(isset($request['from_date']) && isset($request['to_date']) && $request['from_date']!='' && $request['to_date']==''){
            $from = date('Y-m-d 00:00:00',strtotime($request['from_date']));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['inquiry.created_at','>=',$from];
            $whereConditions[]=['inquiry.created_at','<=',$to];
        }
        if(isset($request['from_date']) && isset($request['to_date']) && $request['from_date']!='' && $request['to_date']!=''){
            $from = date('Y-m-d 00:00:00',strtotime($request['from_date']));
            $to = date('Y-m-d 23:59:59',strtotime($request['to_date']));
            $whereConditions[]=['inquiry.created_at','>=',$from];
            $whereConditions[]=['inquiry.created_at','<=',$to];
        }
        //dd($whereConditions);
        $inquiries = Inquiries::where($whereConditions)
        ->leftjoin('order_article_name','order_article_name.id','inquiry.article_id')
        // ->leftjoin('inquiry_factory_response','inquiry_factory_response.inquiry_id','inquiry.id')
        ->leftjoin('users','users.id','inquiry.user_id')
        ->leftjoin('inquiry_factory_response', function ($join) use ($whereConditions2) {
            $join->on('inquiry_factory_response.inquiry_id','inquiry.id')
                 ->where($whereConditions2);
        })
        ->select('inquiry.id','inquiry.style_no','order_article_name.name','inquiry.due_date','inquiry.currency','users.name as user',
        DB::raw('DATE_FORMAT(inquiry.created_at,"%Y-%m-%d") as created_date'),
        'inquiry_factory_response.price as price','inquiry_factory_response.comments as comments',
        DB::raw('DATE_FORMAT(inquiry_factory_response.created_at,"%Y-%m-%d") as response_date'),
        DB::raw('(CASE WHEN FIND_IN_SET('.$contact_id.', read_by_factories)THEN 1 ELSE 0 END) AS is_read'),
        DB::raw('DATEDIFF(inquiry.due_date, NOW()) as due_days'))
        ->orderBy('inquiry.created_at','desc')
        ->paginate(20, ['*'], 'page', $page);
        //->get();

        return $inquiries;
    }

    /* Read Factory Notifications Mobile */
    public static function read_factory_notifications($request){
        $whereConditions=[
            ['inquiry.id','=',$request->inquiry_id],
        ];

        $contact_id = InquiryContact::where('factory_id',$request->user_id)->pluck('id')->first();
        if($contact_id != null){
            $count = Inquiries::where($whereConditions)
            ->whereRaw('FIND_IN_SET('.$contact_id.', read_by_factories)')
            ->count();
            if($count == 0){
                DB::table('inquiry')
                ->where($whereConditions)
                ->limit(1)
                // ->update(array('read_by_factories' => DB::raw("concat(ifnull(read_by_factories,','), '".$contact_id."')")));
                ->update(array('read_by_factories' => DB::raw("concat(if(isnull(read_by_factories),',',concat(read_by_factories,',')), '".$contact_id."')")));
            }
        }
    }

    /* Read Factory Notifications Mobile */
    public static function read_buyer_notifications($request){
        $request->user_id =(int)$request->user_id;
        $count = InquiryFactoryResponse::where('inquiry_id','=',$request->inquiry_id)
        ->whereRaw('FIND_IN_SET('.$request->user_id.', notification_read_by)')
        ->count();
        //if($count ==0){
            DB::table('inquiry_factory_response')
            ->where('inquiry_id', $request->inquiry_id)
            //->limit(1)
            ->update(array('notification_read_by' => DB::raw("concat(ifnull(notification_read_by,','), ',".$request->user_id."')")));
       // }
    }

    /* Get Factory Inquiry Articals */
    public static function get_factory_articals($contact_id){
        $whereConditions=[
            ['inquiry.factory_ids','like','%||'.$contact_id.'||%']
        ];

        $articals = Inquiries::where($whereConditions)
        ->leftjoin('order_article_name','order_article_name.id','inquiry.article_id')
        ->select(DB::raw('DISTINCT(order_article_name.id) as id'),'order_article_name.name')
        ->orderBy('order_article_name.name','asc')
        ->get();
        return $articals;
    }
    /* Get Factory Inquiry Users */
    public static function get_factory_users($contact_id){
        $whereConditions=[
            ['inquiry.factory_ids','like','%||'.$contact_id.'||%']
        ];

        $users = Inquiries::where($whereConditions)
        ->leftjoin('users','users.id','inquiry.user_id')
        ->select(DB::raw('DISTINCT(users.id) as id'),'users.name')
        ->orderBy('users.name','asc')
        ->get();
        return $users;
    }
    /* Get Inqury Factory list */
    public static function get_inquiry_factories($request){
        $whereConditions=[
            ['inquiry.company_id','=',$request->company_id],
            ['inquiry.workspace_id','=',$request->workspace_id]
        ];

        $ids = Inquiries::where($whereConditions)
        ->select(DB::raw('GROUP_CONCAT(factory_ids) as facts'))
        ->orderBy('id','asc')
        ->get()->toArray();
        $id_arr = array();
        if(count($ids)>0){
            $id_arr = array_unique(explode("||",str_replace(',','',$ids[0]['facts'])));
        }

        $factories = InquiryContact::whereIn('id',$id_arr)
        ->select('id','factory')
        ->orderBy('factory','asc')
        ->get();
        return $factories;
    }

    /* Get Inquiry Articals */
    public static function get_inquiry_articles($request){
        $whereConditions=[
            ['inquiry.company_id','=',$request->company_id],
            ['inquiry.workspace_id','=',$request->workspace_id]
        ];

        $articals = Inquiries::where($whereConditions)
        ->leftjoin('order_article_name','order_article_name.id','inquiry.article_id')
        ->select(DB::raw('DISTINCT(order_article_name.id) as id'),'order_article_name.name')
        ->orderBy('order_article_name.name','asc')
        ->get();
        return $articals;
    }
    /* Download Buyer Inquiries */
    public static function download_buyer_inquiries($whereConditions){
        $inquiries = Inquiries::where($whereConditions)
            ->leftjoin('order_article_name','order_article_name.id','inquiry.article_id')
            ->select('inquiry.id','inquiry.style_no','order_article_name.name',
            DB::raw('DATE_FORMAT(inquiry.created_at,"%Y-%m-%d") as created_date'))
            ->orderBy('inquiry.created_at','desc')
            ->get();

        return $inquiries;
    }
    /* Download Factory Inquiries */
    public static function download_factory_inquiries($whereConditions,$whereConditions2){
        $inquiries = Inquiries::where($whereConditions)
        ->leftjoin('order_article_name','order_article_name.id','inquiry.article_id')
        ->leftjoin('users','users.id','inquiry.user_id')
        ->leftjoin('staff','staff.id','inquiry.staff_id')
        ->leftjoin('inquiry_factory_response', function ($join) use ($whereConditions2) {
            $join->on('inquiry_factory_response.inquiry_id','inquiry.id')
                 ->where($whereConditions2);
        })
        ->select('inquiry.id','inquiry.style_no','order_article_name.name','inquiry.due_date','users.name as user','staff.first_name as staff',
        DB::raw('DATE_FORMAT(inquiry.created_at,"%Y-%m-%d") as created_date'),
        DB::raw('DATE_FORMAT(inquiry_factory_response.created_at,"%Y-%m-%d") as response_date'),
        DB::raw('DATEDIFF(inquiry.due_date, NOW()) as due_days'),
        DB::raw('DATEDIFF(inquiry_factory_response.created_at, inquiry.due_date) as days_after_quote_sent'),
        )
        ->orderBy('inquiry.created_at','desc')
        ->get();
        return $inquiries;
    }

    public static function get_the_inquiry($whereConditions){
        $inquiry = Inquiries::where($whereConditions)->first();
        return $inquiry;
    }
    /*Get the Users inquiry lists for Fabric module */
    public static function get_fabric_inquiry_ids($request){
        if($request->login_type =="user"){
            if($request->user_type =="Factory"){
                $factory_contact_id = InquiryContact::where('factory_id',$request->user_id)->pluck('id')->first();
               // if($factory_contact_id > 0){
                    $res = Inquiries::where('factory_ids','LIKE','%||'.$factory_contact_id.'||%')->select('id')->orderBy('id','DESC')->get();
               // }

            }else{
                $res = Inquiries::where('user_id',$request->user_id)->select('id')->orderBy('id','DESC')->get();
            }
        }else{
            if($request->user_type =="Factory"){
                $factory_contact_id = InquiryContact::where('factory_id',$request->user_id)->pluck('id')->first();
               // if($factory_contact_id > 0){
                    $res = Inquiries::where('factory_ids','LIKE','%||'.$factory_contact_id.'||%')->select('id')->orderBy('id','DESC')->get();
               // }

            }else{
                $res = Inquiries::where('staff_id',$request->user_id)->select('id')->orderBy('id','DESC')->get();
            }
        }
        return $res;
    }

    public static function get_inquiry_label($request){
        $res = Inquiries::where('inquiry.id',$request->inquiry_id)
            ->leftjoin('inquiry_media','inquiry.id','inquiry_media.inquiry_id')
            ->leftjoin('order_article_name','order_article_name.id','inquiry.article_id')
            ->leftjoin('order_category','order_category.id','inquiry.category_id')
            ->leftjoin('fabric_type','fabric_type.id','inquiry.fabric_type_id')
            ->select('inquiry.id','print_type','print_no_of_colors','print_size','main_lable','main_lable_info','washcare_lable','washcare_lable_info','hangtag_lable',
            'hangtag_lable_info','barcode_lable','barcode_lable_info','poly_bag_size','poly_bag_material','poly_bag_price','poly_bag_print','carton_bag_dimensions',
            'carton_color','carton_material','carton_edge_finish','carton_mark','media_type','filepath','inquiry.style_no','order_article_name.name as article',
            'fabric_type.name as fabric_composition','order_category.name as category','inquiry.created_at as inq_date')
            ->get();

        return $res;
    }
    public static function po_details($request){
        $whereConditions=[
            ['inquiry_po.id','=',$request->po_id],
        ];
        $inquiries = InquiryPO::where($whereConditions)
        ->leftjoin('order_article_name','order_article_name.id','inquiry_po.article_id')
        ->leftjoin('fabric_type','fabric_type.id','inquiry_po.fabric_type_id')
        ->leftjoin('income_terms','income_terms.id','inquiry_po.incoterms')
        ->leftjoin('order_category','order_category.id','inquiry_po.category_id')
        ->select('inquiry_po.*','order_article_name.name as article_name','fabric_type.name as fabric_composition','income_terms.name as income_terms',
        'order_category.name as category',
        DB::raw('DATE_FORMAT(inquiry_po.created_at,"%Y-%m-%d") as created_date'))
        ->get();

        return $inquiries;
    }

    public static function po_sku($request){
        $whereConditions=[
            ['inquiry_po.id','=',$request->po_id],

        ];
        $inquiries = InquiryPO::where($whereConditions)
        ->join('inquiry_po_sku','inquiry_po_sku.po_id','inquiry_po.id')
        ->leftjoin('color','inquiry_po_sku.color_id','color.id')
        ->leftjoin('size','inquiry_po_sku.size_id','size.id')
        ->select('inquiry_po.id as inquiry_id','inquiry_po_sku.quantity','color.id as color_id','color.name as color','size.id as size_id','size.name as size','size.category as category')
        ->get()->toArray();

        return $inquiries;
    }

    public static function po_media($request){
        $whereConditions=[
            ['inquiry_po.id','=',$request->po_id],

        ];
        $inquiries = InquiryPO::where($whereConditions)
        ->join('inquiry_po_media','inquiry_po_media.po_id','inquiry_po.id')
        ->select('inquiry_po_media.id','inquiry_po_media.filepath','inquiry_po_media.media_type','inquiry_po_media.orginalfilename','inquiry_po_media.datas as datasource')
        ->get();
        foreach($inquiries as $key => $file){
            $inquiries[$key]->org_file_path = $file->filepath;
            $inquiries[$key]->filepath = Storage::disk('s3')->temporaryUrl($file->filepath, '+15 minutes');

        }
        return $inquiries;
    }

    public static function duplicate_inquiry($request){
        $media_reference_id = strtotime(date('Y-m-d H:i:s'));
        $inquiry = Inquiries::where('id',$request->inquiry_id)->first();
        $inquiryMedia = InquiryMedia::where('inquiry_id',$request->inquiry_id)->get();
        $inquirySku = InquirySku::where('inquiry_id',$request->inquiry_id)->get();
        $inquiryTrims = InquiryAdditional::where('inquiry_id',$request->inquiry_id)->get();
        $data = [];
        $data['category_id']= $inquiry->category_id;
        $data['media_reference_id']= $media_reference_id;
        $data['article_id']= $inquiry->article_id;
        $data['style_no']= $inquiry->style_no;
        $data['company_id']= $inquiry->company_id;
        $data['user_id']= $inquiry->user_id;
        $data['staff_id']= $inquiry->staff_id;
        $data['workspace_id']= $inquiry->workspace_id;
        $data['fabric_type_id']= $inquiry->fabric_type_id;
        $data['fabric_GSM']= $inquiry->fabric_GSM;
        $data['yarn_count']= $inquiry->yarn_count;
        $data['style_article_description']= $inquiry->style_article_description;
        $data['special_finish']= $inquiry->special_finish;
        $data['total_qty']= $inquiry->total_qty;
        $data['patterns']= $inquiry->patterns;
        $data['jurisdiction']= $inquiry->jurisdiction;
        $data['customs_declaraion_document']= $inquiry->customs_declaraion_document;
        $data['penality']= $inquiry->penality;
        $data['print_image']= $inquiry->print_image ;
        $data['print_size']= $inquiry->print_size;
        $data['print_type']= $inquiry->print_type;
        $data['print_no_of_colors']= $inquiry->print_no_of_colors ;
        $data['main_lable']= $inquiry->main_lable ;
        $data['main_lable_info']= $inquiry->main_lable_info ;
        $data['washcare_lable']= $inquiry->washcare_lable ;
        $data['washcare_lable_info']= $inquiry->washcare_lable_info;
        $data['hangtag_lable']= $inquiry->hangtag_lable;
        $data['hangtag_lable_info']= $inquiry->hangtag_lable_info;
        $data['barcode_lable']= $inquiry->barcode_lable;
        $data['barcode_lable_info']= $inquiry->barcode_lable_info;
        $data['trims_nominations']= $inquiry->trims_nominations;
        $data['poly_bag_size']= $inquiry->poly_bag_size;
        $data['poly_bag_material']= $inquiry->poly_bag_material;
        $data['poly_bag_price']= $inquiry->poly_bag_price;
        $data['carton_bag_dimensions']= $inquiry->carton_bag_dimensions;
        $data['carton_color']= $inquiry->carton_color;
        $data['carton_material']= $inquiry->carton_material;
        $data['carton_edge_finish']= $inquiry->carton_edge_finish;
        $data['carton_mark']= $inquiry->carton_mark;
        $data['make_up']= $inquiry->make_up;
        $data['films_cd']= $inquiry->films_cd;
        $data['picture_card']= $inquiry->picture_card;
        $data['inner_cardboard']= $inquiry->inner_cardboard;
        $data['shipping_size']= $inquiry->shipping_size;
        $data['air_frieght']= $inquiry->air_frieght;
        $data['estimate_delivery_date']= $inquiry->estimate_delivery_date;
        $data['due_date']= $inquiry->due_date;
        $data['incoterms']= $inquiry->incoterms;
        $data['payment_terms']= $inquiry->payment_terms;
        $data['payment_instructions']= $inquiry->payment_instructions;
        $data['target_price']= $inquiry->target_price;
        $data['forbidden_substance_info']= $inquiry->forbidden_substance_info;
        $data['testing_requirements']= $inquiry->testing_requirements;
        $data['sample_requirements']= $inquiry->sample_requirements;
        $data['special_requests']= $inquiry->special_requests;
        $data['currency']= $inquiry->currency;
        $data['measurement_sheet']=$inquiry->measurement_Chart;
        $data['fabric_type']= $inquiry->fabric_type;
        $data['poly_bag_print']= $inquiry->poly_bag_print;
        try{
            Inquiries::insert($data);
            $inquiry_id = DB::getPdo()->lastInsertId();
            // Copy s3 images
            $companyDetails = CommonApp::getCompanyDetailsbyID($inquiry->company_id);
            $companyFolder = $companyDetails->aws_s3_path;
            $folder = $companyFolder.'/Inquiry/'.$inquiry->media_reference_id;
            $s3 = Storage::disk('s3');
            $images = $s3->allFiles($folder);

            foreach($images as $image)
            {
                $new_loc = str_replace($inquiry->media_reference_id, $media_reference_id, $image);
                $s3->copy($image, $new_loc);
               // dd($new_loc);
            }
            foreach($inquiryMedia as $media){
                $mediaData=[];
                $mediaData['inquiry_id']=$inquiry_id;
                $mediaData['media_type']=$media->media_type;
                $mediaData['filename']=$media->filename;
                $mediaData['temp_id']=$media_reference_id;
                $mediaData['orginalfilename']=$media->orginalfilename;
                $mediaData['filepath']=str_replace($inquiry->media_reference_id, $media_reference_id, $media->filepath);
                $mediaData['datas']=$media->datas;
                $mediaData['filesize']=$media->filesize;
                $mediaData['created_at']=date('Y-m-d H:i:s');
                $mediaData['updated_at']=date('Y-m-d H:i:s');
                InquiryMedia::insert($mediaData);
                //print_r($mediaData);
            }
            foreach($inquirySku as $sku){
                $skuData=[];
                $skuData['inquiry_id']= $inquiry_id;
                $skuData['color_id']=$sku->color_id;
                $skuData['size_id']=$sku->size_id;
                $skuData['color_ratio']=$sku->color_ratio;
                $skuData['size_ratio']=$sku->size_ratio;
                $skuData['quantity']=$sku->quantity;
                $skuData['created_at']=date('Y-m-d H:i:s');
                InquirySku::insert($skuData);
            }
            foreach($inquiryTrims as $trims){
                $trimsData=[];
                $trimsData['inquiry_id']= $inquiry_id;
                $trimsData['label']=$trims->label;
                $trimsData['label_description']=$trims->label_description;
                $trimsData['media_type']=$trims->media_type;
                $trimsData['company_id']=$trims->company_id;
                $trimsData['workspace_id']=$trims->workspace_id;
                $trimsData['created_at']=date('Y-m-d H:i:s');
                InquiryAdditional::insert($trimsData);
            }
            //exit;
            /* Generate Po Log starts */
            try{
                InquiryLog::create_inquiry_log($inquiry_id,$request);
            }catch(Exception $e){

            }
            /* Generate Po Log end */

        }catch(Exception $e){
            DB::rollback();
            $res = json_encode(["status_code"=>401,'status'=>"failure","message"=>$e->getMessage()],200);
            return $res;
        }
        DB::commit();

        $result['inquiry_id']=$inquiry_id;
        $result['data']=$data;
        $result['media']=$inquiryMedia;
        $result['sku']=$inquirySku;

        return $result;
    }

    public static function deleteMultiInquiryMedia($request){
        $filepath = InquiryMedia::whereIn('id',$request->media_id)->select('filepath','filesize','company_id')->get();
        if(!empty($filepath)){
            foreach($filepath as $files){
                if(isset($files['filepath'])){
                    if($files['company_id'] > 0){
                        $companyDetails = CommonApp::getCompanyDetailsbyID($files['company_id']);
                        $storageUsed = $companyDetails->storage_used*1024*1024;
                        $storageToBeFreed = $files['file_size'];
                        $freedStorage = ($storageUsed - $storageToBeFreed)/(1024*1024);
                        $companyDetails->storage_used = $freedStorage;
                        $companyDetails->save();
                    }
                    $file = $files['filepath'];
                    //dd($file);
                    Storage::disk('s3')->delete($file);
                }
            }
        }

        InquiryMedia::whereIn('id',$request->media_id)->delete();
        return true;
    }
}
