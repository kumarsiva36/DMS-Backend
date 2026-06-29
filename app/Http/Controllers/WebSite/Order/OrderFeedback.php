<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\OrderFeedback as ModelsOrderFeedback;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderFeedback extends Controller
{
    /* Add Order Feedback */
    public static function addOrderFeedback(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'order_id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'feedback_given_id' => 'required',
            'feedback_given_by' => 'required',
            'user_type' => 'required',
            'user_id' => 'required',
            'lowest_price' => 'required',
            'ontime_delivery' => 'required',
            'vendor_buyer_relation' => 'required',
            'sample_submission' => 'required',
            'communication' => 'required',
            'less_quality_issue' => 'required',
            'good_sell_through' => 'required',
            'collaborative_approach' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if (strtolower($request->feedback_given_by) == "factory"){
            $res = json_encode(["status_code"=>600,"message"=>"Factory Cannot Enter Feedback"]);
            return CommonApp::webEncrypt($res);
        }
        $filedata['order_id']=$request->order_id;
        $filedata['company_id']=$request->company_id;
        $filedata['workspace_id']=$request->workspace_id;
        $filedata['feedback_given_id']=$request->feedback_given_id ?? 0;
        $filedata['feedback_given_by']=$request->feedback_given_by;
        $filedata['user_id']=$request->user_id ?? 0;
        $filedata['user_type']=$request->user_type;
        $filedata['lowest_price']=$request->lowest_price ?? 0;
        $filedata['lowest_price_comments']=$request->lowest_price_comments ?? "";
        $filedata['ontime_delivery']=$request->ontime_delivery ?? 0;
        $filedata['ontime_delivery_comments']=$request->ontime_delivery_comments ?? '';
        $filedata['vendor_buyer_relation']=$request->vendor_buyer_relation ?? 0;
        $filedata['vendor_buyer_relation_comments']=$request->vendor_buyer_relation_comments ?? '';
        $filedata['sample_submission']=$request->sample_submission ?? 0;
        $filedata['sample_submission_comments']=$request->sample_submission_comments ?? '';
        $filedata['communication']=$request->communication ?? 0;
        $filedata['communication_comments']=$request->communication_comments ?? '';
        $filedata['less_quality_issue']=$request->less_quality_issue ?? 0;
        $filedata['less_quality_issue_comments']=$request->less_quality_issue_comments ?? '';
        $filedata['good_sell_through']=$request->good_sell_through ?? 0;
        $filedata['good_sell_through_comments']=$request->good_sell_through_comments ?? '';
        $filedata['collaborative_approach']=$request->collaborative_approach ?? 0;
        $filedata['collaborative_approach_comments']=$request->collaborative_approach_comments ?? '';
        $filedata['created_at']=date('Y-m-d H:i:s');
        $filedata['upda ted_at']=date('Y-m-d H:i:s');
        try{
            ModelsOrderFeedback::insert($filedata);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Feedback Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    /* Get The Order Feedback */
    public static function viewOrderFeedback(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'order_id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $orderFeedback = ModelsOrderFeedback::where('order_id',$request->order_id)->first();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$orderFeedback],200);
        return CommonApp::webEncrypt($res);
    }
}
