<?php

namespace App\Models;

use App\Common\CommonApp;
use App\Common\Logs;
use App\Common\Uploads;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Illuminate\Support\Facades\Storage;

class CompanySettings extends Model
{
    use HasFactory;

    protected $table = 'company_settings';
    protected $fillable = ['aws_s3_path','logo','company_name','user_id','contact_person','contact_number','address1','address2','city',
    'state','zipcode','country_id','account_no','ifsc_code','gst_number','pan_number','language','currency','timezone',
    'purchased_plan_id','purchased_plan_name','purchased_plan_price','purchased_plan_type','purchased_plan_price_currency','plan_purchase_at','status',
    'account_activated_at','account_expire_at','no_of_group','no_of_user','no_of_style','no_of_workspace',
    'max_storage_size','report_range','download_report','notify_email_upcoming_task','notify_email_delayed_task',
    'notify_whatsapp_upcoming_task','notify_whatsapp_delayed_task','notify_linemessenger_upcoming_task',
    'notify_linemessenger_delayed_task','created_at','updated_at'];

    /* Get when the plan expires for the company */
    public static function getExpiryInfo($data){
        $companyInfo = CompanySettings::where('id',$data->company_id)->select('account_expire_at')->first();
        return $companyInfo;
    }

    /* Get company Info- Using Company ID*/
    public static function getCompanyInfoUsingID($id){
      $company = CompanySettings::where('id',$id)->first();
      return  $company;
    }

    /* Get Company Info - using user Id */
    public static function getCompanyInfoUsingUserID($id){
      $company = CompanySettings::where('user_id',$id)->first();
      return  $company;
    }

    /* Company Registration */
    public static function companyRegisteration($request){
        DB::beginTransaction();
        try{
            $planPaymentDetails = PaymentHistory::getPlanDetailsByUserID($request->user_id);
            $planDetails = Plan::getPlanDetails($planPaymentDetails->plan_id);
            $userDetails = User::getUserByID($request->user_id);
            $companyDetailsArr = [];
            $awsCompanyPath = '';

            $companyAldreadyExists = CompanySettings::getCompanyInfoUsingUserID($userDetails->id);
            if(!empty($companyAldreadyExists)){
                return response()->json(["status_code"=>400,"message"=>"Company Already Exists"],400);
            }
            $awsCompanyPath = date("YmdHis").'_'.$request->user_id.'_'.substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'),1,11);
            if($request->hasfile('logo')){
                $logo = $request->file('logo');
                $logoName = time().'_'.$logo->getClientOriginalName();
                $filepath = $awsCompanyPath.'/Logo/'.$logoName;
                Uploads::companyLogoUpload($filepath,$logo);
                $companyDetailsArr['aws_s3_path'] = $awsCompanyPath ;
                $companyDetailsArr['logo'] = $filepath;
            }
            else{
                Uploads::companyCreateDirectory($awsCompanyPath);
                $companyDetailsArr['aws_s3_path'] = $awsCompanyPath;
                $companyDetailsArr['logo'] = '';
            }
            $companyDetailsArr['company_name']  = $request->company_name;
            $companyDetailsArr['user_id'] = $userDetails->id ;
            $companyDetailsArr['contact_person'] = $request->contact_person ;
            $companyDetailsArr['contact_number'] = $request->contact_number ;
            $companyDetailsArr['address1'] =trim($request->input('address1')) ;
            $companyDetailsArr['address2'] =$request->address2 ;
            $companyDetailsArr['city'] = $request->city ;
            $companyDetailsArr['state'] = $request->state;
            $companyDetailsArr['zipcode'] =$request->zipcode ;
            $companyDetailsArr['country_id'] = $request->country_id;
            $companyDetailsArr['account_no'] = $request->input('account_number','0');
            $companyDetailsArr['ifsc_code'] = $request->input('ifsc_code','0');
            $companyDetailsArr['gst_number'] = $request->input('gst_number','0');
            $companyDetailsArr['pan_number'] = $request->input('pan_number','0');
            $companyDetailsArr['language'] =$userDetails->lang_code ;
            $companyDetailsArr['currency'] = '' ;
            $companyDetailsArr['timezone'] = '' ;
            $companyDetailsArr['purchased_plan_id'] = $planPaymentDetails->plan_id ;
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
            $companyDetailsArr['no_of_workspace'] = $planDetails->no_of_workspace;
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
            $company = CompanySettings::create($companyDetailsArr);
            $company_id = $company->id;
            Logs::userPlanHistoryLog($awsCompanyPath,$planPaymentDetails,$planDetails,$userDetails);
        }catch(Exception $e){
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException('Unable to post Data');
        }
        DB::commit();
        return $company_id;
    }

    /* Get Company Details */
    public static function getCompanyDetails($request){
        $companyDetails = CompanySettings::where('company_settings.id',$request->company_id)
                        ->join('users','users.company_id','company_settings.id')
                        ->select('company_settings.company_name','company_settings.contact_person','company_settings.logo',
                        'company_settings.contact_number','company_settings.address1','company_settings.address2',
                        'company_settings.use_logo','company_settings.city','company_settings.state','company_settings.zipcode',
                        'company_settings.country_id','company_settings.account_no',
                        'company_settings.ifsc_code','company_settings.gst_number','company_settings.pan_number',
                        'company_settings.purchased_plan_name','company_settings.purchased_plan_price','company_settings.no_of_group',
                        'company_settings.no_of_user','company_settings.no_of_style','company_settings.max_storage_size','company_settings.storage_used',
                        'company_settings.report_range','users.user_type as Type','users.email as Email',
                        'company_settings.account_expire_at as ExpiryDate','company_settings.no_of_workspace',
                        'company_settings.purchased_plan_id','company_settings.purchased_plan_type')
                        ->first();
        $workspaceCount = Workspace::where('company_id',$request->company_id)->count();
        $staffCount = Staff::where('company_id',$request->company_id)->count();
        $styleCount = Order::where('company_id',$request->company_id)->where('status',"!=",'3')->count();
        $orderCount = count(Order::where('company_id',$request->company_id)
        ->where('status',"!=",'3')->select('order_no')->groupBy('order_no')->get());
        $getServerURL = config('filesystems.disks.s3.url');
        if($companyDetails->logo){
           // $userLogo = $getServerURL.$companyDetails->logo;
            //$userLogo = Storage::disk('s3')->url($companyDetails->logo);
            $userLogo = Storage::disk('s3')->temporaryUrl($companyDetails->logo, '+5 minutes');
        }else{
            $userLogo = '';
        }
        if($companyDetails->country_id>0){
           $getCountryName=CommonApp::getCountryDetails($companyDetails->country_id);
           $countryName=$getCountryName->name;
        }else{
            $countryName='';
        }

        /* Need to write to get the image from the S3 */
        $companyArr = ["CompanyDetails"=>$companyDetails,"workspaceCount"=>$workspaceCount,"staffCount"=>$staffCount,"logoURL"=>$userLogo
            ,"styleCount"=>$styleCount,"orderCount"=>$orderCount,"countryName"=>$countryName];
        return $companyArr;
    }
}
