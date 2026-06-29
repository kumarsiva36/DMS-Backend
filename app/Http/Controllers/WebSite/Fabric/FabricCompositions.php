<?php

namespace App\Http\Controllers\Website\Fabric;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\FabricComposition;
use App\Common\CommonApp;

class FabricCompositions extends Controller
{
       /* Create new Fabric */
       public function createFabricComposition(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $request->referenceId = $request->referenceId?? '0';
        $request->name = trim($request->name);
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'name' => ['required', Rule::unique('fabric_composition')
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
        FabricComposition::insert($fabricArr);

        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Fabric added Succesfully"]);
        return CommonApp::webEncrypt($res);
    }

       /* Get the fabric composition */
       public function getFabricComposition(Request $request){
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
        $fabric = FabricComposition::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$fabric]);

        return CommonApp::webEncrypt($res);
    }
}
