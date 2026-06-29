<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class PaymentHistory extends Model
{
    use HasFactory;
    protected $table = 'payment_history';
    protected $fillable = [
       'user_id','user_name','user_email','mobile','payment_type','plan_name','plan_id','plan_price','plan_discount',
       'plan_subtotal','plan_grandtotal','payment_currency','reference_id','payment_status','ipaddress','payment_date',
       'reason','payment_intent'
    ];

    public static function getPlanDetailsByUserID($id){
       $history = PaymentHistory::where('user_id',$id)->orderBy('id','DESC')->first();
       return $history;
    }

    public static function insertPlanDetails($request,$header,$type,$payment){
        $user = User::where('email',$request->user_email)->first();
        $paymentHistory=[];
        $paymentHistory['user_id']=$user->id;
        $paymentHistory['user_name']=$user->username;
        $paymentHistory['user_email']=$request->user_email;
        $paymentHistory['mobile']=$user->mobile_number;
        $paymentHistory['payment_type']=$request->payment_type;
        $paymentHistory['plan_name']=$request->plan_name;
        $paymentHistory['plan_id']=$request->plan_id;
        $paymentHistory['plan_price']=$request->plan_price;
        $paymentHistory['plan_type']=$request->plan_type;
        $paymentHistory['plan_discount']=$request->plan_discount;
        $paymentHistory['plan_subtotal']=$request->plan_subtotal;
        $paymentHistory['plan_grandtotal']=$request->plan_grandtotal;
        $paymentHistory['payment_currency']=$request->payment_currency;
        // $paymentHistory['reference_id']=$request->reference_id;
        $paymentHistory['reference_id']=$type === "trialPlan"? "":$payment->payment_intent;
        $paymentHistory['payment_intent']=$type === "trialPlan"? null :$payment->payment_intent;
        // $paymentHistory['payment_status']=$request->payment_status;
        $paymentHistory['payment_status']=$type === "trialPlan"? "Success":$payment->payment_status;
        $paymentHistory['ipaddress']=$header->ip();
        $paymentHistory['payment_date']=date("Y-m-d H:i:s");
        $paymentHistory['created_at']=date("Y-m-d H:i:s");
        $paymentHistory['updated_at']=date("Y-m-d H:i:s");
        if($type === "otherPlans"){
            $user->payment_intent = $payment->payment_intent;
        }
        $user->status = '1';
        $user->save();
        DB::beginTransaction();
        try{
            PaymentHistory::insert($paymentHistory);
        }
        catch(Exception $err){
            DB::rollBack();
            Log::info($err);
            throw new InvalidArgumentException('Unable to post Data');
        }
        DB::commit();
    }

    /* For Plan Updation/Upgradation */
    public static function updatePlanDetails($request,$header,$payment){
        $previousPlanDetails = PaymentHistory::where('user_id',$request->user_id)->orderBy('id','DESC')->first();
        if($request->plan_id < $previousPlanDetails->plan_id){
            throw new InvalidArgumentException('Please choose the same plan or a better plan');
        }
        $user = User::where('id',$request->user_id)->first();
        $paymentHistory=[];
        $paymentHistory['user_id']=$user->id;
        $paymentHistory['user_name']=$user->username;
        $paymentHistory['user_email']=$user->email;
        $paymentHistory['mobile']=$user->mobile_number;
        $paymentHistory['payment_type']=$request->payment_type;
        $paymentHistory['plan_name']=$request->plan_name;
        $paymentHistory['plan_id']=$request->plan_id;
        $paymentHistory['plan_price']=$request->plan_price;
        $paymentHistory['plan_type']=$request->plan_type;
        $paymentHistory['plan_discount']=$request->plan_discount;
        $paymentHistory['plan_subtotal']=$request->plan_subtotal;
        $paymentHistory['plan_grandtotal']=$request->plan_grandtotal;
        $paymentHistory['payment_currency']=$request->payment_currency;
        // $paymentHistory['reference_id']=$request->reference_id;
        $paymentHistory['reference_id']=$payment->payment_intent;
        $paymentHistory['payment_intent']=$payment->payment_intent;
        // $paymentHistory['payment_status']=$request->payment_status;
        $paymentHistory['payment_status']=$payment->payment_status;
        $paymentHistory['ipaddress']=$header->ip();
        $paymentHistory['payment_date']=date("Y-m-d H:i:s");
        $paymentHistory['created_at']=date("Y-m-d H:i:s");
        // dd($paymentHistory);
        // if($request->payment_status === "Success"){
            $user->payment_intent = $payment->payment_intent;
        //     $user->status = '1';
            $user->save();
        // }
        DB::beginTransaction();
        try{
            PaymentHistory::insert($paymentHistory);
            // UserPlanHistory::upgradePlan($request,$previousPlanDetails);
        }
        catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException('Unable to post Data');
        }
        DB::commit();
    }

    /* To Get the payment history of the company */
    public static function getCompanyPaymentHistory($request){
        // $whereCondtions[]=['id','=',$request->company_id];
        $whereCondtions[]=['user_id','=',$request->user_id];

        $paymentHistory = PaymentHistory::where($whereCondtions)
        ->select('id','plan_name','plan_type','plan_price','payment_date',
        'reason',)
        ->selectRaw('CONCAT(UPPER(SUBSTRING(payment_status, 1, 1)), LOWER(SUBSTRING(payment_status FROM 2))) AS payment_status')
        ->orderBy('id','DESC')->get();

        return $paymentHistory;
    }
}
