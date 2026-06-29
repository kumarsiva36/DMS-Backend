<?php

namespace App\Http\Controllers\Mobile\v1\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\OrderTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrderTemplates extends Controller
{
    /* Create a new Order Template */
    public static function createOrderTemplate(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id'=>'required',
            'task_template_structure' => 'required',
            'template_name' => ['required', Rule::unique('order_task_template')
            ->where(function ($query) use($request) {
                $query->where('company_id',$request->company_id);
                $query->where('template_name',$request->template_name);
                $query->orwhere('is_default','=','0');
                return $query;
            })]
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);

        $newTemplateArr = [];
        $newTemplateArr['company_id']= $request->company_id;
        $newTemplateArr['workspace_id']= $request->workspace_id;
        $newTemplateArr['user_id']= $companyDetails['user_id'];
        $newTemplateArr['staff_id']=$request->staff_id ?? 0;
        $newTemplateArr['order_id']= $request->order_id;
        $newTemplateArr['template_name']= $request->template_name;
        $newTemplateArr['status']='1';
        $newTemplateArr['is_default']='1';
        $newTemplateArr['task_template_structure']= json_encode($request->task_template_structure);
        $newTemplateArr['created_by']= $companyDetails['user_id'];
        $newTemplateArr['created_user_type']= 'User';
        $newTemplateArr['created_at']= date('Y-m-d H:i:s');
        $newTemplateArr['updated_at']= date('Y-m-d H:i:s');
        OrderTemplate::insert($newTemplateArr);
        $templateID = DB::getPdo()->lastInsertId();
        $res = json_encode(["status_code"=>200,"status" =>"Success","templateID"=>$templateID,"message"=>"Template Added Successfully"]);
        return CommonApp::apiEncrypt($res);
    }

    /* Get all the templates created for the company and workspace*/
    public static function getOrderTemplates(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereConditions = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id]
        ];
        $orderTemplates = OrderTemplate::select('id','template_name','task_template_structure')->where($whereConditions)->orwhere('is_default','0')->get();

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$orderTemplates]);
        return CommonApp::apiEncrypt($res);

    }
}
