<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;
use App\Common\CommonApp;
use Illuminate\Support\Facades\Storage;

class OrderMedia extends Model
{
    use HasFactory;

    protected $table = 'order_media';

    public static function getFiles($order_id,$media_type){
        $files = OrderMedia::select('id as media_id','filepath','orginalfilename')->where('order_id',$order_id)->where('media_type',$media_type)->get();
        return $files;
    }

    public static function deleteOrderMedia($request){
        $whereConditions1=[
            ['id','=',$request->media_id]
        ];
        $filepath = OrderMedia::where($whereConditions1)->select('filepath')->limit(1)->get();
        if(!empty($filepath) && isset($filepath[0]['id'])){
            //$companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
            //$companyFolder = $companyDetails->aws_s3_path;
            $file = $filepath[0]['filepath'];
            Storage::disk('s3')->delete($file);
        }
        OrderMedia::where($whereConditions1)->delete();
        return true;
    }
    public static function get_materials_media($request){
        $whereConditions = [
            ['order_id','=',$request->order_id]
        ] ;
        $data = OrderMedia::where($whereConditions)->get();
        $datav=[];
        foreach($data as $datas){
         $datav[]=array('id'=>$datas['id'],
         "order_id"=>$datas['order_id'],
         "media_type"=>$datas['media_type'],
         "filename"=>$datas['filename'],
         "orginalfilename"=>$datas['orginalfilename'],
         "filepath"=>$datas['filepath'],
         "filesize"=>$datas['filesize'],
         "fileurl"=>Storage::disk('s3')->temporaryUrl($datas['filepath'], '+5 minutes')
        );
        }
        return $datav;
       // return json_encode(($datav), true, JSON_UNESCAPED_SLASHES);
    }
    public static function get_media_file_type($id){
        return OrderMedia::where('id',$id)->pluck('media_type')->first();
    }



}
