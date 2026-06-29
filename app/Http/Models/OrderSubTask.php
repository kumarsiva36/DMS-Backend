<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderSubTask extends Model
{
    use HasFactory;

    public static function addSubTask($request){
        $subtaskArr = [];
        $subtaskArr['user_id']= $request->userId;
        $subtaskArr['company_id']= $request->company_id;
        $subtaskArr['workspace_id']= $request->workspace_id;
        $subtaskArr['staff_id']= $request->staffId ?? 0;
        $subtaskArr['order_id']= $request->order_id;
        $subtaskArr['template_id']= $request->template_id;
        $subtaskArr['created_by']= $request->userId;
        $subtaskArr['created_user_type']= "User";
        $subtaskArr['task_accomplished_date']= $request->task_accomplished_date??null;
        $subtaskArr['reschedule_reason']= $request->reschedule_reason ?? '';
        $subtaskArr['reschedule_order_task_data_id']= $request->reschedule_order_task_data_id ?? 0;
        $subtaskArr['rescheduled']= $request->rescheduled??null;
        $subtaskArr['category_contacts']= $request->category_contacts ?? '';
        $subtaskArr['task_contacts']= $request->task_contacts ?? '';
        $subtaskArr['cat_title']=$request->cat_title;
        $subtaskArr['task_title']=$request->task_title;
        $subtaskArr['subtask_title']=$request->subtask_title;
        $subtaskArr['parent_task_id']=$request->taskId;
        $subtaskArr['is_subtask']=1;
        $subtaskArr['task_schedule_start_date']= isset($request->startDate) && $request->startDate!=""?
        date('Y-m-d',strtotime($request->startDate)) : null;
        $subtaskArr['actual_start_date']= isset($request->startDate) && $request->startDate!=""?
        date('Y-m-d',strtotime($request->startDate)) : null;
        $subtaskArr['task_schedule_end_date']= isset($request->endDate)&& $request->endDate!=""?
        date('Y-m-d',strtotime($request->endDate)) : null;
        $subtaskArr['task_pic']= isset($request->picId)? $request->picId:0;
        $subtaskArr['created_at']=date('Y-m-d H:i:s');
        $subtaskArr['updated_at']=date('Y-m-d H:i:s');
        DB::beginTransaction();
        try{
            OrderTask::insert($subtaskArr);
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException("Unable to Post Data");
        }
        DB::commit();
    }

    public static function deleteSubTask($request){
        $whereCondition = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['id','=',$request->taskId],
            ['is_subtask','=',1]
        ];
        $theSubtask = OrderTask::where($whereCondition)->first();
        // if(($theSubtask->task_schedule_start_date == null && $theSubtask->task_schedule_end_date == null
        // && $theSubtask->task_pic == 0) /* || $theSubtask->actual_start_date === null */   ){
        if(($theSubtask->task_accomplished_date == null )){
            DB::beginTransaction();
            try{
                $theSubtask->delete();

                $deleteTaskName = $theSubtask->subtask_title;
                $reason = $request->reason ?? '-';
                $logArry = array();
                $logArry['order_id'] =$request->order_id ?? 0;
                $logArry['company_id'] = $request->company_id ?? 0;
                $logArry['workspace_id'] = $request->workspace_id ?? 0;
                $logArry['staff_id'] =$request->staff_id ?? 0;
                $logArry['user_id'] = $request->user_id ?? 0;
                $logArry['action'] = "Delete";
                $logArry['before_values'] = json_encode($theSubtask,true);
                $logArry['after_values'] = "Delete SubTask :".$deleteTaskName.", Deleted Sub Task Line Number ".$request->taskId.", Reason :".$reason;
                Orderlog::insert($logArry);

            }catch(Exception $e){
                DB::rollBack();
                throw new InvalidArgumentException("Unable to Post Data");
            }
            DB::commit();
        }else{
            throw new InvalidArgumentException("Could not delete the task");
        }
    }
}
