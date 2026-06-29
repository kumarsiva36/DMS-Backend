<?php

namespace App\Http\Controllers\WebSite\PurchaseOrder;

use App\Common\CommonApp;
use App\Common\Uploads;
use App\Http\Controllers\Controller;
use App\Imports\ImportXL;
use App\Models\Inquiries;
use App\Models\InquiryAdditional;
use App\Models\InquiryContact;
use App\Models\InquiryFactoryResponse;
use App\Models\InquiryMedia;
use App\Models\InquiryMediaPO;
use App\Models\InquiryPOAdditional;
use App\Models\InquiryPO;
use App\Models\InquiryPOLog;
use App\Models\InquirySku;
use App\Models\InquirySKUPO;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Maatwebsite\Excel\Facades\Excel;

class PO extends Controller
{
    /* PO Generation */
    public static function generate_po_factory(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'factory_id' => 'required|min:1',
            'inquiry_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
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
        $inquiryTrims = InquiryAdditional::where('inquiry_id',$request->inquiry_id)->get();
        /* Check If the Inquiry already has PO */
        $whereConditionsForPO = [
            ['inquiry_id',"=",$request->inquiry_id],
            ['po_status',"=","1"]
        ];
        $isPOAlreadyGenerated = InquiryPO::where($whereConditionsForPO)->get();
        if (count($isPOAlreadyGenerated)>0){
            $res = json_encode(["status_code"=>601,"status"=>"failure","message"=>"PO Already Generated For This Inquiry"]);
            return CommonApp::webEncrypt($res);
        }
        $data = [];
        $data['inquiry_id']= $inquiry->id;
        $data['category_id']= $inquiry->category_id;
        $data['media_reference_id']= $inquiry->media_reference_id;
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
            foreach($inquiryTrims as $trims){
                $trimsData=[];
                $trimsData['po_id']= $poID;
                $trimsData['label']=$trims->label;
                $trimsData['label_description']=$trims->label_description;
                $trimsData['media_type']=$trims->media_type;
                $trimsData['company_id']=$trims->company_id;
                $trimsData['workspace_id']=$trims->workspace_id;
                $trimsData['created_at']=date('Y-m-d H:i:s');
                InquiryPOAdditional::insert($trimsData);
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
            return CommonApp::webEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>"Success"],200);
        return CommonApp::webEncrypt($res);
    }

    /* View PO */
    public static function view_company_po(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions[]= ['company_id','=',$request->company_id];
        $whereConditions[]= ['workspace_id','=',$request->workspace_id];
        $request->page = (isset($request->page) && $request->page!='')?$request->page:1;
        $inquiries= InquiryPO::get_all_po($whereConditions,$request);
        $pdfpath  = config('app.public_url').'PO/';
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiries,"pdfpath"=>$pdfpath],200);
        return CommonApp::webEncrypt($res);
    }

    /* Get PO */
    public static function get_po(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $data['poDetails'] = InquiryPO::get_po_details($request);
        $data['poMediaDetails'] = InquiryMediaPO::getInquiryPOMedia($request);
        $sku = InquirySKUPO::getPOSKU($request);
        $colors = [];
        $sizes = [];
        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size'],"category"=>$s['category']);
            }
        }
        $data['poSKUDetails']['sku'] = $sku;
        $data['poSKUDetails']['colors'] =array_unique($colors,SORT_REGULAR);
        $data['poSKUDetails']['sizes'] =array_unique($sizes,SORT_REGULAR);
        $data['serverURL'] = config('filesystems.disks.s3.url');

        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    /* Upload File/media To PO */
    public static function upload_file_po(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'type' => 'required',
            'po_id' => 'required',
            'referenceId' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $companyFolder = $companyDetails->aws_s3_path;
        $companyFolder = $companyDetails->aws_s3_path;
        $free_storage = CommonApp::calculateFreeStorage($companyDetails->max_storage_size,$companyDetails->storage_used);
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
            $filepath = $companyFolder.'/PO/'.$fileName;
            Uploads::orderAddtionalSpec($file,$filepath);
            $filedata['po_id']=$request->po_id;
            $filedata['filename']=$fileName;
            $filedata['orginalfilename']=$file->getClientOriginalName();
            $filedata['filepath']=$filepath;
            $filedata['filesize']=$file->getSize();
            $filedata['media_type']=$request->type;
            $filedata['company_id']=$request->company_id ?? 0;
            $filedata['workspace_id']=$request->workspace_id ?? 0;
            $filedata['created_at']=date('Y-m-d H:i:s');

            if($request->type =="MeasurementSheet" && (stristr($filedata['orginalfilename'],'.xls'))){
                $datas = Excel::toArray(new ImportXL,request()->file('file'));
                $filedata['datas']=json_encode($datas);
            }else if($request->type =="MeasurementSheet" && (stristr($filedata['orginalfilename'],'.pdf'))){
                $path = public_path() . '/MeasurementSheet/';
                if (!file_exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }
                $name = $request->referenceId.$file->getClientOriginalName();
                $file->move($path, $name);
            }

            try{
                //InquiryMedia::createThumbs($file,$fileName,$companyFolder,120,120);
                InquiryMediaPO::insert($filedata);
                $companyDetails->storage_used = ($storageUsed + (int)$storageToBeAdded) / (1024 * 1024);
                $companyDetails->save();
                /* Inquiry file Add Log starts */
                // try{
                //     if(isset($request->upload_type) && $request->upload_type=='edit'){
                //         InquiryLog::edit_inquiry_media_log($request->referenceId,$request,$filedata);
                //     }
                // }catch(Exception $e){
                // }
                /* Inquiry file Add Log end */
            }catch(Exception $e){
                return response()->json(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            }

            $res['files'] = InquiryMediaPO::getFiles($request->po_id,$request->type);
            $res['serverURL'] = config('filesystems.disks.s3.url');

            return response()->json(["status_code"=>200,'status'=>"success","message"=>"Files Added Successfully","files"=>$res],200);
        }else{
            $res['files'] = InquiryMediaPO::getFiles($request->po_id,$request->type);
            $res['serverURL'] = config('filesystems.disks.s3.url');
            return response()->json(["status_code"=>200,'status'=>"failure","message"=>"Something went wrong","files"=>$res],200);
        }
    }

    public static function delete_inquiry_po_media(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'media_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        /* Inquiry file delete Log starts */
        // try{
        //     if(isset($request->upload_type) && $request->upload_type=='edit'){
        //         InquiryLog::delete_inquiry_media_log($request);
        //     }
        // }catch(Exception $e){
        // }
        /* Inquiry file delete Log end */

        $res = InquiryMediaPO::deleteInquiryPOMedia($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","meassage"=>"File Deleted Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    /* Edit and Update The PO */
    public static function update_po(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_id' => 'required',
            'article_id' => 'required',
            'style_no'=>'required',
            'total_qty'=>'required',
            'fabric_type_id' => 'required',
            'inquiry_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = [];
        $data['inquiry_id']= $request->inquiry_id ?? 0;
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
            InquiryPO::where('id',$request->po_id)->update($data);
            $poID = $request->po_id;
            // $dataToUpdate['inquiry_id']=$inquiryID;
            // InquiryMedia::where('temp_id',$request->referenceId)->update($dataToUpdate);
            if(isset($request->sku_details)){
                InquirySKUPO::where('po_id', $poID)->delete();
                $orderSkuArr = [];
                $orderSkuArr['po_id']= $poID;
                foreach ($request->sku_details as $sku){
                    $sku = (array)$sku;
                    $orderSkuArr['color_id']=$sku['color_id'];
                    $orderSkuArr['size_id']=$sku['size_id'];
                    $orderSkuArr['quantity']=$sku['quantity'];
                    $orderSkuArr['color_ratio']=$sku['color_ratio']?? 0;
                    $orderSkuArr['size_ratio']=$sku['size_ratio']?? 0;
                    $orderSkuArr['created_at']=date('Y-m-d H:i:s');
                    InquirySKUPO::insert($orderSkuArr);
                }
            }
            if(isset($request->trims_additional)){
                InquiryPOAdditional::where('po_id', $poID)->delete();
                $orderTrims = [];
                $orderTrims['po_id']= $poID;
                foreach ($request->trims_additional as $trims){
                    $trims = (array)$trims;
                    $orderTrims['label']=$trims['label_name'];
                    $orderTrims['label_description']=$trims['label_description'];
                    $orderTrims['media_type']=$trims['label_id'];
                    $orderTrims['company_id']=$request->company_id ?? 0;;
                    $orderTrims['workspace_id']=$request->workspace_id ?? 0;;
                    $orderTrims['created_at']=date('Y-m-d H:i:s');
                    InquiryPOAdditional::insert($orderTrims);
                }
            }
            /* Generate The PDF */
            PO::generate_po_pdf($poID,$data,$request);

            /* Necessary Details For Mail */
            $lastUpdatedInquiryPO = InquiryPO::where('id',$poID)->first();
            $factory = InquiryContact::select('id','factory','contact_person','contact_email')->where('id',$lastUpdatedInquiryPO->factory_id)->first();
            /* To Save PO Confirmed Status */
            $lastUpdatedInquiryPO->po_status = "1";
            $lastUpdatedInquiryPO->save();
            /* To Send The Mail To the Factory */
            if(!empty($factory)){
                $details['created_by'] = $factory->contact_person;
                $details["email"] = $factory->contact_email;
                $details["title"] = 'PO Request';
                $file = public_path() . '/' .$poID.'.pdf';
                if(!file_exists($file))
                $file = public_path() . '/PO/' .$poID.'.pdf';

                if($factory->contact_email){
                    Mail::send('InquiryPOMail', ['details'=>$details], function($message)use($details, $file) {
                    $message->to($details["email"])
                            ->subject($details["title"]);
                    if(file_exists($file))
                            $message->attach($file);
                    });
                }
            }


            /* Confirm Po Log starts */
            try{
                InquiryPOLog::confirm_po_log($request);
            }catch(Exception $e){

            }
            /* Confirm Po Log end */

        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"PO Updated Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    /* Generate The PO PDF */
    public static function generate_po_pdf($poID,$data,$request){
        return true;

        $request->po_id = $poID; //$inquiryID;
        $media['files'] = InquiryMediaPO::getInquiryPOMedia($request);
        $media['serverURL'] = ''; //config('filesystems.disks.s3.url');
        $user = CommonApp::getUserDetailsById($request->user_id);
        $company = CommonApp::getCompanyDetailsbyID($request->company_id);
        $sku = InquirySKUPO::getPOSKU($request);
        $colors = [];
        $sizes = [];
        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size']);
            }
        }

        $datas['poID'] = $poID;
        $datas['data'] = $data;
        $datas['request'] = $request;
        $datas['media'] = $media;
        $datas['sku'] = $sku;
        $datas['inqdet'] = InquiryPO::get_po_details($request);
        $datas['sizes'] = array_unique($sizes,SORT_REGULAR);
        $datas['colors'] = array_unique($colors,SORT_REGULAR);
        $datas['user'] = $user;
        $datas['logo'] = $company->logo;
        view()->share("datas",$datas);
        //return view('InquiryPDF');
        $pdf = Pdf::loadView('InquiryPoPDF');
        $pdf->setPaper('A4', 'portrait');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
        $pdf->setOption("enable_php", true);
        $filePath = public_path() . '/PO';
        if (!file_exists($filePath)) {
            File::makeDirectory($filePath, 0777, true, true);
        }
        $path = public_path() . '/PO/' .$poID.'.pdf';
        $pdf->save($path);

        //Measurement pdf merge
        $f=[$path];
        foreach ($media['files'] as $m){

            if($m->media_type =="MeasurementSheet" && (stristr($m->orginalfilename,'.pdf')) ){
                $filepath = public_path()."/MeasurementSheet/".$data['media_reference_id'].$m->orginalfilename;
                $f[]=$filepath;
            }

            //Delete the measurement pdf files in local
            //  $i=0;
            // foreach ($f as $file) {
            //     if(file_exists($file) && $i >0)
            //         unlink($file);

            //     $i++;
            // }
        }
        if(count($f)>1){
            try{
                PO::pdfmerge($poID,$f);
            }catch(Exception $e){
                Log::info($e->getMessage());
            }
        }

    }

    /* PDF Merge */
    public static function pdfmerge($poID,$f) {
        $files = $f;
        $pdf = PdfMerger::init();
        foreach ($files as $file) {
            if(file_exists($file))
                $pdf->addPDF($file, 'all','P');
        }
        $pdf->merge("","",$poID);
        $pdf->save(public_path()."/PO/".$poID.".pdf",'file');
    }

    /* To Cancel a PO */
    public static function cancel_po(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
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
            return CommonApp::webEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"PO Cancelled Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    /* Get The Factory List for whom the PO is generated */
    public static function get_po_factory_list(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $factoriesList = InquiryFactoryResponse::where('inquiry_factory_response.is_po_generated',1)
            ->where('inquiry.company_id',$request->company_id)
            ->where('inquiry.workspace_id',$request->workspace_id)
            ->leftJoin('inquiry','inquiry_factory_response.inquiry_id','inquiry.id')
            ->leftjoin('inquiry_contact','inquiry_factory_response.factory_contact_id','inquiry_contact.id')
            ->select('inquiry_contact.factory','inquiry_contact.id')
            ->groupby('inquiry_contact.id')
            ->get();

        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$factoriesList],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_po_additional_info(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'po_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = InquiryPOAdditional::where('po_id',$request->po_id)->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }
    public static function delete_multiple_po_media(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'media_id' => 'required|array'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $res = InquiryMediaPO::deleteMultiPOMedia($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","meassage"=>"File Deleted Successfully"],200);
        return CommonApp::webEncrypt($res);
    }
}
