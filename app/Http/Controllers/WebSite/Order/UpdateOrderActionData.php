<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\UpdateOrderAction as updateorder;
use App\Models\Orderlog;
use App\Common\CommonApp;
use App\Common\NotificationAddition;
use App\Common\NotificationText;

class UpdateOrderActionData extends Controller
{
    /**
     * Handle the incoming request.
     * Update the order status
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'user_id' => 'required',
            'reason' => 'required',
            'order_action' => 'required',

        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $status=strtolower($request->order_action);

        if(isset($request->staff_id) && $request->staff_id > 0){
            $per_id =0;
            if($status=='delete') //Check if staff have permission to delete orders
                $per_id =45;
            else if($status=='cancel') //Check if staff have permission to cancel orders
                $per_id =44;
            else if($status=='complete') //Check if staff have permission to complete orders
                $per_id =43;
            if($per_id > 0){
                $per = CommonApp::checkStaffPermission($request,$per_id);
                if($per===0){
                    return CommonApp::checkStaffPermissionResponse();
                }
            }
        }

        $orderId=$request->order_id;
        $orderNo=$request->order_no;
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['id','=',$orderId],
            ['status','=',1],
        ];
        $alreadyExists = Order::where($whereConditions)->get();
        if(!empty($alreadyExists)){
            $updArray=[];

            $status_vl=0;
            if(isset($request->staff_id) && $request->staff_id>0){
                $updArray['action_done_staff_id']=$request->staff_id;
                $updArray['action_done_user_id']=0;
                $updArray['action_done_at']=date('Y-m-d');
            }else{
                $updArray['action_done_staff_id']=0;
                $updArray['action_done_user_id']=$request->user_id;
                $updArray['action_done_at']=date('Y-m-d');
            }
            if(strtolower($status)=='delete'){
                $status_vl=3;
                $updArray['status']="3";

            }else if(strtolower($status)=='cancel'){
                $status_vl=10;
                $updArray['status']="10";
            }
            else if(strtolower($status)=='close'){
                $status_vl=11;
                $updArray['status']="11";
            }
            else if(strtolower($status)=='complete'){
                $status_vl=12;
                $updArray['status']="12";
                $updArray['completed_on']=date('Y-m-d');
            }
            if($status_vl>0){

            $updArray['updated_at']=date("Y-m-d H:i:s");
            Order::where('id',$orderId)->update($updArray);
            if($status=='cancel' || $status=='delete'  || $status=='close' || $status=='complete'){
                $ordActionArr = [];
                $ordActionArr['reason']=$request->reason;
                $ordActionArr['company_id']=$request->company_id;
                $ordActionArr['user_id']=$request->user_id;
                $ordActionArr['workspace_id']= $request->workspace_id;
                $ordActionArr['staff_id']=$request->staff_id;
                $ordActionArr['order_id']=$orderId;
                $ordActionArr['order_no']=$orderNo;
                $ordActionArr['action_type']=$status;
                $ordActionArr['created_at']= date('Y-m-d H:i:s');
                $ordActionArr['updated_at']= date("Y-m-d H:i:s");
                updateorder::insert($ordActionArr);
            }
            /* Notification Starts */
            if($status=='cancel' || $status=='delete'){
                $notificationData=[];
                $notificationData['company_id']=$request->company_id;
                $notificationData['workspace_id']=$request->workspace_id;
                $notificationData['user_id']=$request->user_id;
                $notificationData['staff_id'] =$request->staff_id ?? 0;
                $notificationData['order_id']=$orderId;
                if($status == 'cancel'){
                    $notificationData['notification_type']="OrderCancelled";
                }
                if($status == 'delete'){
                    $notificationData['notification_type']="OrderDeleted";
                }
                $notiTexts['orderId'] = $orderId;
                $notiTexts['status'] =$status;
                $notificationData['texts'] = NotificationText::toGetOrderStatus($notiTexts);
                NotificationAddition::addNotifications($notificationData,$notiTexts);
            }
            /* Notification Ends */
            /* Order Log creation starts*/
            $logArry = array();
            $logArry['order_id'] =$orderId;
            $logArry['company_id'] = $request->company_id;
            $logArry['workspace_id'] = $request->workspace_id;
            $logArry['staff_id'] =$request->staff_id ?? 0;
            $logArry['user_id'] = $request->user_id ?? 0;
            $logArry['action'] = ucfirst($status);
            $logArry['before_values'] = $request->reason;
            Orderlog::insert($logArry);
            /* Order Log creation end*/

            $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Order Update Successfully"],200);
            return CommonApp::webEncrypt($res);
            }else{
                $res = json_encode(["status_code"=>400,'status'=>"failed","message"=>"Order Not Update"],400);
                return CommonApp::webEncrypt($res);
            }

        }else{
            $res = json_encode(["status_code"=>400,'status'=>"failed","message"=>"Order Already Deleted"],400);
            return CommonApp::webEncrypt($res);
        }
    }
}
