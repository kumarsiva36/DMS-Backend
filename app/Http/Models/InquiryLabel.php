<?php

namespace App\Models;

use App\Common\CommonApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Inquiries;
use Illuminate\Support\Facades\DB;

class InquiryLabel extends Model
{
    use HasFactory;

    protected $table = 'inquiry_material_label';

    public static function save_chat($request,$ptxt,$pimg,$mltxt,$mlimg,$wctxt,$wcimg,$httxt,$htimg,$bctxt,$bcimg,$pbtxt,$pbimg,$cbtxt,$cbimg,$inquiry_id,$po_add_arr){
        $data = array();
        $data['po_id'] = $request->inquiry_id ?? 0;
        $data['user_id'] = $request->user_id ?? 0;
        $data['user_type'] = $request->user_type ?? 'user';
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['inquiry_id']=$inquiry_id??0;
        if($ptxt!='' || $pimg!=''){
            $data['reference_id'] = rand(11111111,999999999);
            $data['type']='PrintArtWork';
            if($ptxt!=''){
                $data['content']=$ptxt;
                $data['content_type']='text';
                InquiryLabel::insert($data);
            }
            if($pimg!=''){
                $pimgs = explode('||',$pimg);
                foreach($pimgs as $img){
                    if($img!=""){
                        $data['content']=$img;
                        $data['content_type']='image';
                        InquiryLabel::insert($data);
                    }
                }
            }
        }
        if($mltxt!='' || $mlimg!=''){
            $data['reference_id'] = rand(11111111,999999999);
            $data['type']='MainLabel';
            if($mltxt!=''){
                $data['content']=$mltxt;
                $data['content_type']='text';
                InquiryLabel::insert($data);
            }
            if($mlimg!=''){
                $mlimgs = explode('||',$mlimg);
                foreach($mlimgs as $img){
                    if($img!=""){
                        $data['content']=$img;
                        $data['content_type']='image';
                        InquiryLabel::insert($data);
                    }
                }
            }
        }
        if($wctxt!='' || $wcimg!=''){
            $data['reference_id'] = rand(11111111,999999999);
            $data['type']='WashCare';
            if($wctxt!=''){
                $data['content']=$wctxt;
                $data['content_type']='text';
                InquiryLabel::insert($data);
            }
            if($wcimg!=''){
                $wcimgs = explode('||',$wcimg);
                foreach($wcimgs as $img){
                    if($img!=""){
                        $data['content']=$img;
                        $data['content_type']='image';
                        InquiryLabel::insert($data);
                    }
                }
            }
        }
        if($httxt!='' || $htimg!=''){
            $data['reference_id'] = rand(11111111,999999999);
            $data['type']='HangTag';
            if($httxt!=''){
                $data['content']=$httxt;
                $data['content_type']='text';
                InquiryLabel::insert($data);
            }
            if($htimg!=''){
                $htimgs = explode('||',$htimg);
                foreach($htimgs as $img){
                    if($img!=""){
                        $data['content']=$img;
                        $data['content_type']='image';
                        InquiryLabel::insert($data);
                    }
                }
            }
        }
        if($bctxt!='' || $bcimg!=''){
            $data['reference_id'] = rand(11111111,999999999);
            $data['type']='BarCode';
            if($bctxt!=''){
                $data['content']=$bctxt;
                $data['content_type']='text';
                InquiryLabel::insert($data);
            }
            if($bcimg!=''){
                $bcimgs = explode('||',$bcimg);
                foreach($bcimgs as $img){
                    if($img!=""){
                        $data['content']=$img;
                        $data['content_type']='image';
                        InquiryLabel::insert($data);
                    }
                }
            }
        }
        if($pbtxt!='' || $pbimg!=''){
            $data['reference_id'] = rand(11111111,999999999);
            $data['type']='PolyBag';
            if($pbtxt!=''){
                $data['content']=$pbtxt;
                $data['content_type']='text';
                InquiryLabel::insert($data);
            }
            if($pbimg!=''){
                $pbimgs = explode('||',$pbimg);
                foreach($pbimgs as $img){
                    if($img!=""){
                        $data['content']=$img;
                        $data['content_type']='image';
                        InquiryLabel::insert($data);
                    }
                }
            }
        }
        if($cbtxt!='' || $cbimg!=''){
            $data['reference_id'] = rand(11111111,999999999);
            $data['type']='Carton';
            if($cbtxt!=''){
                $data['content']=$cbtxt;
                $data['content_type']='text';
                InquiryLabel::insert($data);
            }
            if($cbimg!=''){
                $cbimgs = explode('||',$cbimg);
                foreach($cbimgs as $img){
                    if($img!=""){
                        $data['content']=$img;
                        $data['content_type']='image';
                        InquiryLabel::insert($data);
                    }
                }
            }
        }

        if(!empty($po_add_arr)){
            $data['reference_id'] = rand(11111111,999999999);
            foreach($po_add_arr as $add){

                $data['type']=$add['type'];
                if($add['content_type']=='text'){
                    $data['content']=$add['content'];
                    $data['content_type']='text';
                    InquiryLabel::insert($data);
                }
                elseif($add['content_type']=='image'){
                    $imgs = explode('||',$add['content']);
                    foreach($imgs as $img){
                        if($img!=""){
                            $data['content']=$img;
                            $data['content_type']='image';
                            InquiryLabel::insert($data);
                        }
                    }
                }
            }
        }
    }

