<?php

namespace App\Http\Controllers\Mobile\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\OrderCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrderCategories extends Controller
{
    /* Create new Category */
    public static function createOrderCategory(Request $request){
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $validated = Validator::make($request->all(),[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'name' => ['required', Rule::unique('order_category')
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
        $OrderCategoryArr = [];
        $OrderCategoryArr['name'] = $request->name;
        $OrderCategoryArr['company_id'] = $request->company_id;
        $OrderCategoryArr['workspace_id'] = $request->workspace_id;
        $OrderCategoryArr['user_id'] = $companyDetails->user_id;
        $OrderCategoryArr['staff_id'] ='0';
        $OrderCategoryArr['is_default'] ='1';
        $OrderCategoryArr['status'] ='1';
        $OrderCategoryArr['created_by'] = $companyDetails->user_id;
        $OrderCategoryArr['created_at'] = date('Y-m-d H:i:s');
        $OrderCategoryArr['updated_at'] = date('Y-m-d H:i:s');
        OrderCategory::insert($OrderCategoryArr);

        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Category added Succesfully"]);
    }

    /* Get the Catigory */
    public static function getOrderCategory(Request $request){
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
        $orderCategory = OrderCategory::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();

        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$orderCategory]);
    }

    /* Create Category */
    public static function createStaffOrderCategory(Request $request){
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
        $OrderCategoryArr = [];
        $OrderCategoryArr['name'] = $request->name;
        $OrderCategoryArr['company_id'] = $staffDetails->company_id;
        $OrderCategoryArr['workspace_id'] = $request->workspace_id;
        $OrderCategoryArr['user_id'] = '0';
        $OrderCategoryArr['staff_id'] =$staffDetails->id;
        $OrderCategoryArr['is_default'] ='1';
        $OrderCategoryArr['status'] ='1';
        $OrderCategoryArr['created_by'] = $staffDetails->id;
        $OrderCategoryArr['created_at'] = date('Y-m-d H:i:s');
        $OrderCategoryArr['updated_at'] = date('Y-m-d H:i:s');
        OrderCategory::insert($OrderCategoryArr);

        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Category added Succesfully"]);
    }
    /* Get Category */
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
        $orderCategory = OrderCategory::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();

        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$orderCategory]);
    }
}
