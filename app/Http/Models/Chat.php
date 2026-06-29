<?php

namespace App\Models;

use App\Common\CommonApp;
use App\Common\Uploads;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chats';

    /* Add New Chat */
    public static function addNewChat($request){
        $chatArr=[];
        $chatArr['company_id'] = $request->company_id;
        $chatArr['workspace_id'] = $request->workspace_id;
        $chatArr['comment_type'] = $request->comment_type;
        $chatArr['comment_status'] = $request->comment_status ?? 0;
        $chatArr['reply_to_id'] = $request->reply_to_id ?? 0;
        $chatArr['user_id'] = $request->user_id;
        $chatArr['staff_id'] = $request->staff_id;
        $chatArr['sender_id'] = $request->sender_id;
        $chatArr['reciever_id'] = $request->reciever_id;
        $chatArr['sender_name'] = $request->sender_name ?? null;
        $chatArr['reciever_name'] = $request->reciever_name ?? null;
        $chatArr['page_type'] = $request->page_type;
        $chatArr['page_id'] = $request->page_id;
        $chatArr['date_time'] = date('Y-m-d H:i:s');
        $chatArr['created_at'] = date('Y-m-d H:i:s');
        $chatArr['updated_at'] = date('Y-m-d H:i:s');
        if($request->text_type === "image" || $request->text_type === "pdf" || $request->text_type === "doc"
        || $request->text_type === "excel"){
            $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
            //$free_storage = ($companyDetails->max_storage_size - ($companyDetails->storage_used + (int)config('constant.plan_storage_free_mb')))*1024*1024;
            $free_storage = CommonApp::calculateFreeStorage($companyDetails->max_storage_size,$companyDetails->storage_used);
            $companyFolder = $companyDetails->aws_s3_path;
            $file = $request->text;
            if($file->getSize() > $free_storage && config('constant.plan_storage_size_validation') == 1){
                return '3';
                //return response()->json(["status_code"=>401,"error"=>"Your Plan storage is full. Please contact DMS Admin"]);
            }

            $subFolder1 = $request->page_type;
            $subFolder2 = $request->page_id;
            $string = str_replace(' ', '-', $file->getClientOriginalName()); // Replaces all spaces with hyphens.
            $nameOfFile = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
            $fileName = time().'_'.$nameOfFile;
            $filepath = $companyFolder.'/Chats/'.$subFolder1.'/'.$subFolder2.'/'.$fileName;
            Uploads::orderAddtionalSpec($file,$filepath);
            $chatArr['text_type'] = $request->text_type;
            $chatArr['text'] = $filepath;
            $chatArr['file_size'] = $file->getSize();
            $chatArr['original_name'] = $file->getClientOriginalName();
            /* To Add Storage Used in Company Table */
            $storageUsed = $companyDetails->storage_used*1024*1024;
            $companyDetails->storage_used = ($storageUsed + (int)$chatArr['file_size'])/(1024*1024);
            $companyDetails->save();
        }
        elseif($request->text_type === "text"){
            $chatArr['text_type'] = $request->text_type;
            $chatArr['text'] = $request->text;
        }
        DB::beginTransaction();
        try{
            Chat::insert($chatArr);
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException("Unable To Post Data");
        }
        DB::commit();
    }

    /* Change the Comment Type */
    public static function changeCommentType($request){
        $whereCondition=[
            ['id','=',$request->id],
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
        ];
        $theChat = Chat::where($whereCondition)->first();
        DB::beginTransaction();
        try{
            if(!empty($theChat)){
                if($theChat->comment_type == $request->comment_type){
                    throw new InvalidArgumentException("Already Updated");
                }else{
                    $theChat->comment_type = $request->comment_type;
                    $theChat->save();
                }
            }
        }catch(Exception $e){
            DB::rollBack();
            if($e->getMessage() === "Already Updated" ){
                throw new InvalidArgumentException("Already Updated");
            }else{
                throw new InvalidArgumentException("Unable to Post Data");
            }
        }
        DB::commit();
    }
}
