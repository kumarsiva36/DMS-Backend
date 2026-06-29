<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class EmailScheduleReport extends Model
{
    use HasFactory;

    protected $table = 'email_schedule_report_orderid';

    public static function addEmailScheduleReportNotification($request,$type=""){
        $getUserId=$request->userId;
        $getworkspaceId=$request->workspaceId;
        $getcompanyId=$request->companyId;
        $getstaffId=$request->staffId;
        $taskId=$request->task_id;

        $whereConditionsDel = [
            ['user_id','=',$getUserId],
            ['workspace_id','=',$getworkspaceId],
            ['staff_id','=',$getstaffId],
            ['company_id','=',$getcompanyId],
            ['email_schedule_task_id','=',$taskId]
        ];
        DB::beginTransaction();
        try{
            EmailScheduleReport::where($whereConditionsDel)->delete();
            $order_ids = implode(',',$request->order_ids);
            $emailScheduleNotificationArr=[];
            $emailScheduleNotificationArr['company_id']= $getcompanyId;
            $emailScheduleNotificationArr['workspace_id']= $getworkspaceId;
            $emailScheduleNotificationArr['user_id']= $getUserId;
            $emailScheduleNotificationArr['staff_id']= $getstaffId ;
            $emailScheduleNotificationArr['email_schedule_task_id']=$taskId ;
            $emailScheduleNotificationArr['order_ids']= $order_ids;
            EmailScheduleReport::insert($emailScheduleNotificationArr);
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException('Unable to post Data');
        }
        DB::commit();
    }
    public static function getEmailScheduleReportSettings($request){
        $getUserId=$request->userId;
        $getworkspaceId=$request->workspaceId;
        $getcompanyId=$request->companyId;
        $getstaffId=$request->staffId;
        $taskId=$request->task_id;

        $whereConditions = [
            ['user_id','=',$getUserId],
            ['workspace_id','=',$getworkspaceId],
            ['staff_id','=',$getstaffId],
            ['company_id','=',$getcompanyId],
            ['email_schedule_task_id','=',$taskId]
        ];
        $emailSettings = EmailScheduleReport::where($whereConditions)
                        ->select('company_id','workspace_id','user_id','email_schedule_task_id','order_ids')
                        ->get();
        return $emailSettings;
    }
}
