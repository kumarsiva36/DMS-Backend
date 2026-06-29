<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use App\Enums\ConsolidatedEmail;

class EmailScheduleSettings extends Model
{
    use HasFactory;

    protected $table = 'email_schedule_notification';

    public static function addEmailScheduleNotification($request,$type=""){
        $getUserId=$request->userId;
        $getworkspaceId=$request->workspaceId;
        $getcompanyId=$request->companyId;
        $getstaffId=0;
        $getTaskDetails=$request->emailSchedule;
        //$companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);

        //$getTaskDetails=$request->emailSchedule;

        $whereConditionsDel = [
            ['user_id','=',$getUserId],
            ['workspace_id','=',$getworkspaceId],
            ['staff_id','=',$getstaffId],
           ['company_id','=',$getcompanyId]
        ];
        DB::beginTransaction();
        try{
            EmailScheduleSettings::where($whereConditionsDel)->delete();
            foreach($getTaskDetails as $taskDetails) {
               // dd($taskDetails->id);
                if($type === 'Mobile'){
                    $getEmailScheduleTaskId=$taskDetails->id;
                    $getEmailScheduleTaskDay=implode(",",$taskDetails->days);
                }else{
                    $getEmailScheduleTaskId=$taskDetails[0];
                    $getEmailScheduleTaskDay=implode(",",$taskDetails[1]);
                }
                $whereConditions = [
                    ['user_id',$getUserId],
                    ['workspace_id','=',$getworkspaceId],
                    ['staff_id','=',$getstaffId],
                    ['email_schedule_task_id','=',$getEmailScheduleTaskId],
                    ['company_id','=',$getcompanyId]
                ];

                $aldreadyExists = EmailScheduleSettings::where($whereConditions)->first();
                //return response()->json($aldreadyExists);
                if(empty($aldreadyExists)){
                    $emailScheduleNotificationArr=[];
                    $emailScheduleNotificationArr['company_id']= $getcompanyId;
                    $emailScheduleNotificationArr['workspace_id']= $getworkspaceId;
                    $emailScheduleNotificationArr['user_id']= $getUserId;
                    $emailScheduleNotificationArr['staff_id']= $getstaffId ;
                    $emailScheduleNotificationArr['email_schedule_task_id']=$getEmailScheduleTaskId ;
                    $emailScheduleNotificationArr['name']= '';
                    $emailScheduleNotificationArr['email_to_user_id']= $getUserId;
                    $emailScheduleNotificationArr['email_to_staff_id']=$getstaffId;
                    $emailScheduleNotificationArr['days']= $getEmailScheduleTaskDay;
                    $emailScheduleNotificationArr['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
                    $emailScheduleNotificationArr['created_at']=  date('Y-m-d H:i:s');
                    $emailScheduleNotificationArr['updated_at']=  date('Y-m-d H:i:s');
                    EmailScheduleSettings::insert($emailScheduleNotificationArr);

                // return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Email Schedule Settings Updated Successfully"]);
                }
                else{
                    $emailScheduleNotificationUpd=[];
                    $emailScheduleNotificationUpd['company_id']= $getcompanyId;
                    $emailScheduleNotificationUpd['workspace_id']= $getworkspaceId;
                    $emailScheduleNotificationUpd['user_id']= $getUserId;
                    $emailScheduleNotificationUpd['staff_id']= $getstaffId ;
                    $emailScheduleNotificationUpd['email_schedule_task_id']=$getEmailScheduleTaskId ;
                    $emailScheduleNotificationUpd['name']= '';
                    $emailScheduleNotificationUpd['email_to_user_id']= $getUserId;
                    $emailScheduleNotificationUpd['email_to_staff_id']=$getstaffId;
                    $emailScheduleNotificationUpd['days']= $getEmailScheduleTaskDay;
                    $emailScheduleNotificationArr['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
                    $emailScheduleNotificationUpd['updated_at']=  date('Y-m-d H:i:s');
                    EmailScheduleSettings::where($whereConditions)->update($emailScheduleNotificationUpd);

                // return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Email Schedule Settings Updated Successfully"]);
                }
            }
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException('Unable to post Data');
        }
        DB::commit();
    }

    public static function getEmailScheduleSettings($whereConditions){
        $emailSettings = EmailScheduleSettings::where($whereConditions)
                        ->select('company_id','workspace_id','user_id','email_schedule_task_id','name','email_to_user_id','days','is_consolidated_mail')
                        ->get();
        return $emailSettings;
    }
}
