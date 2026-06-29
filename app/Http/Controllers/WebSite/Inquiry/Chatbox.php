<?php
namespace App\Http\Controllers\WebSite\Inquiry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Common\CommonApp;
use App\Models\ChatBox as ModelsChatBox;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class Chatbox extends Controller
{
    public static function add_live_chat(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'chat_id' => 'required',
            'message' => 'required',
            'sent_by' => 'required',
            'sender_name' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $chat_id = $request->chat_id;
        $status = ModelsChatBox::where('chat_id',$request->chat_id)->pluck('status')->first();
        if($status==1)
            $chat_id = strtotime(date('Y-m-d H:i:s'));

        $det['chat_id'] = $chat_id;
        $det['message'] = $request->message;
        $det['sent_by'] = $request->sent_by;
        $det['sender_name'] = $request->sender_name;
        $det['user_id'] = $request->user_id;
        $det['staff_id'] = $request->staff_id;
        $det['company_id'] = $request->company_id;
        $det['workspace_id'] = $request->workspace_id;
        $det['created_at'] = date('Y-m-d H:i:s');

        ModelsChatBox::insert($det);

        $res = json_encode(["status_code"=>200,'status'=>"success","chat_id"=>$chat_id,"message"=>"Data Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }
    public static function get_live_chat(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'chat_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['chat_id','=',$request->chat_id],
            ['user_id','=',$request->user_id],
            ['staff_id','=',$request->staff_id],
            ['sent_by','=',2],
            ['is_sent','=',0],
        ];
        $msg =ModelsChatBox::where($whereConditions)->select('message','id','status')->get();
        $update =ModelsChatBox::where($whereConditions)->update(array('is_sent' => 1));
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$msg],200);
        return CommonApp::webEncrypt($res);
    }
    public static function get_pervious_user_chat(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'staff_id' => 'required',
            'user_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['user_id','=',$request->user_id],
            ['staff_id','=',$request->staff_id],
            ['is_sent','=',1],
            ['status','=',0],
        ];
        $msg =ModelsChatBox::where($whereConditions)->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$msg],200);
        return CommonApp::webEncrypt($res);
    }

    public function get_chat_list(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'staff_id' => 'required',
            'user_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $result=ModelsChatBox::getChatList($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$result]);
        return CommonApp::webEncrypt($res);
    }
    public function get_chat_detail(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "user_id" => 'required|integer',
            "staff_id" => 'required|integer',
            "chat_id"=>'required',
        ]);
        if($validated->fails()){
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        $result=ModelsChatBox::get_chat_detail($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$result]);
        return CommonApp::webEncrypt($res);
    }
    public static function chat_export(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "user_id" => 'required|integer',
            "staff_id" => 'required|integer',
            "chat_id"=>'required',
        ]);
        if($validated->fails()){
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        $result=ModelsChatBox::get_chat_detail($request);
        view()->share(["result"=>$result,"request"=>$request]);
        $pdf = Pdf::loadView('chatExportPDF');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->download();
    }
}
