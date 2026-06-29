<?php

namespace App\Http\Controllers\Mobile\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\FabricType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FabricTypes extends Controller
{

    /* Create new Fabric */
    public static function createFabric(Request $request){
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $validated = Validator::make($request->all(),[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'name' => ['required', Rule::unique('fabric_type')
                        ->where(function ($query) use($companyDetails,$request) {
                            $query->where('id',$companyDetails->id);
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
        $fabricArr = [];
        $fabricArr['name'] = $request->name;
        $fabricArr['company_id'] = $request->company_id;
        $fabricArr['workspace_id'] = $request->workspace_id;
        $fabricArr['user_id'] = $companyDetails->user_id;
        $fabricArr['staff_id'] ='0';
        $fabricArr['is_default'] = '1';
        $fabricArr['status'] = '1';
        $fabricArr['created_by'] = $companyDetails->user_id;
        $fabricArr['created_at'] = date('Y-m-d H:i:s');
        $fabricArr['updated_at'] = date('Y-m-d H:i:s');
        FabricType::insert($fabricArr);

        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Fabric added Succesfully"]);
    }

    /* Get the Fabric */
    public static function getFabric(Request $request){
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
        $fabric = FabricType::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();

        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$fabric]);
    }

    public static function createStaffFabric(Request $request){
        $staffDetails = CommonApp::getStaffDetailsByEmail($request->email);
        $validated = Validator::make($request->all(),[
            'email' => 'required',
            'workspace_id' => 'required',
            'name' => ['required', Rule::unique('fabric_type')
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
        $fabricArr = [];
        $fabricArr['name'] = $request->name;
        $fabricArr['company_id'] = $staffDetails->company_id;
        $fabricArr['workspace_id'] = $request->workspace_id;
        $fabricArr['user_id'] = '0';
        $fabricArr['staff_id'] =$staffDetails->id;
        $fabricArr['is_default'] = '1';
        $fabricArr['status'] = '1';
        $fabricArr['created_by'] = $staffDetails->id;
        $fabricArr['created_at'] = date('Y-m-d H:i:s');
        $fabricArr['updated_at'] = date('Y-m-d H:i:s');
        FabricType::insert($fabricArr);

        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Fabric added Succesfully"]);
    }
    /* Create New Fabric */
    public static function getStaffFabric(Request $request){
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
        $fabric = FabricType::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();

        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$fabric]);
    }
}
