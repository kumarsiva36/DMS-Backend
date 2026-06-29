<?php
namespace App\Http\Controllers\WebSite\Inquiry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Common\CommonApp;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Common\Uploads;
use App\Imports\ImportXL;
use App\Models\CompanySettings;
use App\Models\InquiryMedia;
use App\Models\Inquiries;
use App\Models\InquiryAdditional;
use App\Models\InquirySku;
use App\Models\InquiryFactoryResponse;
use App\Models\InquiryContact;
use App\Models\InquiryFactoryFeedback;
use App\Models\InquiryLog;
use App\Models\InquiryMaster;
use App\Models\Staff;
use App\Models\User;
use App\Models\InquiryMailSentDetails;
use Exception;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
//use setasign\Fpdi\Fpdi;
use Maatwebsite\Excel\Facades\Excel;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class Inquiry extends Controller
{
    public static function inquiry_file_upload(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'type' => 'required',
            'referenceId' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $companyFolder = $companyDetails->aws_s3_path;
        //$free_storage = ($companyDetails->max_storage_size - ($companyDetails->storage_used + (int)config('constant.plan_storage_free_mb')))*1024*1024;
        $free_storage = CommonApp::calculateFreeStorage($companyDetails->max_storage_size,$companyDetails->storage_used);
        $storageUsed = $companyDetails->storage_used*1024*1024;
        $storageToBeAdded = 0;
        $inquiry_id = $request->inquiry_id ?? '';
        $filedata = [];
        if($request->file('file')){
            $file = $request->file('file');
            if($file->getSize() > $free_storage && config('constant.plan_storage_size_validation') == 1){
                return response()->json(["status_code"=>401,"status"=>"failure","error"=>"Your Plan storage is full. Please contact DMS Admin"]);
            }
            $string = str_replace(' ', '_', $file->getClientOriginalName()); // Replaces all spaces with hyphens.
            $nameOfFile = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
            $fileName = time().'_'.$nameOfFile;
            $filepath = $companyFolder.'/Inquiry/'.$request->referenceId.'/'.$fileName;
            Uploads::orderAddtionalSpec($file,$filepath);
            $filedata['filename']=$fileName;
            $filedata['orginalfilename']=$file->getClientOriginalName();
            $filedata['filepath']=$filepath;
            $filedata['filesize']=$file->getSize();
            $storageToBeAdded += $file->getSize();
            $filedata['temp_id']=$request->referenceId;
            $filedata['media_type']=$request->type;
            $filedata['inquiry_id']=$request->inquiry_id ?? 0;
            $filedata['company_id']=$request->company_id ?? 0;
            $filedata['workspace_id']=$request->workspace_id ?? 0;
            $filedata['created_at']=date('Y-m-d H:i:s');

            if($request->type =="MeasurementSheet" && (stristr($filedata['orginalfilename'],'.xls'))){
                $datas = Excel::toArray(new ImportXL,request()->file('file'));
                $filedata['datas']=json_encode($datas);
            }else if((stristr($filedata['orginalfilename'],'.pdf'))){
                $path = public_path() . '/MeasurementSheet/';
                if (!file_exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }
               // $path = public_path();
                $name = $request->referenceId.$file->getClientOriginalName();
                $file->move($path, $name);
            }
            //else if($request->type =="MeasurementSheet" && (stristr($filedata['orginalfilename'],'.pdf'))){
            //     $path = public_path();
            //     $name = $request->referenceId.$file->getClientOriginalName();
            //     $file->move($path, $name);
            // }

            try{
                //InquiryMedia::createThumbs($file,$fileName,$companyFolder,120,120);
                InquiryMedia::insert($filedata);
                $companyDetails->storage_used = ($storageUsed + (int)$storageToBeAdded)/(1024*1024);
                $companyDetails->save();
                /* Inquiry file Add Log starts */
                try{
                    if(isset($request->upload_type) && $request->upload_type=='edit'){
                        InquiryLog::edit_inquiry_media_log($request->referenceId,$request,$filedata);
                    }
                }catch(Exception $e){
                }
                /* Inquiry file Add Log end */
            }catch(Exception $e){
                return response()->json(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            }

            $res['files'] = InquiryMedia::getFiles($request->referenceId,$request->type,$inquiry_id);
            $res['serverURL'] = config('filesystems.disks.s3.url');

            return response()->json(["status_code"=>200,'status'=>"success","message"=>"Files Added Successfully","files"=>$res],200);
        }else{
            $res['files'] = InquiryMedia::getFiles($request->referenceId,$request->type,$inquiry_id);
            $res['serverURL'] = config('filesystems.disks.s3.url');
            return response()->json(["status_code"=>200,'status'=>"failure","message"=>"Something went wrong","files"=>$res],200);
        }
    }

    public static function save_inquiry(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'article_id' => 'required',
            'style_no'=>'required',
            'total_qty'=>'required',
            'fabric_type_id' => 'required',
            'language'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $data = [];
        $data['category_id']= $request->category_id ?? 0;
        $data['media_reference_id']= $request->referenceId ?? '';;
        $data['article_id']= $request->article_id ?? 0;
        $data['style_no']= $request->style_no ?? '';
        $data['company_id']= $request->company_id ?? 0;
        $data['user_id']= $request->user_id ?? 0;
        $data['staff_id']= $request->staff_id ?? 0;
        $data['workspace_id']= $request->workspace_id ?? 0;
        $data['fabric_type_id']= $request->fabric_type_id ?? 0;
        $data['fabric_GSM']= $request->fabric_GSM ?? '';
        $data['yarn_count']= $request->yarn_count ?? '';
        $data['style_article_description']= $request->style_article_description ?? '';
        $data['special_finish']= $request->special_finish ?? '';
        $data['total_qty']= $request->total_qty ?? 0;
        $data['patterns']= $request->patterns ?? '';
        $data['jurisdiction']= $request->jurisdiction ?? '';
        $data['customs_declaraion_document']= $request->customs_declaraion_document ?? '';
        $data['penality']= $request->penality ?? '';
        $data['print_image']= $request->print_image  ?? '';
        $data['print_size']= $request->print_size ?? '';
        $data['print_type']= $request->print_type ?? '';
        $data['print_no_of_colors']= $request->print_no_of_colors  ?? '';
        $data['main_lable']= $request->main_lable  ?? '';
        $data['main_lable_info']= $request->main_lable_info  ?? '';
        $data['washcare_lable']= $request->washcare_lable  ?? '';
        $data['washcare_lable_info']= $request->washcare_lable_info ?? '';
        $data['hangtag_lable']= $request->hangtag_lable ?? '';
        $data['hangtag_lable_info']= $request->hangtag_lable_info ?? '';
        $data['barcode_lable']= $request->barcode_lable ?? '';
        $data['barcode_lable_info']= $request->barcode_lable_info ?? '';
        $data['trims_nominations']= $request->trims_nominations ?? '';
        $data['poly_bag_size']= $request->poly_bag_size ?? '';
        $data['poly_bag_material']= $request->poly_bag_material ?? '';
        $data['poly_bag_price']= $request->poly_bag_price ?? '';
        $data['carton_bag_dimensions']= $request->carton_bag_dimensions ?? '';
        $data['carton_color']= $request->carton_color ?? '';
        $data['carton_material']= $request->carton_material ?? '';
        $data['carton_edge_finish']= $request->carton_edge_finish ?? '';
        $data['carton_mark']= $request->carton_mark ?? '';
        $data['make_up']= $request->make_up ?? '';
        $data['films_cd']= $request->films_cd ?? '';
        $data['picture_card']= $request->picture_card ?? '';
        $data['inner_cardboard']= $request->inner_cardboard ?? '';
        $data['shipping_size']= $request->shipping_size ?? '';
        $data['air_frieght']= $request->air_frieght ?? '';
        $data['estimate_delivery_date']= $request->estimate_delivery_date ?? '';
        $data['due_date']= $request->due_date ?? '';
        $data['incoterms']= $request->incoterms ?? 0;
        $data['payment_terms']= $request->payment_terms ?? '';
        $data['payment_instructions']= $request->payment_instructions ?? '';
        $data['target_price']= $request->target_price ?? 0;
        $data['forbidden_substance_info']= $request->forbidden_substance_info ?? '';
        $data['testing_requirements']= $request->testing_requirements ?? '';
        $data['sample_requirements']= $request->sample_requirements ?? '';
        $data['special_requests']= $request->special_requests ?? '';
        $data['currency']= $request->currency ?? '';
        $data['measurement_sheet']='';
        $data['fabric_type']= $request->fabric_type ?? '';
        $data['poly_bag_print']= $request->poly_bag_print ?? '';
        $measurement_sheet= $request->measurement_Chart ?? '';

        if($measurement_sheet!='' && !empty($measurement_sheet)){
            $msarr = [];
            foreach ($measurement_sheet as $i => $sheet) {
                foreach ($sheet as $key => $value) {
                    $msarr[$i][$key]=$value;
                }
            }

            $data['measurement_sheet']=json_encode($msarr);
        }
        //Inquiry::inquiry_pdf_generate('477',$data,$request); exit;
        DB::beginTransaction();
        try{
            Inquiries::insert($data);
            $inquiryID = DB::getPdo()->lastInsertId();
            $dataToUpdate['inquiry_id']=$inquiryID;
            InquiryMedia::where('temp_id',$request->referenceId)->update($dataToUpdate);

            if(isset($request->sku_details)){
                $orderSkuArr = [];
                $orderSkuArr['inquiry_id']= $inquiryID;
                foreach ($request->sku_details as $sku){
                    $sku = (array)$sku;
                    $orderSkuArr['color_id']=$sku['color_id'];
                    $orderSkuArr['size_id']=$sku['size_id'];
                    $orderSkuArr['quantity']=$sku['quantity'];
                    $orderSkuArr['color_ratio']=$sku['color_ratio']?? 0;
                    $orderSkuArr['size_ratio']=$sku['size_ratio']?? 0;
                    $orderSkuArr['created_at']=date('Y-m-d H:i:s');
                    InquirySku::insert($orderSkuArr);
                }
            }
            if(isset($request->trims_additional)){
                $orderTrims = [];
                $orderTrims['inquiry_id']= $inquiryID;
                foreach ($request->trims_additional as $trims){
                    $trims = (array)$trims;
                    $orderTrims['label']=$trims['label_name'];
                    $orderTrims['label_description']=$trims['label_description'];
                    $orderTrims['media_type']=$trims['label_id'];
                    $orderTrims['company_id']=$request->company_id ?? 0;;
                    $orderTrims['workspace_id']=$request->workspace_id ?? 0;;
                    $orderTrims['created_at']=date('Y-m-d H:i:s');
                    InquiryAdditional::insert($orderTrims);
                }
            }
            /* Inquiry Create Log starts */
            try{

                InquiryLog::create_inquiry_log($inquiryID,$request);
            }catch(Exception $e){

            }
            /* Inquiry Create Log end */
            $language = $request->language =='jp'?'jp':'en';
            Inquiry::inquiry_pdf_generate($inquiryID,$data,$request,$language,0);
        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Inquiry Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function inquiry_pdf_generate($inquiryID,$data,$request,$language='jp',$translate='1'){
        return true;
        $lang = ($language=='') ? 'en' : $language;
        App::setlocale($lang);
        $request->inquiry_id = $inquiryID; //$inquiryID;
        $media['files'] = Inquiries::inquiry_media($request);
        $media['serverURL'] = config('filesystems.disks.s3.url');
        $user = CommonApp::getUserDetailsById($request->user_id);
        $company = CommonApp::getCompanyDetailsbyID($request->company_id);
        $sku = Inquiries::inquiry_sku($request);
        $colors = [];
        $sizes = [];
        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size']);
            }
        }
        $datas['lang'] = $lang;
        $datas['translate'] = $translate;
        $datas['inquiryID'] = $inquiryID;
        $datas['data'] = $data;
        $datas['request'] = $request;
        $datas['media'] = $media;
        $datas['sku'] = $sku;
        $datas['inqdet'] = Inquiries::inquiry_details($request);
        $datas['sizes'] = array_unique($sizes,SORT_REGULAR);
        $datas['colors'] = array_unique($colors,SORT_REGULAR);
        $datas['user'] = $user;
        $datas['logo'] = $company->logo;
        view()->share("datas",$datas);
        //return view('InquiryPDF');
        $pdf = Pdf::loadView('InquiryPDF');
        $pdf->setPaper('A4', 'portrait');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
        $pdf->setOption("enable_php", true);
        $filePath = public_path() . '/Inquiry/';
        if (!file_exists($filePath)) {
            File::makeDirectory($filePath, 0777, true, true);
        }
        if($lang=='en')
            $path = public_path() . '/Inquiry/' .$inquiryID.'.pdf';
        else
            $path = public_path() . '/Inquiry/' .$inquiryID.'_jp.pdf';

        $pdf->save($path);

        $enpdf = public_path() . '/Inquiry/' .$inquiryID.'.pdf';
        $jppdf = public_path() . '/Inquiry/' .$inquiryID.'_jp.pdf';
        if(!file_exists($enpdf) || !file_exists($jppdf)){
            $pdflang = ($lang=='en') ? 'jp' : 'en';
            Inquiry::inquiry_pdf_generate($inquiryID,$data,$request,$pdflang,1);
        }

        //pdf merge
        $f=[$path];
        foreach ($media['files'] as $m){

           // if($m->media_type =="MeasurementSheet" && (stristr($m->orginalfilename,'.pdf')) ){
            if(stristr($m->orginalfilename,'.pdf') ){
                $filepath = public_path()."/MeasurementSheet/".$data['media_reference_id'].$m->orginalfilename;
                $f[]=$filepath;
            }


        }
        if(count($f)>1)
        {
            try{
                Inquiry::pdfmerge($inquiryID,$f,$lang);
            }catch(Exception $e){
                 Log::info($e->getMessage());
            }
        }

    }

    public static function get_inquirys(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['inquiry.company_id','=',$request->company_id],
            ['inquiry.workspace_id','=',$request->workspace_id]
        ];
        $request->page = (isset($request->page) && $request->page!='')?$request->page:1;
        $inquiries = Inquiries::get_inquirys($whereConditions,$request);
        $pdfpath  = config('app.public_url').'Inquiry/';
        $factories = Inquiries::get_inquiry_factories($request);
        $articles = Inquiries::get_inquiry_articles($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiries,"pdfpath"=>$pdfpath,"factories"=>$factories,"articles"=>$articles],200);
        return CommonApp::webEncrypt($res);
    }

    public static function inquiry_details(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            //'company_id' => 'required',
            //'workspace_id' => 'required',
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $inquiry = Inquiries::inquiry_details($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiry],200);
        return CommonApp::webEncrypt($res);
    }
    public static function inquiry_sku(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            //'company_id' => 'required',
            //'workspace_id' => 'required',
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $sku = Inquiries::inquiry_sku($request);
        $colors = [];
        $sizes = [];
        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size'],"category"=>$s['category']);
            }
        }
        $data['sku'] =$sku;
        $data['colors'] =array_unique($colors,SORT_REGULAR);
        $data['sizes'] =array_unique($sizes,SORT_REGULAR);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }
    public static function inquiry_media(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            //'company_id' => 'required',
            //'workspace_id' => 'required',
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $media['files'] = Inquiries::inquiry_media($request);
       // $media['serverURL'] = config('filesystems.disks.s3.url');
       $media['serverURL'] = '';
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$media],200);
        return CommonApp::webEncrypt($res);
    }

    public static function save_inquiry_factory_response(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory_id' => 'required',
            'inquiry_id' => 'required',
            'user_id' => 'required',
            'user_type' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $filedata['inquiry_id']=$request->inquiry_id ?? 0;
        $filedata['factory_id']=$request->factory_id ?? 0;
        $filedata['price']=$request->price ?? 0;
        $filedata['comments']=$request->comments ?? '';
        $filedata['updated_by_type']=$request->user_type ?? 'user';
        $filedata['updated_by']=$request->user_id ?? 0;

        $contact_id = InquiryContact::get_factory_contact_id($request->factory_id);

        $filedata['factory_contact_id']=$contact_id;

        InquiryFactoryResponse::insert($filedata);

        $buyer = Inquiries::get_buyer_email($request->inquiry_id);
        if(!empty($buyer)){
            $factory = InquiryContact::where('factory_id',$request->factory_id)->select('factory','contact_person')->first()->toArray();
            $details['name'] = $buyer['name'];
            $details['factory_name'] = $factory['factory'];
            $details['contact_name'] = $factory['contact_person'];
            $details["email"] = $buyer['email'];
            $details['price']=$request->price ?? 0;
            $details['inquiry_id']=$request->inquiry_id ?? 0;
            $details['comments']=$request->comments ?? '';
            $details["title"] = "Inquiry Response from ". $factory['factory']."(IN-".$request->inquiry_id.")";
            if($buyer['email']){
                Mail::send('InquiryResponseMail', ['details'=>$details], function($message)use($details) {
                    $message->to($details["email"])
                            ->subject($details["title"]);
                });
            }
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Inquiry Response Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }
    public static function save_inquiry_contact(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory' => 'required|max:60',
            'contact_person' => 'required|max:60',
            'contact_number' => 'required|unique:inquiry_contact',
            'contact_email' => 'required|email:dns,rfc|unique:inquiry_contact',
            'address' => 'required',
            'city' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $filedata['factory']=$request->factory ?? "";
        $filedata['contact_person']=$request->contact_person ?? "";
        $filedata['contact_number']=$request->contact_number ?? "";
        $filedata['contact_email']=$request->contact_email ?? '';
        $filedata['address']=$request->address ?? '';
        $filedata['city']=$request->city ?? '';
        $filedata['factory_id']=$request->factory_id ?? 0;
        $filedata['created_at']=date('Y-m-d H:i:s');
        InquiryContact::insert($filedata);
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Factory Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_inquiry_contact(){
        $factories = InquiryContact::select('id','factory','contact_person','contact_number','contact_email')->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$factories],200);
        return CommonApp::webEncrypt($res);
    }

    public static function send_inquiry(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory_id' => 'required',
            'inquiry_id' => 'required',
            'user_id' => 'required',
            'company_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $facts = implode('||',$request->factory_id);
        $facts = $facts.'||';
        $update = DB::table('inquiry')
        ->where('id', $request->inquiry_id)
        ->limit(1)
        ->update(array('factory_ids' => DB::raw("concat(ifnull(factory_ids,'||'), '".(string)$facts."')")));
        $factories = InquiryContact::select('id','factory','contact_person','contact_email')->whereIn('id',$request->factory_id)->get();
        $userDetails = User::where('id',$request->user_id)->pluck('name')->first();
        $companyDetail = CompanySettings::where('id',$request->company_id)->pluck('company_name')->first();
        // dd($update);
        foreach ($factories as $fact){
            $details['created_by'] = $fact->contact_person;
            $details["email"] = $fact->contact_email;
            $details["title"] = 'Inquiry Request - '.$companyDetail.' - '.$userDetails;
            $file = public_path() . '/' .$request->inquiry_id.'.pdf';
            if(!file_exists($file))
             $file = public_path() . '/Inquiry/' .$request->inquiry_id.'.pdf';

            if($fact->contact_email){
                Mail::send('InquiryMail', ['details'=>$details], function($message)use($details, $file) {
                    $message->to($details["email"])
                            ->subject($details["title"]);
                    if(file_exists($file))
                        $message->attach($file);
                });
            }
        }
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Inquiry sent Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function inquiry_factory_list(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $factories = Inquiries::getFactories($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$factories],200);
        return CommonApp::webEncrypt($res);
    }

    public static function inquiry_factory_response(Request $request ){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $factories = InquiryFactoryResponse::FactoryResponse($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$factories],200);
        return CommonApp::webEncrypt($res);
    }

    public static function delete_inquiry(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
            'company_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        Inquiries::deleteInquiry($request);
        /* Inquiry file Add Log starts */
        try{
            InquiryLog::delete_inquiry_log($request);
        }catch(Exception $e){
        }
        /* Inquiry file Add Log end */
        $res = json_encode(["status_code"=>200,'status'=>"success","meassage"=>"Inquiry Deleted Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function factory_get_inquirys(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $page = (isset($request->page) && $request->page!='')?$request->page:1;
        $contact_id = InquiryContact::where('factory_id',$request->factory_id)->pluck('id')->first();
        if((int)$contact_id > 0){

            $inquiries = Inquiries::get_factory_inquirys($contact_id,$request->factory_id,$page,(array)$request);
            $response = InquiryFactoryResponse::where('factory_id',$request->factory_id)->pluck('inquiry_id');
            $articals = Inquiries::get_factory_articals($contact_id);
            $users = Inquiries::get_factory_users($contact_id);
            $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiries,"response"=>$response,"articles"=>$articals,"users"=>$users],200);
            return CommonApp::webEncrypt($res);
        }else{
            $data = (object) array('data' => []);
            $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data,"response"=>[],"articles"=>[],"users"=>[]],200);
            return CommonApp::webEncrypt($res);
        }

    }
    public static function factory_inquiry_contact(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['factory_id','=',$request->factory_id]
        ];

        $inquiries = InquiryContact::where($whereConditions)->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiries],200);
        return CommonApp::webEncrypt($res);
    }
    public static function update_inquiry_contact(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory' => 'required|max:60',
            'contact_person' => 'required|max:60',
            'contact_number' => 'required',
            'contact_email' => 'required|email:dns,rfc',
            'address' => 'required',
            'city' => 'required',
            'factory_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $filedata['factory']=$request->factory ?? "";
        $filedata['contact_person']=$request->contact_person ?? "";
        $filedata['contact_number']=$request->contact_number ?? "";
        $filedata['contact_email']=$request->contact_email ?? '';
        $filedata['address']=$request->address ?? '';
        $filedata['city']=$request->city ?? '';

        InquiryContact::where('factory_id',$request->factory_id)->update($filedata);
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Contact Updated Successfully"],200);
        return CommonApp::webEncrypt($res);
    }
    public static function get_inquiry_master(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
           // 'type' => 'required',
            'referenceId'=>'required',
            'company_id' =>'required',
            'workspace_id' =>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        if(isset($request->type) && $request->type!=''){
            $whereConditions=[
                ['type','=',$request->type]
            ];

            $inquiries = InquiryMaster::where($whereConditions)
                        // ->where(function ($query) use($request) {
                        //     $query->where('inq_reference_id', '=', 0)
                        //         ->orWhere('inq_reference_id', '=', $request->referenceId);
                        // })
                        ->where(function ($query) use($request) {
                            $query->where('company_id', '=', $request->company_id)
                                ->where('workspace_id', '=', $request->workspace_id)
                                ->orWhere('inq_reference_id', '=', 0)
                                ->orWhere('inq_reference_id', '=', $request->referenceId);
                        })
                        ->get();
        }else{
            // $inquiries = InquiryMaster::where(function ($query) use($request) {
            //                 $query->where('inq_reference_id', '=', 0)
            //                     ->orWhere('inq_reference_id', '=', $request->referenceId);
            //             })
            //             ->get();
            $inquiries = InquiryMaster::where(function ($query) use($request) {
                $query->where('company_id', '=', $request->company_id)
                        ->where('workspace_id', '=', $request->workspace_id)
                        ->orWhere('inq_reference_id', '=', 0)
                        ->orWhere('inq_reference_id', '=', $request->referenceId);
            })
            ->get();
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiries],200);
        return CommonApp::webEncrypt($res);
    }

    public static function factory_inquiry_response(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
            'factory_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['inquiry_id','=',$request->inquiry_id],
            ['factory_id','=',$request->factory_id]
        ];
        $response = InquiryFactoryResponse::where($whereConditions)->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$response],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_buyer_inquiry_list(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['user_id','=',$request->user_id]
        ];
        $response = Inquiries::where($whereConditions)->select('id','style_no')->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$response],200);
        return CommonApp::webEncrypt($res);
    }
    public static function get_inquiry_factory_list(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['id','=',$request->inquiry_id]
        ];
        $response = Inquiries::where($whereConditions)->pluck('factory_ids')->first();
        $data= array();
        if($response!=""){
            $factory_ids=array_unique(explode("||",$response));
            $factories = InquiryContact::select('id','factory')->whereIn('id',$factory_ids)->get();
            $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$factories],200);
            return CommonApp::webEncrypt($res);
        }
        else
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }
    public static function save_factory_feedback(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'factory_id' => 'required',
            'lowest_price' => 'required',
            'ontime_delivery' => 'required',
            'vendor_buyer_relation' => 'required',
            'sample_submission' => 'required',
            'communication' => 'required',
            'less_quality_issue' => 'required',
            'good_sell_through' => 'required',
            'collaborative_approach' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $filedata['inquiry_id']=$request->inquiry_id ?? 0;
        $filedata['company_id']=$request->company_id ?? 0;
        $filedata['workspace_id']=$request->workspace_id ?? 0;
        $filedata['buyer_id']=$request->user_id ?? 0;
        $filedata['factory_contact_id']=$request->factory_id ?? 0;
        $filedata['lowest_price']=$request->lowest_price ?? 0;
        $filedata['lowest_price_comments']=$request->lowest_price_comments ?? "";
        $filedata['ontime_delivery']=$request->ontime_delivery ?? 0;
        $filedata['ontime_delivery_comments']=$request->ontime_delivery_comments ?? '';
        $filedata['vendor_buyer_relation']=$request->vendor_buyer_relation ?? 0;
        $filedata['vendor_buyer_relation_comments']=$request->vendor_buyer_relation_comments ?? '';
        $filedata['sample_submission']=$request->sample_submission ?? 0;
        $filedata['sample_submission_comments']=$request->sample_submission_comments ?? '';
        $filedata['communication']=$request->communication ?? 0;
        $filedata['communication_comments']=$request->communication_comments ?? '';
        $filedata['less_quality_issue']=$request->less_quality_issue ?? 0;
        $filedata['less_quality_issue_comments']=$request->less_quality_issue_comments ?? '';
        $filedata['good_sell_through']=$request->good_sell_through ?? 0;
        $filedata['good_sell_through_comments']=$request->good_sell_through_comments ?? '';
        $filedata['collaborative_approach']=$request->collaborative_approach ?? 0;
        $filedata['collaborative_approach_comments']=$request->collaborative_approach_comments ?? '';
        InquiryFactoryFeedback::insert($filedata);
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Factory Feedback Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function delete_inquiry_media(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'media_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        /* Inquiry file delete Log starts */
        try{
            if(isset($request->upload_type) && $request->upload_type=='edit'){
                InquiryLog::delete_inquiry_media_log($request);
            }
        }catch(Exception $e){
        }
        /* Inquiry file delete Log end */

        $res = Inquiries::deleteInquiryMedia($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","meassage"=>"File Deleted Successfully"],200);
        return CommonApp::webEncrypt($res);
    }
    public static function get_factory_ratings(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $res = InquiryFactoryFeedback::get_factory_ratings($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$res],200);
        return CommonApp::webEncrypt($res);
    }

    public static function pdfmerge($inquiryID,$f,$lang='') {
        $files = $f;
        $pdf = PdfMerger::init();
        foreach ($files as $file) {
            if(file_exists($file))
                $pdf->addPDF($file, 'all','P');
        }
        $pdf->merge("","",$inquiryID);
        //$pdf->save(public_path()."/Inquiry/".$inquiryID.".pdf",'browser');
        if($lang=='en')
            $path = public_path() . '/Inquiry/' .$inquiryID.'.pdf';
        else
            $path = public_path() . '/Inquiry/' .$inquiryID.'_jp.pdf';

        $pdf->save($path,'file');
    }

    public static function check_factory_feedback(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
            'factory_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $res = InquiryFactoryFeedback::where('factory_contact_id', $request->factory_id)->where('inquiry_id', $request->inquiry_id)->get()->toArray();
        if(count($res) > 0){
            $res = json_encode(["status_code"=>201,'status'=>"Already Feedback Added","data"=>$res],201);
            return CommonApp::webEncrypt($res);
        }else{
            $res = json_encode(["status_code"=>200,'status'=>"success"],200);
            return CommonApp::webEncrypt($res);
        }
    }

    public static function check_buyer_notification(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $res = Inquiries::check_buyer_notification($request);

        $res = json_encode(["status_code"=>200,'status'=>"success",'notifications'=>$res],200);
        return CommonApp::webEncrypt($res);
    }

    /* To Check If the Factory has any New Inquiry Notification */
    public static function check_factory_notification(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $res = Inquiries::check_factory_notification($request);

        $res = json_encode(["status_code"=>200,'status'=>"success",'notifications'=>$res],200);
        return CommonApp::webEncrypt($res);
    }

    /* To get the factory list who are all not using the DMS */
    public static function get_factory_list_response(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $res = InquiryFactoryResponse::get_factory_list_response($request);
        $currency = Inquiries::where('id',$request->inquiry_id)->pluck('currency')->first();
        $res = json_encode(["status_code"=>200,'status'=>"success",'notifications'=>$res,'currency'=>$currency],200);
        return CommonApp::webEncrypt($res);
    }

    public static function save_buyer_inquiry_factory_response(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory_id' => 'required',
            'inquiry_id' => 'required',
            'user_id' => 'required',
            'user_type' => 'required',
            'price'=>'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $filedata['inquiry_id']=$request->inquiry_id ?? 0;
        $filedata['factory_id']=0; //This is to denote that the factory response added for the factory that is out of DMS
        // $filedata['factory_id']= $request->factory_id ?? 0;
        $filedata['factory_contact_id']=$request->factory_id ?? 0;
        $filedata['price']=$request->price ?? 0;
        $filedata['comments']=$request->comments ?? '';
        $filedata['updated_by_type']=$request->user_type ?? 'user';
        $filedata['updated_by']=$request->user_id ?? 0;

        InquiryFactoryResponse::insert($filedata);

        $buyer = Inquiries::get_buyer_email($request->inquiry_id);
        if(!empty($buyer)){
            $factory = InquiryContact::where('id',$request->factory_id)->select('factory','contact_person')->first()->toArray();
            $details['name'] = $buyer['name'];
            $details['factory_name'] = $factory['factory'];
            $details['contact_name'] = $factory['contact_person'];
            $details["email"] = $buyer['email'];
            $details['price']=$request->price ?? 0;
            $details['inquiry_id']=$request->inquiry_id ?? 0;
            $details['comments']=$request->comments ?? '';
            $details["title"] = "Inquiry Response from ". $factory['factory']."(IN-".$request->inquiry_id.")";
            if($buyer['email']){
                Mail::send('InquiryResponseMail', ['details'=>$details], function($message)use($details) {
                    $message->to($details["email"])
                            ->subject($details["title"]);
                });
            }
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Inquiry Response Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function add_inquiry_master(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $request->content = trim($request->content);
        $request->referenceId = $request->referenceId?? '0';
        $validator = Validator::make((array)$request, [
            'type' => 'required',
            //'content'=>'required',
            'referenceId'=>'required',
            'company_id'=>'required',
            'workspace_id'=>'required',
            'content' => ['required', Rule::unique('inquiry_master')
                        ->where(function ($query) use ($request) {
                            // $query->where('type',$request->type);
                            // $query->where('inq_reference_id','=',$request->referenceId);
                            // $query->orwhere('inq_reference_id','=','0');
                            $query->where([
                                ['type','=',$request->type],
                                ['company_id','=',$request->company_id],
                                ['workspace_id','=',$request->workspace_id],
                                //['inq_reference_id','=',$request->referenceId],
                            ])->orWhere([
                                ['inq_reference_id','=','0'],
                                ['type','=',$request->type]
                            ]);

                            return $query;
                        })],
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $filedata['type']=$request->type;
        $filedata['content']=$request->content;
        $filedata['company_id']=$request->company_id ?? 0;
        $filedata['workspace_id']=$request->workspace_id ?? 0;
        $filedata['inq_reference_id']=$request->referenceId;
        $filedata['created_at']=date('Y-m-d H:i:s');

        InquiryMaster::insert($filedata);
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Data Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function inquiry_pdf_download(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $ms_sheet = InquiryMedia::inquiry_pdf_download($request);
        $data['ms_sheet'] = $ms_sheet;
        $data['inq_pdf'] =config('app.public_url').'Inquiry/'.$request->inquiry_id.'.pdf';
        $data['serverURL'] = config('filesystems.disks.s3.url');
        $res = json_encode(["status_code"=>200,'status'=>"success",'data'=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    /* To Edit The Inquiry */
    public static function edit_inquiry(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions = [
            ['id','=',$request->inquiry_id]
        ] ;
        $inquiry= Inquiries::get_the_inquiry($whereConditions);
        $res = json_encode(["status_code"=>200,'status'=>"success",'data'=>$inquiry],200);
        return CommonApp::webEncrypt($res);
    }

    /* To Update The Inquiry */
    public static function update_inquiry(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id'=>'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'article_id' => 'required',
            'style_no'=>'required',
            'total_qty'=>'required',
            'fabric_type_id' => 'required',
            'language'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = [];
        $data['category_id']= $request->category_id ?? 0;
        $data['article_id']= $request->article_id ?? 0;
        $data['style_no']= $request->style_no ?? '';
        $data['company_id']= $request->company_id ?? 0;
        $data['user_id']= $request->user_id ?? 0;
        $data['staff_id']= $request->staff_id ?? 0;
        $data['workspace_id']= $request->workspace_id ?? 0;
        $data['fabric_type_id']= $request->fabric_type_id ?? 0;
        $data['fabric_GSM']= $request->fabric_GSM ?? '';
        $data['yarn_count']= $request->yarn_count ?? '';
        $data['style_article_description']= $request->style_article_description ?? '';
        $data['special_finish']= $request->special_finish ?? '';
        $data['total_qty']= $request->total_qty ?? 0;
        $data['patterns']= $request->patterns ?? '';
        $data['jurisdiction']= $request->jurisdiction ?? '';
        $data['customs_declaraion_document']= $request->customs_declaraion_document ?? '';
        $data['penality']= $request->penality ?? '';
        $data['print_image']= $request->print_image  ?? '';
        $data['print_size']= $request->print_size ?? '';
        $data['print_type']= $request->print_type ?? '';
        $data['print_no_of_colors']= $request->print_no_of_colors  ?? '';
        $data['main_lable']= $request->main_lable  ?? '';
        $data['main_lable_info']= $request->main_lable_info  ?? '';
        $data['washcare_lable']= $request->washcare_lable  ?? '';
        $data['washcare_lable_info']= $request->washcare_lable_info ?? '';
        $data['hangtag_lable']= $request->hangtag_lable ?? '';
        $data['hangtag_lable_info']= $request->hangtag_lable_info ?? '';
        $data['barcode_lable']= $request->barcode_lable ?? '';
        $data['barcode_lable_info']= $request->barcode_lable_info ?? '';
        $data['trims_nominations']= $request->trims_nominations ?? '';
        $data['poly_bag_size']= $request->poly_bag_size ?? '';
        $data['poly_bag_material']= $request->poly_bag_material ?? '';
        $data['poly_bag_price']= $request->poly_bag_price ?? '';
        $data['carton_bag_dimensions']= $request->carton_bag_dimensions ?? '';
        $data['carton_color']= $request->carton_color ?? '';
        $data['carton_material']= $request->carton_material ?? '';
        $data['carton_edge_finish']= $request->carton_edge_finish ?? '';
        $data['carton_mark']= $request->carton_mark ?? '';
        $data['make_up']= $request->make_up ?? '';
        $data['films_cd']= $request->films_cd ?? '';
        $data['picture_card']= $request->picture_card ?? '';
        $data['inner_cardboard']= $request->inner_cardboard ?? '';
        $data['shipping_size']= $request->shipping_size ?? '';
        $data['air_frieght']= $request->air_frieght ?? '';
        $data['estimate_delivery_date']= $request->estimate_delivery_date ?? '';
        $data['due_date']= $request->due_date ?? '';
        $data['incoterms']= $request->incoterms ?? 0;
        $data['payment_terms']= $request->payment_terms ?? '';
        $data['payment_instructions']= $request->payment_instructions ?? '';
        $data['target_price']= $request->target_price ?? 0;
        $data['forbidden_substance_info']= $request->forbidden_substance_info ?? '';
        $data['testing_requirements']= $request->testing_requirements ?? '';
        $data['sample_requirements']= $request->sample_requirements ?? '';
        $data['special_requests']= $request->special_requests ?? '';
        $data['currency']= $request->currency ?? '';
        $data['measurement_sheet']='';
        $data['fabric_type']= $request->fabric_type ?? '';
        $data['poly_bag_print']= $request->poly_bag_print ?? '';
        $data['updated_user_id']= $request->user_id ?? 0;
        $data['updated_staff_id']= $request->staff_id ?? 0;
        $data['media_reference_id']= $request->referenceId ?? 0;
        $data['updated_at']= date('Y-m-d H:i:s');
        $measurement_sheet= $request->measurement_Chart ?? '';

        if($measurement_sheet!='' && !empty($measurement_sheet)){
            $msarr = [];
            foreach ($measurement_sheet as $i => $sheet) {
                foreach ($sheet as $key => $value) {
                    $msarr[$i][$key]=$value;
                }
            }

            $data['measurement_sheet']=json_encode($msarr);
        }
        DB::beginTransaction();
        try{
            Inquiries::where('id',$request->inquiry_id)->update($data);
            $inquiryID = $request->inquiry_id;
            $dataToUpdate['inquiry_id']=$inquiryID;
            InquiryMedia::where('temp_id',$request->referenceId)->update($dataToUpdate);
            if(isset($request->sku_details)){
                InquirySku::where('inquiry_id', $inquiryID)->delete();
                $orderSkuArr = [];
                $orderSkuArr['inquiry_id']= $inquiryID;
                foreach ($request->sku_details as $sku){
                    $sku = (array)$sku;
                    $orderSkuArr['color_id']=$sku['color_id'];
                    $orderSkuArr['size_id']=$sku['size_id'];
                    $orderSkuArr['quantity']=$sku['quantity'];
                    $orderSkuArr['color_ratio']=$sku['color_ratio']?? 0;
                    $orderSkuArr['size_ratio']=$sku['size_ratio']?? 0;
                    $orderSkuArr['created_at']=date('Y-m-d H:i:s');
                    InquirySku::insert($orderSkuArr);
                }
            }

            if(isset($request->trims_additional)){
                InquiryAdditional::where('inquiry_id', $inquiryID)->delete();
                $orderTrims = [];
                $orderTrims['inquiry_id']= $inquiryID;
                foreach ($request->trims_additional as $trims){
                    $trims = (array)$trims;
                    $orderTrims['label']=$trims['label_name'];
                    $orderTrims['label_description']=$trims['label_description'];
                    $orderTrims['media_type']=$trims['label_id'];
                    $orderTrims['company_id']=$request->company_id ?? 0;;
                    $orderTrims['workspace_id']=$request->workspace_id ?? 0;;
                    $orderTrims['created_at']=date('Y-m-d H:i:s');
                    InquiryAdditional::insert($orderTrims);
                }
            }

            $language = $request->language =='jp'?'jp':'en';
            $enpdf = public_path() . '/Inquiry/' .$inquiryID.'.pdf';
            $jppdf = public_path() . '/Inquiry/' .$inquiryID.'_jp.pdf';
            if(file_exists($enpdf)){
                unlink($enpdf);
            }
            if(file_exists($jppdf)){
                unlink($jppdf);
            }
            Inquiry::inquiry_pdf_generate($inquiryID,$data,$request,$language,0);

            /* Inquiry Update Log starts */
            try{
                InquiryLog::edit_inquiry_log($inquiryID,$request);
            }catch(Exception $e){
            }
            /* Inquiry Update Log end */

        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Inquiry Updated Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_factory_feedback(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            //'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = InquiryFactoryFeedback::get_factory_feedback($request);
        $factories = InquiryFactoryFeedback::get_feedback_factories($request);
        $inquiries = InquiryFactoryFeedback::get_feedback_inquiries($request);
        $res = json_encode(["status_code"=>200,'status'=>"success",'data'=>$data,'inquiries'=>$inquiries,'factories'=>$factories],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_factory_inquiry_list(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['inquiry.factory_ids','like','%||'.$request->factory_id.'||%']
        ];
        $response = Inquiries::where($whereConditions)->select('id','style_no')->orderBy('id', 'ASC')->get();
        $data= array();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$response],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_buyer_factory_list(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $response = Inquiries::get_inquiry_factories($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$response],200);
        return CommonApp::webEncrypt($res);
    }

    public static function delete_unused_inquiry_media(Request $request){

        $medias = InquiryMedia::where('inquiry_id','0')->select('id','filepath','filesize','company_id')->limit(100)->get();
        foreach($medias as $m){
            $file = $m->filepath;
            Storage::disk('s3')->delete($file);
            InquiryMedia::where('id',$m->id)->delete();
            if($m->company_id > 0){
                $companyDetails = CommonApp::getCompanyDetailsbyID($m->company_id);
                $storageUsed = $companyDetails->storage_used*1024*1024;
                $storageToBeFreed = $m->filesize;
                $freedStorage = ($storageUsed - $storageToBeFreed)/(1024*1024);
                $companyDetails->storage_used = $freedStorage;
                $companyDetails->save();
            }
        }
        exit('Deleted');
    }

    public static function po_details(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            //'company_id' => 'required',
            //'workspace_id' => 'required',
            'po_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $inquiry = Inquiries::po_details($request);
        $pdfpath  = config('app.public_url').'PO/';
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiry,"pdfpath"=>$pdfpath],200);
        return CommonApp::webEncrypt($res);
    }

    public static function po_sku(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            //'company_id' => 'required',
            //'workspace_id' => 'required',
            'po_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $sku = Inquiries::po_sku($request);
        $colors = [];
        $sizes = [];
        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size'],"category"=>$s['category']);
            }
        }
        $data['sku'] =$sku;
        $data['colors'] =array_unique($colors,SORT_REGULAR);
        $data['sizes'] =array_unique($sizes,SORT_REGULAR);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    public static function po_media(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            //'company_id' => 'required',
            //'workspace_id' => 'required',
            'po_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $media['files'] = Inquiries::po_media($request);
       // $media['serverURL'] = config('filesystems.disks.s3.url');
       $media['serverURL'] = '';
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$media],200);
        return CommonApp::webEncrypt($res);
    }

    public static function duplicate_inquiry(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'inquiry_id' => 'required',
            'staff_id'=> 'required',
            'user_id' => 'required',
            'language' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            $result = Inquiries::duplicate_inquiry($request);
            if(is_array($result)){
                $language = $request->language =='jp'?'jp':'en';
                Inquiry::inquiry_pdf_generate($result['inquiry_id'],$result['data'],$request,$language,0);
            }else{
                $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$result],201);
                return CommonApp::webEncrypt($res);
            }
        }catch(Exception $e){
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Inquiry Created Successfully","inquiry_id"=>$result['inquiry_id']],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_inquiry_additional_info(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = InquiryAdditional::where('inquiry_id',$request->inquiry_id)->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    public static function delete_multiple_inquiry_media(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'media_id' => 'required|array'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $res = Inquiries::deleteMultiInquiryMedia($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","meassage"=>"File Deleted Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function send_mail_inquiry(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
            'user_id' => 'required',
            'company_id' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'content' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        if($request->user_id > 0)
            $userDetails = User::where('id',$request->user_id)->pluck('name')->first();
        else
            $userDetails = Staff::where('id',$request->user_id)->pluck('first_name')->first();
        //$companyDetail = CompanySettings::where('id',$request->company_id)->pluck('company_name')->first();
        $file = public_path() . '/Inquiry/' .$request->inquiry_id.'.pdf';
        if(!file_exists($file)){}
            Inquiry::create_inquiry_pdf($request->inquiry_id,$request);

        $details['created_by'] = $userDetails;
        $details["email"] = $request->email;
        $details["title"] = $request->subject;
        $details["content"] = $request->content;
        Mail::send('InquirySendMail', ['details'=>$details], function($message)use($details, $file) {
            $message->to($details["email"])
                    ->subject($details["title"]);
            if(file_exists($file))
                $message->attach($file);
        });

        $det['inquiry_id'] = $request->inquiry_id;
        $det['email'] = $request->email;
        $det['subject'] = $request->subject;
        $det['content'] = $request->content;
        $det['sent_by'] = $userDetails;
        $det['created_at'] = date('Y-m-d H:i:s');

        InquiryMailSentDetails::insert($det);

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Inquiry sent Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function inquiry_sent_mail_details(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = InquiryMailSentDetails::where('inquiry_id',$request->inquiry_id)->select('inquiry_id','email','subject','sent_by',DB::Raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i") as sent_at'))->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);

    }

    public static function create_inquiry_pdf($inquiryID,$request){
        $lang = 'en';
        App::setlocale($lang);
        $media['files'] = Inquiries::inquiry_media($request);
        $media['serverURL'] = config('filesystems.disks.s3.url');
        $user = CommonApp::getUserDetailsById($request->user_id);
        $company = CommonApp::getCompanyDetailsbyID($request->company_id);
        $sku = Inquiries::inquiry_sku($request);
        $data = Inquiries::inquiry_details($request);
        $additional = InquiryAdditional::where('inquiry_id',$request->inquiry_id)->get();
        $colors = [];
        $sizes = [];
        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size']);
            }
        }
        $datas['lang'] = $lang;
        $datas['translate'] = 0;
        $datas['inquiryID'] = $inquiryID;
        $datas['data'] = $data;
        $datas['additional'] = $additional;
        $datas['request'] = $request;
        $datas['media'] = $media;
        $datas['sku'] = $sku;
        $datas['inqdet'] = Inquiries::inquiry_details($request);
        $datas['sizes'] = array_unique($sizes,SORT_REGULAR);
        $datas['colors'] = array_unique($colors,SORT_REGULAR);
        $datas['user'] = $user;
        $datas['logo'] = $company->logo;
        view()->share("datas",$datas);
        $pdf = Pdf::loadView('InquiryMailPDF');
        $pdf->setPaper('A4', 'portrait');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
        $pdf->setOption("enable_php", true);
        $filePath = public_path() . '/Inquiry/';
        if (!file_exists($filePath)) {
            File::makeDirectory($filePath, 0777, true, true);
        }
        // if($lang=='en')
        //     $path = public_path() . '/Inquiry/' .$inquiryID.'.pdf';
        // else
        //     $path = public_path() . '/Inquiry/' .$inquiryID.'_jp.pdf';
        $path = public_path() . '/Inquiry/' .$inquiryID.'.pdf';
        $pdf->save($path);

        //$enpdf = public_path() . '/Inquiry/' .$inquiryID.'.pdf';

        //pdf merge
        $f=[$path];
        foreach ($media['files'] as $m){
           // if($m->media_type =="MeasurementSheet" && (stristr($m->orginalfilename,'.pdf')) ){
            if(stristr($m->orginalfilename,'.pdf') ){
                $filepath = public_path()."/MeasurementSheet/".$data['media_reference_id'].$m->orginalfilename;
                $f[]=$filepath;
            }
        }
        if(count($f)>1)
        {
            try{
                Inquiry::pdfmerge($inquiryID,$f,$lang);
            }catch(Exception $e){
                 Log::info($e->getMessage());
            }
        }

    }
}
