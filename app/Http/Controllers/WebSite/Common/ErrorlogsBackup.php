<?php

namespace App\Http\Controllers\WebSite\Common;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Exception;

class ErrorlogsBackup extends Controller
{
    /* Function For sending tasks that are due today */
    public static function erorrlogbackup(){
        $logpath = storage_path('logs');
        $date = date('Y-m-d', strtotime('-2 days'));
        $file = $logpath."\laravel-".$date.".log";
        if(file_exists($file))
        {
            try{
                $path = "DMS-Error-Log/laravel-".$date.".log";
                Storage::disk('s3')->put($path,file_get_contents($file));
                unlink($file);
            }catch(Exception $e){

            }
        }
        exit('success');
    }



}