    public static function get_inquiry_label_chat($request){
        $res = InquiryLabel::where('inquiry_material_label.po_id',$request->inquiry_id)
            ->leftjoin('inquiry_po','inquiry_po.id','inquiry_material_label.po_id')
            ->leftjoin('order_article_name','order_article_name.id','inquiry_po.article_id')
            ->leftjoin('order_category','order_category.id','inquiry_po.category_id')
            ->leftjoin('fabric_type','fabric_type.id','inquiry_po.fabric_type_id')
            ->leftjoin('users','users.id','inquiry_material_label.user_id')
            ->leftjoin('staff','staff.id','inquiry_material_label.user_id')
            ->select('inquiry_po.inquiry_id as id','inquiry_po.style_no','order_article_name.name as article',
            'fabric_type.name as fabric_composition','order_category.name as category','inquiry_po.created_at as inq_date','inquiry_material_label.reference_id',
            'inquiry_material_label.type','inquiry_material_label.content','inquiry_material_label.content_type','inquiry_material_label.status',
            'inquiry_material_label.created_at as createdDate','inquiry_material_label.user_type','users.name as username','staff.first_name as staffname','po_id',
            'inquiry_material_label.publish_status','inquiry_material_label.user_id','inquiry_material_label.vendor_id','inquiry_material_label.orginalfilename')
            // ->orderBy('inquiry_material_label.content_type', 'asc')
            ->orderByRaw('FIELD(inquiry_material_label.type, "PrintArtWork","MainLabel","WashCare","HangTag","BarCode","PolyBag","Carton")ASC')
            ->orderBy('inquiry_material_label.created_at', 'asc')
            ->get();
        foreach($res as $key => $file){
            if($file->content_type=="image")
                $res[$key]->content = Storage::disk('s3')->temporaryUrl($file->content, '+30 minutes');
        }
        return $res;
    }

    public static function getFiles($temp_id,$media_type){
        $files = InquiryLabel::select('id as media_id','content','orginalfilename')->where('content_type','image')->where('reference_id',$temp_id)->where('type',$media_type)->get();
        foreach($files as $key => $file){
            $files[$key]->content = Storage::disk('s3')->temporaryUrl($file->content, '+15 minutes');
        }
        return $files;
    }

    public static function deleteInquiryMedia($request){
        $whereConditions1=[
            ['id','=',$request->media_id]
        ];
        $filepath = InquiryLabel::where($whereConditions1)->select('content','filesize','company_id')->limit(1)->get();
        if(!empty($filepath) && isset($filepath[0]['content'])){
           // $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
           // $companyFolder = $companyDetails->aws_s3_path;
            if($filepath[0]['company_id'] > 0){
                $companyDetails = CommonApp::getCompanyDetailsbyID($filepath[0]['company_id']);
                $storageUsed = $companyDetails->storage_used*1024*1024;
                $storageToBeFreed = $filepath[0]['file_size'];
                $freedStorage = ($storageUsed - $storageToBeFreed)/(1024*1024);
                $companyDetails->storage_used = $freedStorage;
                $companyDetails->save();
            }

            $file = $filepath[0]['content'];
            Storage::disk('s3')->delete($file);
        }
        InquiryLabel::where($whereConditions1)->delete();
        return true;
    }

