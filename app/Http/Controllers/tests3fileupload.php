<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class tests3fileupload extends Controller
{
    public function index()
    {
        return view('fileupload');
    }
    public function store(Request $request)
    {
        $this->validate($request, ['image' => 'required|image']);
        if($request->hasfile('image'))
         {
            $file = $request->file('image');
            $name=time().$file->getClientOriginalName();
            $filePath = 'images/' . $name;
           // $exists = Storage::disk('s3')->exists('file.jpg');
           // Storage::disk('s3')->put($filePath, $file,'public');
            Storage::disk('s3')->put($filePath, file_get_contents($file));
           // Storage::disk('local')->put($filePath, file_get_contents($file));
            return back()->with('success','Image Uploaded successfully');
         }
    }

    public static function downloadfile(Request $request){
        $path = $request->path ?? '';
        $filename = $request->filename ?? 'download.pdf';
        $fullpath =  Storage::disk('s3')->temporaryUrl($path, '+5 minutes');
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Content-Type: application/octet-stream" );
        header('Access-Control-Allow-Origin:*');

        return readfile($fullpath);

        echo $path;
        exit;
    }
}
