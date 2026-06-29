<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Common\CommonApp;
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
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'category'=>'required',

        ]);
        if ($validator->fails()) {
            $res = json_encode(['status_code'=>401,'error' => $validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $getSize = ModelsSize::getSizes($request);
        $res = json_encode(['status_code'=>200,'status'=>'Success','data'=>$getSize]);
        return CommonApp::webEncrypt($res);
    }

    /*
     * Store a newly created size in storage
     * using certain conditions.
     */
    public function store(Request $request)
    {
        $request = CommonApp::webDecrypt($request->getContent());
        $request->name = trim($request->name);
        $validator = Validator::make((array)$request,[
            // 'name' => ['required',Rule::unique('size')
            //                     ->where(function ($query) use($request) {
            //                         $query->where([
            //                             ['company_id','=',$request->company_id],
            //                             ['workspace_id','=',$request->workspace_id],
            //                             ['category','=',ucfirst(trim($request->category))]
            //                         ])->orWhere([
            //                             ['is_default','=','0'],
            //                             ['category','=',ucfirst(trim($request->category))]
            //                         ]);
            //                         // $query->where('workspace_id',$request->workspace_id);
            //                         // $query->where('category',ucfirst(trim($request->category)));
            //                         // $query->orwhere('is_default','=','0');
            //                         return $query;
            //                     })

            //                 ],
            'name'=>'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'status' => 'required',
            'category'=>'required',
        ]);

        if($validator->fails()){
            $res = json_encode(['status_code'=>401,'error' => $validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        //$name = strtoupper($request->name);
        $names = explode(",",$request->name);
        $category = ucfirst(trim($request->category));
        $companyId = (int)$request->company_id;
        $workspaceId = (int)$request->workspace_id;
        $userId = (int)$request->user_id;
        $staffId = (int)$request->staff_id;

        $sizeArray=[];
        $sizeArray['company_id']=$companyId;
        $sizeArray['workspace_id']=$workspaceId;
        $sizeArray['user_id']=$userId;
        $sizeArray['staff_id']=$staffId;
        $sizeArray['is_default']='1';
        $sizeArray['status']=$request->status;
        $sizeArray['created_by']='1';
        $sizeArray['created_at']=date("Y-m-d H:i:s");
        $sizeArray['updated_at']=date("Y-m-d H:i:s");
        $sizeArray['category']=$category;

        foreach($names as $name){
            $name = strtoupper(trim($name));
            $whereCondition=[
                ['name','=',$name],
                ['company_id','=',$companyId],
                ['workspace_id','=',$workspaceId],
                ['category','=',$category],
            ];
            $orwhereCondition=[
                ['name','=',$name],
                ['category','=',$category],
                ['is_default','=','0'],
            ];
            $existsSize = ModelsSize::getSizesIfExists($whereCondition,$orwhereCondition,$name,$category);
            if(empty($existsSize) && $name!=''){
                $sizeArray['name']=$name;
                ModelsSize::insert($sizeArray);
            }
        }

        $res = json_encode(['status_code'=>200,'status'=>'Success','message' => "Size Added Succesfully"]);
        return CommonApp::webEncrypt($res);

        // $whereCondition=[
        //     ['name','=',$name],
        //     ['company_id','=',$companyId],
        //     ['workspace_id','=',$workspaceId],
        //     ['category','=',$category],
        // ];
        // $existsSize = ModelsSize::getSizesIfExists($whereCondition);
        // // dd($existsSize);
        // if(empty($existsSize)){
        //     $sizeArray=[];
        //     $sizeArray['name']=$name;
        //     $sizeArray['company_id']=$companyId;
        //     $sizeArray['workspace_id']=$workspaceId;
        //     $sizeArray['user_id']=$userId;
        //     $sizeArray['staff_id']=$staffId;
        //     $sizeArray['is_default']='1';
        //     $sizeArray['status']=$request->status;
        //     $sizeArray['created_by']='1';
        //     $sizeArray['created_at']=date("Y-m-d H:i:s");
        //     $sizeArray['updated_at']=date("Y-m-d H:i:s");
        //     ModelsSize::insert($sizeArray);
        //     $res = json_encode(['status_code'=>200,'status'=>'Success','message' => "Size Added Succesfully"]);
        //     return CommonApp::webEncrypt($res);
        // }else{
        //     /* This condition is to re-enable the deleted size if the same size is added */
        //     $status = "3";
        //     $ifDeletedSizeStatus = ModelsSize::select('status')->where($whereCondition)->first();
        //     $ifDeletedSize = ModelsSize::where($whereCondition)->first();
        //     if($ifDeletedSizeStatus->status === $status){
        //         $ifDeletedSize->status ='1';
        //         $ifDeletedSize->save();
        //         $res = json_encode(['status_code'=>200,'status'=>'Success','message' => "Size Added Succesfully"]);
        //         return CommonApp::webEncrypt($res);
        //     }
        //     else{
        //         $res = json_encode(['status_code'=>400,'status'=>'Failed','message' => "Size Already Exists"]);
        //         return CommonApp::webEncrypt($res);
        //     }
        // }
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
        $orwhereCondition=[];
        $existsSize = ModelsSize::getSizesIfExists($whereCondition,$orwhereCondition);
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

    /*
     * Gets and Displays the default and added size categories of the company.
    */
    public function get_size_categories(Request $request)
    {
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required'

        ]);
        if ($validator->fails()) {
            $res = json_encode(['status_code'=>401,'error' => $validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $getSizeCat = ModelsSize::getSizeCategories($request);
        $res = json_encode(['status_code'=>200,'status'=>'Success','data'=>$getSizeCat]);
        return CommonApp::webEncrypt($res);
    }
    /*
     * Add Size Category and Sizes.
    */
    public function add_size_category(Request $request)
    {
        $request = CommonApp::webDecrypt($request->getContent());

        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'category'=>'required',
            'size' => 'required',

        ]);
        if ($validator->fails()) {
            $res = json_encode(['status_code'=>401,'error' => $validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $existsSize = ModelsSize::getCategoryIfExists($request);
        $category = ucfirst(trim($request->category));
        $companyId = (int)$request->company_id;
        $workspaceId = (int)$request->workspace_id;
        $userId = (int)$request->user_id;
        $staffId = (int)$request->staff_id;
        $sizes = explode(",",$request->size);
        if(empty($existsSize)){
            $sizeArray=[];
            $sizeArray['category']=$category;
            $sizeArray['company_id']=$companyId;
            $sizeArray['workspace_id']=$workspaceId;
            $sizeArray['user_id']=$userId;
            $sizeArray['staff_id']=$staffId;
            $sizeArray['is_default']='1';
            $sizeArray['status']='1';
            $sizeArray['created_by']='1';
            $sizeArray['created_at']=date("Y-m-d H:i:s");
            $sizeArray['updated_at']=date("Y-m-d H:i:s");
            foreach($sizes as $name){
                $name = strtoupper(trim($name));
                $whereCondition=[
                    ['name','=',$name],
                    ['company_id','=',$companyId],
                    ['workspace_id','=',$workspaceId],
                    ['category','=',$category],
                ];
                $orwhereCondition=[
                    ['name','=',$name],
                    ['category','=',$category],
                    ['is_default','=','0'],
                ];
                $existsSizes = ModelsSize::getSizesIfExists($whereCondition,$orwhereCondition,$name,$category);
                if(empty($existsSizes) && $name!=''){
                    $sizeArray['name']=$name;
                    ModelsSize::insert($sizeArray);
                }
            }

            $res = json_encode(['status_code'=>200,'status'=>'Success','message' => "Category and Size Added Succesfully"]);
            return CommonApp::webEncrypt($res);
        }else{
            $res = json_encode(['status_code'=>400,'status'=>'Failed','message' => "Category Already Exists"]);
            return CommonApp::webEncrypt($res);
        }
    }
}
