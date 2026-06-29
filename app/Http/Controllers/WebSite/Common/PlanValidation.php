<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\CompanySettings;
use App\Models\Order;
use App\Models\Staff;
use App\Models\User;
use App\Models\UserPlanHistory;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanValidation extends Controller
{
    /* To Validate the Plan */
    public static function orderValidation(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());

        $validated = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'type' => 'required',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
        }
        $data = UserPlanHistory::planValidation($request);
        $workspaceCount = $data['workspaceCount'];
        $userCount = $data['userCount'];
        $styleCount = $data['styleCount'];
        $orderCount = $data['orderCount'];
        $planSubscribed = $data['planSubscribed'];
        $planSubscribedDates = $data['planSubscribedDates'];

        $orderValidationArr = [ "workspaceCount"=> $workspaceCount,
                                "userCount"=> $userCount,
                                "styleCount"=> $styleCount,
                                "orderCount"=> $orderCount];
        $orderPlanLimit = [
            "workspaceCount"=>$planSubscribed->no_of_workspace,
            "userCount"=>$planSubscribed->no_of_user,
            "styleCount"=>$planSubscribed->no_of_style,
            "orderCount"=>$planSubscribed->no_of_group,
        ];
        if( $request->type == "Order" && $styleCount >= $planSubscribed->no_of_style ){
            $res = json_encode([
                'status_code'=>400,
                'status'=>'failure',
                "OrderData"=>$orderValidationArr,
                "OrderLimit"=>$orderPlanLimit,
                'message'=>"Number of orders exceeded the limit, Please upgrade the plan to add new orders!"]);
            return CommonApp::webEncrypt($res);
        }
        if($request->type == "Staff" && $userCount >= $planSubscribed->no_of_user){
            $res = json_encode([
                'status_code'=>400,
                'status'=>'failure',
                "OrderData"=>$orderValidationArr,
                "OrderLimit"=>$orderPlanLimit,
                'message'=>"Number of Staffs exceeded the limit, Please upgrade the plan to add more staffs!"]);
            return CommonApp::webEncrypt($res);
        }

        $res = json_encode([
            "status_code"=>200,
            "status" =>"Success",
            "SubscribedPlanDetails"=> $planSubscribedDates,
            "OrderData"=>$orderValidationArr,"OrderLimit"=>$orderPlanLimit,], 200);
        return CommonApp::webEncrypt($res);
     }
}
