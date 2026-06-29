<?php

namespace App\Http\Controllers\WebSite\PurchaseOrder;

use App\Common\CommonApp;
use App\Common\Uploads;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebSite\Common\Helper;
use App\Imports\ImportXL;
use App\Models\InquiryMediaPO;
use App\Models\InquiryPO;
use App\Models\InquiryPOForwarder;
use App\Models\InquiryPOrder;
use App\Models\POTesting;
use App\Models\InquiryPOLog;
use App\Models\InquiryPOrderTranslate;
use App\Models\InquirySKUPO;
use App\Models\PoComments;
use App\Models\Staff;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
ini_set('memory_limit',-1);
class POrder extends Controller
{
    /* PO Generation */
    public static function generate_po(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'po_number'=>'required',
            // 'article_id' => 'required',
            // 'style_no'=>'required',
            // 'total_qty'=>'required',
            // 'fabric_type_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $docs='';
        if(count($request->document_requirement)>0)
        {
            foreach($request->document_requirement as $d)
            {
                $docs .=$d->document_name."|".$d->original_copies."|".$d->duplicate_copies."||";
            }

            $docs = substr($docs,0,strlen($docs)-2);
        }
        $fabric_composition='';
        if(count($request->fabric_composition)>0)
        {
            foreach($request->fabric_composition as $d)
            {
                $fabric_composition .=$d->value.",";
            }

            $fabric_composition = substr($fabric_composition,0,strlen($fabric_composition)-1);
        }


        $data = [];
        $data['po_number']= $request->po_number ?? 0;
        $data['sign_option']= is_array($request->sign_option) ? implode(',', $request->sign_option) : NULL;
        $data['company_id']= $request->company_id ?? 0;
        $data['workspace_id']= $request->workspace_id ?? 0;
        $data['user_id']= $request->user_id ?? 0;
        $data['staff_id']= $request->staff_id ?? 0;
        $data['media_reference_id']= $request->media_reference_id ?? '';;
        $data['buyer']= $request->buyer ??'';
        $data['seller']= $request->seller ??'';
        $data['maker']= $request->maker ??'';
        $data['style_no']= $request->style_no ?? '';
        $data['article_id']= $request->article_id ?? 0;
        $data['article_name']= $request->article_name ?? NULL;
        $data['article_description']= $request->article_description ?? '';
        $data['fabric_type_id']= $request->fabric_type_id ?? 0;
        $data['fabric_composition_id']= $request->fabric_composition_id ?? 0;
        $data['fabric_type']= $request->fabric_type ?? '';
        $data['fabric_GSM']= $request->fabric_GSM ?? '';
        $data['gsm_tolerance']= $request->gsm_tolerance ?? '';
        $data['yarn_count_type']= $request->yarn_count_type ?? '';
        $data['total_qty']= $request->total_qty ?? 0;
        $data['total_qty_min_tol']= $request->total_qty_min_tol ?? 0;
        $data['total_qty_max_tol']= $request->total_qty_max_tol ?? 0;
        $data['units']= $request->units ?? 0;
        $data['currency']= $request->currency ?? '';
        $data['price']= $request->price ?? 0;
        $data['incoterms']= $request->incoterms ?? 0;
        $data['delivery_date']= $request->delivery_date ?? '';
        $data['delivery_date_type']= $request->delivery_date_type ?? '';
        $data['origin_port']= $request->origin_port ?? '';
        $data['destination_port']= $request->destination_port ?? '';
        $data['mode_of_shipment']= $request->mode_of_shipment ?? '';
        $data['document_requirement']= $docs; //$request->document_requirement ?? '';
        $data['hs_code']= $request->hs_code ?? '';
        $data['place_of_jurisdiction']= $request->place_of_jurisdiction ?? '';
        $data['penality']= $request->penality ?? '';
        $data['fabric_testing_agency']= $request->fabric_testing_agency ?? '';
        $data['garment_testing_agency']= $request->garment_testing_agency ?? '';
        $data['additional_information']= $request->additional_information ?? '';
        $data['payment_terms']= $request->payment_terms ?? '';
        $data['testing_cost']= $request->testing_cost ?? NULL;
        $data['forwarder']= $request->forwarder ?? NULL;
        $data['forwarder_address']= $request->forwarder_address ?? NULL;
        $data['forwarder_contact_person']= $request->forwarder_contact_person ?? NULL;
        $data['forwarder_phone']= $request->forwarder_phone ?? NULL;
        $data['forwarder_email']= $request->forwarder_email ?? NULL;
        $data['price_units']= $request->price_units ?? 0;
        $data['fabric_composition']= $fabric_composition;


        DB::beginTransaction();
        try{
            InquiryPOrder::insert($data);
            $poID = DB::getPdo()->lastInsertId();
            InquiryMediaPO::where('po_id', $poID)->delete();
            $dataToUpdate['po_id']=$poID;
            InquiryMediaPO::where('temp_id',$request->media_reference_id)->update($dataToUpdate);
            InquirySKUPO::where('po_id', $poID)->delete();
            foreach($request->sku_details as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['color_id']=$sku->color_id;
                $skuData['size_id']=$sku->size_id;
                $skuData['color_ratio']=$sku->color_ratio ?? 0;
                $skuData['size_ratio']=$sku->size_ratio ?? 0;
                $skuData['quantity']=$sku->quantity;
                $skuData['created_at']=date('Y-m-d H:i:s');
                InquirySKUPO::insert($skuData);
            }
            foreach($request->fabric_testing as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['type']= 'fabric_testing';
                $skuData['color_id']=$sku->color_id;
                $skuData['length_qty']=$sku->length;
                if($sku->length > 0)
                    POTesting::insert($skuData);
            }
            foreach($request->garment_testing as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['type']= 'garment_testing';
                $skuData['color_id']=$sku->color_id;
                $skuData['size_id']=$sku->size_id;
                $skuData['length_qty']=$sku->pieces;
                if($sku->pieces > 0)
                    POTesting::insert($skuData);
            }
            foreach($request->fit_sample as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['type']= 'fit_sample';
                $skuData['color_id']=$sku->color_id;
                $skuData['size_id']=$sku->size_id;
                $skuData['length_qty']=$sku->pieces;
                if($sku->pieces > 0)
                    POTesting::insert($skuData);
            }
            foreach($request->pp_sample as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['type']= 'pp_sample';
                $skuData['color_id']=$sku->color_id;
                $skuData['size_id']=$sku->size_id;
                $skuData['length_qty']=$sku->pieces;
                if($sku->pieces > 0)
                    POTesting::insert($skuData);
            }
            foreach($request->testing_sample as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['type']= 'testing_sample';
                $skuData['color_id']=$sku->color_id;
                $skuData['size_id']=$sku->size_id;
                $skuData['length_qty']=$sku->pieces;
                if($sku->pieces > 0)
                    POTesting::insert($skuData);
            }
            foreach($request->shipment_sample as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['type']= 'shipment_sample';
                $skuData['color_id']=$sku->color_id;
                $skuData['size_id']=$sku->size_id;
                $skuData['length_qty']=$sku->pieces;
                POTesting::insert($skuData);
            }
            /* Generate Po Log starts */
            try{
                $ip_address = $header->header('Ip-Address') ?? '';
                $platform = $header->header('Platform') ?? '';
                InquiryPOLog::generate_po_log($poID,$request,array(),$ip_address,$platform);
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
    public static function view_new_po(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions[]= ['inquiry_new_po.company_id','=',$request->company_id];
        $whereConditions[]= ['inquiry_new_po.workspace_id','=',$request->workspace_id];
        $whereConditions[]= ['inquiry_new_po.user_id','=',$request->user_id];
        $whereConditions[]= ['inquiry_new_po.staff_id','=',$request->staff_id];
        $request->page = (isset($request->page) && $request->page!='')?$request->page:1;

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
        $colors = [];
        $sizes = [];
        $media['files'] = InquiryMediaPO::getInquiryPOMedia($request);
        $media['serverURL'] = ''; //config('filesystems.disks.s3.url');
        $sku = InquirySKUPO::getPOSKU($request);
        $testings = POTesting::getPOTesting($request);
        $testingsku = POTesting::getPOTestingSKU($request);
        $result = InquiryPOrder::get_po_details($request);
        $company = CommonApp::getCompanyDetailsbyID($request->company_id);
        $buyer = (explode("\n",$result[0]->buyer)[0]) ?? $result[0]->buyer;
        $seller = (explode("\n",$result[0]->seller)[0]) ?? $result[0]->seller;
        $maker = (explode("\n",$result[0]->maker)[0]) ?? $result[0]->maker;
        $logo= ($company->logo !='' && $company->logo!=NULL) ? Storage::disk('s3')->temporaryUrl($company->logo, '+75 minutes') : "";
        //dd($testingsku);

        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size']);
            }
        }

        $data['poID'] = $request->po_id;
        $data['poDetails'] = $result;
        $data['poMediaDetails'] = $media;
        $data['poSKUDetails']['sku'] = $sku;
        $data['poSKUDetails']['colors'] =(object)array_unique($colors,SORT_REGULAR);
        $data['poSKUDetails']['sizes'] =(object)array_unique($sizes,SORT_REGULAR);
        $data['serverURL'] = config('filesystems.disks.s3.url');
        $data['testings'] = $testings;
        $data['testingsku'] = $testingsku;
        $data['logo'] = $logo;
        $data['buyer_name'] = str_replace(',', '', $buyer);
        $data['seller_name'] = str_replace(',', '', $seller);
        $data['maker_name'] = str_replace(',', '', $maker);

        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    /* Upload File/media To PO */
    public static function upload_file_po(Request $request){
        $header=$request;
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'type' => 'required',
            'po_id' => 'required',
            'referenceId' => 'required',
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

    public static function delete_inquiry_po_media(Request $request){
        $header=$request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'media_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition=[
            ['id','=',$request->media_id]
        ];
        $files = InquiryMediaPO::select("id as media_id", "parent_po_id", "media_type", "filepath", "orginalfilename")->where($whereCondition)->first();
        /* PO file delete Log starts */
        try{
            if(isset($request->upload_type) && $request->upload_type=='edit'){
                $ip_address = $header->header('Ip-Address') ?? '';
                $platform = $header->header('Platform') ?? '';
                InquiryPOLog::po_delete_media_log($request,$files,$ip_address,$platform);
            }
        }catch(Exception $e){
           // return $e->getMessage();
        }
        /* PO file delete Log end */

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
            //'article_id' => 'required',
            'style_no'=>'required',
            'total_qty'=>'required',
            //'fabric_type_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $docs='';
        if(count($request->document_requirement)>0)
        {
            foreach($request->document_requirement as $d)
            {
                $docs .=$d->document_name."|".$d->original_copies."|".$d->duplicate_copies."||";
            }

            $docs = substr($docs,0,strlen($docs)-2);
        }
        $fabric_composition='';
        if(count($request->fabric_composition)>0)
        {
            foreach($request->fabric_composition as $d)
            {
                $fabric_composition .=$d->value.",";
            }

            $fabric_composition = substr($fabric_composition,0,strlen($fabric_composition)-1);
        }

        $data = [];
        $data['po_number']= $request->po_number ?? 0;
        $data['sign_option']= is_array($request->sign_option) ? implode(',', $request->sign_option) : NULL;
        $data['company_id']= $request->company_id ?? 0;
        $data['workspace_id']= $request->workspace_id ?? 0;
        $data['updated_user_id']= $request->user_id ?? 0;
        $data['updated_staff_id']= $request->staff_id ?? 0;
        $data['media_reference_id']= $request->media_reference_id ?? '';;
        $data['buyer']= $request->buyer ??'';
        $data['seller']= $request->seller ??'';
        $data['maker']= $request->maker ??'';
        $data['style_no']= $request->style_no ?? '';
        $data['article_id']= $request->article_id ?? 0;
        $data['article_name']= $request->article_name ?? 0;
        $data['article_description']= $request->article_description ?? '';
        $data['fabric_type_id']= $request->fabric_type_id ?? 0;
        $data['fabric_composition_id']= $request->fabric_composition_id ?? 0;
        $data['fabric_type']= $request->fabric_type ?? '';
        $data['fabric_GSM']= $request->fabric_GSM ?? '';
        $data['gsm_tolerance']= $request->gsm_tolerance ?? '';
        $data['yarn_count_type']= $request->yarn_count_type ?? '';
        $data['total_qty']= $request->total_qty ?? 0;
        $data['total_qty_min_tol']= $request->total_qty_min_tol ?? 0;
        $data['total_qty_max_tol']= $request->total_qty_max_tol ?? 0;
        $data['units']= $request->units ?? 0;
        $data['currency']= $request->currency ?? '';
        $data['price']= $request->price ?? 0;
        $data['incoterms']= $request->incoterms ?? 0;
        $data['payment_terms']= $request->payment_terms ?? '';
        $data['delivery_date']= $request->delivery_date ?? '';
        $data['delivery_date_type']= $request->delivery_date_type ?? '';
        $data['origin_port']= $request->origin_port ?? '';
        $data['destination_port']= $request->destination_port ?? '';
        $data['mode_of_shipment']= $request->mode_of_shipment ?? '';
        $data['document_requirement']= $docs; //$request->document_requirement ?? '';
        $data['hs_code']= $request->hs_code ?? '';
        $data['place_of_jurisdiction']= $request->place_of_jurisdiction ?? '';
        $data['penality']= $request->penality ?? '';
        $data['fabric_testing_agency']= $request->fabric_testing_agency ?? '';
        $data['garment_testing_agency']= $request->garment_testing_agency ?? '';
        $data['additional_information']= $request->additional_information ?? '';
        $data['testing_cost']= $request->testing_cost ?? '';
        $data['forwarder']= $request->forwarder ?? NULL;
        $data['forwarder_address']= $request->forwarder_address ?? NULL;
        $data['forwarder_contact_person']= $request->forwarder_contact_person ?? NULL;
        $data['forwarder_phone']= $request->forwarder_phone ?? NULL;
        $data['forwarder_email']= $request->forwarder_email ?? NULL;
        $data['price_units']= $request->price_units ?? 0;
        $data['fabric_composition']= $fabric_composition;

        DB::beginTransaction();
        try{
            InquiryPOrder::where('id',$request->po_id)->update($data);
            $poID = $request->po_id;

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
            POTesting::where('po_id', $poID)->delete();
            foreach($request->fabric_testing as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['type']= 'fabric_testing';
                $skuData['color_id']=$sku->color_id;
                $skuData['length_qty']=$sku->length;
                if($sku->length > 0)
                    POTesting::insert($skuData);
            }
            foreach($request->garment_testing as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['type']= 'garment_testing';
                $skuData['color_id']=$sku->color_id;
                $skuData['size_id']=$sku->size_id;
                $skuData['length_qty']=$sku->pieces;
                if($sku->pieces > 0)
                    POTesting::insert($skuData);
            }
            foreach($request->fit_sample as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['type']= 'fit_sample';
                $skuData['color_id']=$sku->color_id;
                $skuData['size_id']=$sku->size_id;
                $skuData['length_qty']=$sku->pieces;
                if($sku->pieces > 0)
                    POTesting::insert($skuData);
            }
            foreach($request->pp_sample as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['type']= 'pp_sample';
                $skuData['color_id']=$sku->color_id;
                $skuData['size_id']=$sku->size_id;
                $skuData['length_qty']=$sku->pieces;
                if($sku->pieces > 0)
                    POTesting::insert($skuData);
            }
            foreach($request->testing_sample as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['type']= 'testing_sample';
                $skuData['color_id']=$sku->color_id;
                $skuData['size_id']=$sku->size_id;
                $skuData['length_qty']=$sku->pieces;
                if($sku->pieces > 0)
                    POTesting::insert($skuData);
            }
            foreach($request->shipment_sample as $sku){
                $skuData=[];
                $skuData['po_id']= $poID;
                $skuData['type']= 'shipment_sample';
                $skuData['color_id']=$sku->color_id;
                $skuData['size_id']=$sku->size_id;
                $skuData['length_qty']=$sku->pieces;
                POTesting::insert($skuData);
            }


            /* Confirm Po Log starts */
            try{
                //InquiryPOLog::edit_po_log($request);
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

    /* Generate The PO PDF */
    public static function generate_po_pdf_new(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'po_id' => 'required',
            'user_id' => 'required',
            'company_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $colors = [];
        $sizes = [];
        $media['files'] = InquiryMediaPO::getInquiryPOMedia($request);
        $media['serverURL'] = ''; //config('filesystems.disks.s3.url');
        $user = CommonApp::getUserDetailsById($request->user_id);
        $company = CommonApp::getCompanyDetailsbyID($request->company_id);
        $sku = InquirySKUPO::getPOSKU($request);
        $testings = POTesting::getPOTesting($request);
        $data = InquiryPOrder::get_po_details($request);
        //dd($testings);

        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size']);
            }
        }

        $datas['poID'] = $request->po_id;
        $datas['data'] = $data;
        $datas['request'] = $request;
        $datas['media'] = $media;
        $datas['sku'] = $sku;
        $datas['testings'] = $testings;
        $datas['sizes'] = array_unique($sizes,SORT_REGULAR);
        $datas['colors'] = array_unique($colors,SORT_REGULAR);
        $datas['user'] = $user;
        $datas['logo'] = $company->logo;
        view()->share("datas",$datas);
        //return view('InquiryPDF');
        $pdf = Pdf::loadView('PoPDF');
        $pdf->setPaper('A4', 'portrait');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
        $pdf->setOption("enable_php", true);

        return $pdf->download();

    }

    /* Update PO Status */
    public static function update_po_status(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            $data['status']=1;
            InquiryPOrder::where('id',$request->po_id)->update($data);
            /* Update PO Status Log starts */
            try{
                $ip_address = $header->header('Ip-Address') ?? '';
                $platform = $header->header('Platform') ?? '';
                InquiryPOLog::po_status_update_log($request,$ip_address,$platform);
            }catch(Exception $e){

            }
            /* Update PO Status Log end */
        }catch(Exception $e){
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"PO Published Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    /* Get PO Id */
    public static function get_po_id(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $id = 0;
        try{
            $id = InquiryPOrder::where('company_id',$request->company_id)->where('workspace_id',$request->workspace_id)->count();
            /* Update PO Status Log starts */
            try{
                $ip_address = $header->header('Ip-Address') ?? '';
                $platform = $header->header('Platform') ?? '';
                InquiryPOLog::po_status_update_log($request,$ip_address,$platform);
            }catch(Exception $e){

            }
            /* Update PO Status Log end */
        }catch(Exception $e){
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>($id+1)],200);
        return CommonApp::webEncrypt($res);
    }

    /* To Download All the PO List */
    public static function download_po_list(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions[]= ['inquiry_new_po.company_id','=',$request->company_id];
        $whereConditions[]= ['inquiry_new_po.workspace_id','=',$request->workspace_id];
        $whereConditions[]= ['inquiry_new_po.user_id','=',$request->user_id];
        $whereConditions[]= ['inquiry_new_po.staff_id','=',$request->staff_id];
        $request->page = (isset($request->page) && $request->page!='')?$request->page:1;

        $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        $advFilter = [];
        if(isset($request->article_id) && $request->article_id!=''){
            $whereConditions[]=['inquiry_new_po.article_id','=',$request->article_id];
            $advFilter['article'] = CommonApp::get_inquiry_article_name($request->article_id);
        }
        if(isset($request->fabric_type_id) && $request->fabric_type_id!=''){
           // $whereConditions[]=['inquiry_new_po.fabric_type_id','=',$request->fabric_type_id];
            $advFilter['fabric'] = CommonApp::get_fabric_name($request->fabric_type_id);
        }
        if(isset($request->incoterms_id) && $request->incoterms_id!=''){
           // $whereConditions[]=['inquiry_new_po.incoterms','=',$request->incoterms_id];
            $advFilter['incoterms_name'] = CommonApp::get_incoterms_name($request->incoterms_id);
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date==''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['inquiry_new_po.created_at','>=',$from];
            $whereConditions[]=['inquiry_new_po.created_at','<=',$to];
            $advFilter['startDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($from));
            $advFilter['endDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($to));
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date!=''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->to_date));
            $whereConditions[]=['inquiry_new_po.created_at','>=',$from];
            $whereConditions[]=['inquiry_new_po.created_at','<=',$to];
            $advFilter['startDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($from));
            $advFilter['endDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($to));
        }

        $inquiries= InquiryPOrder::get_all_po($whereConditions,$request,1);

        App::setlocale($dateFormatAndLanguage['language']);

        $data['inquiries']=$inquiries;
        $data['dateFormat'] = $dateFormatAndLanguage['dateFormat'];
        $data['advFilter'] = $advFilter;
        if(count($inquiries)>0){
            view()->share("data",$data);
            $pdf = Pdf::loadView('POListPDF');
            $pdf->setPaper('A4', 'portrait');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            return $pdf->download();
        }
    }

    /** Get Po details mobile view */
    public static function get_po_mobile(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'po_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $colors = [];
        $sizes = [];
        $media['files'] = InquiryMediaPO::getInquiryPOMedia($request);
        $media['serverURL'] = ''; //config('filesystems.disks.s3.url');
        $sku = InquirySKUPO::getPOSKU($request);
        $testings = POTesting::getPOTesting($request);
        $testingsku = POTesting::getPOTestingSKU($request);
        $result = InquiryPOrder::get_po_details($request);
        $company_id = 0;
        if(!empty($result))
        {
            $company_id = $result[0]->company_id;
        }
        $company = CommonApp::getCompanyDetailsbyID($company_id);
        $logo= ($company->logo !='' && $company->logo!=NULL) ? Storage::disk('s3')->temporaryUrl($company->logo, '+75 minutes') : "";
        //dd($testingsku);

        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size']);
            }
        }

        $data['poID'] = $request->po_id;
        $data['poDetails'] = $result;
        $data['poMediaDetails'] = $media;
        $data['poSKUDetails']['sku'] = $sku;
        $data['poSKUDetails']['colors'] =(object)array_unique($colors,SORT_REGULAR);
        $data['poSKUDetails']['sizes'] =(object)array_unique($sizes,SORT_REGULAR);
        $data['serverURL'] = config('filesystems.disks.s3.url');
        $data['testings'] = $testings;
        $data['testingsku'] = $testingsku;
        $data['logo'] = $logo;

        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    /* Delete PO */
    public static function delete_po(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            InquiryPOrder::where('id',$request->po_id)->delete();
            /* Update PO Status Log starts */
            try{
                $ip_address = $header->header('Ip-Address') ?? '';
                $platform = $header->header('Platform') ?? '';
                InquiryPOLog::po_delete_update_log($request,$ip_address,$platform);
            }catch(Exception $e){

            }
            /* Update PO Status Log end */
        }catch(Exception $e){
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"PO Deleted Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    /*Multi Style PO Generation */
    public static function generate_po_multiple(Request $request){
        $header=$request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'po_number'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $after_values = array();
        $same_testing_company = $i = $parent_po_id =0;
        $testing_company='';
        $data = [];
        $data['po_number']= $request->po_number ?? 0;
        $data['media_reference_id']= $request->media_reference_id ?? '';;
        $data['sign_option']= is_array($request->sign_option) ? implode(',', $request->sign_option) : NULL;
        $data['company_id']= $request->company_id ?? 0;
        $data['workspace_id']= $request->workspace_id ?? 0;
        $data['user_id']= $request->user_id ?? 0;
        $data['staff_id']= $request->staff_id ?? 0;
        $data['buyer']= $request->client_info[0]->buyer ??'';
        $data['seller']= $request->client_info[0]->seller ??'';
        $data['maker']= $request->client_info[0]->maker ??'';
        $data['additional_information']= $request->additional_information ?? '';
        $data['language']= $request->language ?? 'en';
        $data['translated']= 0;
        $after_values[$i]['po_number'] = $data['po_number'];
        $after_values[$i]['sign_option'] = $data['sign_option'];
        $after_values[$i]['buyer'] = $data['buyer'];
        $after_values[$i]['seller'] = $data['seller'];
        $after_values[$i]['maker'] = $data['maker'];
        $after_values[$i]['additional_information'] = $data['additional_information'];
        foreach($request->order_info as $order)
        {
            $after_values[$i]['order_info'] = $order->style_details;
            $styles = $order->style_details;
            $style_no =$styles[0]->style_no ?? '';
            $data['style_no']= $style_no;
            $data['article_id']= $styles[0]->artice_id ?? 0;
            $data['article_name']= $styles[0]->article_name ?? NULL;
            $data['article_description']= $styles[0]->article_description ?? '';
            $data['fabric_type_id']= $styles[0]->fabric_type_id ?? 0;
            $data['fabric_composition_id']= $styles[0]->fabric_composition_id ?? 0;
            $data['fabric_type']= $styles[0]->fabric_type ?? '';
            $data['fabric_GSM']= $styles[0]->fabric_GSM ?? '';
            $data['gsm_tolerance']= $styles[0]->gsm_tolerance ?? '';
            $data['yarn_count_type']= $styles[0]->yarn_count_type ?? '';
            $data['total_qty']= $styles[0]->total_qty ?? 0;
            $data['total_qty_min_tol']= $styles[0]->total_qty_min_tol ?? 0;
            $data['total_qty_max_tol']= $styles[0]->total_qty_max_tol ?? 0;
            $data['units']= $styles[0]->units ?? 0;
            $data['currency']= $styles[0]->currency ?? '';
            $data['price']= $styles[0]->price ?? 0;
            $data['price_units']= $styles[0]->price_units ?? 0;
            $data['gsm_percent_type']= $styles[0]->gsm_percent_type ?? 0;
            $data['total_qty_percent_type']= $styles[0]->total_qty_percent_type ?? 0;
            $fabric_composition='';
            if(count($styles[0]->fabric_composition)>0)
            {
                foreach($styles[0]->fabric_composition as $d)
                {
                    $fabric_composition .=$d->value.",";
                }

                $fabric_composition = substr($fabric_composition,0,strlen($fabric_composition)-1);
            }
            $data['fabric_composition']= $fabric_composition;

            $after_values[$i]['commercial_info'] = $order->commercial_info;
            $commercial = $order->commercial_info;
            $data['incoterms']= $commercial[0]->incoterms ?? 0;
            $data['payment_terms']= $commercial[0]->payment_terms ?? '';
            $data['delivery_date']= $commercial[0]->delivery_date ?? '';
            $data['delivery_date_type']= $commercial[0]->delivery_date_type ?? '';
            $data['origin_port']= $commercial[0]->origin_port ?? '';
            $data['destination_port']= $commercial[0]->destination_port ?? '';
            $data['mode_of_shipment']= $commercial[0]->mode_of_shipment ?? '';
            $docs='';
            if(count($commercial[0]->document_requirement)>0)
            {
                foreach($commercial[0]->document_requirement as $d)
                {
                    $docs .=$d->value."|".$d->original_copies."|".$d->duplicate_copies."||";
                }

                $docs = substr($docs,0,strlen($docs)-2);
            }
            $data['document_requirement']= $docs;
            $data['hs_code']= $commercial[0]->hs_code ?? '';
            $data['place_of_jurisdiction']= $commercial[0]->place_of_jurisdiction ?? '';
            $data['penality']= $commercial[0]->penality ?? '';
            $data['forwarder']= $commercial[0]->forwarder ?? NULL;
            $data['forwarder_id']= $commercial[0]->forwarder_id ?? 0;
            $data['forwarder_address']= $commercial[0]->forwarder_address ?? NULL;
            $data['forwarder_contact_person']= $commercial[0]->forwarder_contact_person ?? NULL;
            $data['forwarder_phone']= $commercial[0]->forwarder_phone ?? NULL;
            $data['forwarder_email']= $commercial[0]->forwarder_email ?? NULL;

            $after_values[$i]['testing_info'] = $order->testing_info;
            $testing = $order->testing_info;
            $data['fabric_testing_agency']= $testing[0]->fabric_testing_agency ?? '';
            $data['garment_testing_agency']= $testing[0]->garment_testing_agency ?? '';
            $data['testing_cost']= $testing[0]->testing_cost ?? NULL;

            $after_values[$i]['inspection'] = $order->inspection_info;
            $inspection = $order->inspection_info;
            $data['inspection_company']= $inspection[0]->inspection_company ?? '';
            $data['inspection_type']= $inspection[0]->inspection_type ?? '';
            $data['inspection_cost']= $inspection[0]->inspection_cost ?? NULL;

            $t_req='';
            if(count($testing[0]->testing_requirements)>0)
            {
                foreach($testing[0]->testing_requirements as $d)
                {
                    $d->grade = ($d->grade=="") ? '-':$d->grade;
                    $d->remarks = ($d->remarks=="") ? '-':$d->remarks;
                    $t_req .=$d->value."|".$d->grade."|".$d->test_method."|".$d->remarks."||";
                }

                $t_req = substr($t_req,0,strlen($t_req)-2);
            }
            $data['testing_requirements']= $t_req;


            if($data['fabric_testing_agency'] === $data['garment_testing_agency'] && ($data['fabric_testing_agency'] ===$testing_company) && $data['fabric_testing_agency']!=''){
                $same_testing_company = 1;
            }
            if($data['fabric_testing_agency'] === $data['garment_testing_agency'] && $i==0){
                $testing_company = $data['fabric_testing_agency'];
            }

            DB::beginTransaction();
            try{
                InquiryPOrder::insert($data);
                $poID = DB::getPdo()->lastInsertId();
                if($i==0){
                    $parent_po_id = $poID;
                    $i++;
                }
                $parent_idupdate['parent_id']=$parent_po_id;
                InquiryPOrder::where('id',$poID)->update($parent_idupdate);

                InquiryMediaPO::where('po_id', $poID)->delete();
                $dataToUpdate['po_id']=$poID;
                $dataToUpdate['parent_po_id']=$parent_po_id;
                $sty_media_reference_id = $request->media_reference_id.'_'.$style_no;
                InquiryMediaPO::where('temp_id',$sty_media_reference_id)->update($dataToUpdate);
                InquiryMediaPO::where('temp_id',$request->media_reference_id)->where('media_type','!=','ProductImage')->update($dataToUpdate);

                InquirySKUPO::where('po_id', $poID)->delete();
                foreach($styles[0]->sku_data as $sku){
                    $skuData=[];
                    $skuData['po_id']= $poID;
                    $skuData['parent_po_id']= $parent_po_id;
                    $skuData['color_id']=$sku->color_id;
                    $skuData['size_id']=$sku->size_id;
                    $skuData['color_ratio']=$sku->color_ratio ?? 0;
                    $skuData['size_ratio']=$sku->size_ratio ?? 0;
                    $skuData['quantity']=$sku->quantity;
                    $skuData['created_at']=date('Y-m-d H:i:s');
                    InquirySKUPO::insert($skuData);
                }
                $after_values[$i]['sample_requirements'] = $order->sample_requirements;
                $sample = $order->sample_requirements;
                foreach ($sample as $s){
                    $skuData=[];
                    $skuData['po_id']= $poID;
                    $skuData['po_parent_id']= $parent_po_id;
                    $skuData['type']= $s->type;
                    $skuData['color_id']=$s->color_id;
                    $skuData['size_id']=$s->size_id;
                    $skuData['length_qty']=$s->length_qty;
                    if($s->length_qty > 0)
                        POTesting::insert($skuData);
                }

            }catch(Exception $e){
                DB::rollback();
                $res = json_encode(["status_code"=>401,'status'=>"failure","message"=>$e->getMessage()],200);
                return CommonApp::webEncrypt($res);
            }
            DB::commit();
        }

        DB::beginTransaction();
        /* Generate Po Log starts */
        try{
            $ip_address = $header->header('Ip-Address') ?? '';
            $platform = $header->header('Platform') ?? '';
            InquiryPOLog::generate_po_log($parent_po_id,$request,$after_values,$ip_address,$platform);
        }catch(Exception $e){

        }
        /* Generate Po Log end */
        try{
            if($same_testing_company==1){
                $update['same_testing_agency']=$same_testing_company;
                InquiryPOrder::where('parent_id',$parent_po_id)->update($update);
            }

        }catch(Exception $e){
            DB::rollback();
            $res = json_encode(["status_code"=>401,'status'=>"failure","message"=>$e->getMessage()],200);
            return CommonApp::webEncrypt($res);
        }
        DB::commit();



        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>"Success"],200);
        return CommonApp::webEncrypt($res);
    }

    /*Multi Style View PO */
    public function view_new_po_multiple(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions[]= ['inquiry_new_po.company_id','=',$request->company_id];
        $whereConditions[]= ['inquiry_new_po.workspace_id','=',$request->workspace_id];
        $whereConditions[]= ['inquiry_new_po.user_id','=',$request->user_id];
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
        if(isset($request->style_no) && $request->style_no!=''  && $request->style_no!='0'){
            $whereConditions[]=['inquiry_new_po.style_no','=',$request->style_no];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date==''){
            $from = date('Y-m-d',strtotime($request->from_date));
            $to = date('Y-m-d');
            $whereConditions[]=['inquiry_new_po.delivery_date','>=',$from];
            $whereConditions[]=['inquiry_new_po.delivery_date','<=',$to];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date!=''){
            $from = date('Y-m-d',strtotime($request->from_date));
            $to = date('Y-m-d',strtotime($request->to_date));
            $whereConditions[]=['inquiry_new_po.delivery_date','>=',$from];
            $whereConditions[]=['inquiry_new_po.delivery_date','<=',$to];
        }
        if(isset($request->buyer) && $request->buyer!=''  && $request->buyer!='0'){
            $whereConditions[]=['inquiry_new_po.buyer','like','%'.$request->buyer.'%'];
        }
        if(isset($request->seller) && $request->seller!=''  && $request->seller!='0'){
            $whereConditions[]=['inquiry_new_po.seller','like','%'.$request->seller.'%'];
        }
        if(isset($request->maker) && $request->maker!=''  && $request->maker!='0'){
            $whereConditions[]=['inquiry_new_po.maker','like','%'.$request->maker.'%'];
        }
        if(isset($request->status) && $request->status!='' ){
            $whereConditions[]=['inquiry_new_po.status','=',$request->status];
        }

        $inquiries= InquiryPOrder::get_all_po_multiple($whereConditions,$request,0);
        $pdfpath  = config('app.public_url').'PO/';
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiries,"filterData"=>$this->po_Filters($request),"pdfpath"=>$pdfpath],200);
        return CommonApp::webEncrypt($res);
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

        $buyer =  InquiryPOrder::select(DB::raw("SUBSTRING_INDEX(buyer, '\n', 1) as buyer"), DB::raw("'buyer' as type"))
        ->where($whereCondition)
        ->groupBy("buyer")
        ->orderBy("buyer", "asc")->get();

        $seller =  InquiryPOrder::select(DB::raw("SUBSTRING_INDEX(seller, '\n', 1) as seller"), DB::raw("'seller' as type"))
        ->where($whereCondition)
        ->groupBy("seller")
        ->orderBy("seller", "asc")->get();

        $maker =  InquiryPOrder::select(DB::raw("SUBSTRING_INDEX(maker, '\n', 1) as maker"), DB::raw("'maker' as type"))
        ->where($whereCondition)
        ->groupBy("maker")
        ->orderBy("maker", "asc")->get();

        $filterArray['article'] = $filterArrayData->where('type', 'article')->values();
        $filterArray['fabric'] = $filterArrayData->where('type', 'fabric')->values();
        $filterArray['style_no'] = $filterArrayData->where('type', 'style_no')->values();
        $filterArray['incoterms'] = $inoterms;
        $filterArray['buyer'] = $buyer;
        $filterArray['seller'] = $seller;
        $filterArray['maker'] = $maker;
        return $filterArray;
    }
    /* Multi Style Get PO */
    public static function get_po_multiple(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_parent_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
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
        $table = (isset($request->page) && $request->page =='View') ? '' : 'main';
        $result = InquiryPOrder::get_po_details_multi($request,$table);
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
        $data['poSKUDetails']['colors'] =(object)array_unique($colors,SORT_REGULAR);
        $data['poSKUDetails']['sizes'] =(object)array_unique($sizes,SORT_REGULAR);
        $data['serverURL'] = config('filesystems.disks.s3.url');
        $data['testings'] = $testings;
        $data['testingsku'] = $testingsku;
        $data['logo'] = $logo;
        $data['buyer_name'] = str_replace(',', '', $buyer);
        $data['seller_name'] = str_replace(',', '', $seller);
        $data['maker_name'] = str_replace(',', '', $maker);

        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    /* Generate The PO PDF */
    public static function generate_multiple_po_pdf_new(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'po_parent_id' => 'required',
            'user_id' => 'required',
            'company_id' => 'required',
           // 'language' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
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

    /* Update PO Status */
    public static function update_multiple_po_status(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_parent_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            $data['status']=1;
            InquiryPOrder::where('parent_id',$request->po_parent_id)->update($data);
            /* Update PO Status Log starts */
            try{
                $ip_address = $header->header('Ip-Address') ?? '';
                $platform = $header->header('Platform') ?? '';
                InquiryPOLog::po_status_update_log($request,$ip_address,$platform);
            }catch(Exception $e){

            }
            /* Update PO Status Log end */
        }catch(Exception $e){
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"PO Published Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    /* Delete Multi PO */
    public static function delete_multi_po(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_parent_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            InquiryPOrder::where('parent_id',$request->po_parent_id)->delete();
            InquirySKUPO::where('parent_po_id', $request->po_parent_id)->delete();
            InquiryMediaPO::where('parent_po_id', $request->po_parent_id)->delete();
            POTesting::where('po_parent_id', $request->po_parent_id)->delete();
            /* Update PO Status Log starts */
            try{
                $ip_address = $header->header('Ip-Address') ?? '';
                $platform = $header->header('Platform') ?? '';
                InquiryPOLog::po_delete_update_log($request,$ip_address,$platform);
            }catch(Exception $e){

            }
            /* Update PO Status Log end */
        }catch(Exception $e){
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"PO Deleted Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    /* Edit and Update The Multi style PO */
    public static function update_multi_po(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_parent_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $before_values = $after_values = array();
        $same_testing_company = 0;
        $testing_company='';
        $i = 0;
        $data = [];
        $data['parent_id']= $request->po_parent_id ?? 0;
        $data['po_number']= $request->po_number ?? 0;
        $data['media_reference_id']= $request->media_reference_id ?? '';;
        $data['sign_option']= is_array($request->sign_option) ? implode(',', $request->sign_option) : NULL;
        $data['company_id']= $request->company_id ?? 0;
        $data['workspace_id']= $request->workspace_id ?? 0;
        $data['user_id']= $request->user_id ?? 0;
        $data['staff_id']= $request->staff_id ?? 0;
        $data['buyer']= $request->client_info[0]->buyer ??'';
        $data['seller']= $request->client_info[0]->seller ??'';
        $data['maker']= $request->client_info[0]->maker ??'';
        $data['additional_information']= $request->additional_information ?? '';
        $after_values[$i]['po_number'] = $data['po_number'];
        $after_values[$i]['sign_option'] = $data['sign_option'];
        $after_values[$i]['buyer'] = $data['buyer'];
        $after_values[$i]['seller'] = $data['seller'];
        $after_values[$i]['maker'] = $data['maker'];
        $after_values[$i]['additional_information'] = $data['additional_information'];

        $parent_po_id = $request->po_parent_id;
        foreach($request->order_info as $order)
        {
            $after_values[$i]['order_info'] = $order->style_details;
            $styles = $order->style_details;
            $style_no =$styles[0]->style_no ?? '';
            $po_id =$styles[0]->po_id ?? 0;
            $data['style_no']= $style_no;
            $data['article_id']= $styles[0]->artice_id ?? 0;
            $data['article_name']= $styles[0]->article_name ?? NULL;
            $data['article_description']= $styles[0]->article_description ?? '';
            $data['fabric_type_id']= $styles[0]->fabric_type_id ?? 0;
            $data['fabric_composition_id']= $styles[0]->fabric_composition_id ?? 0;
            $data['fabric_type']= $styles[0]->fabric_type ?? '';
            $data['fabric_GSM']= $styles[0]->fabric_GSM ?? '';
            $data['gsm_tolerance']= $styles[0]->gsm_tolerance ?? '';
            $data['yarn_count_type']= $styles[0]->yarn_count_type ?? '';
            $data['total_qty']= $styles[0]->total_qty ?? 0;
            $data['total_qty_min_tol']= $styles[0]->total_qty_min_tol ?? 0;
            $data['total_qty_max_tol']= $styles[0]->total_qty_max_tol ?? 0;
            $data['units']= $styles[0]->units ?? 0;
            $data['currency']= $styles[0]->currency ?? '';
            $data['price']= $styles[0]->price ?? 0;
            $data['price_units']= $styles[0]->price_units ?? 0;
            $data['gsm_percent_type']= $styles[0]->gsm_percent_type ?? 0;
            $data['total_qty_percent_type']= $styles[0]->total_qty_percent_type ?? 0;
            $fabric_composition='';
            if(count($styles[0]->fabric_composition)>0)
            {
                foreach($styles[0]->fabric_composition as $d)
                {
                    $fabric_composition .=$d->value.",";
                }

                $fabric_composition = substr($fabric_composition,0,strlen($fabric_composition)-1);
            }
            $data['fabric_composition']= $fabric_composition;

            $commercial = $order->commercial_info;
            $after_values[$i]['commercial_info'] = $order->commercial_info;
            $data['incoterms']= $commercial[0]->incoterms ?? 0;
            $data['payment_terms']= $commercial[0]->payment_terms ?? '';
            $data['delivery_date']= $commercial[0]->delivery_date ?? '';
            $data['delivery_date_type']= $commercial[0]->delivery_date_type ?? '';
            $data['origin_port']= $commercial[0]->origin_port ?? '';
            $data['destination_port']= $commercial[0]->destination_port ?? '';
            $data['mode_of_shipment']= $commercial[0]->mode_of_shipment ?? '';
            $docs='';
            if(count($commercial[0]->document_requirement)>0)
            {
                foreach($commercial[0]->document_requirement as $d)
                {
                    $docs .=$d->value."|".$d->original_copies."|".$d->duplicate_copies."||";
                }

                $docs = substr($docs,0,strlen($docs)-2);
            }
            $data['document_requirement']= $docs;
            $data['hs_code']= $commercial[0]->hs_code ?? '';
            $data['place_of_jurisdiction']= $commercial[0]->place_of_jurisdiction ?? '';
            $data['penality']= $commercial[0]->penality ?? '';
            $data['forwarder']= $commercial[0]->forwarder ?? NULL;
            $data['forwarder_id']= $commercial[0]->forwarder_id ?? 0;
            $data['forwarder_address']= $commercial[0]->forwarder_address ?? NULL;
            $data['forwarder_contact_person']= $commercial[0]->forwarder_contact_person ?? NULL;
            $data['forwarder_phone']= $commercial[0]->forwarder_phone ?? NULL;
            $data['forwarder_email']= $commercial[0]->forwarder_email ?? NULL;

            $testing = $order->testing_info;
            $after_values[$i]['testing_info'] = $order->testing_info;
            $data['fabric_testing_agency']= $testing[0]->fabric_testing_agency ?? '';
            $data['garment_testing_agency']= $testing[0]->garment_testing_agency ?? '';
            $data['testing_cost']= $testing[0]->testing_cost ?? NULL;
            $t_req='';
            if(count($testing[0]->testing_requirements)>0)
            {
                foreach($testing[0]->testing_requirements as $d)
                {
                    $d->grade = ($d->grade=="") ? '-':$d->grade;
                    $d->remarks = ($d->remarks=="") ? '-':$d->remarks;
                    $t_req .=$d->value."|".$d->grade."|".$d->test_method."|".$d->remarks."||";
                }

                $t_req = substr($t_req,0,strlen($t_req)-2);
            }
            $data['testing_requirements']= $t_req;

            $inspection = $order->inspection_info;
            $after_values[$i]['inspection'] = $order->inspection_info;
            $data['inspection_company']= $inspection[0]->inspection_company ?? '';
            $data['inspection_type']= $inspection[0]->inspection_type ?? '';
            $data['inspection_cost']= $inspection[0]->inspection_cost ?? NULL;

            if($data['fabric_testing_agency'] === $data['garment_testing_agency'] && ($data['fabric_testing_agency'] ===$testing_company) && $data['fabric_testing_agency']!=''){
                $same_testing_company = 1;
            }
            if($data['fabric_testing_agency'] === $data['garment_testing_agency'] && $i==0){
                $testing_company = $data['fabric_testing_agency'];
            }

            DB::beginTransaction();
            try{
                if((int)$po_id>0){
                    InquiryPOrder::where('id',$po_id)->update($data);
                    $poID = $po_id;
                }else{
                    InquiryPOrder::insert($data);
                    $poID = DB::getPdo()->lastInsertId();
                }

                /**Translate Update */
                $trnsUpdate['translated']=0;
                InquiryPOrder::where('id',$poID)->update($trnsUpdate);

                //InquiryMediaPO::where('po_id', $poID)->delete();
                $dataToUpdate['po_id']=$poID;
                $dataToUpdate['parent_po_id']=$parent_po_id;
                $sty_media_reference_id = $request->media_reference_id.'_'.$style_no;
                InquiryMediaPO::where('temp_id',$sty_media_reference_id)->update($dataToUpdate);
                InquiryMediaPO::where('temp_id',$request->media_reference_id)->where('media_type','!=','ProductImage')->update($dataToUpdate);

                InquirySKUPO::where('po_id', $poID)->delete();
                //$after_values[$i]['sku_data'] = $styles[0]->sku_data;
                foreach($styles[0]->sku_data as $sku){
                    $skuData=[];
                    $skuData['po_id']= $poID;
                    $skuData['parent_po_id']= (int)$parent_po_id;
                    $skuData['color_id']=(int)$sku->color_id;
                    $skuData['size_id']=(int)$sku->size_id;
                    $skuData['color_ratio']=$sku->color_ratio ?? 0;
                    $skuData['size_ratio']=$sku->size_ratio ?? 0;
                    $skuData['quantity']=(int)$sku->quantity;
                    $skuData['created_at']=date('Y-m-d H:i:s');
                    InquirySKUPO::insert($skuData);
                }
                POTesting::where('po_id', $poID)->delete();
                $sample = $order->sample_requirements;
                $after_values[$i]['sample_requirements'] = $order->sample_requirements;
                foreach ($sample as $s){
                    $skuData=[];
                    $skuData['po_id']= (int)$poID;
                    $skuData['po_parent_id']= (int)$parent_po_id;
                    $skuData['type']= $s->type;
                    $skuData['color_id']=(int)$s->color_id;
                    $skuData['size_id']=(int)$s->size_id;
                    $skuData['length_qty']=(int)$s->length_qty;
                    if((int)$s->length_qty > 0)
                        POTesting::insert($skuData);
                }

            }catch(Exception $e){
                DB::rollback();
                $res = json_encode(["status_code"=>401,'status'=>"failure","message"=>$e->getMessage()],200);
                return CommonApp::webEncrypt($res);
            }
            DB::commit();
            $i++;
        }

        DB::beginTransaction();
        /* Edit Po Log starts */
        try{
            $before_values = InquiryPOLog::where('po_id',$request->po_parent_id)
            ->where(function($query) {
                $query->where('action', 'Generate')
                      ->orWhere('action', 'Edit');
            })
            ->orderBy('id', 'desc')->value('after_values');
            //return $before_values;
            $ip_address = $header->header('Ip-Address') ?? '';
            $platform = $header->header('Platform') ?? '';
            InquiryPOLog::edit_po_log($request->po_parent_id,$request,$after_values,$before_values,$ip_address,$platform);
        }catch(Exception $e){
           // return $e->getMessage();
        }
        /* Edit Po Log end */
        try{
            if($same_testing_company==1){
                $update['same_testing_agency']=$same_testing_company;
                InquiryPOrder::where('parent_id',$parent_po_id)->update($update);
            }
            //Delete Translate PO data
            InquiryPOrderTranslate::where('parent_id',$parent_po_id)->delete();
        }catch(Exception $e){
            DB::rollback();
            $res = json_encode(["status_code"=>401,'status'=>"failure","message"=>$e->getMessage()],200);
            return CommonApp::webEncrypt($res);
        }
        DB::commit();


        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"PO Updated Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    /* Delete PO */
    public static function delete_style_po(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validator= Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'po_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            InquiryPOrder::where('id',$request->po_id)->delete();
            InquirySKUPO::where('po_id', $request->po_id)->delete();
            InquiryMediaPO::where('po_id', $request->po_id)->delete();
            POTesting::where('po_id', $request->po_id)->delete();
            /* Update PO Status Log starts */
            try{
                $ip_address = $header->header('Ip-Address') ?? '';
                $platform = $header->header('Platform') ?? '';
                InquiryPOLog::po_delete_update_log($request,$ip_address,$platform);
            }catch(Exception $e){

            }
            /* Update PO Status Log end */
        }catch(Exception $e){
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"PO Deleted Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    /*PO translate*/
    public static function po_translate($parent_id){
        $result = InquiryPOrder::where('parent_id',$parent_id)->get();
        foreach($result as $res){
            $trans_arr['lang'] = $res->language=='en' ? 'jp' :'en';
            $trans_arr['translate'] = 1;

            $data = [];
            $data['po_number']= $res->po_number ?? 0;
            $data['id']= $res->id ?? 0;
            $data['parent_id']= $res->parent_id ?? 0;
            $data['media_reference_id']= $res->media_reference_id ?? '';;
            $data['sign_option']= $res->sign_option;
            $data['company_id']= $res->company_id ?? 0;
            $data['workspace_id']= $res->workspace_id ?? 0;
            $data['user_id']= $res->user_id ?? 0;
            $data['staff_id']= $res->staff_id ?? 0;
            $data['buyer']= $res->buyer ??'';
            $data['seller']= $res->seller ??'';
            $data['maker']= $res->maker ??'';
            $data['additional_information']= ($res->additional_information!==null && $res->additional_information!='')?Helper::translate($res->additional_information,$trans_arr):'';
            $data['language']= $res->language=='en' ? 'jp' :'en';
            $data['translated']= 1;
            $data['style_no']= $res->style_no;
            $data['article_id']= $res->article_id;
            $data['article_name']= ($res->article_name!==null && $res->article_name!='')?Helper::translate($res->article_name,$trans_arr): NULL;
            $data['article_description']= ($res->article_description!==null && $res->article_description!='')?Helper::translate($res->article_description,$trans_arr):'';
            $data['fabric_type_id']= $res->fabric_type_id;
            $data['fabric_composition_id']= $res->fabric_composition_id;
            $data['fabric_type']= ($res->fabric_type!==null && $res->fabric_type!='')?Helper::translate($res->fabric_type,$trans_arr):'';
            $data['fabric_GSM']= $res->fabric_GSM ?? '';
            $data['gsm_tolerance']= $res->gsm_tolerance ?? '';
            $data['yarn_count_type']= ($res->yarn_count_type!==null && $res->yarn_count_type!='')?Helper::translate($res->yarn_count_type,$trans_arr):'';
            $data['total_qty']= $res->total_qty ?? 0;
            $data['total_qty_min_tol']= $res->total_qty_min_tol ?? 0;
            $data['total_qty_max_tol']= $res->total_qty_max_tol ?? 0;
            $data['units']= $res->units ?? 0;
            $data['currency']= $res->currency ?? '';
            $data['price']= $res->price ?? 0;
            $data['price_units']= $res->price_units ?? 0;
            $data['gsm_percent_type']= $res->gsm_percent_type ?? 0;
            $data['total_qty_percent_type']= $res->total_qty_percent_type ?? 0;
            $data['fabric_composition']= ($res->fabric_composition!==null && $res->fabric_composition!='')?Helper::translate($res->fabric_composition,$trans_arr):'';
            $data['incoterms']= $res->incoterms ?? 0;
            $data['payment_terms']= ($res->payment_terms!==null && $res->payment_terms!='')?Helper::translate($res->payment_terms,$trans_arr):'';
            $data['delivery_date']= $res->delivery_date ?? '';
            $data['delivery_date_type']= $res->delivery_date_type ?? '';
            $data['origin_port']= ($res->origin_port!==null && $res->origin_port!='')?Helper::translate($res->origin_port,$trans_arr):'';
            $data['destination_port']= ($res->destination_port!==null && $res->destination_port!='')?Helper::translate($res->destination_port,$trans_arr):'';
            $data['mode_of_shipment']= ($res->mode_of_shipment!==null && $res->mode_of_shipment!='')?Helper::translate($res->mode_of_shipment,$trans_arr):'';
            $data['document_requirement']= ($res->document_requirement!==null && $res->document_requirement!='')?Helper::translate($res->document_requirement,$trans_arr):'';
            $data['hs_code']= $res->hs_code ?? '';
            $data['place_of_jurisdiction']= ($res->place_of_jurisdiction!==null && $res->place_of_jurisdiction!='')?Helper::translate($res->place_of_jurisdiction,$trans_arr):'';
            $data['penality']= ($res->penality!==null && $res->penality!='')?Helper::translate($res->penality,$trans_arr):'';
            $data['forwarder']= $res->forwarder ?? NULL;
            $data['forwarder_id']= $res->forwarder_id ?? 0;
            $data['forwarder_address']= $res->forwarder_address ?? NULL;
            $data['forwarder_contact_person']= $res->forwarder_contact_person ?? NULL;
            $data['forwarder_phone']= $res->forwarder_phone ?? NULL;
            $data['forwarder_email']= $res->forwarder_email ?? NULL;
            $data['fabric_testing_agency']= $res->fabric_testing_agency ?? '';
            $data['garment_testing_agency']= $res->garment_testing_agency ?? '';
            $data['testing_cost']= ($res->testing_cost!==null && $res->testing_cost!='')?Helper::translate($res->testing_cost,$trans_arr):'';
            $data['testing_requirements']= ($res->testing_requirements!==null && $res->testing_requirements!='')?Helper::translate($res->testing_requirements,$trans_arr):'';
            $data['same_testing_agency']= $res->same_testing_agency;

            InquiryPOrderTranslate::where('id',$res->id)->delete();

            InquiryPOrderTranslate::insert($data);

            $trnsUpdate['translated']=1;
            InquiryPOrder::where('id',$res->id)->update($trnsUpdate);
        }

        //dd($data);
    }
    /**Forwarder List */
    public static function all_forwarer_list(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
           // 'category_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = InquiryPOForwarder::get_forwarder_list($request);
        $forwarder_id = $request->forwarder_id ?? 0;
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data,"forwarder_id"=>$forwarder_id],200);
        return CommonApp::webEncrypt($res);
    }

    /** Add Forwarder */
    public static function add_forwarder(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            // 'company_name' => 'required',
            // 'address' => 'required',
            // 'contact_person' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $filedata = [];
        $filedata['company_id']=$request->company_id ?? 0;
        $filedata['workspace_id']=$request->workspace_id ?? 0;
        $filedata['company_name'] = $request->company_name ?? NULL;
        $filedata['address'] = $request->address ?? NULL;
        $filedata['contact_person'] = $request->contact_person ?? NULL;
        $filedata['contact_phone'] = $request->contact_phone ?? NULL;
        $filedata['contact_email'] = $request->contact_email ?? NULL;
        $filedata['created_at'] = date('Y-m-d H:i:s');

        InquiryPOForwarder::insert($filedata);
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Forwarder Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }
    /** Edit Forwarder */
    public static function edit_forwarder(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            // 'company_name' => 'required',
            // 'address' => 'required',
            // 'contact_person' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $filedata = [];
        $filedata['company_id']=$request->company_id ?? 0;
        $filedata['workspace_id']=$request->workspace_id ?? 0;
        $filedata['company_name'] = $request->company_name ?? NULL;
        $filedata['address'] = $request->address ?? NULL;
        $filedata['contact_person'] = $request->contact_person ?? NULL;
        $filedata['contact_phone'] = $request->contact_phone ?? NULL;
        $filedata['contact_email'] = $request->contact_email ?? NULL;
        InquiryPOForwarder::where('id',$request->id)->update($filedata);
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Forwarder Updated Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    /**Basic Information */
    public static function get_basic_information(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = InquiryPOrder::select('id','style_no','article_id','article_name','fabric_type_id as fabric_id','fabric_type','po_number as po_no','id as po_id')->where('id',$request->id)->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }
    /**Inquiry Dashboard Information */
    public static function inquiry_dashboard_details(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'year' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = InquiryPOrder::get_inq_dashboard_details($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    /*Add PO Comments*/
    public function addPOComments(Request $request)
    {
    $header = $request;
    $request = CommonApp::webDecrypt($request->getContent());
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
        return CommonApp::webEncrypt($res);
    }

    /*Get PO comments details*/
    public function getPOComments(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
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
        return CommonApp::webEncrypt($res);

    }

    public static function delete_po_comments_file(Request $request){
        $header=$request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'id' => 'required',
            'reason'=>'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
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
        return CommonApp::webEncrypt($res);
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
            return CommonApp::webEncrypt($res);
        }
        $res = json_encode(["status_code" => 401, "status" => "error", "message" => "Failed to upload the audio file."]);

        return $res;
    }

    /*PO translate*/
    public static function translate_po(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'parent_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $result = InquiryPOrder::where('parent_id',$request->parent_id)->get();
        foreach($result as $res){
            $trans_arr['lang'] = $res->language=='en' ? 'jp' :'en';
            $trans_arr['translate'] = 1;

            $data = [];
            $data['po_number']= $res->po_number ?? 0;
            $data['id']= $res->id ?? 0;
            $data['parent_id']= $res->parent_id ?? 0;
            $data['media_reference_id']= $res->media_reference_id ?? '';;
            $data['sign_option']= $res->sign_option;
            $data['company_id']= $res->company_id ?? 0;
            $data['workspace_id']= $res->workspace_id ?? 0;
            $data['user_id']= $res->user_id ?? 0;
            $data['staff_id']= $res->staff_id ?? 0;
            $data['buyer']= $res->buyer ??'';
            $data['seller']= $res->seller ??'';
            $data['maker']= $res->maker ??'';
            $data['additional_information']= ($res->additional_information!==null && $res->additional_information!='')?Helper::translate($res->additional_information,$trans_arr):'';
            $data['language']= $res->language=='en' ? 'jp' :'en';
            $data['translated']= 1;
            $data['style_no']= $res->style_no;
            $data['article_id']= $res->article_id;
            $data['article_name']= ($res->article_name!==null && $res->article_name!='')?Helper::translate($res->article_name,$trans_arr): NULL;
            $data['article_description']= ($res->article_description!==null && $res->article_description!='')?Helper::translate($res->article_description,$trans_arr):'';
            $data['fabric_type_id']= $res->fabric_type_id;
            $data['fabric_composition_id']= $res->fabric_composition_id;
            $data['fabric_type']= ($res->fabric_type!==null && $res->fabric_type!='')?Helper::translate($res->fabric_type,$trans_arr):'';
            $data['fabric_GSM']= $res->fabric_GSM ?? '';
            $data['gsm_tolerance']= $res->gsm_tolerance ?? '';
            $data['yarn_count_type']= ($res->yarn_count_type!==null && $res->yarn_count_type!='')?Helper::translate($res->yarn_count_type,$trans_arr):'';
            $data['total_qty']= $res->total_qty ?? 0;
            $data['total_qty_min_tol']= $res->total_qty_min_tol ?? 0;
            $data['total_qty_max_tol']= $res->total_qty_max_tol ?? 0;
            $data['units']= $res->units ?? 0;
            $data['currency']= $res->currency ?? '';
            $data['price']= $res->price ?? 0;
            $data['price_units']= $res->price_units ?? 0;
            $data['gsm_percent_type']= $res->gsm_percent_type ?? 0;
            $data['total_qty_percent_type']= $res->total_qty_percent_type ?? 0;
            $data['fabric_composition']= ($res->fabric_composition!==null && $res->fabric_composition!='')?Helper::translate($res->fabric_composition,$trans_arr):'';
            $data['incoterms']= $res->incoterms ?? 0;
            $data['payment_terms']= ($res->payment_terms!==null && $res->payment_terms!='')?Helper::translate($res->payment_terms,$trans_arr):'';
            $data['delivery_date']= $res->delivery_date ?? '';
            $data['delivery_date_type']= $res->delivery_date_type ?? '';
            $data['origin_port']= ($res->origin_port!==null && $res->origin_port!='')?Helper::translate($res->origin_port,$trans_arr):'';
            $data['destination_port']= ($res->destination_port!==null && $res->destination_port!='')?Helper::translate($res->destination_port,$trans_arr):'';
            $data['mode_of_shipment']= ($res->mode_of_shipment!==null && $res->mode_of_shipment!='')?Helper::translate($res->mode_of_shipment,$trans_arr):'';
            $data['document_requirement']= ($res->document_requirement!==null && $res->document_requirement!='')?Helper::translate($res->document_requirement,$trans_arr):'';
            $data['hs_code']= $res->hs_code ?? '';
            $data['place_of_jurisdiction']= ($res->place_of_jurisdiction!==null && $res->place_of_jurisdiction!='')?Helper::translate($res->place_of_jurisdiction,$trans_arr):'';
            $data['penality']= ($res->penality!==null && $res->penality!='')?Helper::translate($res->penality,$trans_arr):'';
            $data['forwarder']= $res->forwarder ?? NULL;
            $data['forwarder_id']= $res->forwarder_id ?? 0;
            $data['forwarder_address']= $res->forwarder_address ?? NULL;
            $data['forwarder_contact_person']= $res->forwarder_contact_person ?? NULL;
            $data['forwarder_phone']= $res->forwarder_phone ?? NULL;
            $data['forwarder_email']= $res->forwarder_email ?? NULL;
            $data['fabric_testing_agency']= $res->fabric_testing_agency ?? '';
            $data['garment_testing_agency']= $res->garment_testing_agency ?? '';
            $data['testing_cost']= ($res->testing_cost!==null && $res->testing_cost!='')?Helper::translate($res->testing_cost,$trans_arr):'';
            $data['testing_requirements']= ($res->testing_requirements!==null && $res->testing_requirements!='')?Helper::translate($res->testing_requirements,$trans_arr):'';
            $data['same_testing_agency']= $res->same_testing_agency;

            InquiryPOrderTranslate::where('id',$res->id)->delete();

            InquiryPOrderTranslate::insert($data);

            $trnsUpdate['translated']=1;
            InquiryPOrder::where('id',$res->id)->update($trnsUpdate);
        }
        $res = json_encode(["status_code" => 200, "status" => "Success", "message" => "Translated Successfully"]);
        return CommonApp::webEncrypt($res);
        //dd($data);
    }

}
