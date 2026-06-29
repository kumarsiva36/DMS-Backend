<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Jobs\staffOrderInviteJob;
use App\Models\OrderContacts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderSku;
use App\Models\Staff;
use App\Models\Workspace;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class AddOrderContacts extends Controller
{
    /* Add/overwrite contact in the order */

    public static function addContact(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $request->contacts = json_decode(json_encode($request->contacts), true);
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
            'contacts' => 'required|array',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to add orders
            $per = CommonApp::checkStaffPermission($request,'18');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        try{
            OrderContacts::addContact($request);
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Contacts Added Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* List the selected contacts in the order list */

    public static function getContact(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            $arr = OrderContacts::getContact($request);
            $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$arr],200);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /************To get a list of contacts id in an array ****************/
    public static function getContacts(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            $arr = OrderContacts::getContacts($request);
            $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$arr],200);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }
}
