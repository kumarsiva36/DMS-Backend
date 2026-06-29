<?php

namespace App\Common;

use App\Models\CompanySettings;
use App\Models\PaymentHistory;
use App\Models\UpdateSkuQuantityLog;
use App\Models\User;
use App\Models\UserHistory;
use App\Models\UserPlanHistory;

class Logs
{


        // public static function insertPaymentHistory($paymentHistory)
        // {
        //     PaymentHistory::insert($paymentHistory);
        // }

    /* This is to Log the user/staff login and Logout records */
    static function userLogs($userDetails){
        $userHistory = [];
        $userHistory['logging_user_id'] = $userDetails['userID'];
        $userHistory['logging_user_name'] = $userDetails['userName'];
        $userHistory['ipaddress'] = $userDetails['ipAddress'];
        $userHistory['browser_details'] = $userDetails['browserDetails'];
        $userHistory['login_status'] = $userDetails['loginStatus'];
        $userHistory['login_user_type'] = $userDetails['logUserType'] ;
        $userHistory['log_type'] = $userDetails['logType'];
        $userHistory['logging_in_datetime'] = date("Y-m-d H:i:s");
        // $userHistory['logged_out_datetime'] = date("Y-m-d H:i:s");
        $userHistory['created_at'] = date("Y-m-d H:i:s");
        $userHistory['updated_at'] = date("Y-m-d H:i:s");
        if(!empty($userDetails['companyID'])){
            $userHistory['company_id'] = $userDetails['companyID'];
        }
        UserHistory::insert($userHistory);
    }

    static function userPlanHistoryLog($awsCompanyPath,$planPaymentDetails,$planDetails,$userDetails){
        $company = CompanySettings::where('user_id',$userDetails['id'])->first();
        $updateInUsers=User::where('id',$userDetails['id'])->first(); // To add company_id to the Users Table
        $updateInUsers->company_id = $company['id'];
        $updateInUsers->save();
        $companyDetailsArr=[];
        $companyDetailsArr['company_id']  = $company['id'] ;
        $companyDetailsArr['user_id'] = $userDetails->id ;
        $companyDetailsArr['workspace_id'] = 0 ;
        $companyDetailsArr['user_name'] = $userDetails->name ;
        $companyDetailsArr['user_email'] =$userDetails->email ;
        $companyDetailsArr['language'] =$userDetails->lang_code ;
        $companyDetailsArr['plan_name'] =$planDetails->plan_name ;
        $companyDetailsArr['no_of_month'] = $planDetails->no_of_month ;
        $companyDetailsArr['no_of_days'] = $planDetails->no_of_days;
        $companyDetailsArr['price'] = $planDetails->price ;
        $companyDetailsArr['special_price'] = $planDetails->special_price ;
        $companyDetailsArr['currency'] = "currency" ;
        $companyDetailsArr['aws_s3_path'] = $awsCompanyPath;
        $companyDetailsArr['purchased_plan_id'] = $planPaymentDetails->id ;
        $companyDetailsArr['purchased_plan_name'] = $planPaymentDetails->plan_name ;
        $companyDetailsArr['purchased_plan_type'] = $planPaymentDetails->plan_type ;
        $companyDetailsArr['purchased_plan_price'] = $planPaymentDetails->plan_price ;
        $companyDetailsArr['purchased_plan_price_currency'] = $planPaymentDetails->payment_currency ;
        $companyDetailsArr['plan_purchase_at'] = $planPaymentDetails->payment_date ;
        $companyDetailsArr['status'] = '1';
        $date = date('Y-m-d H:i:s');
        $planExpiryDays = $planDetails->no_of_days;
        $companyDetailsArr['account_activated_at'] = $date;
        $companyDetailsArr['account_expire_at'] = date('Y-m-d H:i:s', strtotime($date. '+'.$planExpiryDays.' days'));
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
        UserPlanHistory::insert($companyDetailsArr);
    }

    static function insertSKUDataInputHistory($request,$skus,$skuData,$type,$header){
        $SKUArr=[];
        $SKUArr['company_id']=$request->company_id;
        $SKUArr['workspace_id']=$request->workspace_id;
        $SKUArr['user_id']=$request->user_id ?? 0;
        $SKUArr['staff_id']=$request->staff_id ?? 0;
        $SKUArr['order_id']=$request->order_id;
        $SKUArr['color_id']=$skus['color_id'];
        $SKUArr['size_id']=$skus['size_id'];
        $SKUArr['updated_quantity']=$skus['quantity'];
        $SKUArr['sku_date']=date('Y-m-d',strtotime($request->date));
        $SKUArr['type_of_production']=$request->type_of_production;
        $SKUArr['sku_id']=$skuData->id;
        if($type === "Web"){
            $SKUArr['device_details']=$header->header('User-Agent');
        }
        if($type === "Mobile"){
            $detailsArr=[];
            $detailsArr['device_id'] = $header->header('device-id');
            $detailsArr['platform'] = $header->header('platform');
            $detailsArr['os_version'] = $header->header('os-version');
            $detailsArr['app_version'] = $header->header('app-version');
            $detailsArr['mobile_model'] = $header->header('mobile-model');
            $SKUArr['device_details']=json_encode($detailsArr);
        }
        $SKUArr['created_at']=date('Y-m-d H:i:s');
        $SKUArr['updated_at']=date('Y-m-d H:i:s');
        UpdateSkuQuantityLog::insert($SKUArr);
    }
}
