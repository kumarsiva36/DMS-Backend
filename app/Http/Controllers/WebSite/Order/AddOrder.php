<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use App\Jobs\CreateOrderEmail;
use App\Mail\SendNewOrderEmail;
use App\Models\Order;
use App\Models\OrderComments;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Orderlog;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use Exception;
use Illuminate\Support\Facades\Log;

class AddOrder extends Controller
{
    /**
     * Handle the incoming request.
     *  Add a new Order
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_no' => 'required',
            'style_no' => ['required', Rule::unique('orders')
                            ->where(function ($query) use ($request) {
                                $query->where('company_id', $request->company_id);
                                $query->where('workspace_id', $request->workspace_id);
                                $query->where('order_no', $request->order_no);
                                $query->where('status','!=','3');
                            })],
            // 'buyer_id' => 'required',
           // 'pcu_id' => 'required',
           // 'factory_id' => 'required',
           // 'category_id'=> 'required',
            'fabric_id'=> 'required',
            'article_id' => 'required',
            'total_quantity' => 'required|integer|min:0',
            'no_of_deliverys' => 'required|numeric|max:20',
            'delivery_date'=> 'required|date',
            'delivery_dates'=> 'required|array',
           // 'order_price' => 'required|numeric|min:0',
        ]);
        if($validator->fails()){
           $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
           return CommonApp::webEncrypt($res);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $orderCount = Order::where('company_id',$request->company_id)->where('workspace_id',$request->workspace_id)
        ->where('status','!=','3')->count();
        if($orderCount >= $companyDetails->no_of_style){
           $res = json_encode(["status_code"=>600,"message"=>"Please upgrade Your Plan To Add Orders"]);
           return CommonApp::webEncrypt($res);
        }
        elseif(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to add orders
            $per = CommonApp::checkStaffPermission($request,'18');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        try{
            $orderID = Order::addOrder($request,$companyDetails);
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Order Added Successfully","id"=>$orderID,"totalQuantity"=>$request->total_quantity]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"errors"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    public static function addComments(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'order_id'=>'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $videoPath = $audioPath = $documentPath = NULL;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('document/'.$request->company_id.'/'.$request->workspace_id, 'public');
        }
        if ($request->hasFile('audio')) {
            $audioPath = $request->file('audio')->store('audio/'.$request->company_id.'/'.$request->workspace_id, 'public');
        }
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('video/'.$request->company_id.'/'.$request->workspace_id, 'public');
        }
        try{
            $commentsArr = [];
            $commentsArr['company_id'] = $request->company_id;
            $commentsArr['workspace_id'] = $request->workspace_id;
            $commentsArr['order_id'] = $request->order_id;
            $commentsArr['comments'] = $request->comments;
            $commentsArr['document_url'] =($documentPath!=NULL) ? config('app.public_url').'storage/'.$documentPath : NULL;
            $commentsArr['audio_url'] = ($audioPath!=NULL) ? config('app.public_url').'storage/'.$audioPath : NULL;
            $commentsArr['video_url'] = ($videoPath!=NULL) ? config('app.public_url').'storage/'.$videoPath : NULL;
            $commentsArr['reason'] = $request->reason ?? NULL;
            $commentsArr['created_by_type'] = $request->staff_id > 0 ? "Staff" : "Admin";
            $commentsArr['created_by_id'] = $request->staff_id > 0 ? $request->staff_id : $request->user_id;
            $commentsArr['created_at'] = date('Y-m-d H:i:s');
            OrderComments::insert($commentsArr);
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Comments Added Successfully"]);
        }catch (Exception $e) {
            $res = json_encode(["status_code"=>401,"errors"=>$e->getMessage()]);
            Log::info($e->getMessage());
        }
        return $res;

    }
}
