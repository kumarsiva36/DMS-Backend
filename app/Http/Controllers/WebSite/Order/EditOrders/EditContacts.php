<?php

namespace App\Http\Controllers\WebSite\Order\EditOrders;

use App\Common\CommonApp;
use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Jobs\OrderInfoPDFJob;
use App\Jobs\staffOrderInviteJob;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\Staff;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Orderlog;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class EditContacts extends Controller
{
    /******************** Edit the Contacts ***********************/
    public static function editContact(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
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
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to Edit order
            $per = CommonApp::checkStaffPermission($request,'20');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id]
        ];
        $aldreadyExists = OrderContacts::where($whereConditions)->get();
        if(!empty($aldreadyExists)){
            OrderContacts::where($whereConditions)->delete();
        }
        /*Order basic info pdf creation starts*/
        OrderInfoPDFJob::dispatch($request);
        /*Order basic info pdf creation end*/

        // $workspaceName = (Workspace::where('id',$request->workspace_id)->where('company_id',$request->company_id)->first())->name;
        // $orderNo = (Order::where('id',$request->order_id)->where('company_id',$request->company_id)->where('workspace_id',$request->workspace_id)
        //             ->first())->order_no;
        $orderContactsArr = [];
        $orderContactsArr['user_id']= $companyDetails->user_id;
        $orderContactsArr['company_id']= $request->company_id;
        $orderContactsArr['workspace_id']= $request->workspace_id;
        $orderContactsArr['order_id']= $request->order_id;
        foreach ($request->contacts as $contact){
            $orderContactsArr['staff_id']= $contact['staff_id'];
            $orderContactsArr['created_at']=date('Y-m-d H:i:s');
            $orderContactsArr['updated_at']=date('Y-m-d H:i:s');
            OrderContacts::insert($orderContactsArr);
            // $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"Staff",$contact['staff_id']);
            // $details=[];
            // $staff = Staff::where('company_id',$request->company_id)->where('workspace_id',$request->workspace_id)
            //         ->where('id',$contact['staff_id'])->first();
            // $details['to']=$staff->email;
            // $details['userName']=$staff->first_name." ".$staff->last_name;
            // $details['workspaceName'] = $workspaceName;
            // $details['orderNo'] = $orderNo;
            // $details['language'] = $language;
            // $details['pdf_path'] =$pdf_path;
            // staffOrderInviteJob::dispatch($details);
        }

         /* Order Log creation starts*/
         $logArry = array();
         $logArry['order_id'] =$request->order_id;
         $logArry['company_id'] = $request->company_id;
         $logArry['workspace_id'] = $request->workspace_id;
         $logArry['staff_id'] =$request->staff_id ?? 0;
         $logArry['user_id'] = $request->user_id ?? 0;
         $logArry['action'] = 'Edit';
         $logArry['before_values'] = json_encode($request->before_values) ?? '';
         $logArry['after_values'] = json_encode($request->after_values) ?? '';
         Orderlog::insert($logArry);
         /* Order Log creation end*/

         $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Contacts Updated Successfully"]);
        return CommonApp::webEncrypt($res);
    }
}
