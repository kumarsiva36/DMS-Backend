<?php

namespace App\Http\Controllers\WebSite\Order\EditOrders;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Models\Orderlog;
use App\Models\MultipleDeliveryDates;
use App\Models\OrderComments;
use Illuminate\Support\Facades\Log;

class EditOrder extends Controller
{
    /* To View the added order */
    public static function getOrderDetails(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions = [
            ['orders.id','=',$request->order_id],
            ['orders.company_id','=',$request->company_id],

            ['orders.workspace_id','=',$request->workspace_id]
        ];
        $order = Order::where($whereConditions)
                ->leftjoin('order_comments','orders.id','order_comments.order_id')
                ->select('order_no','style_no','buyer_id','factory_id','pcu_id',
                'category_id','article_id','fabric_id','order_price','income_terms','total_quantity',
                'no_of_deliverys','quantity_wise','tolerance_perc','tolerance_volume','currency_type',
                'is_tolerance_req','order_priority','status','step_level','delivery_date','units','inquiry_date',
                'order_comments.comments','order_comments.document_url','order_comments.audio_url','order_comments.video_url')
                ->first();

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$order]);
        return CommonApp::webEncrypt($res);
    }

    /* Update the order */
    public function updateOrderDetails( Request $request)
    {
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
        'order_id' => 'required',
        'company_id' => 'required',
        'workspace_id' => 'required',
       // 'order_no' => 'required',
       // 'style_no' => 'required',
       // 'category_id' => 'required',
        'fabric_id' => 'required',
        'article_id' => 'required',
        'total_quantity' => 'required',
        'no_of_deliverys' => 'required',
        'delivery_dates'=> 'required|array',
        //'order_price' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,
            "error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to Edit order
            $per = CommonApp::checkStaffPermission($request,'20');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        $whereCondition = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['id','=',$request->order_id],
            ];
        $updateOrder = Order::select("step_level","total_quantity","tolerance_volume","tolerance_perc")->where($whereCondition)->first();

