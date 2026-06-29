<?php

namespace App\Http\Controllers\WebSite\Plan;

use App\Common\Logs;
use App\Http\Controllers\Controller;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlanHistory;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Common\CommonApp;
use App\Jobs\PlanPaymentConfirmJob;
use App\Models\CompanySettings;
use DateTime;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class ActivePlans extends Controller
{
    /**
     * Handle the incoming request.
     * Get the Plans
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $plans = Plan::getAllPlans();
        $res = json_encode(["status_code" => 200,"data"=>$plans]);
        return CommonApp::webEncrypt($res);
    }

    /* The Plan selected by the User - History */
    public function selectPlanType(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'user_email' => 'required',
            'plan_name' => 'required',
            'plan_id' => 'required',
            'plan_price' => 'required',
            'plan_discount' => 'required',
            'plan_subtotal' => 'required',
            'plan_grandtotal' => 'required',
            'payment_currency' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code" =>401,"errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            if($request->plan_id == 1){
                PaymentHistory::insertPlanDetails($request,$header,'trialPlan','');
                $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Plan Updated"],200);
            }else{
                Stripe::setApiKey(config('stripe.api_keys.secret_key'));
                $lineItems = [
                    [
                        'price_data' =>[
                            'currency' => 'INR',
                            'product_data'=>[
                                'name' => $request->plan_name,
                                'description' => 'Plan type : '.$request->plan_type,
                            ],
                            'unit_amount' => ((int)$request->plan_price*10)*100,
                        ],
                        'quantity'=>'1',
                    ]
                ];
                $frontEndUrl = config('stripe.frontend_url');
                $session = CheckoutSession::create([
                    'payment_method_types'=>['card'],
                    'line_items'=>$lineItems,
                    'mode'=>'payment',
                    'success_url'=> $frontEndUrl.'successpaymentstatus?type=sling',
                    'cancel_url'=> $frontEndUrl.'failurepaymentstatus?type=sling'
                ]);
                PaymentHistory::insertPlanDetails($request,$header,'otherPlans',$session);
                $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Plan Updated","payment_url"=>$session->url],200);
            }
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"status" =>"Failure","error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Update the User Plan */
    public static function updatePlan(Request $request){
        $header = $request;
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'user_id' => 'required',
            'plan_name' => 'required',
            'plan_id' => 'required',
            'plan_price' => 'required',
            'plan_discount' => 'required',
            'plan_subtotal' => 'required',
            'plan_grandtotal' => 'required',
            'payment_currency' => 'required',
            'plan_type' => 'required',
            'payment_type' => 'required',
            // 'payment_status' => 'required',
            // 'reference_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code" =>401,"errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            Stripe::setApiKey(config('stripe.api_keys.secret_key'));
            $lineItems = [
                [
                    'price_data' =>[
                        'currency' => 'INR',
                        'product_data'=>[
                            'name' => $request->plan_name,
                            'description' => 'Plan type : '.$request->plan_type,
                        ],
                        'unit_amount' => ((int)$request->plan_price*10)*100,
                    ],
                    'quantity'=>'1',
                ]
            ];
            $frontEndUrl = config('stripe.frontend_url');
            $session = CheckoutSession::create([
                'payment_method_types'=>['card'],
                'line_items'=>$lineItems,
                'mode'=>'payment',
                'success_url'=> $frontEndUrl.'successpaymentstatus?type=shot',
                'cancel_url'=> $frontEndUrl.'failurepaymentstatus?type=shot'
            ]);
            // dd($session->payment_status);
            PaymentHistory::updatePlanDetails($request,$header,$session);
            $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Plan Updated","payment_url"=>$session->url],200);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"status" =>"Failure",
            "error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* To Update the payment Status */
    public static function paymentUpdation(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            // 'user_email' => 'required',
            'type'=>'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code" =>401,"errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            Stripe::setApiKey(config('stripe.api_keys.secret_key'));
            if($request->type === "firstTime"){
                $user = User::where('email',$request->user_email)->first();
                $paymentHistory = PaymentHistory::where('user_id',$user->id)
                ->where('user_email',$request->user_email)->orderBy('id','DESC')->first();
                $planDetails = Plan::where('id',$paymentHistory->plan_id)->first();
                $intent = PaymentIntent::retrieve($paymentHistory->reference_id);
                /* For Mail Starts*/
                $forMail['to'] = $user->email;
                $forMail['planName']=$planDetails->plan_name;
                $forMail['planType']=$planDetails->type;
                $forMail['planPrice']=(int)$planDetails->price*10;
                $forMail['language']=$user->lang_code;
                $forMail['name']=$user->name;
                /* For Mail Ends */
                if($intent->status === "succeeded"){
                    $paymentHistory->payment_status = "Success";
                    $paymentHistory->save();
                    $user->status = '1';
                    $user->payment_intent=null;
                    $user->save();
                    PlanPaymentConfirmJob::dispatch($forMail);
                    $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Success"],200);
                    return CommonApp::webEncrypt($res);
                }else{
                    $paymentHistory->payment_status = "Failure";
                    $paymentHistory->save();
                    $user->status = '0';
                    $user->payment_intent=null;
                    $user->save();
                    $res = json_encode(["status_code"=>202,"status" =>"failure","message"=>"Failure! Please Try Again"],200);
                    return CommonApp::webEncrypt($res);
                }
            }else if($request->type === "updation"){
                $user = User::where('id',$request->user_id)->first();
                $paymentHistory = PaymentHistory::where('user_id',$request->user_id)
                ->where('user_email',$user->email)->orderBy('id','DESC')->first();
                $previousPlanDetails = PaymentHistory::where('user_id',$request->user_id)
                ->orderBy('id','DESC')->skip(1)->take(1)->first();
                $planDetails = Plan::where('id',$paymentHistory->plan_id)->first();
                $intent = PaymentIntent::retrieve($paymentHistory->reference_id);
                /* For Mail Starts*/
                $forMail['to'] = $user->email;
                $forMail['planName']=$planDetails->plan_name;
                $forMail['planType']=$planDetails->type;
                $forMail['planPrice']=(int)$planDetails->price*10;
                $forMail['language']=$user->lang_code;
                $forMail['name']=$user->name;
                /* For Mail Ends */
                if($intent->status === "succeeded"){
                    UserPlanHistory::upgradePlan($request,$previousPlanDetails);
                    $paymentHistory->payment_status = "Success";
                    $paymentHistory->save();
                    $user->status = '1';
                    $user->payment_intent=null;
                    $user->save();
                    PlanPaymentConfirmJob::dispatch($forMail);
                    $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Success"],200);
                    return CommonApp::webEncrypt($res);
                }else{
                    $paymentHistory->payment_status = "Failure";
                    $paymentHistory->save();
                    $user->status = '1';
                    $user->payment_intent=null;
                    $user->save();
                    $res = json_encode(["status_code"=>202,"status" =>"failure","message"=>"Failure! Please Try Again"],200);
                    return CommonApp::webEncrypt($res);
                }
            }
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"status" =>"Failure",
            "error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* To Get Company's Plan Payment History */
    public static function getPaymentHistoryOfCompany(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'user_id'=>'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code" =>401,"errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $paymentHistory= PaymentHistory::getCompanyPaymentHistory($request);

        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$paymentHistory],200);
        return CommonApp::webEncrypt($res);
    }

    /* Get Plan Status */
    public static function getPlanStatus(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        if(isset($request->company_id)){
            if(isset($request->company_id) && $request->company_id != ""){
                $plan = CompanySettings::where('id', $request->company_id)->select('account_expire_at')->first();
                $today = new DateTime(date("Y-m-d"));
                $planExpiryDate = new DateTime(date('Y-m-d', strtotime($plan->account_expire_at)));
                $dayDiff = (int)$today->diff($planExpiryDate)->format("%r%a");
                if($dayDiff < 0){
                    $res = json_encode(["status_code"=>4004,"status" =>"failure","message"=>"Plan Expired"]);
                    return response(CommonApp::webEncrypt($res));
                }else{
                    $res = json_encode(["status_code"=>4001,"status" =>"success","message"=>"Plan Active"]);
                    return response(CommonApp::webEncrypt($res));
                }
            }
        }
    }
}
