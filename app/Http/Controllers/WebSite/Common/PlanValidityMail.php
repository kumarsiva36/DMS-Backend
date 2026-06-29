<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Jobs\PlanExpiredJob;
use App\Jobs\PlanExpiryJob;
use App\Jobs\TrialPlanExpiredJob;
use App\Models\CompanySettings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlanValidityMail extends Controller
{
    /* For Plan Validation on the current day and 3 day before. */
    public static function planValidation(){
        $today = date('Y-m-d');
        $threeDaysLater = date('Y-m-d',strtotime('+3 days'));
        $companies = CompanySettings::where(DB::raw('DATE_FORMAT(account_expire_at, "%d-%m-%Y")'), '>=', date("d-m-Y"))->get();
        //$companies = CompanySettings::whereRaw(DB::raw('DATE_FORMAT(account_expire_at, "%Y-%m-%d") >= CURDATE()'))->get();
        // Log::info("Plan Validity");
        // Log::info($companies);
        foreach($companies as $company){
            $expiryDate = date('Y-m-d',strtotime($company->account_expire_at));
            $user = User::where('id',$company->user_id)->first();
            $language = GetUserLanguage::getLanguageOfCompanyWithUser($company->id,$user->id);
            $plan_id = $company->purchased_plan_id;
            $details=[];
            $details['to']=$user->email;
            $details['userName']=$user->name;
            $details['language']=$language;
            if($plan_id===1 && (strtotime($today) == strtotime($expiryDate)) ){
                TrialPlanExpiredJob::dispatch($details);
                // Log::info("Trial Plan Expired");
            }
            else if(strtotime($today) == strtotime($expiryDate) ){
                PlanExpiredJob::dispatch($details);
                // Log::info("Today");
            }
            else if(strtotime($threeDaysLater) == strtotime($expiryDate) ){
                PlanExpiryJob::dispatch($details);
                // Log::info("Three Days From Today");
            }
        }
    }

}
