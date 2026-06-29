<?php

namespace App\Common;

use Illuminate\Support\Facades\Storage;

class Uploads
{
    static function companyLogoUpload($path,$file){
        Storage::disk('s3')->put($path,file_get_contents($file));
    }

    static function orderAddtionalSpec($file,$path){
        Storage::disk('s3')->put($path,file_get_contents($file));
    }
    static function companyCreateDirectory($dirname){
        Storage::disk('s3')->makeDirectory($dirname);
    }
    static function imageURL($imagePath){
      return $imageUrl = Storage::disk('s3')->url($imagePath);
    }

    static function deleteS3File($imagePath){
        return $imageUrl = Storage::disk('s3')->delete($imagePath);
      }
  


}
