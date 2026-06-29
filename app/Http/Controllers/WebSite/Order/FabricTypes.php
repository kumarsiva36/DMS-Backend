<?php

namespace App\Http\Controllers\WebSite\Order;

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
        $request = CommonApp::webDecrypt($request->getContent());
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $request->referenceId = $request->referenceId?? '0';
        $request->name = trim($request->name);
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'name' => ['required', Rule::unique('fabric_type')
                        ->where(function ($query) use ($request) {
                            $query->where('company_id',$request->company_id);
                            $query->where('workspace_id',$request->workspace_id);
                            $query->where('inquiry_reference_id','=',"0");
                            $query->orwhere('is_default','=','0');
                            $query->orwhere('inquiry_reference_id','=',$request->referenceId);
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
        $fabricArr = [];
        $fabricArr['name'] = ucfirst(strtolower($request->name));
        $fabricArr['company_id'] = $request->company_id;
        $fabricArr['workspace_id'] = $request->workspace_id;
        $fabricArr['user_id'] = $companyDetails->user_id;
        $fabricArr['staff_id'] ='0';
        $fabricArr['is_default'] = '1';
        $fabricArr['inquiry_reference_id'] = $request->referenceId?? '0';
        $fabricArr['status'] = '1';
        $fabricArr['created_by'] = $companyDetails->user_id;
        $fabricArr['created_at'] = date('Y-m-d H:i:s');
        $fabricArr['updated_at'] = date('Y-m-d H:i:s');
        FabricType::insert($fabricArr);

        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Fabric added Succesfully"]);
        return CommonApp::webEncrypt($res);
    }

    /* Get the Fabric */
    public static function getFabric(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validator->errors()]);
        }
        $whereConditions = [
                ['workspace_id','=',$request->workspace_id],
                ['company_id','=',$request->company_id],
                ['inquiry_reference_id','=',"0"]
        ];
        if(isset($request->referenceId) && $request->referenceId!=''){
            $fabric = FabricType::select('id','name')->where($whereConditions)->orwhere('inquiry_reference_id',$request->referenceId)->orwhere('is_default','0')->get();
        }else{
            $fabric = FabricType::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();
        }
        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$fabric]);

        return CommonApp::webEncrypt($res);
    }

    /* Create Staff Fabric */
    public static function createStaffFabric(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $staffDetails = CommonApp::getStaffDetailsByEmail($request->email);
        $validated = Validator::make((array)$request,[
            'email' => 'required',
            'workspace_id' => 'required',
            'name' => ['required', Rule::unique('fabric_type')
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
        $fabricArr = [];
        $fabricArr['name'] = $request->name;
        $fabricArr['company_id'] = $staffDetails->company_id;
        $fabricArr['workspace_id'] = $request->workspace_id;
        $fabricArr['user_id'] = '0';
        $fabricArr['staff_id'] =$staffDetails->id;
        $fabricArr['is_default'] = '1';
        $fabricArr['inquiry_reference_id'] = $request->referenceId?? '0';
        $fabricArr['status'] = '1';
        $fabricArr['created_by'] = $staffDetails->id;
        $fabricArr['created_at'] = date('Y-m-d H:i:s');
        $fabricArr['updated_at'] = date('Y-m-d H:i:s');
        FabricType::insert($fabricArr);

        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Fabric added Succesfully"]);
        return CommonApp::webEncrypt($res);
    }

    /* Get the staff created fabric */
    public static function getStaffFabric(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validator->errors()]);
        }
        $whereConditions = [
                ['workspace_id','=',$request->workspace_id],
                ['company_id','=',$request->company_id],
                ['inquiry_reference_id','=',"0"]
        ];
        $fabric = FabricType::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$fabric]);

        return CommonApp::webEncrypt($res);
    }
}