    /*Get the Users inquiry lists for Fabric module */
    public static function get_label_inquiry_ids($request){
        if(strtolower($request->login_type) =="user"){
            if($request->user_type =="Factory"){
                $factory_contact_id = InquiryContact::where('factory_id',$request->user_id)->pluck('id')->first();
               // if($factory_contact_id > 0){
                    $res = Inquiries::where('inquiry_po.factory_id',$factory_contact_id)->where('inquiry_po.po_status',1)
                    ->join('inquiry_po','inquiry.id','inquiry_po.inquiry_id')
                    ->select('inquiry_po.id','inquiry_po.inquiry_id')->orderBy('id','DESC')->get();
               // }

            }else{
                // $res = Inquiries::where('inquiry.user_id',$request->user_id)->where('inquiry_po.po_status',1)
                // ->join('inquiry_po','inquiry.id','inquiry_po.inquiry_id')
                // ->select('inquiry_po.id','inquiry_po.inquiry_id')->orderBy('id','DESC')->get();

                $res = InquiryPO::where('inquiry_po.user_id',$request->user_id)->where('inquiry_po.po_status',1)
                ->join('inquiry','inquiry.id','inquiry_po.inquiry_id')
                ->leftjoin('inquiry_po_additional','inquiry_po_additional.po_id','inquiry_po.id')
                ->select('inquiry_po.id','inquiry_po.inquiry_id', DB::raw('GROUP_CONCAT( inquiry_po_additional.label ) as additional_label '))
                ->groupBy('inquiry_po.id')
                ->orderBy('inquiry_po.id','DESC')->get();
            }
        }else{
            if($request->user_type =="Factory"){
                $factory_contact_id = InquiryContact::where('factory_id',$request->user_id)->pluck('id')->first();
               // if($factory_contact_id > 0){
                $res = Inquiries::where('inquiry_po.factory_id',$factory_contact_id)->where('inquiry_po.po_status',1)
                ->join('inquiry_po','inquiry.id','inquiry_po.inquiry_id')
                ->select('inquiry_po.id','inquiry_po.inquiry_id')->orderBy('id','DESC')->get();
               // }

            }else{
                // $res = Inquiries::where('inquiry.staff_id',$request->user_id)->where('inquiry_po.po_status',1)
                // ->join('inquiry_po','inquiry.id','inquiry_po.inquiry_id')
                // ->select('inquiry_po.id','inquiry_po.inquiry_id')->orderBy('id','DESC')->get();
                $res = InquiryPO::where('inquiry_po.staff_id',$request->user_id)->where('inquiry_po.po_status',1)
                ->join('inquiry','inquiry.id','inquiry_po.inquiry_id')
                ->leftjoin('inquiry_po_additional','inquiry_po_additional.po_id','inquiry_po.id')
                ->select('inquiry_po.id','inquiry_po.inquiry_id', DB::raw('GROUP_CONCAT( inquiry_po_additional.label ) as additional_label '))
                ->groupBy('inquiry_po.id')
                ->orderBy('inquiry_po.id','DESC')->get();
            }
        }
        return $res;
    }

    public static function get_label_text_content($request){
        $res = InquiryLabel::where('inquiry_id',$request->inquiry_id)->where('reference_id',$request->referenceId)->where('content_type','text')
        ->select('id','content')->get();
        return $res;
    }
    public static function get_label_image_content($request){
        $res = InquiryLabel::where('inquiry_id',$request->inquiry_id)->where('reference_id',$request->referenceId)->where('content_type','image')
        ->select('id','content','orginalfilename')->get();
        foreach($res as $key => $file){
            $res[$key]->content = Storage::disk('s3')->temporaryUrl($file->content, '+15 minutes');
        }
        return $res;
    }

    public static function assign_po_vendor($request){
        $type = 'PrintArtWork';
        switch($request->category_id){
            case 1:
                $type = 'PrintArtWork';
                break;
            case 2:
                $type = 'MainLabel';
                break;
            case 3:
                $type = 'WashCare';
                break;
            case 4:
                $type = 'HangTag';
                break;
            case 5:
                $type = 'BarCode';
                break;
            case 6:
                $type = 'PolyBag';
                break;
            case 7:
                $type = 'Carton';
                break;
            default:
                $type = $request->category_id;
        }
        $filedata['updated_user_id'] = $request->user_id ?? 0;
        $filedata['updated_user_type'] = strtolower($request->user_type) ?? 'user';
        $filedata['vendor_id'] = $request->vendor_id ?? 0;
        InquiryLabel::where('po_id',$request->po_id)->where('type',$type)->limit(1)->update($filedata);
        return true;
    }
}
