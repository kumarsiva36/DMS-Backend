<?php

namespace App\Http\Controllers\Mobile\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\OrderContacts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;

class AddOrderContacts extends Controller
{
    /* Add/overwrite contact in the order */

    public static function addContact(Request $request){
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
        $orderId=$request->order_id;
        $orderContactsArr = [];
        $orderContactsArr['user_id']= $companyDetails->user_id;
        $orderContactsArr['company_id']= $request->company_id;
        $orderContactsArr['workspace_id']= $request->workspace_id;
        $orderContactsArr['order_id']= $orderId;
        foreach ($request->contacts as $contact){
            $orderContactsArr['staff_id']= $contact['staff_id'];
            $orderContactsArr['created_at']=date('Y-m-d H:i:s');
            $orderContactsArr['updated_at']=date('Y-m-d H:i:s');
            OrderContacts::insert($orderContactsArr);
        }

        /*Update Order Step Status*/
        $addOrderArr=[];
        $addOrderArr['step_level'] = '3';
         Order::where('id',$orderId)->update($addOrderArr);
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Contacts Added Successfully"]);
    }

    /* List the selected contacts in the order list */

    public static function getContact(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereConditions =[
            ['order_contacts.workspace_id','=',$request->workspace_id],
            ['order_contacts.company_id', '=', $request->company_id],
            ['order_contacts.order_id','=',$request->order_id]
        ];

        $skuDetails = OrderContacts::where($whereConditions)
                        ->join('staff','staff.id','order_contacts.staff_id')
                        ->select('order_contacts.staff_id', 'staff.first_name', 'staff.last_name')
                        ->get();
		$arr=array();$i=0;
    	foreach ($skuDetails as $value) {
    		$arr[$i]['staff_id']=$value->staff_id;

    		$arr[$i]['staff_name']=$value->first_name.' '.$value->last_name;
    		$i++;
		}
        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$arr],200);
    }

    /************To get a list of contacts id in an array ****************/
    public static function getContacts(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id]
        ];

        $skuDetails = OrderContacts::where($whereConditions)
                        ->select('staff_id')
                        ->get();
		$arr=array();$i=0;
    	foreach ($skuDetails as $value) {
    		$arr[$i]=$value->staff_id;
    		$i++;
		}
        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$arr],200);
    }
}
