<?php

namespace App\Http\Controllers\Mobile\PurchaseOrder;

use App\Common\CommonApp;
use App\Common\Uploads;
use App\Http\Controllers\Controller;
use App\Models\Inquiries;
use App\Models\InquiryFactoryResponse;
use App\Models\InquiryMedia;
use App\Models\InquiryMediaPO;
use App\Models\InquiryPO;
use App\Models\InquiryPOLog;
use App\Models\InquiryPOrder;
use App\Models\InquirySku;
use App\Models\InquirySKUPO;
use App\Models\PoComments;
use App\Models\POTesting;
use App\Models\Staff;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PO extends Controller
{
    /* PO Generation */
    public static function generate_po_factory(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'factory_id' => 'required|min:1',
            'inquiry_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereConditions=[
            ['inquiry_id','=',$request->inquiry_id],
            ['factory_contact_id','=',$request->factory_id],
            // ['company_id','=',$request->company_id],
            // ['workspace_id','=',$request->workspace_id],
        ];
        $inquiry = Inquiries::where('id',$request->inquiry_id)->first();
        $factory = InquiryFactoryResponse::where($whereConditions)->first();
        $inquiryMedia = InquiryMedia::where('inquiry_id',$request->inquiry_id)->get();
        $inquirySku = InquirySku::where('inquiry_id',$request->inquiry_id)->get();
        /* Check If the Inquiry already has PO */
        $whereConditionsForPO = [
            ['inquiry_id',"=",$request->inquiry_id],
            ['po_status',"=","1"]
        ];
        $isPOAlreadyGenerated = InquiryPO::where($whereConditionsForPO)->get();
        if (count($isPOAlreadyGenerated)>0){
            $res = json_encode(["status_code"=>601,"status"=>"failure","message"=>"PO Already Generated For This Inquiry"]);
            return CommonApp::apiEncrypt($res);
        }
        $data = [];
        $data['inquiry_id']= $inquiry->id;
        $data['category_id']= $inquiry->category_id;
        $data['media_reference_id']= $inquiry->referenceId;
        $data['article_id']= $inquiry->article_id;
        $data['factory_id']=$request->factory_id;
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
        DB::beginTransaction();
        try{
            InquiryPO::insert($data);
            $poID = DB::getPdo()->lastInsertId();
            foreach($inquiryMedia as $media){
                $mediaData=[];
                $mediaData['po_id']=$poID;
                $mediaData['media_type']=$media->media_type;
                $mediaData['filename']=$media->filename;
                $mediaData['orginalfilename']=$media->orginalfilename;
                $mediaData['filepath']=$media->filepath;
                $mediaData['datas']=$media->datas;
                $mediaData['filesize']=$media->filesize;
                $mediaData['created_at']=date('Y-m-d H:i:s');
                $mediaData['updated_at']=date('Y-m-d H:i:s');
                InquiryMediaPO::insert($mediaData);
            }
            foreach($inquirySku as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['color_id']=$sku->color_id;
                $skuData['size_id']=$sku->size_id;
                $skuData['color_ratio']=$sku->color_ratio;
                $skuData['size_ratio']=$sku->size_ratio;
                $skuData['quantity']=$sku->quantity;
                $skuData['created_at']=date('Y-m-d H:i:s');
                InquirySKUPO::insert($skuData);
            }

            if($request->factory_id > 0){
                $factory->is_po_generated=1;
                $factory->save();
            }

            $inquiry->is_po_generated=1;
            $inquiry->save();

            /* Generate Po Log starts */
            try{

                InquiryPOLog::generate_po_log($poID,$request);
            }catch(Exception $e){

            }
            /* Generate Po Log end */

        }catch(Exception $e){
            DB::rollback();
            $res = json_encode(["status_code"=>401,'status'=>"failure","message"=>$e->getMessage()],200);
            return CommonApp::apiEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>"Success"],200);
        return CommonApp::apiEncrypt($res);
    }

    /* View PO */
    public static function view_company_po(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereConditions[]= ['company_id','=',$request->company_id];
        $whereConditions[]= ['workspace_id','=',$request->workspace_id];
        $inquiries= InquiryPO::get_all_po($whereConditions,$request);
        $webviewUrl = config('app.frontend_url').'inquiry/viewpurchaseorderdetailsmobile?id=';
        $pdfpath  = config('app.public_url').'PO/';
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiries,"pdfpath"=>$pdfpath,"webviewUrl"=>$webviewUrl],200);
        return CommonApp::apiEncrypt($res);
    }

    /* To Cancel a PO */
    public static function cancel_po(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        try{
            InquiryPO::cancel_po($request);

            /* Cancel Po Log starts */
            try{

                InquiryPOLog::cancel_po_log($request);
            }catch(Exception $e){

            }
            /* Cancel Po Log end */
        }catch(Exception $e){
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::apiEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"PO Cancelled Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }

    /* View New PO */
    public static function view_all_po_old(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereConditions[]= ['inquiry_new_po.company_id','=',$request->company_id];
        $whereConditions[]= ['inquiry_new_po.workspace_id','=',$request->workspace_id];
        $whereConditions[]= ['inquiry_new_po.user_id','=',$request->user_id];
        $whereConditions[]= ['inquiry_new_po.staff_id','=',$request->staff_id];
        $request->page = (isset($request->page) && $request->page!='')?$request->page:1;

        $whereConditions1 = $whereConditions;

        if(isset($request->article_id) && $request->article_id!=''){
            $whereConditions[]=['inquiry_new_po.article_id','=',$request->article_id];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date==''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['inquiry_new_po.created_at','>=',$from];
            $whereConditions[]=['inquiry_new_po.created_at','<=',$to];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date!=''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->to_date));
            $whereConditions[]=['inquiry_new_po.created_at','>=',$from];
            $whereConditions[]=['inquiry_new_po.created_at','<=',$to];
        }

        $inquiries= InquiryPOrder::get_all_po($whereConditions,$request,0);
        $articles= InquiryPOrder::get_all_po_articles($whereConditions1);
        $webviewUrl = config('app.frontend_url').'inquiry/viewpurchaseorderdetailsmobile?id=';
        $pdfpath  = config('app.public_url').'PO/';
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiries,"articles"=>$articles,"pdfpath"=>$pdfpath,"webviewUrl"=>$webviewUrl],200);
        return CommonApp::apiEncrypt($res);
    }
    /* Update PO Status */
    public static function update_po_status_old(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        try{
            $data['status']=1;
            InquiryPOrder::where('id',$request->po_id)->update($data);
            /* Update PO Status Log starts */
            try{

                InquiryPOLog::po_status_update_log($request);
            }catch(Exception $e){

            }
            /* Update PO Status Log end */
        }catch(Exception $e){
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::apiEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"PO Published Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }
    /* Delete PO */
    public static function delete_po_old(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        try{
            InquiryPOrder::where('id',$request->po_id)->delete();
            /* Update PO Status Log starts */
            try{

                InquiryPOLog::po_delete_update_log($request);
            }catch(Exception $e){

            }
            /* Update PO Status Log end */
        }catch(Exception $e){
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::apiEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"PO Deleted Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }

    /*Multi Style View PO */
    public function view_all_po(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereConditions[]= ['inquiry_new_po.company_id','=',$request->company_id];
        $whereConditions[]= ['inquiry_new_po.workspace_id','=',$request->workspace_id];
        //$whereConditions[]= ['inquiry_new_po.user_id','=',$request->user_id];
        //$whereConditions[]= ['inquiry_new_po.staff_id','=',$request->staff_id];
        $request->page = (isset($request->page) && $request->page!='')?$request->page:1;

        /* Check untranslated PO starts */
        // $parent_id = InquiryPOrder::where($whereConditions)->where('translated','0')->pluck('parent_id')->first();
        // if((int)$parent_id > 0){
        //     POrder::po_translate($parent_id);
        // }
        // dd($parent_id);
        /* Check untranslated PO end */

        if(isset($request->article_id) && $request->article_id!=''){
            $whereConditions[]=['inquiry_new_po.article_id','=',$request->article_id];
        }
        if(isset($request->incoterms_id) && $request->incoterms_id!=''  && $request->incoterms_id!='0'){
            $whereConditions[]=['inquiry_new_po.incoterms','=',$request->incoterms_id];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date==''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['inquiry_new_po.created_at','>=',$from];
            $whereConditions[]=['inquiry_new_po.created_at','<=',$to];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date!=''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->to_date));
            $whereConditions[]=['inquiry_new_po.created_at','>=',$from];
            $whereConditions[]=['inquiry_new_po.created_at','<=',$to];
        }

        $inquiries= InquiryPOrder::get_all_po_multiple($whereConditions,$request,0);
        $pdfpath  = config('app.public_url').'PO/';
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiries,"filterData"=>$this->po_Filters($request),"pdfpath"=>$pdfpath],200);
        return CommonApp::apiEncrypt($res);
    }
    /*PO Filters */
    private function po_Filters($request){
        $whereCondition = [
            ['workspace_id', '=', $request->workspace_id],
            ['company_id', '=', $request->company_id]
        ];
        $filterArray=[];
        $filterArrayData = InquiryPOrder::select("article_id as id", "article_name as name", DB::raw("'article' as type"))
        ->where($whereCondition)
        ->where("article_name", "!=", "")
        ->groupBy("article_name")
        ->orderBy("article_name", "asc")
        ->union(
            InquiryPOrder::select("fabric_type_id as id", "fabric_type as name", DB::raw("'fabric' as type"))
                ->where($whereCondition)
                ->where("fabric_type", "!=", "")
                ->groupBy("fabric_type")
                ->orderBy("fabric_type", "asc")
        )
        ->union(
            InquiryPOrder::select("style_no as id", "style_no as name", DB::raw("'style_no' as type"))
                ->where($whereCondition)
                ->where("style_no", "!=", "")
                ->groupBy("style_no")
                ->orderBy("style_no", "asc")
        )

        ->get();

        $inoterms =  InquiryPOrder::select("income_terms.id as id", "income_terms.name as name", DB::raw("'incoterms' as type"))
        ->where($whereCondition)
        ->join('income_terms','income_terms.id','inquiry_new_po.incoterms')
        ->where("incoterms", "!=", "0")
        ->groupBy("incoterms")
        ->orderBy("incoterms", "asc")->get();

        $filterArray['article'] = $filterArrayData->where('type', 'article')->values();
        $filterArray['fabric'] = $filterArrayData->where('type', 'fabric')->values();
        $filterArray['style_no'] = $filterArrayData->where('type', 'style_no')->values();
        $filterArray['incoterms'] = $inoterms;
        return $filterArray;
    }
    /* Update PO Status */
    public static function update_po_status(Request $request){
        $header= $request;
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_parent_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        try{
            $data['status']=1;
            InquiryPOrder::where('parent_id',$request->po_parent_id)->update($data);
            /* Update PO Status Log starts */
            try{
                $ip_address = $header->header('ip-address') ?? '';
                $platform = $header->header('platform') ?? '';
                InquiryPOLog::po_status_update_log($request,$ip_address,$platform);
            }catch(Exception $e){

            }
            /* Update PO Status Log end */
        }catch(Exception $e){
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::apiEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"PO Published Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }
    /* Delete Multi PO */
    public static function delete_po(Request $request){
        $header = $request;
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_parent_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        try{
            InquiryPOrder::where('parent_id',$request->po_parent_id)->delete();
            InquirySKUPO::where('parent_po_id', $request->po_parent_id)->delete();
            InquiryMediaPO::where('parent_po_id', $request->po_parent_id)->delete();
            POTesting::where('po_parent_id', $request->po_parent_id)->delete();
            /* Update PO Status Log starts */
            try{
                $ip_address = $header->header('ip-address') ?? '';
                $platform = $header->header('platform') ?? '';
                InquiryPOLog::po_delete_update_log($request,$ip_address,$platform);
            }catch(Exception $e){

            }
            /* Update PO Status Log end */
        }catch(Exception $e){
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::apiEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"PO Deleted Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }
    /* Generate The PO PDF */
    public static function generate_multiple_po_pdf_new(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'po_parent_id' => 'required',
            'user_id' => 'required',
            'company_id' => 'required',
            'language' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $colors = [];
        $sizes = [];
        $media=[];
        $media['files'] = InquiryMediaPO::getInquiryPOMediaMulti($request);
        $media['serverURL'] = ''; //config('filesystems.disks.s3.url');
        //dd($media);
        $sku = InquirySKUPO::getPOSKUMultiple($request);
        //dd($sku);
        $testings = POTesting::getPOTestingMulti($request);
        // dd($testings);
        //$testingsku = POTesting::getPOTestingSKUMulti($request);
        //dd($testingsku);
        $result = InquiryPOrder::get_po_details_multi($request);
        //dd($result);
        $company = CommonApp::getCompanyDetailsbyID($request->company_id);
        $buyer = (explode("\n",$result[0]->buyer)[0]) ?? $result[0]->buyer;
        $seller = (explode("\n",$result[0]->seller)[0]) ?? $result[0]->seller;
        $maker = (explode("\n",$result[0]->maker)[0]) ?? $result[0]->maker;
        $logo= ($company->logo !='' && $company->logo!=NULL) ? Storage::disk('s3')->temporaryUrl($company->logo, '+75 minutes') : "";

        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color'],"po_id"=>$s['po_id']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size'],"po_id"=>$s['po_id']);
            }
        }
        App::setlocale($request->language);
        $user = CommonApp::getUserDetailsById($request->user_id);

        $datas['po_parent_id'] = $request->po_parent_id;
        $datas['po_number'] = $result[0]->po_number;
        $datas['data'] = $result;
        $datas['request'] = $request;
        $datas['media'] = $media;
        $datas['sku'] = $sku;
        $datas['testings'] = $testings;
        $datas['sizes'] = array_unique($sizes,SORT_REGULAR);
        $datas['colors'] = array_unique($colors,SORT_REGULAR);
        $datas['user'] = $user;
        $datas['logo'] = $company->logo;
        $datas['buyer'] = $buyer;
        $datas['seller'] = $seller;
        $datas['maker'] = $maker;
        view()->share("datas",$datas);
        //return view('InquiryPDF');
        $pdf = Pdf::loadView('PoPDFmulti');
        $pdf->setPaper('A4', 'portrait');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
        $pdf->setOption("enable_php", true);

        // Water Mark settings
        // $canvas = $pdf->getDomPDF()->getCanvas();
        // $height = $canvas->get_height();
        // $width = $canvas->get_width();
        // $canvas->set_opacity(.2,"Multiply");
        // $canvas->page_text($width/5, $height/2, 'Unpublished', null,70, array(0,0,0),2,2,-30);

        return $pdf->download();
    }

    /* Multi Style Get PO */
    public static function get_po_multiple(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_parent_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $colors = [];
        $sizes = [];
        $media['files'] = InquiryMediaPO::getInquiryPOMediaMulti($request);
        $media['serverURL'] = ''; //config('filesystems.disks.s3.url');
        //dd($media);
        $sku = InquirySKUPO::getPOSKUMultiple($request);
        //dd($sku);
        $testings = POTesting::getPOTestingMulti($request);
        // dd($testings);
        $testingsku = POTesting::getPOTestingSKUMulti($request);
        //dd($testingsku);
        $result = InquiryPOrder::get_po_details_multi($request);
        //dd($result);
        $company = CommonApp::getCompanyDetailsbyID($request->company_id);
        $buyer = (explode("\n",$result[0]->buyer)[0]) ?? $result[0]->buyer;
        $seller = (explode("\n",$result[0]->seller)[0]) ?? $result[0]->seller;
        $maker = (explode("\n",$result[0]->maker)[0]) ?? $result[0]->maker;
        $logo= ($company->logo !='' && $company->logo!=NULL) ? Storage::disk('s3')->temporaryUrl($company->logo, '+75 minutes') : "";

        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color'],"po_id"=>$s['po_id']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size'],"po_id"=>$s['po_id']);
            }
        }

        //dd($sku);

        $data['po_parent_id'] = $request->po_parent_id;
        $data['po_number'] = $result[0]->po_number ?? 0;
        $data['poDetails'] = $result;
        $data['poMediaDetails'] = $media;
        $data['poSKUDetails']['sku'] = $sku;
        $data['poSKUDetails']['colors'] =array_values(array_unique($colors,SORT_REGULAR));
        $data['poSKUDetails']['sizes'] =array_values(array_unique($sizes,SORT_REGULAR));
        $data['serverURL'] = config('filesystems.disks.s3.url');
        $data['testings'] = $testings;
        $data['testingsku'] = $testingsku;
        $data['logo'] = $logo;
        $data['buyer_name'] = str_replace(',', '', $buyer);
        $data['seller_name'] = str_replace(',', '', $seller);
        $data['maker_name'] = str_replace(',', '', $maker);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::apiEncrypt($res);
    }

    /*Add PO Comments*/
    public function addPOComments(Request $request)
    {
        $header = $request;
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'po_id'=> 'required',
            'comment_details'=> 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }

        $teckPackDetAr = [];
        $teckPackDetAr['company_id'] = $request->company_id;
        $teckPackDetAr['workspace_id'] = $request->workspace_id;
        $teckPackDetAr['user_id'] = $request->user_id;
        $teckPackDetAr['comment_type'] = 'Text';
        $teckPackDetAr['comment_data'] = $request->comment_details;
        $teckPackDetAr['po_id'] = $request->po_id;
        $teckPackDetAr['staff_id'] = $request->staff_id;
        $teckPackDetAr['created_by'] = $request->staff_id > 0 ? $request->staff_id : $request->user_id;
        $teckPackDetAr['created_by_type'] = $request->staff_id > 0 ? "Staff" : "Admin";
        $teckPackDetAr['created_at'] = date('Y-m-d H:i:s');
        PoComments::insert($teckPackDetAr);

        /* Generate PO comments Log starts */
        try {
            $ip_address = $header->header('Ip-Address') ?? '';
            $platform = $header->header('Platform') ?? '';
            InquiryPOLog::generate_po_comments_log($request->po_id, $request,$ip_address,$platform);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
        /* Generate PO comments Log end */


        $res = json_encode(["status_code" => 200, "status" => "Success", "message" => "Comment Added Successfully"]);
        return CommonApp::apiEncrypt($res);
    }

    /*Get PO comments details*/
    public function getPOComments(Request $request){
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'po_id'=> 'required',
            'staff_id'=> 'required',
            'user_id'=> 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }

        $whereCondition = [
            ['po_id', '=', $request->po_id],
        ];


        $tecDet = PoComments::select("id", "comment_type", "comment_data","user_id","staff_id","po_id","created_by_type","created_at","created_by","orginalfilename","filepath")
                ->where($whereCondition)
                ->orderBy("created_at", "ASC")->get();
        $tecDetAry = [];
        if (!empty($tecDet)) {
            $imgcount_arr = $id_arr= $last_id_arr=[];
            $user_id = $staff_id = $i = $j = 0;
            foreach ($tecDet as $techdata){
                if($i==0 || $j==0){
                    $user_id = $techdata['user_id'];
                    $staff_id = $techdata['staff_id'];
                }
                if($techdata['comment_type'] == 'File' && $user_id==$techdata['user_id'] && $staff_id==$techdata['staff_id'] ){
                    $id_arr[]=$techdata['id'];
                    $j++;
                }else{
                    if(count($id_arr)>1){
                        $imgcount_arr=array_merge($id_arr,$imgcount_arr);
                        $last_id_arr[]=end($id_arr);
                    }
                    $id_arr=[];
                    $j=0;
                }
                $user_id = $techdata['user_id'];
                $staff_id = $techdata['staff_id'];
                $i++;
                if(count($tecDet)==$i && $techdata['comment_type'] == 'File'){
                    if(count($id_arr)>1){
                        $imgcount_arr=array_merge($id_arr,$imgcount_arr);
                        $last_id_arr[]=end($id_arr);
                    }
                }
            }
            foreach ($tecDet as $techdata) {
                $tecp = [];
                $tecp['id'] = $techdata['id'];
                $tecp['comment_type'] = $techdata['comment_type'];
                $tecp['comment_data'] = ($techdata['comment_type'] == 'File' || $techdata['comment_type'] == 'Audio') ? Storage::disk('s3')->temporaryUrl($techdata['comment_data'], '+60 minutes') : $techdata['comment_data'];
                $tecp['user_id'] = $techdata['user_id'];
                $tecp['staff_id'] = $techdata['staff_id'];
                $tecp['po_id'] = $techdata['po_id'];
                $tecp['created_by_type'] = $techdata['created_by_type'];
                $tecp['file_type'] = ($techdata['comment_type'] == 'File' || $techdata['comment_type'] == 'Audio') ? pathinfo(strtolower($techdata['orginalfilename']), PATHINFO_EXTENSION) : '';
                $tecp['orginalfilename'] = $techdata['orginalfilename'];
                $tecp['filepath'] = $techdata['filepath'];
                $tecp['is_left']=0;
                $tecp['show_name']=0;
                if($techdata['comment_type'] == 'File'){
                    $tecp['is_left'] = in_array($techdata['id'],$imgcount_arr) ? 1 : 0;
                    $tecp['show_name'] = in_array($techdata['id'],$last_id_arr) ? 1 : 0;
                }
                $created_by = '';
                if($techdata['created_by_type']=='Admin')
                {
                    $created_by = User::where('id',$techdata['created_by'])->pluck('name')->first();
                }else if($techdata['created_by_type']=='Staff')
                {
                    $staff = Staff::where('id',$techdata['created_by'])->select('first_name','last_name')->first();
                    $created_by = $staff->first_name." ".$staff->last_name;
                }
                $tecp['created_by'] = $created_by;
                $tecp['created_id'] = $techdata['created_by'];
                $tecp['create_date'] = date('Y-m-d H:i:s',strtotime($techdata['created_at']));

                $tecDetAry[] = $tecp;
            }

            $res = json_encode(["status_code" => 200, "status" => "Success", "data" => $tecDetAry]);

        } else {
            $res = json_encode(["status_code" => 200, "status" => "Success", "data" => $tecDetAry]);
        }
        return CommonApp::apiEncrypt($res);

    }

    public static function delete_po_comments_file(Request $request){
        $header=$request;
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'id' => 'required',
            'reason'=>'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereCondition=[
            ['id','=',$request->id]
        ];
        $files = PoComments::select("id", "po_id", "filepath", "orginalfilename")->where($whereCondition)->first();
        /* PO file delete Log starts */
        try{
            $ip_address = $header->header('Ip-Address') ?? '';
            $platform = $header->header('Platform') ?? '';
            InquiryPOLog::po_delete_comments_file_log($request,$files,$ip_address,$platform);
        }catch(Exception $e){
           // return $e->getMessage();
        }
        /* PO file delete Log end */

        $res = PoComments::deletePOCommentFile($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","meassage"=>"File Deleted Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }
    /* Add PO Comments Audio files  */
    public static function addPOCommentsAudioFile(Request $request){
        $header = $request;
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'audio' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'po_id'=> 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }

        if ($request->hasFile('audio')) {
            $file = $request->file('audio');
            try{
                $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
                $companyFolder = $companyDetails->aws_s3_path;
                $free_storage = CommonApp::calculateFreeStorage($companyDetails->max_storage_size,$companyDetails->storage_used);
                if($file->getSize() > $free_storage && config('constant.plan_storage_size_validation') == 1){
                    return response()->json(["status_code"=>401,"status"=>"failure","error"=>"Your Plan storage is full. Please contact DMS Admin"]);
                }
                $storageUsed = $companyDetails->storage_used*1024*1024;
                $storageToBeAdded = 0;
                $storagepath = $companyFolder.'/PO/Comments/'.$request->po_id;
                $filePath = $file->storeAs(
                    $storagepath, // S3 folder
                    uniqid() . '.' . $file->getClientOriginalExtension(),
                    's3' // disk name
                );
                $storageToBeAdded += $file->getSize();
                $companyDetails->storage_used = ($storageUsed + (int)$storageToBeAdded) / (1024 * 1024);
                $companyDetails->save();

                $inArr = [];
                $inArr['company_id'] = $request->company_id;
                $inArr['workspace_id'] = $request->workspace_id;
                $inArr['user_id'] = $request->user_id;
                $inArr['comment_type'] = 'Audio';
                $inArr['comment_data'] = $filePath;
                $inArr['po_id'] = $request->po_id;
                $inArr['staff_id'] = $request->staff_id;
                $inArr['created_by'] = $request->staff_id > 0 ? $request->staff_id : $request->user_id;
                $inArr['created_by_type'] = $request->staff_id > 0 ? "Staff" : "Admin";
                $inArr['created_at'] = date('Y-m-d H:i:s');
                $inArr['filepath'] = $filePath;
                PoComments::insert($inArr);

            }catch(Exception $e){
                return response()->json(["status_code"=>401,"error"=>$e]);
            }

            /* Generate PO comments Log starts */
            try {
                $ip_address = $header->header('Ip-Address') ?? '';
                $platform = $header->header('Platform') ?? '';
                InquiryPOLog::generate_po_comments_log($request->po_id, $request,$ip_address,$platform,"Audio");
            } catch (Exception $e) {
                Log::info($e->getMessage());
            }
            /* Generate PO comments Log end */
            $res = json_encode(["status_code" => 200, "status" => "Success", "message" => "Comment Added Successfully"]);
            //return CommonApp::apiEncrypt($res);
            return $res;
        }
        $res = json_encode(["status_code" => 401, "status" => "error", "message" => "Failed to upload the audio file."]);

        return $res;
    }

    /* Upload File/media To PO */
    public static function upload_file_po(Request $request){
        $header=$request;
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'type' => 'required',
            'po_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $comments = $request->type =='Comments'? 1 : 0;
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $companyFolder = $companyDetails->aws_s3_path;
        $free_storage = CommonApp::calculateFreeStorage($companyDetails->max_storage_size,$companyDetails->storage_used);
       // $free_storage = ($companyDetails->max_storage_size - ($companyDetails->storage_used + (int)config('constant.plan_storage_free_mb')))*1024*1024;
        $storageUsed = $companyDetails->storage_used*1024*1024;
        $storageToBeAdded = 0;
        $filedata = [];
        if($request->file('file')){
            $file = $request->file('file');
            if($file->getSize() > $free_storage && config('constant.plan_storage_size_validation') == 1){
                return response()->json(["status_code"=>401,"status"=>"failure","error"=>"Your Plan storage is full. Please contact DMS Admin"]);
            }
            $string = str_replace(' ', '_', $file->getClientOriginalName()); // Replaces all spaces with hyphens.
            $nameOfFile = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
            $fileName = time().'_'.$nameOfFile;
            //$filepath = $companyFolder.'/Inquiry/'.$request->referenceId.'/'.$fileName;
            if($comments==0)
                $filepath = $companyFolder.'/PO/'.$fileName;
            else
                $filepath = $companyFolder.'/PO/Comments/'.$request->po_id.'/'.$fileName;

            Uploads::orderAddtionalSpec($file,$filepath);
            if($comments==0)
            {
                $filedata['po_id']=$request->po_id;
                $filedata['parent_po_id']=$request->po_parent_id ?? 0;
                $filedata['temp_id']=$request->referenceId;
                $filedata['filename']=$fileName;
                $filedata['orginalfilename']=$file->getClientOriginalName();
                $filedata['filepath']=$filepath;
                $filedata['filesize']=$file->getSize();
                $filedata['media_type']=$request->type;
                $filedata['company_id']=$request->company_id ?? 0;
                $filedata['workspace_id']=$request->workspace_id ?? 0;
                $filedata['created_at']=date('Y-m-d H:i:s');
            }else{
                $filedata['po_id']=$request->po_id;
                $filedata['company_id']=$request->company_id ?? 0;
                $filedata['workspace_id']=$request->workspace_id ?? 0;
                $filedata['comment_type']='File';
                $filedata['comment_data']=$filepath;
                $filedata['filename']=$fileName;
                $filedata['orginalfilename']=$file->getClientOriginalName();
                $filedata['filepath']=$filepath;
                $filedata['filesize']=$file->getSize();
                $filedata['user_id']=$request->user_id ?? 0;
                $filedata['staff_id']=$request->staff_id ?? 0;
                $filedata['created_by']=$request->staff_id > 0 ? $request->staff_id : $request->user_id;
                $filedata['created_by_type']=$request->staff_id > 0 ? "Staff" : "Admin";;
                $filedata['created_at']=date('Y-m-d H:i:s');
            }
            $storageToBeAdded += $file->getSize();

            try{
                if($comments==0)
                    InquiryMediaPO::insert($filedata);
                else
                    PoComments::insert($filedata);
                $companyDetails->storage_used = ($storageUsed + (int)$storageToBeAdded) / (1024 * 1024);
                $companyDetails->save();
                /* PO file Add Log starts */
                try{
                    if((isset($request->upload_type) && $request->upload_type=='edit') || $comments==1){
                        $ip_address = $header->header('Ip-Address') ?? '';
                        $platform = $header->header('Platform') ?? '';
                        InquiryPOLog::po_add_media_log($request->referenceId,$request,$filedata,$ip_address,$platform,$comments);
                    }
                }catch(Exception $e){
                }
                /* PO file Add Log end */
            }catch(Exception $e){
                return response()->json(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            }

            $res['files'] = InquiryMediaPO::getFiles($request->po_id,$request->type,$request->referenceId);
            $res['serverURL'] = config('filesystems.disks.s3.url');

            return response()->json(["status_code"=>200,'status'=>"success","message"=>"Files Added Successfully","files"=>$res],200);
        }else{
            $res['files'] = InquiryMediaPO::getFiles($request->po_id,$request->type,$request->referenceId);
            $res['serverURL'] = config('filesystems.disks.s3.url');
            return response()->json(["status_code"=>200,'status'=>"failure","message"=>"Something went wrong","files"=>$res],200);
        }
    }

}
