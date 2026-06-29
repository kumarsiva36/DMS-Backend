<?php

namespace App\Http\Controllers\Mobile\v1\Plan;

use App\Common\Logs;
use App\Http\Controllers\Controller;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ActivePlans extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $plans = Plan::getAllPlans();
        return response()->json(["status_code" => 200,"data"=>$plans]);
    }

    /* The Plan selected by the User - History */
    public function selectPlanType(Request $request){
        $validated = Validator::make($request->all(),[
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
            return response()->json(["status_code" =>401,"errors"=>$validated->errors()]);
        }
        PaymentHistory::insertPlanDetails($request);
        return response()->json(["status_code"=>200,"status" =>"success","message"=>"Plan Updated"],200);
    }
}
