<?php

namespace App\Http\Controllers\WebSite\Chats;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatDeleteHistory;
use App\Models\Order;
use App\Models\OrderTask;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Chats extends Controller
{
    //
    /* This function is uced to Add Chat */
    public static function addChats(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'page_type'=>'required',
            'text_type' => 'required',
            'comment_type' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(["status_code" =>401,"status"=>"failure","errors"=>$validator->errors()]);
        }
        try{
          $sts =  Chat::addNewChat($request);
          if($sts=='3'){
            return response()->json(["status_code"=>401,"status"=>"failure","error"=>"Your Plan storage is full. Please contact DMS Admin"]);
          }
        }catch(Exception $e){
            return response()->json(["status_code"=>601,"status"=>"failure","errors"=>$e->getMessage()]);
        }
        return response()->json(["status_code"=>200,"status" =>"success"]);
    }

    /* This function is uced to Get Chats for the task */
    public static function getChat(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'page_type'=>'required',
            'page_id'=>'required',
        ]);
        if($validator->fails()){
            return response()->json(["status_code" =>401,"errors"=>$validator->errors()]);
        }
        $whereConditions=[
            ['chats.company_id','=',$request->company_id],
            ['chats.workspace_id','=',$request->workspace_id],
            ['chats.page_type','=',$request->page_type],
            ['chats.page_id','=',$request->page_id]
        ];
         $res = Chat::where($whereConditions)
        ->select('page_type','text_type','sender_name as sender','reciever_name as recipient','text','comment_type',
        'reply_to_id',  'comment_status',DB::raw('DATE_FORMAT(chats.date_time,"%d %b %Y %H:%i") as send_at'),'user_id','staff_id','id')
        ->get();
        $chats['serverURL']= '';//config('filesystems.disks.s3.url');

        foreach($res as $key => $file){
            if($file->text_type=="image" || $file->text_type=="pdf")
                $res[$key]->text = Storage::disk('s3')->temporaryUrl($file->text, '+15 minutes');
        }

        $chats['chats'] = $res;

        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$chats]);
        return CommonApp::webEncrypt($res);
    }

    /* This function is used to Export Chats */
    public static function exportChats(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $whereConditions=[
            ['chats.company_id','=',$request->company_id],
            ['chats.workspace_id','=',$request->workspace_id],
            ['chats.page_type','=',$request->page_type],
            ['chats.page_id','=',$request->page_id]
        ];
        $chats=[];
        $theChats = Chat::where($whereConditions)->where('reply_to_id',0)
        ->select('id','text_type','sender_name as sender','reciever_name as recipient','text','comment_type','comment_status',
        'reply_to_id',DB::raw('DATE_FORMAT(chats.date_time,"%d %b %Y %H:%i") as send_at'),'user_id','staff_id','original_name')
        ->get();

        foreach($theChats as $key => $file){
            if($file->text_type=="image" || $file->text_type=="pdf")
                $theChats[$key]->text = Storage::disk('s3')->temporaryUrl($file->text, '+15 minutes');
        }

        if(isset($request->user_id) && $request->user_id>0){
            // $whereConditionToSend=[
            //     ['company_id','=',$request->company_id],
            //     ['id','=',$request->user_id]
            // ];
            $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
            $dateFormat = $dateFormatAndLanguage['dateFormat'];
            $language = $dateFormatAndLanguage['language'];
        }
        else if(isset($request->staff_id) && $request->staff_id>0){
            // $whereConditionToSend=[
            //     ['company_id','=',$request->company_id],
            //     ['workspace_id','=',$request->workspace_id],
            //     ['id','=',$request->staff_id]
            // ];
            $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
            $dateFormat = $dateFormatAndLanguage['dateFormat'];
            $language = $dateFormatAndLanguage['language'];
        }
        App::setlocale($language);
        $chats['serverURL']= ''; //config('filesystems.disks.s3.url');
        $chats['dateFormat']=$dateFormat;
        $chats['useLogo'] = $dateFormatAndLanguage['useLogo'];
        $chats['userLogo'] = $dateFormatAndLanguage['userLogo'] !="" ?  Storage::disk('s3')->temporaryUrl($dateFormatAndLanguage['userLogo'], '+15 minutes') : ""; //$dateFormatAndLanguage['userLogo'];
        if($request->page_type === "taskupdate"){
            $theTask= OrderTask::where('id',$request->page_id)->first();
            $theOrder= Order::where('id',$theTask->order_id)->first();
            if($theOrder->factory_id != NULL){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$theOrder->factory_id]
                ];
                $chats['factory'] = (CommonApp::getOrderEssentialDetails($forType,"Factory"))->name;
            }
            if($theOrder->pcu_id != NULL){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$theOrder->pcu_id]
                ];
                $chats['pcu'] = (CommonApp::getOrderEssentialDetails($forType,"PCU"))->name;
            }
            if($theOrder->buyer_id != NULL){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$theOrder->buyer_id]
                ];
                $chats['buyer'] =  (CommonApp::getOrderEssentialDetails($forType,"Buyer"))->name;
            }
            if($theOrder->factory_id != NULL && $theOrder->pcu_id){
                $chats['workspacetype']="buyer";
            }
            elseif($theOrder->factory_id != NULL && $theOrder->buyer_id){
                $chats['workspacetype']="pcu";
            }
            elseif($theOrder->pcu_id != NULL && $theOrder->buyer_id){
                $chats['workspacetype']="factory";
            }
            $chats['orderNo']=$theOrder->order_no;
            $chats['styleNo']=$theOrder->style_no;
            $chats['title']=$theTask->cat_title;
            $chats['subtitle']=$theTask->task_title;
            $chats['pic']= CommonApp::getPIC($theTask->task_pic);
            $chats['taskChats']=$theChats;
            $res=Chat::where($whereConditions)->where('reply_to_id',"!=",0)
            ->select('id','text_type','sender_name as sender','reciever_name as recipient','text','comment_type','comment_status',
            'reply_to_id',DB::raw('DATE_FORMAT(chats.date_time,"%d %b %Y %H:%i") as send_at'),'user_id','staff_id','original_name')
            ->get();
            foreach($res as $key => $file){
                if($file->text_type=="image" || $file->text_type=="pdf")
                    $res[$key]->text = Storage::disk('s3')->temporaryUrl($file->text, '+15 minutes');
            }
            $chats['taskChatsReplies'] = $res;

            // $isSubtasksAvailable = OrderTask::where('is_subtask',1)->where('parent_task_id',$theTask->id)->get();
            // if(count($isSubtasksAvailable)>0){
            //     $whereConditions1=[
            //         ['chats.company_id','=',$request->company_id],
            //         ['chats.workspace_id','=',$request->workspace_id],
            //         ['chats.page_type','=',$request->page_type],
            //     ];
            //     foreach($isSubtasksAvailable as $subtasks){
            //         $whereConditions1['chats.page_id']=$subtasks->id;
            //         $subtaskArr=[];
            //         $subtaskChat=Chat::where($whereConditions1)->where('reply_to_id',0)
            //         ->select('id','text_type','sender_name as sender','reciever_name as recipient','text','comment_type','comment_status',
            //         'reply_to_id',DB::raw('DATE_FORMAT(chats.date_time,"%d %b %Y %H:%i") as send_at'),'user_id','staff_id','original_name')
            //         ->get();
            //         $subtaskArr['subtasktitle']=$subtasks->subtask_title;
            //         $subtaskArr['chats']=$subtaskChat;
            //         $subtaskArr['taskChatsReplies']=Chat::where($whereConditions)->where('reply_to_id',"!=",0)
            //         ->select('id','text_type','sender_name as sender','reciever_name as recipient','text','comment_type','comment_status',
            //         'reply_to_id',DB::raw('DATE_FORMAT(chats.date_time,"%d %b %Y %H:%i") as send_at'),'user_id','staff_id','original_name')
            //         ->get();
            //         $subtaskArr['pic']= CommonApp::getPIC($subtasks->task_pic);
            //         count($subtaskChat)>0 ? $chats['subtasks'][]=$subtaskArr:"";
            //     }
            // }
            if(/* isset($request->user_id) && */ $request->staff_id == 0){
                $chats['user_id']=$request->user_id;
                $chats['user_type']="User";
            }
            else if(/* isset($request->staff_id) && */ $request->staff_id > 0){
                $chats['user_id']=$request->staff_id;
                $chats['user_type']="Staff";
            }
        }
        // dd($chats);
        view()->share("chats",$chats);
        // $pdf = Pdf::loadView('chatsPDF');
        // $pdf = Pdf::loadView('ChatTablePDF');
        $pdf = Pdf::loadView('ChatFinal2TablePDF');
        $pdf->setPaper('A4', 'landscape');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->set_option("enable_php", true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
        return $pdf->download();
    }

    /* This function is used to delete the chat */
    public static function deleteChat(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'id'=>'required',
            'user_type' => 'required',
            'the_user_id' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code" =>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['id','=',$request->id],
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
        ];
        $chatToDelete = Chat::where($whereConditions)->first();
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $ifChatHasRepliedChat = Chat::where('reply_to_id',$request->id)->get();
        if(count($ifChatHasRepliedChat)>0){
            $res = json_encode(["status_code"=>600,"status" =>"failure","message"=>"Please Delete The Replies for this Conversation and Continue."]);
            return CommonApp::webEncrypt($res);
        }else{
            DB::beginTransaction();
            try{
                ChatDeleteHistory::chatDeleteLog($chatToDelete,$request);
                if($chatToDelete->text_type != "text"){
                    /* To Delete The File and free its storage */
                    $storageUsed = $companyDetails->storage_used*1024*1024;
                    $storageToBeFreed = $chatToDelete->file_size;
                    $freedStorage = ($storageUsed - $storageToBeFreed)/(1024*1024);
                    $companyDetails->storage_used = $freedStorage;
                    Storage::disk('s3')->delete($chatToDelete->text);
                    $companyDetails->save();
                }
                $chatToDelete->delete();
            }catch(Exception $e){
                DB::rollBack();
                $res = json_encode(["status_code"=>600,"status" =>"failure","message"=>$e->getMessage()]);
                return CommonApp::webEncrypt($res);
            }
            DB::commit();
            $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Deleted Successfully"]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* This function is used to change the comment type */
    public static function changeCommentType(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'id'=>'required',
            'comment_type' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code" =>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            Chat::changeCommentType($request);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>600,"status" =>"failure","message"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
        $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Updated Successfully"]);
        return CommonApp::webEncrypt($res);
    }
}
