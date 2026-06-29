<?php

namespace App\Http\Controllers\Mobile\v1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color as ColorModel;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
class Color extends Controller
{
    /*Get Color Details Using Conditions*/
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

        $getColors = ColorModel::getColors($request);
        return response()->json(['status_code'=>200,'status'=>'success','data'=>$getColors]);
    }

    /*Save Color Details Using Conditions*/
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required',Rule::unique('color')
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
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code'=>401,'error' => $validator->errors()]);
        }

        $name = ucfirst(strtolower($request->input('name')));
        $companyId = (int)$request->input('company_id');
        $workspaceId = (int)$request->input('workspace_id');
        $userId = (int)$request->input('user_id');
        $staffId = (int)$request->input('staff_id');
        $createdBy = (int)$request->input('created_by');
        $whereCondition=[
            ['name','=',$name],
            ['company_id','=',$companyId],
            ['workspace_id','=',$workspaceId],
            ['user_id','=',$userId],
            ['staff_id','=',$staffId]
        ];
        $existsColor = ColorModel::checkIfColorExists($whereCondition);

        if(empty($existsColor)){
            $colorArray=[];
            $colorArray['name']=$name;
            $colorArray['company_id']=$companyId;
            $colorArray['workspace_id']=$workspaceId;
            $colorArray['user_id']=$userId;
            $colorArray['staff_id']=$staffId;
            $colorArray['is_default']='1';
            $colorArray['status']=$request->status;
            $colorArray['created_by']=$createdBy;
            $colorArray['created_at']=date("Y-m-d H:i:s");
            $colorArray['updated_at']=date("Y-m-d H:i:s");
            ColorModel::insert($colorArray);
            return response()->json(['status_code'=>200, 'status'=>'success','message' => "Color Added Succesfully"]);
        }else{
            return response()->json(['status_code'=>400, 'status'=>'success','message' => "Color Name Already Exists"]);
        }
    }

    /*Update Color Details Using Conditions*/
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required',Rule::unique('color')
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
            'staff_id' => 'required',
            'id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code'=>401,'error' => $validator->errors()]);
        }
        $id = (int)$request->input('id');
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
        $existsColor = ColorModel::checkIfColorExists($whereCondition);

        if(empty($existsColor)){
            $colorArray=[];
            $colorArray['name']=$name;
            $colorArray['staff_id']=$staffId;
            $colorArray['status']=$request->status;
            $colorArray['updated_at']=date("Y-m-d H:i:s");
            ColorModel::where('id',$id)->update($colorArray);
            return response()->json(['status_code'=>200, 'status'=>'success','message' => "Color Updated Succesfully"]);
        }else{
            return response()->json(['status_code'=>400, 'status'=>'success','message' => "Color Name Already Exists"]);
        }
    }

    /*Delete Color Details Using Conditions*/
    public function delete(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'id' => 'required'

        ]);
        if ($validator->fails()) {
            return response()->json(['status_code'=>401,'error' => $validator->errors()]);
        }
        $id = (int)$request->input('id');
        $name = $request->input('name');
        $companyId = (int)$request->input('company_id');
        $workspaceId = (int)$request->input('workspace_id');
        $userId = (int)$request->input('user_id');
        $staffId = (int)$request->input('staff_id');
        $whereCondition=[
            ['id','=',$id],
            ['name','=',$name],
            ['company_id','=',$companyId],
            ['workspace_id','=',$workspaceId],
            ['user_id','=',$userId],
        ];
        $existsColor = ColorModel::checkIfColorExists($whereCondition);

        if(!empty($existsColor)){
            $colorArray=[];
            $colorArray['status']='3';
            $colorArray['updated_at']=date("Y-m-d H:i:s");
            ColorModel::deleteColor($id,$colorArray);
            return response()->json(['status_code'=>200, 'status'=>'success','message' => "Color Deleted Succesfully"]);
        }else{
            return response()->json(['status_code'=>400, 'status'=>'success','message' => "Color Name Not Exists"]);
        }

    }

    /* To Edit Color */
    public static function edit(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'id' => 'required'

        ]);
        if ($validator->fails()) {
            return response()->json(['status_code'=>401,'error' => $validator->errors()]);
        }
        $color = ColorModel::getColorForEdit($request);

        return response()->json(['status_code'=>200, 'status'=>'success','data' => $color]);
    }
}
