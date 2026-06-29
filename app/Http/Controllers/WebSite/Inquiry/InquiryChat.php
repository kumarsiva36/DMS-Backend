<?php

namespace App\Http\Controllers\WebSite\Inquiry;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Common\Uploads;
use App\Models\Inquiries;
use App\Models\InquiryLabel;
use App\Models\InquiryLabelCreate;
use App\Models\InquiryLabelVendor;
use App\Models\InquiryPO;
use App\Models\InquiryPOAdditional;
use App\Models\OrderUnits;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

ini_set('memory_limit',-1);
class InquiryChat extends Controller
{
    public static function label_file_upload(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'type' => 'required',
            'referenceId' => 'required',
            'inquiry_id'=> 'required',
            'user_id' => 'required',
            'user_type' => 'required',
            'po_id' => 'required',
            'status' => 'required',
            'publish_status' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        /**Check the label contents in chat log */
        // $check = InquiryLabel::where('inquiry_id',$request->inquiry_id)->count();
        // if($check ==0){
        //     $responses= Inquiries::get_inquiry_label($request);
        //     if(!empty($responses)){
        //         $ptxt=$pimg=$mltxt=$mlimg=$wctxt=$wcimg=$httxt=$htimg=$bctxt=$bcimg=$pbtxt=$pbimg=$cbtxt=$cbimg='';
        //         if ($responses[0]['print_type']!='')
        //                 $ptxt.= "Print Type :".$responses[0]['print_type'];
        //         if ($responses[0]['print_size']!='')
        //             $ptxt.= "<br>"."Print Size :".$responses[0]['print_size'].'(in cm)';
        //         if ($responses[0]['print_no_of_colors']!='')
        //             $ptxt.= "<br>"."No of colors :".$responses[0]['print_no_of_colors'];
        //         if ($responses[0]['main_lable']!='')
        //             $mltxt.= "Main Label :".$responses[0]['main_lable'];
        //         if ($responses[0]['washcare_lable']!='')
        //             $wctxt.= "Wash Care Label :".$responses[0]['washcare_lable'];
        //         if ($responses[0]['hangtag_lable']!='')
        //             $httxt.= "Hangtag Label :".$responses[0]['hangtag_lable'];
        //         if ($responses[0]['barcode_lable']!='')
        //             $bctxt.= "BarCode Label :".$responses[0]['barcode_lable'];
        //         if ($responses[0]['poly_bag_size']!='')
        //             $pbtxt.= "Polybag Size :".$responses[0]['poly_bag_size'].'(in cm)';
        //         if ($responses[0]['poly_bag_material']!='')
        //             $pbtxt.= "<br>"."Polybag material :".$responses[0]['poly_bag_material'];
        //         if ($responses[0]['poly_bag_print']!='')
        //             $pbtxt.= "<br>"."Polybag Print details :".$responses[0]['poly_bag_print'];
        //         if ($responses[0]['carton_bag_dimensions']!='')
        //             $cbtxt.= "Carton Box Dimensions :".$responses[0]['carton_bag_dimensions'].'(in cm)';
        //         if ($responses[0]['carton_color']!='')
        //             $cbtxt.= "<br>"."Carton Color :".$responses[0]['carton_color'];
        //         if ($responses[0]['carton_material']!='')
        //             $cbtxt.= "<br>"."No of Ply :".$responses[0]['carton_material'];
        //         if ($responses[0]['carton_edge_finish']!='')
        //             $cbtxt.= "<br>"."Carton Edge Finish :".$responses[0]['carton_edge_finish'];
        //         if ($responses[0]['carton_mark']!='')
        //             $cbtxt.= "<br>"."Carton Mark Details :".$responses[0]['carton_mark'];

        //         foreach($responses as $res){
        //             if($res['media_type']=="PrintImage"){
        //                 $pimg.="||".$res['filepath'];
        //             }
        //             if($res['media_type']=="MainLabel"){
        //                 $mlimg.="||".$res['filepath'];
        //             }
        //             if($res['media_type']=="WashCareLabel"){
        //                 $wcimg.="||".$res['filepath'];
        //             }
        //             if($res['media_type']=="Hangtag"){
        //                 $htimg.="||".$res['filepath'];
        //             }
        //             if($res['media_type']=="BarcodeStickers"){
        //                 $bcimg.="||".$res['filepath'];
        //             }
        //             if($res['media_type']=="Polybag"){
        //                 $pbimg.="||".$res['filepath'];
        //             }
        //             if($res['media_type']=="Carton"){
        //                 $cbimg.="||".$res['filepath'];
        //             }
        //         }
        //         InquiryLabel::save_chat($request,$ptxt,$pimg,$mltxt,$mlimg,$wctxt,$wcimg,$httxt,$htimg,$bctxt,$bcimg,$pbtxt,$pbimg,$cbtxt,$cbimg);
        //     }
        // }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $companyFolder = $companyDetails->aws_s3_path;
        //$free_storage = ($companyDetails->max_storage_size - ($companyDetails->storage_used + (int)config('constant.plan_storage_free_mb')))*1024*1024;
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
            $filepath = $companyFolder.'/Labels/'.$request->referenceId.'/'.$fileName;
            Uploads::orderAddtionalSpec($file,$filepath);
            $filedata['orginalfilename']=$file->getClientOriginalName();
            $filedata['content']=$filepath;
            $filedata['filesize']=$file->getSize();
            $storageToBeAdded += $file->getSize();
            $filedata['reference_id']=$request->referenceId;
            $filedata['inquiry_id'] = $request->inquiry_id ?? 0;
            $filedata['user_id'] = $request->user_id ?? 0;
            $filedata['user_type'] = $request->user_type ?? 'user';
            $filedata['created_at'] = date('Y-m-d H:i:s');
            $filedata['content_type']='image';
            $filedata['type']=$request->type;
            $filedata['po_id']=$request->po_id;
            $filedata['status']=$request->status;
            $filedata['publish_status']=$request->publish_status;
            $filedata['company_id']=$request->company_id ?? 0;
            $filedata['workspace_id']=$request->workspace_id ?? 0;
            InquiryLabel::insert($filedata);
            $companyDetails->storage_used = ($storageUsed + (int)$storageToBeAdded)/(1024*1024);
            $companyDetails->save();
            $res['files'] = InquiryLabel::getFiles($request->referenceId,$request->type);
            $res['serverURL'] = config('filesystems.disks.s3.url');

            return response()->json(["status_code"=>200,'status'=>"success","message"=>"Files Added Successfully","files"=>$res],200);
        }else{
            $res['files']=InquiryLabel::getFiles($request->referenceId,$request->type);
            $res['serverURL'] = config('filesystems.disks.s3.url');
            return response()->json(["status_code"=>200,'status'=>"failure","message"=>"Something went wrong","files"=>$res],200);
        }
    }

    public static function add_label_content(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'type' => 'required',
            'referenceId' => 'required',
            'inquiry_id'=> 'required',
            'user_id' => 'required',
            'user_type' => 'required',
            'content' => 'required',
            'po_id' => 'required',
            'status' => 'required',
            'publish_status' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        /**Check the label contents in chat log */
        // $check = InquiryLabel::where('inquiry_id',$request->inquiry_id)->count();
        // if($check ==0){
        //     $responses= Inquiries::get_inquiry_label($request);
        //     if(!empty($responses)){
        //         $ptxt=$pimg=$mltxt=$mlimg=$wctxt=$wcimg=$httxt=$htimg=$bctxt=$bcimg=$pbtxt=$pbimg=$cbtxt=$cbimg='';
        //         if ($responses[0]['print_type']!='')
        //                 $ptxt.= "Print Type :".$responses[0]['print_type'];
        //         if ($responses[0]['print_size']!='')
        //             $ptxt.= "<br>"."Print Size :".$responses[0]['print_size'].'(in cm)';
        //         if ($responses[0]['print_no_of_colors']!='')
        //             $ptxt.= "<br>"."No of colors :".$responses[0]['print_no_of_colors'];
        //         if ($responses[0]['main_lable']!='')
        //             $mltxt.= "Main Label :".$responses[0]['main_lable'];
        //         if ($responses[0]['washcare_lable']!='')
        //             $wctxt.= "Wash Care Label :".$responses[0]['washcare_lable'];
        //         if ($responses[0]['hangtag_lable']!='')
        //             $httxt.= "Hangtag Label :".$responses[0]['hangtag_lable'];
        //         if ($responses[0]['barcode_lable']!='')
        //             $bctxt.= "BarCode Label :".$responses[0]['barcode_lable'];
        //         if ($responses[0]['poly_bag_size']!='')
        //             $pbtxt.= "Polybag Size :".$responses[0]['poly_bag_size'].'(in cm)';
        //         if ($responses[0]['poly_bag_material']!='')
        //             $pbtxt.= "<br>"."Polybag material :".$responses[0]['poly_bag_material'];
        //         if ($responses[0]['poly_bag_print']!='')
        //             $pbtxt.= "<br>"."Polybag Print details :".$responses[0]['poly_bag_print'];
        //         if ($responses[0]['carton_bag_dimensions']!='')
        //             $cbtxt.= "Carton Box Dimensions :".$responses[0]['carton_bag_dimensions'].'(in cm)';
        //         if ($responses[0]['carton_color']!='')
        //             $cbtxt.= "<br>"."Carton Color :".$responses[0]['carton_color'];
        //         if ($responses[0]['carton_material']!='')
        //             $cbtxt.= "<br>"."No of Ply :".$responses[0]['carton_material'];
        //         if ($responses[0]['carton_edge_finish']!='')
        //             $cbtxt.= "<br>"."Carton Edge Finish :".$responses[0]['carton_edge_finish'];
        //         if ($responses[0]['carton_mark']!='')
        //             $cbtxt.= "<br>"."Carton Mark Details :".$responses[0]['carton_mark'];

        //         foreach($responses as $res){
        //             if($res['media_type']=="PrintImage"){
        //                 $pimg.="||".$res['filepath'];
        //             }
        //             if($res['media_type']=="MainLabel"){
        //                 $mlimg.="||".$res['filepath'];
        //             }
        //             if($res['media_type']=="WashCareLabel"){
        //                 $wcimg.="||".$res['filepath'];
        //             }
        //             if($res['media_type']=="Hangtag"){
        //                 $htimg.="||".$res['filepath'];
        //             }
        //             if($res['media_type']=="BarcodeStickers"){
        //                 $bcimg.="||".$res['filepath'];
        //             }
        //             if($res['media_type']=="Polybag"){
        //                 $pbimg.="||".$res['filepath'];
        //             }
        //             if($res['media_type']=="Carton"){
        //                 $cbimg.="||".$res['filepath'];
        //             }
        //         }
        //         InquiryLabel::save_chat($request,$ptxt,$pimg,$mltxt,$mlimg,$wctxt,$wcimg,$httxt,$htimg,$bctxt,$bcimg,$pbtxt,$pbimg,$cbtxt,$cbimg);
        //     }
        // }
        $filedata = [];
        $filedata['content']=$request->content ?? '';
        $filedata['reference_id']=$request->referenceId;
        $filedata['inquiry_id'] = $request->inquiry_id ?? 0;
        $filedata['user_id'] = $request->user_id ?? 0;
        $filedata['user_type'] = $request->user_type ?? 'user';
        $filedata['created_at'] = date('Y-m-d H:i:s');
        $filedata['content_type']='text';
        $filedata['type']=$request->type;
        $filedata['po_id']=$request->po_id;
        $filedata['status']=$request->status;
        $filedata['publish_status']=$request->publish_status;
        InquiryLabel::insert($filedata);
        return response()->json(["status_code"=>200,'status'=>"success","message"=>"Data Added Successfully"],200);
    }

    public static function get_inquiry_po_chat(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
            'user_id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $po_add_labels = [];
        /**Check the label contents in chat log */
        $check = InquiryLabel::where('po_id',$request->inquiry_id)->count();
        if($check ==0){
            $responses= InquiryPO::get_inquiry_label($request);
            //dd($responses);
            if(!empty($responses)){
                $ptxt=$pimg=$mltxt=$mlimg=$wctxt=$wcimg=$httxt=$htimg=$bctxt=$bcimg=$pbtxt=$pbimg=$cbtxt=$cbimg='';
                $inquiry_id=$responses[0]['inquiry_id'];
                if ($responses[0]['print_type']!='')
                        $ptxt.= "Print Type : ".$responses[0]['print_type'];
                if ($responses[0]['print_size']!='')
                    $ptxt.= "<br>"."Print Size : ".$responses[0]['print_size'].'(in cm)';
                if ($responses[0]['print_no_of_colors']!='')
                    $ptxt.= "<br>"."No of Colors : ".$responses[0]['print_no_of_colors'];
                if ($responses[0]['main_lable']!='')
                    $mltxt.= "Main Label :".$responses[0]['main_lable'];
                if ($responses[0]['washcare_lable']!='')
                    $wctxt.= "Wash Care Label : ".$responses[0]['washcare_lable'];
                if ($responses[0]['hangtag_lable']!='')
                    $httxt.= "Hangtag Label : ".$responses[0]['hangtag_lable'];
                if ($responses[0]['barcode_lable']!='')
                    $bctxt.= "BarCode Label : ".$responses[0]['barcode_lable'];
                if ($responses[0]['poly_bag_size']!='')
                    $pbtxt.= "Polybag Size : ".$responses[0]['poly_bag_size'].'(in cm)';
                if ($responses[0]['poly_bag_material']!='')
                    $pbtxt.= "<br>"."Polybag Material : ".$responses[0]['poly_bag_material'];
                if ($responses[0]['poly_bag_print']!='')
                    $pbtxt.= "<br>"."Polybag Print Details : ".$responses[0]['poly_bag_print'];
                if ($responses[0]['carton_bag_dimensions']!='')
                    $cbtxt.= "Carton Box Dimensions : ".$responses[0]['carton_bag_dimensions'].'(in cm)';
                if ($responses[0]['carton_color']!='')
                    $cbtxt.= "<br>"."Carton Color : ".$responses[0]['carton_color'];
                if ($responses[0]['carton_material']!='')
                    $cbtxt.= "<br>"."No of Ply : ".$responses[0]['carton_material'];
                if ($responses[0]['carton_edge_finish']!='')
                    $cbtxt.= "<br>"."Carton Edge Finish : ".$responses[0]['carton_edge_finish'];
                if ($responses[0]['carton_mark']!='')
                    $cbtxt.= "<br>"."Carton Mark Details : ".$responses[0]['carton_mark'];

                foreach($responses as $res){
                    if($res['media_type']=="PrintImage"){
                        $pimg.="||".$res['filepath'];
                    }
                    if($res['media_type']=="MainLabel"){
                        $mlimg.="||".$res['filepath'];
                    }
                    if($res['media_type']=="WashCareLabel"){
                        $wcimg.="||".$res['filepath'];
                    }
                    if($res['media_type']=="Hangtag"){
                        $htimg.="||".$res['filepath'];
                    }
                    if($res['media_type']=="BarcodeStickers"){
                        $bcimg.="||".$res['filepath'];
                    }
                    if($res['media_type']=="Polybag"){
                        $pbimg.="||".$res['filepath'];
                    }
                    if($res['media_type']=="Carton"){
                        $cbimg.="||".$res['filepath'];
                    }
                }
                $po_add_arr = [];
                $po_add_info = InquiryPOAdditional::where('po_id',$request->inquiry_id)->get();
                $s=0;
                foreach($po_add_info as $add){
                    $po_add_labels[]=$add->label;
                    $po_add_arr[$s]['type']=str_replace(' ','',$add->label);
                    $po_add_arr[$s]['content']=$add->label_description;
                    $po_add_arr[$s]['content_type']='text';
                    $po_add_arr[$s]['media_type']=$add->media_type;
                    foreach($responses as $res){
                        if($res['media_type']=='trims_additional_'.$add->media_type){
                            $s++;
                            $po_add_arr[$s]['type']=str_replace(' ','',$add->label);
                            $po_add_arr[$s]['content']="||".$res['filepath'];
                            $po_add_arr[$s]['content_type']='image';
                        }
                    }
                    $s++;
                }
                //dd($po_add_arr);
                InquiryLabel::save_chat($request,$ptxt,$pimg,$mltxt,$mlimg,$wctxt,$wcimg,$httxt,$htimg,$bctxt,$bcimg,$pbtxt,$pbimg,$cbtxt,$cbimg,$inquiry_id,$po_add_arr);
            }
        }

        $po_add_info = InquiryPOAdditional::where('po_id',$request->inquiry_id)->get();
        foreach($po_add_info as $add){
            $po_add_labels[]=$add->label;
        }

        $responses= InquiryLabel::get_inquiry_label_chat($request);
        $serverURL = config('filesystems.disks.s3.url');
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$responses,"trims_additional"=>$po_add_labels,"serverURL"=>$serverURL],200);
        //return $res;
        return CommonApp::webEncrypt($res);
    }

    public static function download_po_inquiry_label(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
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

        /*Inquriy Label creation fetch and create */
        $user_info = InquiryLabelCreate::get_label_create_info($request);

        $responses= InquiryLabel::get_inquiry_label_chat($request);
        $trims_additional= InquiryPOAdditional::where('po_id',$request->inquiry_id)->select('label')->get();
        $data['filter_type']=$request->filter_type??[];
        $data['responses']=$responses;
        $data['trims_additional']=$trims_additional;
        $data['user_info']=$user_info;
        $data['dateFormat']=$dateFormatAndLanguage['dateFormat'];
        $data['serverURL'] = ''; // config('filesystems.disks.s3.url');
        $data['useLogo'] = $dateFormatAndLanguage['useLogo'];
        //$data['userLogo'] = $dateFormatAndLanguage['userLogo'];
        $data['userLogo'] =$dateFormatAndLanguage['useLogo'] !=0 ?  Storage::disk('s3')->temporaryUrl($dateFormatAndLanguage['userLogo'], '+15 minutes') : "";
        //dd($data);
        if(count($responses)>0){
            view()->share("data",$data);
            $pdf = Pdf::loadView('InquiryPoLabelPDF');
            $pdf->setPaper('A4', 'landscape');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            return $pdf->download();
            // $path = public_path() . '/Inquiry/label.pdf';
            // $pdf->save($path);
        }
    }

    public static function label_file_delete(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'media_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $res = InquiryLabel::deleteInquiryMedia($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","meassage"=>"File Deleted Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_label_inquiry_ids(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'user_type' => 'required',
            'user_id'=>'required',
            'company_id'=>'required',
            'workspace_id'=>'required',
            'login_type' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $inquires =InquiryLabel::get_label_inquiry_ids($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquires],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_label_content(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'referenceId' => 'required',
            'inquiry_id'=> 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $text_content =InquiryLabel::get_label_text_content($request);
        $image_content =InquiryLabel::get_label_image_content($request);
        $content['text'] = $text_content;
        $content['files']=$image_content;
        $content['serverURL'] = config('filesystems.disks.s3.url');
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$content],200);
        return CommonApp::webEncrypt($res);
    }

    public static function edit_label_content(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'type' => 'required',
            'referenceId' => 'required',
            'inquiry_id'=> 'required',
            'user_id' => 'required',
            'user_type' => 'required',
            'content' => 'required',
            'po_id' => 'required',
            'status' => 'required',
            'publish_status' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }

        $filedata = [];
        $filedata['content']=$request->content ?? '';
        $filedata['updated_user_id'] = $request->user_id ?? 0;
        $filedata['updated_user_type'] = $request->user_type ?? 'user';
        $filedata['updated_at'] = date('Y-m-d H:i:s');
        $filedata['content_type']='text';
        $filedata['po_id']=$request->po_id;
        $filedata['status']=$request->status;
        $filedata['publish_status']=$request->publish_status;
        $filedata1['publish_status']=$request->publish_status;
        $count = InquiryLabel::where('inquiry_id',$request->inquiry_id)->where('reference_id',$request->referenceId)->where('content_type','text')->count();
        if($count>0)
            InquiryLabel::where('inquiry_id',$request->inquiry_id)->where('reference_id',$request->referenceId)->where('content_type','text')->update($filedata);
        else{
            $filedata['reference_id']=$request->referenceId;
            $filedata['inquiry_id'] = $request->inquiry_id ?? 0;
            $filedata['user_id'] = $request->user_id ?? 0;
            $filedata['user_type'] = $request->user_type ? strtolower($request->user_type) : 'user';
            $filedata['created_at'] = date('Y-m-d H:i:s');
            $filedata['type']=$request->type;
            InquiryLabel::insert($filedata);
        }
        InquiryLabel::where('inquiry_id',$request->inquiry_id)->where('reference_id',$request->referenceId)->update($filedata1);
        return response()->json(["status_code"=>200,'status'=>"success","message"=>"Data Updated Successfully"],200);
    }

    public static function label_vendor_list(Request $request){
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
        $data = InquiryLabelVendor::get_vendors_list($request);
        $vendor_id = $request->vendor_id ?? 0;
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data,"vendor_id"=>$vendor_id],200);
        return CommonApp::webEncrypt($res);
    }

    public static function add_label_vendor(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'vendor_name' => 'required',
            'office_address' => 'required',
            //'factory_address'=> 'required',
            'user_id' => 'required',
            'user_type' => 'required',
            //'category_ids'=> 'required|array',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $filedata = [];
        $filedata['company_id']=$request->company_id ?? 0;
        $filedata['workspace_id']=$request->workspace_id ?? 0;
        $filedata['vendor_name'] = $request->vendor_name ?? NULL;
        $filedata['created_user_id'] = $request->user_id ?? 0;
        $filedata['created_user_type'] = $request->user_type ? strtolower($request->user_type) : 'user';
        $filedata['created_at'] = date('Y-m-d H:i:s');
        $filedata['website']=$request->website ?? '';
        $filedata['office_address']=$request->office_address ?? NULL;
        $filedata['factory_address']=$request->factory_address ?? NULL;
        $filedata['category_ids']=isset($request->category_ids)? (is_array($request->category_ids)?implode(",",$request->category_ids):NULL ): NULL;
        $filedata['contact_details']=isset($request->contact_details)? json_encode($request->contact_details) : NULL;
       // echo $filedata['contact_details']; exit;
        InquiryLabelVendor::insert($filedata);
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Vendor Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_label_vendor(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = InquiryLabelVendor::where('id',$request->id)->get();
        if(count($data)>0 && isset($data[0]->contact_details)){
            $data[0]->contact_details = json_decode($data[0]->contact_details); //exit;
        }
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    public static function edit_label_vendor(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'id' => 'required',
            'vendor_name' => 'required',
            'office_address' => 'required',
           // 'category_ids'=> 'required|array',
            'user_id' => 'required',
            'user_type' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $filedata = [];
        $filedata['vendor_name'] = $request->vendor_name ?? NULL;
        $filedata['updated_user_id'] = $request->user_id ?? 0;
        $filedata['updated_user_type'] = $request->user_type ? strtolower($request->user_type) : 'user';
        $filedata['website']=$request->website ?? '';
        $filedata['office_address']=$request->office_address ?? NULL;
        $filedata['factory_address']=$request->factory_address ?? NULL;
        $filedata['category_ids']=isset($request->category_ids)? (is_array($request->category_ids)?implode(",",$request->category_ids):NULL ): NULL;
        $filedata['contact_details']=isset($request->contact_details)? json_encode($request->contact_details) : NULL;
        InquiryLabelVendor::where('id',$request->id)->update($filedata);
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Vendor Updated Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function assign_po_vendor(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'vendor_id'   => 'required',
            'category_id' => 'required',
            'po_id'       => 'required',
            'user_id'     => 'required',
            'user_type'   => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        InquiryLabel::assign_po_vendor($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Vendor Updated Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function all_vendor_list(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $type = $request->units ?? 'bom_units';
        $data = InquiryLabelVendor::get_all_vendors_list($request);
        $units = OrderUnits::get_order_units($request,$type);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data,"units"=>$units],200);
        return CommonApp::webEncrypt($res);
    }
}
