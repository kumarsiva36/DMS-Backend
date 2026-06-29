<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $table = 'plan_price_details';

    public static function getPlanDetails($plan_id){
       $planDetails= Plan::where('id',$plan_id)->first();
       return $planDetails;
    }

    public static function getAllPlans(){
        $status = '1';
        $whereCondition=[
            ['status','=',$status]
        ];
        $getPlan = Plan::where($whereCondition)->get();
        $monthly=$yearly=$totalPlans=[];
        foreach ($getPlan as $plan){
            if($plan->type === "Monthly"){
                $monthly[] = $plan;
            }
            else if($plan->type === "Yearly"){
                $yearly[] = $plan;
            }
        }
        $totalPlans['Monthly'] = $monthly;
        $totalPlans['Yearly'] = $yearly;
        return $totalPlans;
    }

    public static function getPlanRemainingDays($whereCondition){
        $planValidity = CompanySettings::where($whereCondition)
        ->select('account_expire_at')
        ->first();
        $today = new DateTime();
        $later = new DateTime($planValidity->account_expire_at);
        $days = $today->diff($later)->format("%r%a");

        return $days;
    }
}