//if(count($updateOrder)>0){
        $updOrderAry=$qty_arr=[];
        $price_change=$tol_change=0;
        $updOrderAry['category_id'] = $request->category_id;
        $updOrderAry['fabric_id']  = $request->fabric_id;
        $updOrderAry['article_id']  = $request->article_id;

        $updOrderAry['no_of_deliverys']  = $request->no_of_deliverys;
        //if($request->order_price!=''){
            if($request->staff_id>0){
                $per = CommonApp::checkStaffPermission($request,'41');
                if($per===1){
                    $updOrderAry['order_price']  = $request->order_price;
                }

            }else{
        $updOrderAry['order_price']  = $request->order_price;
            }
        $updOrderAry['currency_type']  = $request->currency_type;
        $updOrderAry['units']  = $request->units;
        $updOrderAry['inquiry_date']  = $request->inquiry_date;
       // }
        if($updateOrder->step_level==1){
            $updOrderAry['total_quantity']  = $request->total_quantity;
            $updOrderAry['tolerance_volume']  = $request->tolerance_volume;
            $updOrderAry['tolerance_perc']  = $request->tolerance_perc;
            $updOrderAry['quantity_wise']  = $request->quantity_wise;
            if($request->is_tolerance_req==1){
                $is_tol_req="1";
            }else{
                $is_tol_req="0";
            }
            $updOrderAry['is_tolerance_req']  =$is_tol_req;
        }else{
            if($request->total_quantity != $updateOrder->total_quantity)
                $price_change=1;
            if($request->tolerance_volume != $updateOrder->tolerance_volume)
                $tol_change=1;
            if($request->tolerance_perc != $updateOrder->tolerance_perc)
                $tol_change=1;

            $qty_arr['total_quantity']  = $request->total_quantity;
            $qty_arr['tolerance_volume']  = $request->tolerance_volume;
            $qty_arr['tolerance_perc']  = $request->tolerance_perc;
            $qty_arr['is_tolerance_req']  =$request->is_tolerance_req;
        }

        $updOrderAry['income_terms']  = $request->income_terms;
        $updOrderAry['order_priority'] =$request->order_priority??null;
       // $updOrderAry['delivery_date'] =$request->delivery_date??null;

        Order::where($whereCondition)->update($updOrderAry);
        MultipleDeliveryDates::updateMultipleDeliveryDates($request,$request->order_id,$request->delivery_dates);

        /* Order Log creation starts*/
        $logArry = array();
        $logArry['order_id'] =$request->order_id;
        $logArry['company_id'] = $request->company_id;
        $logArry['workspace_id'] = $request->workspace_id;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] = 'Edit';
        $logArry['comments'] = $request->comments ?? '';
        $logArry['before_values'] = json_encode($request->before_values) ?? '';
        $logArry['after_values'] = json_encode($request->after_values) ?? '';
        Orderlog::insert($logArry);
        /* Order Log creation end*/

        if($price_change==0 && $tol_change==0){
            $res = json_encode([ "status_code"=>200,"qty_status"=>1,"status"=>"Success","message"=>"Order updated successfully","data"=>$updOrderAry,"qty_arr"=>$qty_arr]);
        }else if($price_change==1 && $tol_change==1){
            $res = json_encode([ "status_code"=>200,"qty_status"=>2,"status"=>"Success","message"=>"Total Quantity and Tolerance value changed. Please update the SKU details for the Updated quantity",
            "data"=>$updOrderAry,"qty_arr"=>$qty_arr]);
        }else if($price_change==1 && $tol_change==0){
            $res = json_encode([ "status_code"=>200,"qty_status"=>3,"status"=>"Success","message"=>"Total Quantity value changed. Please update the SKU details for the Updated quantity",
            "data"=>$updOrderAry,"qty_arr"=>$qty_arr]);
        }else if($price_change==0 && $tol_change==1){
            $res = json_encode([ "status_code"=>200,"qty_status"=>4,"status"=>"Success","message"=>"Tolerance value changed. Please update the SKU details for the Updated quantity",
            "data"=>$updOrderAry,"qty_arr"=>$qty_arr]);
        }

        return CommonApp::webEncrypt($res);
    // }else{
    //     return response()->json([ "status_code"=>401, "status"=>"error",
    //     "message"=>"Order Not updated"]);
    // }
    }

    /* Update the order Comments */
    public function updateComments( Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'order_id'=>'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $comments = OrderComments::select('video_url','audio_url','document_url')->where('order_id',$request->order_id)->first();
        $videoPath = $comments->video_url ?? NULL;
        $audioPath = $comments->audio_url ?? NULL;
        $documentPath = $comments->document_url ?? NULL;
        $doc=$audio=$video=0;
        if ($request->hasFile('document')) {
            $doc=1;
            if(isset($comments->document_url)){
                $comments->document_url = str_replace(config('app.public_url'),'',$comments->document_url);
                if(file_exists($comments->document_url))
                    unlink($comments->document_url);
            }
            $documentPath = $request->file('document')->store('document/'.$request->company_id.'/'.$request->workspace_id, 'public');
        }
        if ($request->hasFile('audio')) {
            $audio=1;
            if(isset($comments->audio_url)){
                $comments->audio_url = str_replace(config('app.public_url'),'',$comments->audio_url);
                if(file_exists($comments->audio_url))
                    unlink($comments->audio_url);
            }
            $audioPath = $request->file('audio')->store('audio/'.$request->company_id.'/'.$request->workspace_id, 'public');
        }
        if ($request->hasFile('video')) {
            $video=1;
            if(isset($comments->video_url)){
                $comments->video_url = str_replace(config('app.public_url'),'',$comments->video_url);
                if(file_exists($comments->video_url))
                    unlink($comments->video_url);
            }
            $videoPath = $request->file('video')->store('video/'.$request->company_id.'/'.$request->workspace_id, 'public');
        }

        try{
            $commentsArr = [];
            $commentsArr['company_id'] = $request->company_id;
            $commentsArr['workspace_id'] = $request->workspace_id;
            $commentsArr['order_id'] = $request->order_id;
            $commentsArr['comments'] = $request->comments;
            $commentsArr['document_url'] = ($doc==1) ? config('app.public_url').'storage/'.$documentPath : $documentPath;
            $commentsArr['audio_url'] = ($audio==1) ? config('app.public_url').'storage/'.$audioPath : $audioPath;
            $commentsArr['video_url'] = ($video==1) ? config('app.public_url').'storage/'.$videoPath : $videoPath;
            $commentsArr['reason'] = $request->reason ?? NULL;
            $commentsArr['updated_by_type'] = $request->staff_id > 0 ? "Staff" : "Admin";
            $commentsArr['updated_by_id'] = $request->staff_id > 0 ? $request->staff_id : $request->user_id;
            if($comments)
                OrderComments::where('order_id',$request->order_id)->update($commentsArr);
            else{
                $commentsArr['order_id'] = $request->order_id;
                $commentsArr['created_by_type'] = $request->staff_id > 0 ? "Staff" : "Admin";
                $commentsArr['created_by_id'] = $request->staff_id > 0 ? $request->staff_id : $request->user_id;
                $commentsArr['created_at'] = date('Y-m-d H:i:s');
                OrderComments::insert($commentsArr);
            }
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Comments Updated Successfully"]);
        }catch (Exception $e) {
            Log::info($e->getMessage());
            $res = json_encode(["status_code"=>401,"errors"=>$e->getMessage()]);
        }
        return $res;
    }

    /* Delete the order Comments file */                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
    public function deleteCommentsFile( Request $request)
    {
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'order_id'=>'required',
            'type'=> 'required',
        ]);
        if ($validator->fails()){
            $res = response()->json(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $comments = OrderComments::select('video_url','audio_url','document_url')->where('order_id',$request->order_id)->first();
        if ($request->type =='document') {
            $comments->document_url = str_replace(config('app.public_url'),'',$comments->document_url);
            if(file_exists($comments->document_url))
                unlink($comments->document_url);
            $commentsArr['document_url'] = NULL;
            OrderComments::where('order_id',$request->order_id)->update($commentsArr);
        }
        if ($request->type == 'audio') {
            $comments->audio_url = str_replace(config('app.public_url'),'',$comments->audio_url);
            if(file_exists($comments->audio_url))
                unlink($comments->audio_url);
            $commentsArr['audio_url'] = NULL;
            OrderComments::where('order_id',$request->order_id)->update($commentsArr);
        }
        if ($request->type == 'video') {
            $comments->video_url = str_replace(config('app.public_url'),'',$comments->video_url);
            if(file_exists($comments->video_url))
                unlink($comments->video_url);
            $commentsArr['video_url'] = NULL;
            OrderComments::where('order_id',$request->order_id)->update($commentsArr);
        }

        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"File Deleted Successfully"]);
        return CommonApp::webEncrypt($res);
    }
}
