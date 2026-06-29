<?php

namespace App\Http\Controllers\Mobile\v1\Order\EditOrders;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\OrderContacts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EditContacts extends Controller
{
    /******************** Edit the Contacts ***********************/
    public static function editContact(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
            'contacts' => 'required|array',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
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
        }
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Contacts Updated Successfully"]);
    }
}
