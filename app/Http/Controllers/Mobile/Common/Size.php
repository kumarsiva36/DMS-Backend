<?php

namespace App\Http\Controllers\Mobile\Common;

use App\Http\Controllers\Controller;
use App\Models\Size as ModelsSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Size extends Controller
{
    /*
     * Gets and Displays the default and added sizes of
     * the company.
    */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required'

        ]);
        if ($validator->fails()) {
         return response()->json(['status_code'=>401,'error' => $validator->errors()]);
        }

        $getSize = ModelsSize::getSizes($request);
        return response()->json(['status_code'=>200,'status'=>'Success','data'=>$getSize]);
    }

    /*
     * Store a newly created size in storage
     * using certain conditions.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required',Rule::unique('size')
                                ->where(function ($query) use($request) {
                                    $query->where('company_id',$request->company_id);
                                    $query->where('workspace_id',$request->workspace_id);
                                    $query->orwhere('is_default','=','0');
                                    return $query;
                                })],
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['status_code'=>401,'error' => $validator->errors()]);
        }
        $name = strtoupper($request->input('name'));
        $companyId = (int)$request->input('company_id');
        $workspaceId = (int)$request->input('workspace_id');
        $userId = (int)$request->input('user_id');
        $staffId = (int)$request->input('staff_id');
        $whereCondition=[
            ['name','=',$name],
            ['company_id','=',$companyId],
            ['workspace_id','=',$workspaceId]
        ];
        $existsSize = ModelsSize::getSizesIfExists($whereCondition);
        // dd($existsSize);
        if(empty($existsSize)){
            $sizeArray=[];
            $sizeArray['name']=$name;
            $sizeArray['company_id']=$companyId;
            $sizeArray['workspace_id']=$workspaceId;
            $sizeArray['user_id']=$userId;
            $sizeArray['staff_id']=$staffId;
            $sizeArray['is_default']='1';
            $sizeArray['status']=$request->status;
            $sizeArray['created_by']='1';
            $sizeArray['created_at']=date("Y-m-d H:i:s");
            $sizeArray['updated_at']=date("Y-m-d H:i:s");
            ModelsSize::insert($sizeArray);
            return response()->json(['status_code'=>200,'status'=>'Success','message' => "Size Added Succesfully"]);
        }else{
            /* This condition is to re-enable the deleted size if the same size is added */
            $status = "3";
            $ifDeletedSizeStatus = ModelsSize::select('status')->where($whereCondition)->first();
            $ifDeletedSize = ModelsSize::where($whereCondition)->first();
            if($ifDeletedSizeStatus->status === $status){
                $ifDeletedSize->status ='1';
                $ifDeletedSize->save();
                return response()->json(['status_code'=>200,'status'=>'Success','message' => "Size Added Succesfully"]);
            }
            else{
                return response()->json(['status_code'=>400,'status'=>'Failed','message' => "Size Already Exists"]);
            }
        }
    }
    /*
     * Update the company specific size in storage.
    */
    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required',Rule::unique('size')
                                ->where(function ($query) use($request) {
                                    $query->where('id',"!=",$request->id);
                                    $query->where('company_id',$request->company_id);
                                    $query->where('workspace_id',$request->workspace_id);
                                    $query->orwhere('is_default','=','0');
                                    return $query;
                                })],
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $name = $request->input('name');
        $companyId = (int)$request->input('company_id');
        $workspaceId = (int)$request->input('workspace_id');
        $userId = (int)$request->input('user_id');
        $staffId = (int)$request->input('staff_id');
        $whereCondition=[
            ['id','!=',$id],
            ['name','=',$name],
            ['company_id','=',$companyId],
            ['workspace_id','=',$workspaceId],
            ['user_id','=',$userId],
                       ];
        $existsSize = ModelsSize::getSizesIfExists($whereCondition);
        if(empty($existsSize)){
            $sizeArray=[];
            $sizeArray['name']=$name;
            $sizeArray['staff_id']=$staffId;
            $sizeArray['status']=$request->status;
            $sizeArray['updated_at']=date("Y-m-d H:i:s");
            ModelsSize::where('id',$id)->update($sizeArray);
            return response()->json(['status_code'=>200,'status'=>'Success','message' => "Size Updated Succesfully"]);
        }else{
            return response()->json(['status_code'=>400,'status'=>'Failed','message' => "Size already exists and could not be updated"]);
        }
    }

    /* Remove the company specific size from storage. */
    public function destroy($id, Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'company_id' => 'required',
        //     'workspace_id' => 'required',
        //     'user_id' => 'required',
        //     'staff_id' => 'required'
        // ]);
        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()], 401);
        // }
        // $name = $request->input('name');
        // $companyId = (int)$request->input('company_id');
        // $workspaceId = (int)$request->input('workspace_id');
        // $userId = (int)$request->input('user_id');
        // $whereCondition=[
        //     ['id','=',$id],
        //     ['name','=',$name],
        //     ['company_id','=',$companyId],
        //     ['workspace_id','=',$workspaceId],
        //     ['user_id','=',$userId],
        //                ];
        $existsSize = ModelsSize::deleteSize($id);
        if(!empty($existsSize)){
           $sizeArray=[];
           $sizeArray['status']='3';
           $sizeArray['updated_at']=date("Y-m-d H:i:s");
           ModelsSize::where('id',$id)->update($sizeArray);
           return response()->json(['status_code'=>200, 'status'=>'Success','message' => "Size Deleted Succesfully"]);
        }else{
            return response()->json(['status_code'=>400, 'status'=>'Failure','message' => "Size does not exists"]);
        }

    }
}
