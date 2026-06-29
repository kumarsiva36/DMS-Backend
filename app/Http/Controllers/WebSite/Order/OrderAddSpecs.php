<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Common\Uploads;
use App\Http\Controllers\Controller;
use App\Models\OrderAddSpec;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class OrderAddSpecs extends Controller
{
    /* Add New files in the Order Task Section */
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
        try{
            //$free_storage = ($companyDetails->max_storage_size - ($companyDetails->storage_used + (int)config('constant.plan_storage_free_mb')))*1024*1024;
            $free_storage = CommonApp::calculateFreeStorage($companyDetails->max_storage_size,$companyDetails->storage_used);
            $storageUsed = $companyDetails->storage_used*1024*1024;
            $storageToBeAdded = 0;
            foreach($additionalSpecs as $specs){
                if(!empty($specs)){
                    // dd($specs);
                    $file = $specs;
                    $string = str_replace(' ', '-', $specs->getClientOriginalName()); // Replaces all spaces with hyphens.
                    $nameOfFile = preg_replace('/[^A-Za-z0-9.\-]/', '', $string); // Removes special chars.
                    $fileName = time().'_'.$nameOfFile;
                    // $awsCompanyPath = time().'_'.$companyDetails->company_name;
                    $filepath = $companyFolder.'/Orders/'.$fileName;
                    if($file->getSize() > $free_storage && config('constant.plan_storage_size_validation') == 1){
                        return response()->json(["status_code"=>401,"status"=>"failure","error"=>"Your Plan storage is full. Please contact DMS Admin"]);
                    }
                    Uploads::orderAddtionalSpec($file,$filepath);
                    $additionalSpecsArr['filename']=$fileName;
                    $additionalSpecsArr['orginalfilename']=$specs->getClientOriginalName();
                    $additionalSpecsArr['filepath']=$filepath;
                    $additionalSpecsArr['filesize']=$file->getSize();
                    $storageToBeAdded += $file->getSize();
                    $additionalSpecsArr['fileorder']='0';
                    $additionalSpecsArr['status']='1';
                    $additionalSpecsArr['created_at']=date('Y-m-d H:i:s');
                    $additionalSpecsArr['updated_at']=date('Y-m-d H:i:s');
                    OrderAddSpec::insert($additionalSpecsArr);
                }
            }
            $companyDetails->storage_used = ($storageUsed + (int)$storageToBeAdded)/(1024*1024);
            $companyDetails->save();
            return response()->json(["status_code"=>200,'status'=>"success","message"=>"Files Added Successfully"],200);
        }catch(Exception $e){
            return response()->json(["status_code"=>401,'status'=>"failure","error"=>$e->getMessage()]);
        }
    }

    /* Get the Uploaded File List */
    public static function getUploadedFiles(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->order_id],
            ['status','=','1']
        ];
        $uploadedFiles = OrderAddSpec::where($whereConditions)
                        ->select('filename','orginalfilename','filepath','filesize')
                        ->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$uploadedFiles],200);
        return CommonApp::webEncrypt($res);
    }

    /* Download a file*/
    public static function downloadFile(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator =Validator::make((array)$request,[
            'fileName' =>'required',
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
            ['filename','=',$request->fileName]
        ];
        $getFileUrl = OrderAddSpec::where($whereConditions)->select('filepath','orginalfilename')->first();
        $getServerURL = config('filesystems.disks.s3.url');
      //  $fullpath = $getServerURL.($getFileUrl->filepath);
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
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'fileName' =>'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to Delete task file
            $per = CommonApp::checkStaffPermission($request,'34');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->order_id],
            ['filename','=',$request->fileName]
        ];
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $fileToDelete=OrderAddSpec::where($whereConditions)->first();
        /* To Get the Storage and Delete It */
        $storageUsed = $companyDetails->storage_used*1024*1024;
        $storageToBeFreed = $fileToDelete->filesize;
        $freedStorage = ($storageUsed - $storageToBeFreed)/(1024*1024);
        $companyDetails->storage_used = $freedStorage;
        $companyDetails->save();
        $fileToDelete->status = "2";
        $fileToDelete->save();

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"File Deleted Successfully"]);
        return CommonApp::webEncrypt($res);
    }
}
