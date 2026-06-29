<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Orderlog;
use App\Models\OrderSubTask;
use App\Models\OrderTask;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubTask extends Controller
{

    /* Add New Subtask  */
    public static function addSubTask(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'taskId' => 'required',
            'cat_title' => 'required',
            'task_title' => 'required',
            'subtask_title' => 'required',
            'userId' => 'required',
            'template_id' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions2=[
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=', $request->company_id],
            ['id','=',$request->taskId]
        ];
        $whereConditions1=[
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=', $request->company_id],
            ['parent_task_id','=',$request->taskId],
            ['subtask_title','=',trim($request->subtask_title)],
            ['is_subtask','=',1],
        ];
        $isSubtaskOfSameName = OrderTask::where($whereConditions1)->get();
        if (count($isSubtaskOfSameName) > 0) {
            $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"Please Enter Different Subtask Title Name"]);
            return CommonApp::webEncrypt($res);
        }
        $parentTask = OrderTask::where($whereConditions2)->first();
        if(isset($request->startDate) && isset($request->endDate)){
            if($parentTask->task_schedule_start_date != null && $parentTask->task_schedule_end_date != null){
                if( strtotime($request->startDate) < strtotime($parentTask->task_schedule_start_date)
                || strtotime($request->endDate) > strtotime($parentTask->task_schedule_end_date)){
                    $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"The Dates Should Be In The Range of Parent Task Dates"]);
                    return CommonApp::webEncrypt($res);
                }
            }else{
                $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"Please Enter Main Task Dates"]);
                return CommonApp::webEncrypt($res);
            }
        }
        try{
            OrderSubTask::addSubTask($request);
            $res = json_encode(["status_code"=>200,"status"=>"success","message"=>"Sub Task Added Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"errors"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Delete Subtask */
    public static function deleteSubTask(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            // 'order_id' => 'required',
            'taskId' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to Delete Sub task
            $per = CommonApp::checkStaffPermission($request,'38');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        try{
            OrderSubTask::deleteSubTask($request);

            $res = json_encode(["status_code"=>200,"message"=>"Deleted Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>600,"errors"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }
}
