<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Http\Controllers\Controller;
use App\Models\DashboardSettings;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\UserPreferences;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Common\CommonApp;

class DashboardWidgetsSettings extends Controller
{
    /* To get the orders for user settings order widgets */
    public function getWidgetOrders(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'widget_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $data = UserPreferences::dashboardWidgetSettingsOrders($request);

        $res = json_encode(['status_code' => 200, 'status'=>"success",'data' => $data]);
        return CommonApp::webEncrypt($res);
    }

    /* To add the orders to be shown on the dashboard */
    public static function addDashboardOrders(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'dashboardOrders'=>'required',
            'widget_id'=>'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            UserPreferences::addDashboardWidgetsOrders($request);
            $res = json_encode(['status_code' => 200, 'status'=>"success",'message'=>'Added Successfully']);
            return CommonApp::webEncrypt($res);
        }catch(Exception $err){
            $res = json_encode(['status_code' => 400, 'status'=>"failure",'message'=>'Try Again']);
            return CommonApp::webEncrypt($res);
        }
    }
}
