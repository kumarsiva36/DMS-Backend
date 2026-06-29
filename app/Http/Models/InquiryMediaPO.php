<?php

namespace App\Models;

use App\Common\CommonApp;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class InquiryMediaPO extends Model
{
    use HasFactory;

    protected $table = "inquiry_po_media";

    /* Get Inquiry PO Media */
    static function getInquiryPOMedia($request){
        $whereConditions=[
            ['inquiry_po_media.po_id','=',$request->po_id],
        ];

        $mediaPO = InquiryMediaPO::where($whereConditions)
       // ->join('inquiry_po_media','inquiry_po_media.po_id','inquiry_po.id')
        ->select('inquiry_po_media.id','inquiry_po_media.filepath','inquiry_po_media.media_type','temp_id',
        'inquiry_po_media.orginalfilename','inquiry_po_media.datas as datasource','inquiry_po_media.id as media_id')
        ->get();

        foreach($mediaPO as $key => $file){
            $mediaPO[$key]->org_file_path = $file->filepath;
            $mediaPO[$key]->filepath = Storage::disk('s3')->temporaryUrl($file->filepath, '+15 minutes');

            $temp_id = explode('_',$file->temp_id)[0];
            $style_no = str_replace($temp_id.'_', '', $file->temp_id);
            $mediaPO[$key]->style_no = $style_no;

        }

        return $mediaPO;
    }

    /* Get The Files for PO after uploading/EDIT */
    public static function getFiles($po_id,$media_type,$referenceId){
        try{
        if((int)$po_id > 0){
            $files = InquiryMediaPO::select('id as media_id','filepath','orginalfilename','media_type','temp_id')
            ->where('po_id',$po_id)
            ->where('media_type',$media_type)
            ->get();
        }else{
            if(stristr($referenceId,'_')){
                $referenceId = explode('_',$referenceId)[0];
            }
            $files = InquiryMediaPO::select('id as media_id','filepath','orginalfilename','media_type','temp_id')
            //->where('temp_id',$referenceId)
            ->where('temp_id', 'like', '%' . $referenceId . '%')
            ->where('media_type',$media_type)
            ->get();
        }

        foreach($files as $key => $file){
            $files[$key]->filepath = Storage::disk('s3')->temporaryUrl($file->filepath, '+15 minutes');
            $temp_id = explode('_',$file->temp_id)[0];
            $style_no = str_replace($temp_id.'_', '', $file->temp_id);
            $files[$key]->style_no = $style_no;
        }
        return $files;
    }catch(Exception $e){

    }
    }

    /* Delete The Inquiry PO Media */
    public static function deleteInquiryPOMedia($request){
        $whereConditions1=[
            ['id','=',$request->media_id]
        ];
        $filepath = InquiryMediaPO::where($whereConditions1)->select('filepath','filesize','company_id')->limit(1)->get();
        if(!empty($filepath) && isset($filepath[0]['filepath'])){
            if($filepath[0]['company_id'] > 0){
               // dd($filepath[0]['filesize']);
                $companyDetails = CommonApp::getCompanyDetailsbyID($filepath[0]['company_id']);
                $storageUsed = $companyDetails->storage_used*1024*1024;
                $storageToBeFreed = $filepath[0]['filesize'];
                $freedStorage = ($storageUsed - $storageToBeFreed)/(1024*1024);
                $companyDetails->storage_used = $freedStorage;
                $companyDetails->save();
            }
            //$companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
            //$companyFolder = $companyDetails->aws_s3_path;
            $file = $filepath[0]['filepath'];
            Storage::disk('s3')->delete($file);
        }
        InquiryMediaPO::where($whereConditions1)->delete();
        return true;
    }

    public static function deleteMultiPOMedia($request){
        $filepath = InquiryMediaPO::whereIn('id',$request->media_id)->select('filepath','filesize','company_id')->get();
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

        InquiryMediaPO::whereIn('id',$request->media_id)->delete();
        return true;
    }

    /* Get Inquiry PO Media */
    static function getInquiryPOMediaMulti($request){
        $whereConditions=[
            ['inquiry_po_media.parent_po_id','=',$request->po_parent_id],
        ];

        $mediaPO = InquiryMediaPO::where($whereConditions)
       // ->join('inquiry_po_media','inquiry_po_media.po_id','inquiry_po.id')
        ->select('inquiry_po_media.id','inquiry_po_media.filepath','inquiry_po_media.media_type','temp_id',
        'inquiry_po_media.orginalfilename','inquiry_po_media.datas as datasource','inquiry_po_media.id as media_id','parent_po_id','po_id')
        ->get();

        foreach($mediaPO as $key => $file){
            $mediaPO[$key]->org_file_path = $file->filepath;
            $mediaPO[$key]->filepath = Storage::disk('s3')->temporaryUrl($file->filepath, '+15 minutes');

            $temp_id = explode('_',$file->temp_id)[0];
            $style_no = str_replace($temp_id.'_', '', $file->temp_id);
            $mediaPO[$key]->style_no = $style_no;

        }

        return $mediaPO;
    }
}
