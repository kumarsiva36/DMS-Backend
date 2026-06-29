<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Roles as ModelsRoles;
use App\Models\RolesAndPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Roles extends Controller
{
    /* Get the roles list */
    public function get_roles_list(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['status_code'=>401,'errors'=>$validator->errors()]);
        }
        $companyID = $request->company_id;
        $workSpaceID = $request->workspace_id;
        $role_id = ($request->role_id)?$request->role_id:'';
        $module = ($request->module)?$request->module:'';
        $permissions = ModelsRoles::get_permissions($module);
        $roles_has_permissions = ModelsRoles::roles_has_permissions($companyID,$workSpaceID,$role_id);
        $result=[];
        $result['permissions'] = $permissions;
        $result['roles_has_permissions'] = $roles_has_permissions;

        return response()->json(['status_code'=>200,'status'=>'Success','data'=>$result],200);
    }

    /* Create the new role */
    public function create_new_role(Request $request){

        $companyID = $request->company_id;
        $workSpaceID = $request->workspace_id;
        $validator = Validator::make($request->all(),[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'name' => ['required', Rule::unique('roles')
                        ->where(function ($query) use($companyID, $workSpaceID) {
                            $query->where('company_id',$companyID);
                            $query->where('workspace_id',$workSpaceID);
                            $query->orwhere('is_default','=','0');
                            return $query;
                        })
                    ],

        ]);
        if($validator->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validator->errors()]);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $rolesArr = [];
        $rolesArr['name'] = $request->input('name');
        $rolesArr['company_id'] = $companyID;
        $rolesArr['workspace_id'] = $workSpaceID;
        $rolesArr['user_id'] = $companyDetails->user_id;
        $rolesArr['staff_id'] = "0";
        $rolesArr['is_default'] = "1";
        $rolesArr['status'] = "1";
        $rolesArr['created_by'] = $companyDetails->user_id;
        $rolesArr['created_at']=date("Y-m-d H:i:s");
        $rolesArr['updated_at']=date("Y-m-d H:i:s");
        ModelsRoles::insert($rolesArr);

        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Role added Successfully"]);
    }

    /* Add New Role permissions */
    public function add_role_privileges(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'role_id' => 'required',
            'user_id' => 'required',
            'permission_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validator->errors()]);
        }

        $companyId = $request->input('company_id');
        $workspaceId = $request->input('workspace_id');
        $userId = $request->input('user_id');
        $roleId = $request->input('role_id');
        $permissionId = $request->input('permission_id');
        $whereCondition=[
            ['company_id','=',$companyId],
            ['workspace_id','=',$workspaceId],
            ['user_id','=',$userId],
            ['permission_id','=',$permissionId],
            ['role_id','=',$roleId]
        ];
        $permissionArray = [];
        /* If Check all boxes are clicked  */
        if($permissionId == 0){
            $whereCondition1 = [
                ['company_id','=',$companyId],
                ['role_id','=',$roleId],
                ['workspace_id','=',$workspaceId]
            ];
            $alreadyExists = RolesAndPermissions::where($whereCondition1)->get();
            if(count($alreadyExists)>0){
                RolesAndPermissions::where($whereCondition1)->delete();
            }
            if($request->checked)
            {
                $permissions = ModelsRoles::getPermissionOnly();
                $permissionArray['company_id'] = $companyId;
                $permissionArray['workspace_id'] = $workspaceId;
                $permissionArray['user_id'] = $userId;
                $permissionArray['role_id'] = $roleId;
                foreach ($permissions as $permission) {
                    $permissionArray['permission_id']= $permission->id;
                    $permissionArray['created_at']=date("Y-m-d H:i:s");
                    $permissionArray['updated_at']=date("Y-m-d H:i:s");
                    RolesAndPermissions::insert($permissionArray);
                }
            }
            return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Role Updated Successfully"]);
        }
        $duplicateValue = RolesAndPermissions::select('id')->where($whereCondition)->first();
        if(empty($duplicateValue)){
            $permissionArray['company_id'] = $companyId;
            $permissionArray['workspace_id'] = $workspaceId;
            $permissionArray['user_id'] = $userId;
            $permissionArray['role_id'] = $roleId;
            $permissionArray['permission_id']= $permissionId;
            $permissionArray['created_at']=date("Y-m-d H:i:s");
            $permissionArray['updated_at']=date("Y-m-d H:i:s");
            RolesAndPermissions::insert($permissionArray);

            return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Role Updated Successfully"]);
        }
        else{
            RolesAndPermissions::where('id',$duplicateValue->id)->first()->delete();
            return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Role Updated Successfully"]);
        }
    }

    /* Get the roles */
    public static function getRoles(Request $request){
        $validate = Validator::make($request->all(),[
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if($validate->fails()){
            return response()->json(["status_code"=>401,"error"=>$validate->errors()]);
        }
        $whereCondition = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id]
        ];
        $roles = ModelsRoles::getRolesOnly($whereCondition);

        return response()->json(["status_code"=>200,"status"=>"success","data"=>$roles]);
    }

    /* Get the modules- category names */
    public static function getModules(){
        $modules = ModelsRoles::getModulesOnly();
        return response()->json(["status_code"=>200,"status"=>"success","data"=>$modules]);
    }
}
