<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\ArticleName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ArticleNames extends Controller
{
    /* Create new Article */
    public static function createArticle(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $request->referenceId = $request->referenceId?? '0';
        $request->name = trim($request->name);
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'name' => ['required', Rule::unique('order_article_name')
                        ->where(function ($query) use($request) {
                            $query->where('company_id',$request->company_id);
                            $query->where('workspace_id',$request->workspace_id);
                            //$query->where('inquiry_reference_id','=',"0");
                            $query->orwhere('is_default','=','0');
                            //$query->orwhere('inquiry_reference_id','=',$request->referenceId);
                            return $query;
                        })],
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        // $whereConditions = [
        //     ['name','=',$request->name],
        //     ['workspace_id','=',$request->workspace_id],
        //     ['company_id','=',$userDetails->company_id]
        // ];
        $articleNameArr = [];
        $articleNameArr['name'] = $request->name;
        $articleNameArr['company_id'] = $request->company_id;
        $articleNameArr['workspace_id'] = $request->workspace_id;
        $articleNameArr['user_id'] = $companyDetails->user_id;
        $articleNameArr['staff_id'] ='0';
        $articleNameArr['is_default'] ='1';
        $articleNameArr['inquiry_reference_id'] =$request->referenceId?? '0';
        $articleNameArr['status'] ='1';
        $articleNameArr['created_by'] = $companyDetails->user_id;
        $articleNameArr['created_at'] = date('Y-m-d H:i:s');
        $articleNameArr['updated_at'] = date('Y-m-d H:i:s');
        ArticleName::insert($articleNameArr);

        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Article added Succesfully"]);
        return CommonApp::webEncrypt($res);
    }

    /* Get the Article */
    public static function getArticle(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions = [
                ['workspace_id','=',$request->workspace_id],
                ['company_id','=',$request->company_id],
                ['inquiry_reference_id','=',"0"]
        ];
        if(isset($request->referenceId) && $request->referenceId!=''){
            $article = ArticleName::select('id','name')->where($whereConditions)->orwhere('inquiry_reference_id',$request->referenceId)->orwhere('is_default','0')->get();
        }else{
            $article = ArticleName::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();
        }
        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$article]);
        return CommonApp::webEncrypt($res);
    }

    /* Create Staff Article */
    public static function createStaffArticle(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $staffDetails = CommonApp::getStaffDetailsByEmail($request->email);
        $validated = Validator::make((array)$request,[
            'email' => 'required',
            'workspace_id' => 'required',
            'name' => ['required', Rule::unique('order_category')
                        ->where(function ($query) use($staffDetails,$request) {
                            $query->where('company_id',$staffDetails->company_id);
                            $query->where('workspace_id',$request->workspace_id);
                            $query->where('inquiry_reference_id','=',"0");
                            $query->orwhere('is_default','=','0');
                            return $query;
                        })],
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        // $whereConditions = [
        //     ['name','=',$request->name],
        //     ['workspace_id','=',$request->workspace_id],
        //     ['company_id','=',$userDetails->company_id]
        // ];
        $articleNameArr = [];
        $articleNameArr['name'] = $request->name;
        $articleNameArr['company_id'] = $staffDetails->company_id;
        $articleNameArr['workspace_id'] = $request->workspace_id;
        $articleNameArr['user_id'] = '0';
        $articleNameArr['staff_id'] =$staffDetails->id;
        $articleNameArr['is_default'] ='1';
        $articleNameArr['inquiry_reference_id'] = $request->referenceId?? '0';
        $articleNameArr['status'] ='1';
        $articleNameArr['created_by'] = $staffDetails->id;
        $articleNameArr['created_at'] = date('Y-m-d H:i:s');
        $articleNameArr['updated_at'] = date('Y-m-d H:i:s');
        ArticleName::insert($articleNameArr);

        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Article added Succesfully"]);
        return CommonApp::webEncrypt($res);
    }

    /* Get the staff created article */
    public static function getStaffOrderCategory(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'email' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $staffDetails = CommonApp::getStaffDetailsByEmail($request->email);
        $whereConditions = [
                ['workspace_id','=',$request->workspace_id],
                ['company_id','=',$staffDetails->company_id],
                ['inquiry_reference_id','=',"0"]
        ];
        $article = ArticleName::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$article]);
        return CommonApp::webEncrypt($res);
    }
}
