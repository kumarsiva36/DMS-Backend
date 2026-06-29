<?php

namespace App\Http\Controllers\Mobile\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\ArticleName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ArticleNames extends Controller
{
    /* Create new Article */
    public static function createArticle(Request $request){
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $validated = Validator::make($request->all(),[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'name' => ['required', Rule::unique('order_article_name')
                        ->where(function ($query) use($request) {
                            $query->where('company_id',$request->company_id);
                            $query->where('workspace_id',$request->workspace_id);
                            $query->orwhere('is_default','=','0');
                            return $query;
                        })],
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
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
        $articleNameArr['status'] ='1';
        $articleNameArr['created_by'] = $companyDetails->user_id;
        $articleNameArr['created_at'] = date('Y-m-d H:i:s');
        $articleNameArr['updated_at'] = date('Y-m-d H:i:s');
        ArticleName::insert($articleNameArr);
        $lastInsertedId = DB::getPdo()->lastInsertId();
        $regData=[];
        $regData['id']=$lastInsertedId;
        $regData['name']=$request->name;
        $regData['company_id']=$request->company_id;

        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Article added Succesfully","data"=>$regData]);
    }

    /* Get the Article */
    public static function getArticle(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validator->errors()]);
        }
        $whereConditions = [
                ['workspace_id','=',$request->workspace_id],
                ['company_id','=',$request->company_id]
        ];
        $article = ArticleName::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();

        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$article]);
    }
    /* Create New Article */
    public static function createStaffArticle(Request $request){
        $staffDetails = CommonApp::getStaffDetailsByEmail($request->email);
        $validated = Validator::make($request->all(),[
            'email' => 'required',
            'workspace_id' => 'required',
            'name' => ['required', Rule::unique('order_category')
                        ->where(function ($query) use($staffDetails,$request) {
                            $query->where('company_id',$staffDetails->company_id);
                            $query->where('workspace_id',$request->workspace_id);
                            $query->orwhere('is_default','=','0');
                            return $query;
                        })],
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"error"=>$validated->errors()]);
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
        $articleNameArr['status'] ='1';
        $articleNameArr['created_by'] = $staffDetails->id;
        $articleNameArr['created_at'] = date('Y-m-d H:i:s');
        $articleNameArr['updated_at'] = date('Y-m-d H:i:s');
        ArticleName::insert($articleNameArr);
        $lastInsertedId = DB::getPdo()->lastInsertId();

        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Article added Succesfully"]);
    }
    /* Get the Articles */
    public static function getStaffOrderCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validator->errors()]);
        }
        $staffDetails = CommonApp::getStaffDetailsByEmail($request->email);
        $whereConditions = [
                ['workspace_id','=',$request->workspace_id],
                ['company_id','=',$staffDetails->company_id]
        ];
        $article = ArticleName::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();

        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$article]);
    }
}
