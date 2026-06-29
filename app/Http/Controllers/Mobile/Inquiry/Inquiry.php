<?php

namespace App\Http\Controllers\Mobile\Inquiry;

use App\Common\CommonApp;
use App\Common\Uploads;
use App\Http\Controllers\Controller;
use App\Imports\ImportXL;
use App\Models\CompanySettings;
use App\Models\Inquiries;
use App\Models\InquiryAdditional;
use App\Models\InquiryContact;
use App\Models\InquiryFactoryFeedback;
use App\Models\InquiryFactoryResponse;
use App\Models\InquiryLog;
use App\Models\InquiryMaster;
use App\Models\InquiryMedia;
use App\Models\InquirySku;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use setasign\Fpdi\Fpdi;

class Inquiry extends Controller
{
    //
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
        $filedata = [];
        if($request->file('file')){
            $file = $request->file('file');
            $string = str_replace(' ', '-', $file->getClientOriginalName()); // Replaces all spaces with hyphens.
            $nameOfFile = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
            $fileName = time().'_'.$nameOfFile;
            $filepath = $companyFolder.'/Inquiry/'.$request->referenceId.'/'.$fileName;
            Uploads::orderAddtionalSpec($file,$filepath);
            $filedata['filename']=$fileName;
            $filedata['orginalfilename']=$file->getClientOriginalName();
            $filedata['filepath']=$filepath;
            $filedata['filesize']=$file->getSize();
            $filedata['temp_id']=$request->referenceId;
            $filedata['media_type']=$request->type;
            $filedata['created_at']=date('Y-m-d H:i:s');

            if($request->type =="MeasurementSheet" && (stristr($filedata['orginalfilename'],'.xls'))){
                $datas = Excel::toArray(new ImportXL,request()->file('file'));
                $filedata['datas']=json_encode($datas);
            }else if($request->type =="MeasurementSheet" && (stristr($filedata['orginalfilename'],'.pdf'))){
                $path = public_path();
                $name = 'ms.pdf';
                $file->move($path, $name);
            }

            try{
                //InquiryMedia::createThumbs($file,$fileName,$companyFolder,120,120);
                InquiryMedia::insert($filedata);
            }catch(Exception $e){
                return response()->json(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            }

            $res['files'] = InquiryMedia::getFiles($request->referenceId,$request->type);
            $res['serverURL'] = config('filesystems.disks.s3.url');

            return response()->json(["status_code"=>200,'status'=>"success","message"=>"Files Added Successfully","files"=>$res],200);
        }else{
            $res['files'] = InquiryMedia::getFiles($request->referenceId,$request->type);
            $res['serverURL'] = config('filesystems.disks.s3.url');
            return response()->json(["status_code"=>200,'status'=>"failure","message"=>"Something went wrong","files"=>$res],200);
        }
    }

