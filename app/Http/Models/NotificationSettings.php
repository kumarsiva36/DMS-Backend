<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use App\Enums\ConsolidatedEmail;

class NotificationSettings extends Model
{
    use HasFactory;

    protected $table = "notification_settings";

    public static function addNotificationSettings($request,$companyDetails,$whereConditions){
        DB::beginTransaction();
        try{
            $notificationAlreadyExists = NotificationSettings::where($whereConditions)->first();
            if(empty($notificationAlreadyExists)){
                $notificationSettingsArr=[];
                $notificationSettingsArr['company_id'] = $companyDetails->id;
                $notificationSettingsArr['workspace_id'] = $request->workspace_id;
                $notificationSettingsArr['user_id'] = $companyDetails->user_id;
                $notificationSettingsArr['staff_id'] = '0';
                $notificationSettingsArr['email_daily_reminder'] = $request->email_daily_reminder ?? '7' ;
                $notificationSettingsArr['email_weekly_reminder'] = $request->email_weekly_reminder ?? '7' ;
                $notificationSettingsArr['email_task_accomplishment'] = $request->email_task_accomplishment ?? '7' ;
                $notificationSettingsArr['email_task_reschedule'] = $request->email_task_reschedule ?? '7';
                $notificationSettingsArr['email_due_today'] = $request->email_due_today ?? '7';
                $notificationSettingsArr['email_due_tomorrow'] = $request->email_due_tomorrow ?? '7';
                $notificationSettingsArr['email_daily_schedule'] = $request->email_daily_schedule ?? '7';
                $notificationSettingsArr['whatsapp'] =  $request->whatsapp ?? '7';
                $notificationSettingsArr['linemessenger'] = $request->linemessenger ?? '7';
                $notificationSettingsArr['sms'] = $request->sms ?? '7';
                $notificationSettingsArr['backup'] =  $request->backup ?? '7';
                $notificationSettingsArr['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
                $notificationSettingsArr['created_at'] = date('Y-m-d H:i:s');
                $notificationSettingsArr['updated_at'] = date('Y-m-d H:i:s');
                NotificationSettings::insert($notificationSettingsArr);
            }
            else{
                $notificationAlreadyExists['company_id'] = $companyDetails->id;
                $notificationAlreadyExists['workspace_id'] = $request->workspace_id;
                $notificationAlreadyExists['user_id'] = $companyDetails->user_id;
                $notificationAlreadyExists['staff_id'] = '0';
                $notificationAlreadyExists['email_daily_reminder'] = $request->email_daily_reminder ?? '7';
                $notificationAlreadyExists['email_weekly_reminder'] = $request->email_weekly_reminder ?? '7';
                $notificationAlreadyExists['email_task_accomplishment'] = $request->email_task_accomplishment ?? '7';
                $notificationAlreadyExists['email_task_reschedule'] = $request->email_task_reschedule ?? '7';
                $notificationAlreadyExists['email_due_today'] = $request->email_due_today ?? '7';
                $notificationAlreadyExists['email_due_tomorrow'] = $request->email_due_tomorrow ?? '7';
                $notificationAlreadyExists['email_daily_schedule'] = $request->email_daily_schedule ?? '7';
                $notificationAlreadyExists['whatsapp'] =  $request->whatsapp ?? '7';
                $notificationAlreadyExists['linemessenger'] = $request->linemessenger ?? '7';
                $notificationAlreadyExists['sms'] = $request->sms ?? '7';
                $notificationAlreadyExists['backup'] =  $request->backup ?? '7';
                $notificationAlreadyExists['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
                $notificationAlreadyExists['created_at'] = date('Y-m-d H:i:s');
                $notificationAlreadyExists['updated_at'] = date('Y-m-d H:i:s');
                $notificationAlreadyExists->save();
            }
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException('Unable to post Data');
        }
        DB::commit();
    }

    public static function getNotificationSettings($whereConditions){
        $notificationSettings = NotificationSettings::where($whereConditions)->first();
        return $notificationSettings;
    }
}
