<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ConsolidatedEmail;

class EmailNotificationSettings extends Model
{
    use HasFactory;
    protected $table = 'email_notification_settings';

    public static function addEmailNotinficationSettings($request){
$getEmailsetting=json_decode($request->email_settings,true);
if(!empty($getEmailsetting)){
    foreach($getEmailsetting as $eSetting){
      
        if($request->staff_id>0){
            $whereConditions = [
                ['staff_id','=',$request->staff_id],
                ['workspace_id','=',$request->workspace_id],
                ['company_id','=',$request->company_id],
                ['order_type','=',$eSetting['order_type']]
            ];
        }else{
           
        $whereConditions = [
            ['user_id','=',$request->user_id],
            ['staff_id','=',0],
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$request->company_id],
            ['order_type','=',$eSetting['order_type']]
        ];
   
    }

       $getEmail=EmailNotificationSettings::where($whereConditions)->first();
        if(empty($getEmail)){
        $emailConfigArr=[];
        $emailConfigArr['company_id']=$request->company_id;
        $emailConfigArr['workspace_id']=$request->workspace_id;
        $emailConfigArr['user_id']=$request->user_id;
        $emailConfigArr['staff_id']=$request->staff_id?$request->staff_id:0;
        $emailConfigArr['notify_admin']=$eSetting['admin']?$eSetting['admin']:0;
        $emailConfigArr['order_type']=$eSetting['order_type'];
        $emailConfigArr['no_of_delays']=$eSetting['no_of_delays']!=''?json_encode($eSetting['no_of_delays'],true):'';
       // $emailConfigArr['email_ids']=$eSetting['emailids'];
       // $emailConfigArr['email_no_of_delays']=$eSetting['email_no_of_delays'];
        $emailConfigArr['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
        $emailConfigArr['created_at']=date('Y-m-d H:i:s');
        $emailConfigArr['updated_at']=date('Y-m-d H:i:s');
        
        EmailNotificationSettings::insert($emailConfigArr);
        }else{
            $emailConfigArr=[];
         $emailConfigArr['order_type']=$eSetting['order_type'];
         $emailConfigArr['notify_admin']=$eSetting['admin']?$eSetting['admin']:0;
        $emailConfigArr['no_of_delays']=$eSetting['no_of_delays']!=''?json_encode($eSetting['no_of_delays'],true):'';
       // $emailConfigArr['email_ids']=$eSetting['emailids'];
       // $emailConfigArr['email_no_of_delays']=$eSetting['email_no_of_delays']?$eSetting['email_no_of_delays']:0;
        $emailConfigArr['is_consolidated_mail']=$request->consolidated_mail==1?ConsolidatedEmail::Yes:ConsolidatedEmail::No;
        $emailConfigArr['updated_at']=date('Y-m-d H:i:s');
        EmailNotificationSettings::where($whereConditions)->update($emailConfigArr);
   
        }
    }
}
}
public static function ViewEmailNotificationSettings($request){
    if($request->staff_id>0){
        $whereConditions = [
            ['staff_id','=',$request->staff_id],
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$request->company_id]          
        ];
    }else{
    $whereConditions = [
        ['user_id','=',$request->user_id],
        ['staff_id','=',0],
        ['workspace_id','=',$request->workspace_id],
        ['company_id','=',$request->company_id]     
    ];
}
   return EmailNotificationSettings::where($whereConditions)->get();
}
}