    public static function save_inquiry(Request $request){

        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'article_id' => 'required',
            'style_no'=>'required',
            'total_qty'=>'required',
            'fabric_type_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $data = [];
        $data['category_id']= $request->category_id ?? 0;
        $data['article_id']= $request->article_id ?? 0;
        $data['style_no']= $request->style_no ?? '';
        $data['company_id']= $request->company_id ?? 0;
        $data['user_id']= $request->user_id ?? 0;
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
        $data['target_price']= $request->target_price ?? 0;
        $data['forbidden_substance_info']= $request->forbidden_substance_info ?? '';
        $data['testing_requirements']= $request->testing_requirements ?? '';
        $data['sample_requirements']= $request->sample_requirements ?? '';
        $data['special_requests']= $request->special_requests ?? '';
        $data['currency']= $request->currency ?? '';
        //Inquiry::inquiry_pdf_generate('43',$data,$request); exit;
        // header("Cache-Control: public");
        // header("Content-Description: File Transfer");
        // header("Content-Disposition: attachment; filename=doc");
        // header("Content-Type: application/json" );
        // header('Access-Control-Allow-Origin:*');
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
                    $orderSkuArr['color_id']=$sku['color_id'];
                    $orderSkuArr['size_id']=$sku['size_id'];
                    $orderSkuArr['quantity']=$sku['quantity'];
                    $orderSkuArr['color_ratio']=$sku['color_ratio']?? 0;
                    $orderSkuArr['size_ratio']=$sku['size_ratio']?? 0;
                    $orderSkuArr['created_at']=date('Y-m-d H:i:s');
                    InquirySku::insert($orderSkuArr);
                }
            }
            Inquiry::inquiry_pdf_generate($inquiryID,$data,$request);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
        }
        DB::commit();
        return response()->json(["status_code"=>200,'status'=>"success","message"=>"Inquiry Added Successfully"],200);
    }

    public static function inquiry_pdf_generate($inquiryID,$data,$request){
        return true;
        $request->inquiry_id = $inquiryID; //$inquiryID;
        $media['files'] = Inquiries::inquiry_media($request);
        $media['serverURL'] = config('filesystems.disks.s3.url');
        $sku = Inquiries::inquiry_sku($request);
        $colors = [];
        $sizes = [];
        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size']);
            }
        }

        $datas['inquiryID'] = $inquiryID;
        $datas['data'] = $data;
        $datas['request'] = $request;
        $datas['media'] = $media;
        $datas['sku'] = $sku;
        $datas['inqdet'] = Inquiries::inquiry_details($request);
        $datas['sizes'] = array_unique($sizes,SORT_REGULAR);
        $datas['colors'] = array_unique($colors,SORT_REGULAR);
        view()->share("datas",$datas);
        //return view('InquiryPDF');
        $pdf = Pdf::loadView('InquiryPDF');
        $pdf->setPaper('A4', 'portrait');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
        $path = public_path() . '/Inquiry/' .$inquiryID.'.pdf';
        $pdf->save($path);

        // Measurement pdf merge
        foreach ($media['files'] as $m){
            if($m->media_type =="MeasurementSheet" && (stristr($m->orginalfilename,'.pdf')) ){
                $filepath = public_path() . '/ms.pdf';
                $f=[$path,$filepath];
                // Inquiry::pdfmerge($inquiryID,$f);
            }
        }


        //return $pdf->download();
    }

    public static function get_inquirys(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
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
        $webviewUrl = config('app.frontend_url').'inquiry/viewinquirydetailsmobile?id=';
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiries,"pdfpath"=>$pdfpath,"factories"=>$factories,"articles"=>$articles,"webviewUrl"=>$webviewUrl],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function inquiry_details(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            //'company_id' => 'required',
            //'workspace_id' => 'required',
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $inquiry = Inquiries::inquiry_details($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiry],200);
        return CommonApp::apiEncrypt($res);
    }
    public static function inquiry_sku(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            //'company_id' => 'required',
            //'workspace_id' => 'required',
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $sku = Inquiries::inquiry_sku($request);
        $colors = [];
        $sizes = [];
        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size']);
            }
        }
        $data['sku'] =$sku;
        $data['colors'] =array_values(array_unique($colors,SORT_REGULAR));
        $data['sizes'] =array_values(array_unique($sizes,SORT_REGULAR));
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        //return $res;
        return CommonApp::apiEncrypt($res);
    }
    public static function inquiry_media(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            //'company_id' => 'required',
            //'workspace_id' => 'required',
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $media['files'] = Inquiries::inquiry_media($request);
        $media['serverURL'] = config('filesystems.disks.s3.url');
        $res =  json_encode(["status_code"=>200,'status'=>"success","data"=>$media],200);
        return CommonApp::apiEncrypt($res);
    }
    public static function get_inquiry_additional_info(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $data = InquiryAdditional::where('inquiry_id',$request->inquiry_id)->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function save_inquiry_factory_response(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory_id' => 'required',
            'inquiry_id' => 'required',
            'user_id' => 'required',
            'user_type' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
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
        return CommonApp::apiEncrypt($res);
    }
    public static function save_inquiry_contact(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory' => 'required|max:60',
            'contact_person' => 'required|max:60',
            'contact_number' => 'required|unique:inquiry_contact',
            'contact_email' => 'required|email:dns,rfc|unique:inquiry_contact',
            'address' => 'required',
            'city' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
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
        return CommonApp::apiEncrypt($res);
    }

    public static function get_inquiry_contact(){
        $factories = InquiryContact::select('id','factory','contact_person','contact_number','contact_email')->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$factories],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function send_inquiry(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory_id' => 'required',
            'inquiry_id' => 'required',
            'user_id' => 'required',
            'company_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
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
        return CommonApp::apiEncrypt($res);
    }

    public static function inquiry_factory_list(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $factories = Inquiries::getFactories($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$factories],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function inquiry_factory_response(Request $request ){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $factories = InquiryFactoryResponse::FactoryResponse($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$factories],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function delete_inquiry(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
            'company_id' => 'required'
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $res = Inquiries::deleteInquiry($request);
        try{
            InquiryLog::delete_inquiry_log($request);
        }catch(Exception $e){
        }
        $res=json_encode(["status_code"=>200,'status'=>"success","message"=>"Inquiry Deleted Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function factory_get_inquirys(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }

        $contact_id = InquiryContact::where('factory_id',$request->factory_id)->pluck('id')->first();
        $page = (isset($request->page) && $request->page!='')?$request->page:1;
        if((int)$contact_id > 0){
            $inquiries = Inquiries::get_factory_inquirys_mobile($contact_id,$request->factory_id,$page,(array)$request);
            $articals = Inquiries::get_factory_articals($contact_id);
            $users = Inquiries::get_factory_users($contact_id);
            $response = InquiryFactoryResponse::where('factory_id',$request->factory_id)->pluck('inquiry_id');
            $pdfpath  = config('app.public_url').'Inquiry/';
            $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiries,"response"=>$response,"pdfPath"=>$pdfpath,"articles"=>$articals,"users"=>$users],200);
            return CommonApp::apiEncrypt($res);
        }else{
            $res = json_encode(["status_code"=>200,'status'=>"success","data"=>[],"response"=>[],"articles"=>[],"users"=>[]],200);
            return CommonApp::apiEncrypt($res);
        }

    }
    public static function factory_inquiry_contact(Request $request){
        $validator = Validator::make($request->all(), [
            'factory_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereConditions=[
            ['factory_id','=',$request->factory_id]
        ];

        $inquiries = InquiryContact::where($whereConditions)->get();
        return response()->json(["status_code"=>200,'status'=>"success","data"=>$inquiries],200);
    }
    public static function update_inquiry_contact(Request $request){
        $validator = Validator::make($request->all(), [
            'factory' => 'required|max:60',
            'contact_person' => 'required|max:60',
            'contact_number' => 'required',
            'contact_email' => 'required|email:dns,rfc',
            'address' => 'required',
            'city' => 'required',
            'factory_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $filedata['factory']=$request->factory ?? "";
        $filedata['contact_person']=$request->contact_person ?? "";
        $filedata['contact_number']=$request->contact_number ?? "";
        $filedata['contact_email']=$request->contact_email ?? '';
        $filedata['address']=$request->address ?? '';
        $filedata['city']=$request->city ?? '';

        InquiryContact::where('factory_id',$request->factory_id)->update($filedata);
        return response()->json(["status_code"=>200,'status'=>"success","message"=>"Contact Updated Successfully"],200);
    }
    public static function get_inquiry_master(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereConditions=[
            ['type','=',$request->type]
        ];

        $inquiries = InquiryMaster::where($whereConditions)->get();
        return response()->json(["status_code"=>200,'status'=>"success","data"=>$inquiries],200);
    }

    public static function factory_inquiry_response(Request $request){
        $validator = Validator::make($request->all(), [
            'inquiry_id' => 'required',
            'factory_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereConditions=[
            ['inquiry_id','=',$request->inquiry_id],
            ['factory_id','=',$request->factory_id]
        ];
        $response = InquiryFactoryResponse::where($whereConditions)->get();
        return response()->json(["status_code"=>200,'status'=>"success","data"=>$response],200);
    }

    public static function get_buyer_inquiry_list(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['user_id','=',$request->user_id]
        ];
        $response = Inquiries::where($whereConditions)->select('id','style_no')->get();
        return response()->json(["status_code"=>200,'status'=>"success","data"=>$response],200);
    }
    public static function get_inquiry_factory_list(Request $request){
        $validator = Validator::make($request->all(), [
            'inquiry_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereConditions=[
            ['id','=',$request->inquiry_id]
        ];
        $response = Inquiries::where($whereConditions)->pluck('factory_ids')->first();
        $data= array();
        if($response!=""){
            $factory_ids=array_unique(explode("||",$response));
            $factories = InquiryContact::select('id','factory')->whereIn('id',$factory_ids)->get();
            return response()->json(["status_code"=>200,'status'=>"success","data"=>$factories],200);
        }
        else
        return response()->json(["status_code"=>200,'status'=>"success","data"=>$data],200);
    }
    public static function save_factory_feedback(Request $request){
        $validator = Validator::make($request->all(), [
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
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
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
        return response()->json(["status_code"=>200,'status'=>"success","message"=>"Factory Feedback Added Successfully"],200);
    }

    public static function delete_inquiry_media(Request $request){
        $validator = Validator::make($request->all(), [
            'media_id' => 'required'
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }

        $res = Inquiries::deleteInquiryMedia($request);
        return response()->json(["status_code"=>200,'status'=>"success","message"=>"File Deleted Successfully"],200);
    }
    public static function get_factory_ratings(Request $request){
        $validator = Validator::make($request->all(), [
            'factory_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }

        $res = InquiryFactoryFeedback::get_factory_ratings($request);
        return response()->json(["status_code"=>200,'status'=>"success","data"=>$res],200);
    }

    public static function check_factory_feedback(Request $request){
        $validator = Validator::make($request->all(), [
            'inquiry_id' => 'required',
            'factory_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }

        $res = InquiryFactoryFeedback::where('factory_contact_id', $request->factory_id)->where('inquiry_id', $request->inquiry_id)->get()->toArray();
        if(count($res) > 0){
            return response()->json(["status_code"=>201,'status'=>"Already Feedback Added","data"=>$res],201);
        }else{
            return response()->json(["status_code"=>200,'status'=>"success"],200);
        }
    }

    public static function check_buyer_notification(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }

        $response = Inquiries::check_buyer_notification($request);

        $res = json_encode(["status_code"=>200,'status'=>"success",'notifications'=>$response],200);
        return CommonApp::apiEncrypt($res);
    }

    /* To Check If the Factory has any New Inquiry Notification */
    public static function check_factory_notification(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }

        $response = Inquiries::check_factory_notification($request);

        $res = json_encode(["status_code"=>200,'status'=>"success",'notifications'=>$response],200);
        return CommonApp::apiEncrypt($res);
    }

    /* Read Factory Notifications */
    public static function read_factory_notifications(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'user_id' => 'required',
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }

        Inquiries::read_factory_notifications($request);

        $res = json_encode(["status_code"=>200,'status'=>"success",'notifications'=>"Success"],200);
        return CommonApp::apiEncrypt($res);
    }

    /* Read Factory Notifications */
    public static function read_buyer_notifications(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'user_id' => 'required',
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }

        Inquiries::read_buyer_notifications($request);

        $res = json_encode(["status_code"=>200,'status'=>"success",'notifications'=>"Success"],200);
        return CommonApp::apiEncrypt($res);
    }

    /* To get the factory list who are all not using the DMS */
    public static function get_factory_list_response(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $res = InquiryFactoryResponse::get_factory_list_response($request);
        $currency = Inquiries::where('id',$request->inquiry_id)->pluck('currency')->first();
        $res = json_encode(["status_code"=>200,'status'=>"success",'data'=>$res,'currency'=>$currency],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function save_buyer_inquiry_factory_response(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'factory_id' => 'required',
            'inquiry_id' => 'required',
            'user_id' => 'required',
            'user_type' => 'required',
            'price'=>'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $filedata['inquiry_id']=$request->inquiry_id ?? 0;
        $filedata['factory_id']= 0;
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
        return CommonApp::apiEncrypt($res);
    }
}
