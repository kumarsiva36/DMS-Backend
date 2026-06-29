<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class UserPlanHistory extends Model
{
    use HasFactory;

    protected $table = 'user_plan_history_log' ;

    /* Get Plan Validation necessary data */
    public static function planValidation($request){
        $data=[];
        $data['workspaceCount'] = Workspace::where('company_id',$request->company_id)->count();
        $data['userCount'] = Staff::where('company_id',$request->company_id)->where('workspace_id',$request->workspace_id)->count();
        $data['styleCount'] = Order::where('company_id',$request->company_id)->where('workspace_id',$request->workspace_id)
        ->where("status","!=",'3')->count();
        $data['orderCount'] = count(Order::where('company_id',$request->company_id)->where('workspace_id',$request->workspace_id)
        ->where("status","!=",'3')->select('order_no')->groupBy('order_no')->get());

        $data['planSubscribed'] = CompanySettings::where('user_id',  $request->user_id)
                        ->select('purchased_plan_id', 'purchased_plan_name', 'purchased_plan_type',
                                 'no_of_group', 'no_of_style', 'no_of_user','no_of_workspace',
                                 'plan_purchase_at as purchasedAt', 'account_expire_at as accountExpired')
                        ->first();
        $data['planSubscribedDates'] = CompanySettings::where('user_id', $request->user_id)
        ->select('plan_purchase_at as purchasedAt', 'account_expire_at as accountExpired')
        ->orderBy('id','DESC')
        ->first();

        return $data;
    }

    /* Plan Upgrade */
    public static function upgradePlan($request,$previousPlanDetails){
        $planPaymentDetails = PaymentHistory::getPlanDetailsByUserID($request->user_id);
        $planDetails = Plan::getPlanDetails($planPaymentDetails->plan_id);
        $userDetails = User::getUserByID($request->user_id);
        $company = CompanySettings::where('id',$request->company_id)->first();
        $whereCondition = [
            ['id','=',$request->company_id]
        ];
        $planValidity = Plan::getPlanRemainingDays($whereCondition);
        $companyDetailsArr=[];
        $companyDetailsArr['company_id']  = $company->id ;
        $companyDetailsArr['user_id'] = $userDetails->id ;
        $companyDetailsArr['workspace_id'] = 0 ;
        $companyDetailsArr['user_name'] = $userDetails->name ;
        $companyDetailsArr['user_email'] =$userDetails->email ;
        $companyDetailsArr['language'] =$userDetails->lang_code ;
        $companyDetailsArr['plan_name'] =$planDetails->plan_name ;
        $companyDetailsArr['plan_type'] =$planDetails->type ;
        $companyDetailsArr['no_of_month'] = $planDetails->no_of_month ;
        $companyDetailsArr['no_of_days'] = $planDetails->no_of_days;
        $companyDetailsArr['price'] = $planDetails->price ;
        $companyDetailsArr['special_price'] = $planDetails->special_price ;
        $companyDetailsArr['currency'] = "currency" ;
        $companyDetailsArr['aws_s3_path'] = $company->aws_s3_path;
        $companyDetailsArr['purchased_plan_id'] = $planPaymentDetails->id ;
        $companyDetailsArr['purchased_plan_name'] = $planPaymentDetails->plan_name ;
        $companyDetailsArr['purchased_plan_type'] = $planPaymentDetails->plan_type ;
        $companyDetailsArr['purchased_plan_price'] = $planPaymentDetails->plan_price ;
        $companyDetailsArr['purchased_plan_price_currency'] = $planPaymentDetails->payment_currency ;
        $companyDetailsArr['plan_purchase_at'] = $planPaymentDetails->payment_date ;
        $companyDetailsArr['status'] = '1';
        $planExpiryDays = $planDetails->no_of_days;
        $date = date('Y-m-d H:i:s');
        if($planValidity == "-0" || $planValidity < 0){
            $company->purchased_plan_id = $planPaymentDetails->plan_id ;
            $company->purchased_plan_name = $planPaymentDetails->plan_name ;
            $company->purchased_plan_type = $planPaymentDetails->plan_type ;
            $company->purchased_plan_price = $planPaymentDetails->plan_price ;
            $company->purchased_plan_price_currency = $planPaymentDetails->payment_currency ;
            $company->plan_purchase_at = $planPaymentDetails->payment_date ;
            $company->account_activated_at = $date;
            $company->account_expire_at = date('Y-m-d H:i:s', strtotime($date.'+'.$planExpiryDays.' days'));
            $company->no_of_group = $planDetails->no_of_group;
            $company->no_of_user = $planDetails->no_of_user;
            $company->no_of_style = $planDetails->no_of_style;
            $company->no_of_workspace = $planDetails->no_of_workspace;
            $company->max_storage_size = $planDetails->max_storage_size;
            $company->report_range = $planDetails->report_range;
            $company->download_report = $planDetails->download_report;
        }else{
            if($previousPlanDetails->plan_id === $planPaymentDetails->plan_id){
                $company->plan_purchase_at = $planPaymentDetails->payment_date ;
                $company->account_activated_at = $date;
                $company->account_expire_at = date('Y-m-d H:i:s', strtotime($date.'+'.$planExpiryDays.' days'));
            }
            else if($planPaymentDetails->plan_id > $previousPlanDetails->plan_id){
                $company->purchased_plan_id = $planPaymentDetails->plan_id ;
                $company->purchased_plan_name = $planPaymentDetails->plan_name ;
                $company->purchased_plan_type = $planPaymentDetails->plan_type ;
                $company->purchased_plan_price = $planPaymentDetails->plan_price ;
                $company->purchased_plan_price_currency = $planPaymentDetails->payment_currency ;
                $company->plan_purchase_at = $planPaymentDetails->payment_date ;
                $company->account_activated_at = $date;
                $company->account_expire_at = date('Y-m-d H:i:s', strtotime($date.'+'.$planExpiryDays.' days'));
                $company->no_of_group = $planDetails->no_of_group;
                $company->no_of_user = $planDetails->no_of_user;
                $company->no_of_style = $planDetails->no_of_style;
                $company->no_of_workspace = $planDetails->no_of_workspace;
                $company->max_storage_size = $planDetails->max_storage_size;
                $company->report_range = $planDetails->report_range;
                $company->download_report = $planDetails->download_report;
            }
        }
        $company->save();
        $companyDetailsArr['status'] = '1';
        $companyDetailsArr['account_activated_at'] = $date;
        $companyDetailsArr['account_expire_at'] = date('Y-m-d H:i:s', strtotime($date.'+'.$planExpiryDays.' days'));
        $companyDetailsArr['no_of_group'] = $planDetails->no_of_group;
        $companyDetailsArr['no_of_user'] = $planDetails->no_of_user;
        $companyDetailsArr['no_of_style'] = $planDetails->no_of_style;
        $companyDetailsArr['max_storage_size'] = $planDetails->max_storage_size;
        $companyDetailsArr['report_range'] = $planDetails->report_range;
        $companyDetailsArr['download_report'] = $planDetails->download_report;
        $companyDetailsArr['notify_email_upcoming_task'] = $planDetails->notify_email_upcoming_task ;
        $companyDetailsArr['notify_email_delayed_task'] = $planDetails->notify_email_delayed_task;
        $companyDetailsArr['notify_whatsapp_upcoming_task'] = $planDetails->notify_whatsapp_upcoming_task;
        $companyDetailsArr['notify_whatsapp_delayed_task'] = $planDetails->notify_whatsapp_delayed_task ;
        $companyDetailsArr['notify_linemessenger_upcoming_task'] = $planDetails->notify_linemessenger_upcoming_task;
        $companyDetailsArr['notify_linemessenger_delayed_task'] = $planDetails->notify_linemessenger_delayed_task;
        $companyDetailsArr['created_at'] = date("Y-m-d H:i:s");
        $companyDetailsArr['updated_at'] = date("Y-m-d H:i:s");
        DB::beginTransaction();
        try{
            UserPlanHistory::insert($companyDetailsArr);
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException('Unable to post Data');
        }
        DB::commit();
    }
}
