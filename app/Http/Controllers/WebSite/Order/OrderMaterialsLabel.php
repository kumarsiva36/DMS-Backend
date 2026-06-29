<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\OrderContacts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Common\Uploads;
use App\Models\InquiryLabelVendor;
use App\Models\Order;
use App\Models\OrderBOM;
use App\Models\Orderlog;
use App\Models\OrderMedia;
use App\Models\OrderMaterialsLabel as OrderMaterialsLabels;
use App\Models\OrderUnits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\OrderApprovalHistory;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use App\Models\RolesAndPermissions;

ini_set('memory_limit',-1);
class OrderMaterialsLabel extends Controller
{
    public static function add_materials_label_media(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $companyFolder = $companyDetails->aws_s3_path;
        $free_storage = ($companyDetails->max_storage_size - ($companyDetails->storage_used + (int)config('constant.plan_storage_free_mb')))*1024*1024;
        $storageUsed = $companyDetails->storage_used*1024*1024;
        $storageToBeAdded = 0;
        $filedata = [];
        if($request->file('file')){
            $file = $request->file('file');
            if($file->getSize() > $free_storage ){
                return response()->json(["status_code"=>401,"status"=>"failure","error"=>"Your Plan storage is full. Please contact DMS Admin"]);
            }
            $string = str_replace(' ', '_', $file->getClientOriginalName()); // Replaces all spaces with hyphens.
            $nameOfFile = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
            $fileName = time().'_'.$nameOfFile;
            $filepath = $companyFolder.'/Orders/Materials/'.$request->order_id.'/'.$fileName;
            Uploads::orderAddtionalSpec($file,$filepath);
            $filedata['filename']=$fileName;
            $filedata['orginalfilename']=$file->getClientOriginalName();
            $filedata['filepath']=$filepath;
            $filedata['filesize']=$file->getSize();
            $storageToBeAdded += $file->getSize();
            $filedata['order_id']=$request->order_id;
            $filedata['media_type']=$request->media_type;
            $filedata['company_id']=$request->company_id ?? 0;
            $filedata['workspace_id']=$request->workspace_id ?? 0;
            $filedata['created_at']=date('Y-m-d H:i:s');

            try{
                OrderMedia::insert($filedata);
                $companyDetails->storage_used = ($storageUsed + (int)$storageToBeAdded)/(1024*1024);
                $companyDetails->save();
                /* Order file Add Log starts */
                try{
                    if(isset($request->upload_type) && $request->upload_type=='Edit'){
                        $data=[];
                        $data['file_type']=$request->media_type;
                        $data['filepath']=config('filesystems.disks.s3.url').$filedata['filepath'];
                        $data['orginalfilename']=$filedata['orginalfilename'];

                        $logArry = array();
                        $logArry['order_id'] =$request->order_id;
                        $logArry['company_id'] = $request->company_id??0;
                        $logArry['workspace_id'] = $request->workspace_id??0;
                        $logArry['staff_id'] =$request->staff_id ?? 0;
                        $logArry['user_id'] = $request->user_id ?? 0;
                        $logArry['action'] ="File Added";
                        $logArry['after_values'] = json_encode($data);
                        Orderlog::insert($logArry);
                    }
                }catch(Exception $e){
                }
                /* Order file Add Log end */

            }catch(Exception $e){
                return response()->json(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            }

            $res['files'] = OrderMedia::getFiles($request->order_id,$request->media_type);
            $res['serverURL'] = config('filesystems.disks.s3.url');

            $filePath = public_path() . '/OrderPO';
            if (!file_exists($filePath)) {
                File::makeDirectory($filePath, 0777, true, true);
            }
            if($request->media_type =="MeasurementSheet" && (stristr($filedata['orginalfilename'],'.pdf'))){
                $name = $request->order_id."_".$file->getClientOriginalName();
                $file->move($filePath, $name);
            }
            if($request->media_type =="TechPack" && (stristr($filedata['orginalfilename'],'.pdf'))){
                $name = $request->order_id."_".$file->getClientOriginalName();
                $file->move($filePath, $name);
            }

            return response()->json(["status_code"=>200,'status'=>"success","message"=>"Files Added Successfully","files"=>$res],200);
        }else{
            $res['files'] = OrderMedia::getFiles($request->order_id,$request->media_type);
            $res['serverURL'] = config('filesystems.disks.s3.url');
            return response()->json(["status_code"=>200,'status'=>"failure","message"=>"Something went wrong","files"=>$res],200);
        }
    }
    /* Add materials and labels in the order */
    public static function add_materials_label(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $data = [];
        // $data['company_id']= $request->company_id ?? 0;
        // $data['workspace_id']= $request->workspace_id ?? 0;
        $data['created_user_id']= $request->user_id ?? 0;
        $data['crated_staff_id']= $request->staff_id ?? 0;
        $data['order_id']= $request->order_id ?? 0;
        $data['print_type']= $request->print_type ?? Null;
        $data['print_size']= $request->print_size ?? Null;
        $data['print_no_colors']= $request->print_no_colors ?? Null;
        $data['print_vendor_id']= $request->print_vendor_id ?? 0;
        $data['main_label']= $request->main_label ?? Null;
        $data['main_label_vendor_id']= $request->main_label_vendor_id ?? 0;
        $data['washcare_label']= $request->washcare_label ?? Null;
        $data['washcare_label_vendor_id']= $request->washcare_label_vendor_id ?? 0;
        $data['barcode_label']= $request->barcode_label ?? Null;
        $data['barcode_label_vendor_id']= $request->barcode_label_vendor_id ?? 0;
        $data['hangtag']= $request->hangtag ?? Null;
        $data['hangtag_vendor_id']= $request->hangtag_vendor_id ?? 0;
        $data['trims_notifications']= $request->trims_notifications ?? Null;
        $data['polybag_size_thickness']= $request->polybag_size_thickness ?? Null;
        $data['polybag_material']= $request->polybag_material ?? Null;
        $data['polybag_print_details']= $request->polybag_print_details ?? Null;
        $data['polybag_vendor_id']= $request->polybag_vendor_id ?? 0;
        $data['carton_dimensions']= $request->carton_dimensions ?? Null;
        $data['carton_color']= $request->carton_color ?? Null;
        $data['carton_no_of_ply']= $request->carton_no_of_ply ?? Null;
        $data['carton_vendor_id']= $request->carton_vendor_id ?? 0;
        $data['carton_edge_finish']= $request->carton_edge_finish ?? Null;
        $data['carton_mark_details']= $request->carton_mark_details ?? Null;
        $data['carton_make_up']= $request->carton_make_up ?? Null;
        $data['air_freight']= $request->air_freight ?? Null;
        $data['flims_cd']= $request->flims_cd ?? Null;
        $data['picture_card']= $request->picture_card ?? Null;
        $data['inner_cardboard']= $request->inner_cardboard ?? Null;
        $data['shiping_size']= $request->shiping_size ?? Null;
        $data['created_at']= date('Y-m-d H:i:s');

        DB::beginTransaction();
        try{
            OrderMaterialsLabels::insert($data);
            /*Update Order Step Status*/
            $addOrderArr=[];
            $addOrderArr['step_level'] = '5';
            Order::where('id',$request->order_id)->update($addOrderArr);
        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Materials and Labels Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function delete_order_media(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'media_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        /* Order file Delete Log starts */
        try{
            $data=[];
            $data['file_type']=OrderMedia::get_media_file_type($request->media_id);
            $data['reason']=$request->reason??'-';

            $logArry = array();
            $logArry['order_id'] =$request->order_id;
            $logArry['company_id'] = $request->company_id??0;
            $logArry['workspace_id'] = $request->workspace_id??0;
            $logArry['staff_id'] =$request->staff_id ?? 0;
            $logArry['user_id'] = $request->user_id ?? 0;
            $logArry['action'] ="File Delete";
            $logArry['after_values'] = json_encode($data);
            Orderlog::insert($logArry);
        }catch(Exception $e){
        }
        /* Order file Add Log end */
        $res = OrderMedia::deleteOrderMedia($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","meassage"=>"File Deleted Successfully"],200);
        return CommonApp::webEncrypt($res);
    }
    public static function get_materials_label(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'order_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data= OrderMaterialsLabels::get_materials_details($request);
        $media= OrderMedia::get_materials_media($request);
        $serverURL = config('filesystems.disks.s3.url');
        $res = json_encode(["status_code"=>200,'status'=>"success",'data'=>$data,'media'=>$media,'serverURL'=>$serverURL],200);
        return CommonApp::webEncrypt($res);
    }

    /* Add materials and labels in the order */
    public static function update_materials_label(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $data = [];
        $data['updated_user_id']= $request->user_id ?? 0;
        $data['updated_staff_id']= $request->staff_id ?? 0;
        $data['order_id']= $request->order_id ?? 0;
        $data['print_type']= $request->print_type ?? Null;
        $data['print_size']= $request->print_size ?? Null;
        $data['print_no_colors']= $request->print_no_colors ?? Null;
        $data['print_vendor_id']= $request->print_vendor_id ?? 0;
        $data['main_label']= $request->main_label ?? Null;
        $data['main_label_vendor_id']= $request->main_label_vendor_id ?? 0;
        $data['washcare_label']= $request->washcare_label ?? Null;
        $data['washcare_label_vendor_id']= $request->washcare_label_vendor_id ?? 0;
        $data['barcode_label']= $request->barcode_label ?? Null;
        $data['barcode_label_vendor_id']= $request->barcode_label_vendor_id ?? 0;
        $data['hangtag']= $request->hangtag ?? Null;
        $data['hangtag_vendor_id']= $request->hangtag_vendor_id ?? 0;
        $data['trims_notifications']= $request->trims_notifications ?? Null;
        $data['polybag_size_thickness']= $request->polybag_size_thickness ?? Null;
        $data['polybag_material']= $request->polybag_material ?? Null;
        $data['polybag_print_details']= $request->polybag_print_details ?? Null;
        $data['polybag_vendor_id']= $request->polybag_vendor_id ?? 0;
        $data['carton_dimensions']= $request->carton_dimensions ?? Null;
        $data['carton_color']= $request->carton_color ?? Null;
        $data['carton_no_of_ply']= $request->carton_no_of_ply ?? Null;
        $data['carton_vendor_id']= $request->carton_vendor_id ?? 0;
        $data['carton_edge_finish']= $request->carton_edge_finish ?? Null;
        $data['carton_mark_details']= $request->carton_mark_details ?? Null;
        $data['carton_make_up']= $request->carton_make_up ?? Null;
        $data['air_freight']= $request->air_freight ?? Null;
        $data['flims_cd']= $request->flims_cd ?? Null;
        $data['picture_card']= $request->picture_card ?? Null;
        $data['inner_cardboard']= $request->inner_cardboard ?? Null;
        $data['shiping_size']= $request->shiping_size ?? Null;

        DB::beginTransaction();
        try{
            OrderMaterialsLabels::where('order_id',$request->order_id)->update($data);
        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Materials and Labels Updated Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function download_materials_label(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'order_id' => 'required',
            //'user_id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        App::setlocale($dateFormatAndLanguage['language']);

        $responses= OrderMaterialsLabels::get_order_materials_label($request);
        $vendors = InquiryLabelVendor::get_all_vendors_list_array($request);
       // $data['filter_type']=$request->filter_type??[];
        $data['responses']=$responses;
        $data['vendors']=$vendors;
        $data['user_info']=array();
        $data['dateFormat']=$dateFormatAndLanguage['dateFormat'];
        $data['serverURL'] = config('filesystems.disks.s3.url');
        $data['useLogo'] = $dateFormatAndLanguage['useLogo'];
        $data['userLogo'] = $dateFormatAndLanguage['userLogo'];
        //dd($data);
        if(count($responses)>0){
            view()->share("data",$data);
            $pdf = Pdf::loadView('OrderLabelPDF');
            $pdf->setPaper('A4', 'landscape');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            $pdf->setOption("enable_php", true);
            $filePath = public_path() . '/OrderPO';
            if (!file_exists($filePath)) {
                File::makeDirectory($filePath, 0777, true, true);
            }
            $path = public_path() . '/OrderPO/' .$request->order_id.'.pdf';
            $pdf->save($path);
           // return $pdf->download();

           //Measurement pdf merge
            $f=[$path];
            foreach ($responses as $m){
                if($m['media_type'] =="MeasurementSheet" && (stristr($m['orginalfilename'],'.pdf')) ){
                    $filepath = public_path()."/OrderPO/".$request->order_id."_".$m['orginalfilename'];
                    //$filepath = $data['serverURL'].$m['filepath'];
                    $f[]=$filepath;
                }
                if($m['media_type'] =="TechPack" && (stristr($m['orginalfilename'],'.pdf')) ){
                    $filepath = public_path()."/OrderPO/".$request->order_id."_".$m['orginalfilename'];
                    //$filepath = $data['serverURL'].$m['filepath'];
                    $f[]=$filepath;
                }
            }
            if(count($f)>1){
                try{
                    OrderMaterialsLabel::pdfmerge($request->order_id,$f);
                }catch(Exception $e){
                    Log::info($e->getMessage());
                }
            }
            $url = config('app.public_url')."OrderPO/".$request->order_id.".pdf";
            $res = json_encode(["status_code"=>200,'status'=>"success", "data"=>$url,"message"=>"success"],200);
            return CommonApp::webEncrypt($res);
        }

    }

    /* PDF Merge */
    public static function pdfmerge($orderId,$f) {
        $file = public_path() . '/OrderPO/' .$orderId.'.pdf';
       // try{
            $files = $f;
            $pdf = PdfMerger::init();
            foreach ($files as $file) {
                if(file_exists($file))
                    $pdf->addPDF($file, 'all','P');
            }
            $pdf->merge("","",$orderId,"Order");
            $pdf->save(public_path()."/OrderPO/".$orderId.".pdf",'file');

    }

    /* Add materials and labels in the order */
    public static function add_order_bom(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $data = [];
        $data['created_user_id']= $request->user_id ?? 0;
        $data['created_staff_id']= $request->staff_id ?? 0;
        $data['order_id']= $request->order_id ?? 0;
        $data['sewing_accessories']= $request->sewing_accessories ?json_encode($request->sewing_accessories): Null;
        $data['packing_accessories']= $request->packing_accessories ?json_encode($request->packing_accessories): Null;
        $data['miscellaneous']= $request->miscellaneous_accessories ?json_encode($request->miscellaneous_accessories): Null;
        $data['created_at']= date('Y-m-d H:i:s');

        DB::beginTransaction();
        try{
            $getBOM=OrderBOM::where("order_id",$request->order_id)->first();
            if(empty($getBOM)){
            OrderBOM::insert($data);
            }else{
                $approvalst=0;
                if($getBOM['is_approval']==1){
                    $approvalst=3;
                }
                $datav = [];
                $datav['created_user_id']= $request->user_id ?? 0;
                $datav['created_staff_id']= $request->staff_id ?? 0;
                $datav['order_id']= $request->order_id ?? 0;
                $datav['sewing_accessories']= $request->sewing_accessories ?json_encode($request->sewing_accessories): Null;
                $datav['packing_accessories']= $request->packing_accessories ?json_encode($request->packing_accessories): Null;
                $datav['miscellaneous']= $request->miscellaneous_accessories ?json_encode($request->miscellaneous_accessories): Null;
                $datav['updated_at']= date('Y-m-d H:i:s');
                if($approvalst>0){
                    $datav['is_approval']=$approvalst;
                }
                OrderBOM::where("order_id",$request->order_id)->update($datav);
            }
            /*Update Order Step Status*/
            $addOrderArr=[];
            $addOrderArr['step_level'] = '5';
            Order::where('id',$request->order_id)->update($addOrderArr);
        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Bill of Materials Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }
    public static function get_order_bom(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'order_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data= OrderBOM::get_materials_details($request);
        $media= OrderMedia::get_materials_media($request);
        $approvalBOM=OrderApprovalHistory::where('order_id',$request->order_id)->orderby("id","DESC")->first();
        $serverURL = config('filesystems.disks.s3.url');
        /*BOM Prepared BY */
        $preparedName=[];
        if(!empty($data)){
          if($data->created_staff_id>0){
            $getStaff=Staff::select("first_name","last_name")->where("id",$data->created_staff_id)->first();
            if($getStaff['last_name']!=''){
            $appName=$getStaff['first_name'].' '.$getStaff['last_name'];
            }else{
                $appName=$getStaff['first_name'];
            }
          }else{
            $getStaff=User::select("name")->where("id",$data->created_user_id)->first();

            $appName=$getStaff['name'];

          }
          $datetime= Carbon::createFromFormat('Y-m-d H:i:s', $data['created_at'])
          ->format('Y-m-d H:i:s');
          $preparedName['prepared_by']=$appName;
          $preparedName['prepared_date']=$datetime;
        }

        $res = json_encode(["status_code"=>200,'status'=>"success",'data'=>$data,'media'=>$media,'approvalBOM'=>$approvalBOM,'preparedBy'=>$preparedName,'serverURL'=>$serverURL],200);
        return CommonApp::webEncrypt($res);
    }
    /* Update materials and labels in the order */
    public static function update_order_bom(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $data = [];
        $data['updated_user_id']= $request->user_id ?? 0;
        $data['updated_staff_id']= $request->staff_id ?? 0;
        //$data['order_id']= $request->order_id ?? 0;
        $data['sewing_accessories']= $request->sewing_accessories ?json_encode($request->sewing_accessories): Null;
        $data['packing_accessories']= $request->packing_accessories ?json_encode($request->packing_accessories): Null;
        $data['miscellaneous']= $request->miscellaneous_accessories ?json_encode($request->miscellaneous_accessories): Null;

              /* Start Update Re-approval*/
              $oldData = OrderBOM::select('sewing_accessories','packing_accessories','miscellaneous','is_approval')->where('order_id',$request->order_id)->first();
              if(!empty($oldData)){
                  $sewing_acc_Data=$oldData['sewing_accessories']?json_decode($oldData['sewing_accessories']):'';
                  $packing_acc_Data=$oldData['packing_accessories']?json_decode($oldData['packing_accessories']):'';
                  $miscellaneous_Data=$oldData['miscellaneous']?json_decode($oldData['miscellaneous']):'';
                  // $sewa=CommonApp::compareArrayFieldValue($sewing_acc_Data,$request->sewing_accessories);
                //   $packa=CommonApp::compareArrayFieldValue($packing_acc_Data,$request->packing_accessories);
                //   $misca=CommonApp::compareArrayFieldValue($miscellaneous_Data,$request->miscellaneous_accessories);

                try{
                $sewingareEqual = \Illuminate\Support\Arr::sortRecursive($sewing_acc_Data) == \Illuminate\Support\Arr::sortRecursive($request->sewing_accessories);
                $packingareEqual = \Illuminate\Support\Arr::sortRecursive($packing_acc_Data) == \Illuminate\Support\Arr::sortRecursive($request->packing_accessories);
                $miscellaneousareEqual = \Illuminate\Support\Arr::sortRecursive($miscellaneous_Data) == \Illuminate\Support\Arr::sortRecursive($request->miscellaneous_accessories);
               $valary=0;
                if (!$sewingareEqual) {
                    $valary=1;
                }
                if (!$packingareEqual) {
                    $valary=1;
                }
                if (!$miscellaneousareEqual) {
                    $valary=1;
                }
                if($valary==1 &&  $oldData['is_approval']==1){
                    $data['is_approval']=3;
                }

            }catch(Exception $e){
             // dd($e);
            }


              }
               /* End Update Re-approval*/


        DB::beginTransaction();
        try{

           $update = OrderBOM::where('order_id',$request->order_id)->update($data);

           if(!$update){
            $data['created_user_id']= $request->user_id ?? 0;
            $data['created_staff_id']= $request->staff_id ?? 0;
            $data['order_id']= $request->order_id ?? 0;
            $data['created_at']= date('Y-m-d H:i:s');
            OrderBOM::insert($data);
           }else{

           }

           /* Order Log creation starts*/
           $after_values = $data;
           $logArry = array();
           $logArry['order_id'] =$request->order_id;
           $logArry['company_id'] = $request->company_id;
           $logArry['workspace_id'] = $request->workspace_id;
           $logArry['staff_id'] =$request->staff_id ?? 0;
           $logArry['user_id'] = $request->user_id ?? 0;
           $logArry['action'] = 'Edit';
           $logArry['before_values'] = json_encode($request->before_values) ?? '';
           $logArry['after_values'] = json_encode($after_values) ?? '';
           Orderlog::insert($logArry);
           /* Order Log creation end*/


        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Bill of Materials Updated Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function download_order_bom(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'order_id' => 'required',
           // 'user_id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'image' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        App::setlocale($dateFormatAndLanguage['language']);

        $responses= OrderBOM::get_order_materials_label($request);
        $vendors = InquiryLabelVendor::get_all_vendors_list_array($request);
        $media = OrderMedia::get_materials_media($request);
       // $data['filter_type']=$request->filter_type??[];
        $data['responses']=$responses;
        $data['media']=$media;
        $data['vendors']=$vendors;
        $data['user_info']=array();
        $data['dateFormat']=$dateFormatAndLanguage['dateFormat'];
        $data['serverURL'] = config('filesystems.disks.s3.url');
        $data['useLogo'] = $dateFormatAndLanguage['useLogo'];
        //$data['userLogo'] = $dateFormatAndLanguage['userLogo'];
        $data['userLogo'] =$dateFormatAndLanguage['useLogo'] !=0 ?  Storage::disk('s3')->temporaryUrl($dateFormatAndLanguage['userLogo'], '+15 minutes') : "";
        $data['img_req']=$request->image??0;
        if(count($responses)>0){
            view()->share("data",$data);
            $pdf = Pdf::loadView('OrderBOMPDF');
            $pdf->setPaper('A4', 'landscape');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            $pdf->setOption("enable_php", true);
            // $filePath = public_path() . '/OrderPO';
            // if (!file_exists($filePath)) {
            //     File::makeDirectory($filePath, 0777, true, true);
            // }
            //$path = public_path() . '/OrderPO/' .$request->order_id.'.pdf';
            //$pdf->save($path);
            return $pdf->download();
        }
    }

    /* Download a file*/
    public static function download_files(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator =Validator::make((array)$request,[
            'filepath' =>'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'orginalfilename' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }

        $fullpath =  Storage::disk('s3')->temporaryUrl($request->filepath, '+5 minutes');
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . $request->orginalfilename);
        header("Content-Type: application/octet-stream" );
        header('Access-Control-Allow-Origin:*');

        return readfile($fullpath);
    }

    public static function download_ms_files(Request $request){
        $path = urldecode($request->query('filepath'));

        $path =  Storage::disk('s3')->temporaryUrl($path, '+5 minutes');

        return redirect($path);
    }

    /* Create new Unit */
    public static function createOrderUnit(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id'=>'required',
            'staff_id' => 'required',
            'bom_unit'=>'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $OrderCategoryArr = [];
        $OrderCategoryArr['name'] = ucfirst(strtolower($request->name));
        $OrderCategoryArr['company_id'] = $request->company_id;
        $OrderCategoryArr['workspace_id'] = $request->workspace_id;
        $OrderCategoryArr['user_id'] = $request->user_id;
        $OrderCategoryArr['staff_id'] =$request->staff_id;
        $OrderCategoryArr['is_default'] ='1';
        $OrderCategoryArr['status'] ='1';
        $OrderCategoryArr['bom_unit'] =$request->bom_unit;
        $OrderCategoryArr['created_at'] = date('Y-m-d H:i:s');
        $OrderCategoryArr['updated_at'] = date('Y-m-d H:i:s');
        OrderUnits::insert($OrderCategoryArr);

        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Unit added Succesfully"]);
        return CommonApp::webEncrypt($res);
    }

/*Get Factory Dashboard BOM Details*/
public function get_dashboard_bom(Request $request){
    $request= CommonApp::webDecrypt($request->getContent());
    $validator = Validator::make((array)$request, [
        'company_id' => 'required',
        'workspace_id' => 'required',
        //'order_id' => 'required'
    ]);
    if ($validator->fails()){
        $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
        return CommonApp::webEncrypt($res);
    }
    $whereCondition=[
        ['orders.company_id','=',$request->company_id],
        ['orders.workspace_id','=',$request->workspace_id],
        ['orders.status','=',"1"],
        //['order_contacts.status','=',1]
    ];
    if($request->staff_id>0){
        $staffRoleHasPermission = Staff::select('id','role_id')->where('id',$request->staff_id)->first();

        $whereCondition1[]=['company_id','=',$request->company_id];
        $whereCondition1[]=['workspace_id','=',$request->workspace_id];
        $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
        $whereCondition1[]=['permission_id','=','19'];
        $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
        if(empty($isPermissionGiven)){
            $whereCondition[]=['order_contacts.staff_id',"=",$request->staff_id];
           }

    }

    if(isset($request->order_id) && $request->order_id>0){
        $whereCondition[]=['orders.id','=',$request->order_id];
    }
    $data=OrderContacts::select("orders.id","orders.order_no","orders.style_no","order_bom.sewing_accessories","order_bom.packing_accessories","order_bom.miscellaneous","order_bom.created_staff_id","order_bom.created_user_id","order_bom.created_at")
    ->leftjoin("orders","orders.id","order_contacts.order_id")
    ->leftjoin("order_bom","orders.id","order_bom.order_id")
    ->where($whereCondition)
    ->orderby("order_bom.id","DESC")
    ->first();

    if(isset($request->order_id) && $request->order_id>0){
        $order_id=$request->order_id;
    }
    else if(!empty($data)){
        $order_id= $data->id;
    }else{
        $order_id=0;
    }
      /*BOM Prepared BY */
      $preparedName=[];

      if(!empty($data)){
        if($data->created_staff_id>0){
          $getStaff=Staff::select("first_name","last_name")->where("id",$data->created_staff_id)->first();
            if($getStaff['last_name']!=''){
          $appName=$getStaff['first_name'].' '.$getStaff['last_name'];
          }else{
              $appName=$getStaff['first_name'];
          }
        }else{
          $getStaff=User::select("name")->where("id",$data->created_user_id)->first();

          $appName=$getStaff['name'];

        }
        $datetime= Carbon::createFromFormat('Y-m-d H:i:s', $data['created_at'])
        ->format('Y-m-d,H:i A');
        $preparedName['prepared_by']=$appName;
        $preparedName['prepared_date']=$datetime;
      }
    $approvalBOM=OrderApprovalHistory::select("order_id","approval_date","approval_type","approved_by","comments",DB::raw("DATE_FORMAT(approval_date, '%Y-%m-%d,%h:%i %p') as formatted_date"))->where('order_id',$order_id)->where("workspace_id",$request->workspace_id)->where("company_id",$request->company_id)->orderby("id","ASC")->get();
      $res = json_encode(["status_code"=>200,'status'=>"success",'data'=>$data,'approvalBOM'=>$approvalBOM,'preparedBy'=>$preparedName,],200);
    return CommonApp::webEncrypt($res);
}

}
