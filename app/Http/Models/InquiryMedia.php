<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class InquiryMedia extends Model
{
    use HasFactory;

    protected $table = 'inquiry_media';

    public static function getFiles($temp_id,$media_type, $inquiry_id=''){
        if($inquiry_id!='' && (int)$inquiry_id > 0){
            $files = InquiryMedia::select('id as media_id','filepath','orginalfilename','media_type')->where('inquiry_id',$inquiry_id)->where('media_type',$media_type)->get();
        }else{

            $files = InquiryMedia::select('id as media_id','filepath','orginalfilename','media_type')->where('temp_id',$temp_id)->where('media_type',$media_type)->get();
        }
        foreach($files as $key => $file){
            $files[$key]->filepath = Storage::disk('s3')->temporaryUrl($file->filepath, '+15 minutes');
        }
        return $files;
    }

    public static function createThumbs($file,$fileName,$filePath,$height,$width){
        $destinationPath = "Inquiry/".$filePath;
        if(!file_exists($destinationPath)){
            mkdir($destinationPath,777,true);
        }
        $image = Image::make($file)->resize($width, $height, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        })->save($destinationPath.'/'.$fileName.'.'.$file->extension());
    }

    /* Get Inquiry PDF & Measurement sheets */
    public static function inquiry_pdf_download($request){
        $whereConditions=[
            ['inquiry_id','=',$request->inquiry_id],
            ['media_type','=','MeasurementSheet']
        ];

        $files = InquiryMedia::where($whereConditions)
        ->select('orginalfilename','filepath')
        ->orderBy('id','asc')
        ->get();
        return $files;
    }
}
