<?php

namespace App\Http\Controllers\Mobile\Order;

use App\Common\CommonApp;
use App\Common\Uploads;
use App\Http\Controllers\Controller;
use App\Models\OrderAddSpec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class OrderAddSpecs extends Controller
{
    /* Add Files  */
    public static function addAdditionalSpecs(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            // 'additional_spec' => 'required'
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $additionalSpecsArr=[];
        $companyFolder = $companyDetails->aws_s3_path;
        $additionalSpecsArr['company_id']=$request->company_id;
        $additionalSpecsArr['workspace_id']=$request->workspace_id;
        $additionalSpecsArr['user_id']=$companyDetails->user_id;
        $additionalSpecsArr['staff_id']=$request->input('staff_id','0');
        $additionalSpecsArr['order_id']=$request->order_id;
        $additionalSpecs = [$request->file('additional_spec_0'),$request->file('additional_spec_1',[]),$request->file('additional_spec_2',[])];
        foreach($additionalSpecs as $specs){
            if(!empty($specs)){
                // dd($specs);
                $file = $specs;
                $string = str_replace(' ', '-', $specs->getClientOriginalName()); // Replaces all spaces with hyphens.
                $nameOfFile = preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
                $fileName = time().'_'.$nameOfFile;
                // $awsCompanyPath = time().'_'.$companyDetails->company_name;
                $filepath = $companyFolder.'/Orders/'.$fileName;
                Uploads::orderAddtionalSpec($file,$filepath);
                $additionalSpecsArr['filename']=$fileName;
                $additionalSpecsArr['orginalfilename']=$specs->getClientOriginalName();
                $additionalSpecsArr['filepath']=$filepath;
                $additionalSpecsArr['filesize']=$file->getSize();
                $additionalSpecsArr['fileorder']='0';
                $additionalSpecsArr['status']='1';
                $additionalSpecsArr['created_at']=date('Y-m-d H:i:s');
                $additionalSpecsArr['updated_at']=date('Y-m-d H:i:s');
                OrderAddSpec::insert($additionalSpecsArr);
            }
        }
        return response()->json(["status_code"=>200,'status'=>"success","message"=>"Files Added Successfully"],200);
    }
    /* View the uploaded files */
    public static function getUploadedFiles(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereConditions = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->order_id],
            ['status','=','1']
        ];
        $uploadedFiles = OrderAddSpec::where($whereConditions)
                        ->select('id','filename','orginalfilename','filepath','filesize')
                        ->get();
                        $getServerURL = config('filesystems.disks.s3.url');
        return response()->json(["status_code"=>200,'status'=>"success","data"=>$uploadedFiles,"URL_path"=>$getServerURL],200);
    }

    /* Download a file*/
    public static function downloadFile(Request $request){
        $validator = Validator::make($request->all(),[
            'fileId' =>'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required'
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->order_id],
            ['id','=',$request->fileId]
        ];
        $getFileUrl = OrderAddSpec::where($whereConditions)->select('filepath','orginalfilename')->first();
        $getServerURL = config('filesystems.disks.s3.url');
       // $fullpath = $getServerURL.($getFileUrl->filepath);
        $fullpath =  Storage::disk('s3')->temporaryUrl($getFileUrl->filepath, '+5 minutes');
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . $getFileUrl->orginalfilename);
        header("Content-Type: application/octet-stream" );
        header('Access-Control-Allow-Origin:*');

        return readfile($fullpath);
    }

    /* Delete a file*/
    public static function deleteFile(Request $request){
        $validator = Validator::make($request->all(),[
            'fileId' =>'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->order_id],
            ['id','=',$request->fileId]
        ];
        $fileToDelete=OrderAddSpec::where($whereConditions)->first();
        $fileToDelete->status = "2";
        $fileToDelete->save();

        return response()->json(["status_code"=>200,'status'=>"success","message"=>"File Deleted Successfully"]);
    }
}
