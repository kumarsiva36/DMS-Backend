<?php

namespace App\Http\Controllers\WebSite\Order\EditOrders;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EditTasks extends Controller
{
    /* To update the Tasks in the templates */
    public static function editTaskData(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $request->template_data = json_decode(json_encode($request->template_data), true);
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'template_id' => 'required',
            'template_data'=>'required|array',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id]
        ];

        $aldreadyExists = OrderTask::where($whereConditions)->get();
        if(!empty($aldreadyExists)){
            OrderTask::where($whereConditions)->delete();
        }
        /******************** To add template id in ORDERS Table ******************/
        $addTemplateToOrder = Order::where('id',$request->order_id)->first();
        $addTemplateToOrder->order_task_template = $request->template_id;
        $addTemplateToOrder->save();

        $orderProductionArr = [];
        $orderProductionArr['user_id']= $companyDetails->user_id;
        $orderProductionArr['company_id']= $request->company_id;
        $orderProductionArr['workspace_id']= $request->workspace_id;
        $orderProductionArr['staff_id']= $request->staff_id??'0';
        $orderProductionArr['order_id']= $request->order_id;
        $orderProductionArr['template_id']= $request->template_id;
        $orderProductionArr['created_by']= $companyDetails->user_id;
        $orderProductionArr['created_user_type']= "User";
        $orderProductionArr['task_accomplished_date']= $request->task_accomplished_date??NULL;
        $orderProductionArr['reschedule_reason']= $request->reschedule_reason??'';
        $orderProductionArr['reschedule_order_task_data_id']= $request->reschedule_order_task_data_id??'0';
        $orderProductionArr['rescheduled']= $request->rescheduled??null;
        $orderProductionArr['category_contacts']= $request->category_contacts??'';
        $orderProductionArr['task_contacts']= $request->task_contacts??'';
        // foreach($request->template_data as $templates){
            foreach($request->template_data as $key=>$template){
                $orderProductionArr['cat_title']= $key;
                foreach($template as $data){
                    $orderProductionArr['task_title']= $data['title'];
                    $orderProductionArr['task_schedule_start_date']= $data['startdate'] != "" ? date('Y-m-d',strtotime($data['startdate'])) : NULL;
                    $orderProductionArr['task_schedule_end_date']= $data['enddate'] != "" ? date('Y-m-d',strtotime($data['enddate'])) : NULL;
                    $orderProductionArr['task_pic']= $data['pic_id'] != "" ? $data['pic_id'] : "0";
                    $orderProductionArr['created_at']=date('Y-m-d H:i:s');
                    $orderProductionArr['updated_at']=date('Y-m-d H:i:s');
                    OrderTask::insert($orderProductionArr);
                }
            }
        // }
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Task Data updated Successfully"],200);
        return CommonApp::webEncrypt($res);
    }
}
