<?php

namespace App\Http\Controllers\WebSite\Auth;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Roles as ModelsRoles;
use App\Models\RolesAndPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Staff;
use App\Models\RolesPermissionsChanges;
use Exception;
use Laravel\Passport\HasApiTokens;

class Roles extends Controller
{
    /* This function is used to Get the Roles list */
    public function get_roles_list(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(['status_code'=>401,'errors'=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
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

        $res = json_encode(['status_code'=>200,'status'=>'Success','data'=>$result],200);
        return CommonApp::webEncrypt($res);
    }

    /* This function is used to create new role */
    public function create_new_role(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $companyID = $request->company_id;
        $workSpaceID = $request->workspace_id;
        $validator = Validator::make((array)$request,[
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
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $rolesArr = [];
        $rolesArr['name'] = $request->name;
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

        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Role added Successfully"]);
        return CommonApp::webEncrypt($res);
    }

    /* This is used to add new permissions */
    public function add_role_privileges(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'role_id' => 'required',
            'user_id' => 'required',
            'permission_id' => 'required'
        ]);

        $type = (isset($request->type))?$request->type:2;

        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->login_staff_id) && $request->login_staff_id > 0){ //Check if staff have permission to add Permissions
            $per = CommonApp::checkStaffPermission($request,'23');
            $Inq_per = CommonApp::checkStaffPermission($request,'94');
            if($per===0 && $Inq_per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        $companyId = $request->company_id ?? 0;
        $workspaceId = $request->workspace_id ?? 0;
        $userId = $request->user_id ?? 0;
        $roleId = $request->role_id ?? 0;
        $permissionId = $request->permission_id ?? 0;
        $whereCondition=[
            ['company_id','=',$companyId],
            ['workspace_id','=',$workspaceId],
            ['user_id','=',$userId],
            ['permission_id','=',$permissionId],
            ['role_id','=',$roleId]
        ];
        $permissionArray = [];
             /*Update Staff Session Logout for updated roles member */
             if($roleId>0){
                try{
                $staffToken=Staff::select("id")->where("role_id",$roleId)->where("workspace_id",$workspaceId)->where("company_id",$companyId)->get();
                foreach($staffToken as $stff){
                    $dat['company_id'] = $companyId;
                    $dat['workspace_id'] = $workspaceId;
                    $dat['staff_id'] = $stff->id;
                    $dat['type'] = 'Permission';
                    RolesPermissionsChanges::insert($dat);
                    //$stff->tokens()->where('name', 'StaffAPIToken')->delete();
                }
            }catch(Exception $e){
                //throw new InvalidArgumentException("Unable To Save Data");
            }

            }
        /* If Check all boxes are clicked  */
        if($permissionId == 0){
            $whereCondition1 = [
                ['role_privileges.company_id','=',$companyId],
                ['role_privileges.role_id','=',$roleId],
                ['role_privileges.workspace_id','=',$workspaceId],
            ];
            //$alreadyExists = RolesAndPermissions::where($whereCondition1)->get();
            $alreadyExists = RolesAndPermissions::where($whereCondition1)->where('permissions.type',$type)
             ->join('permissions','permissions.id','role_privileges.permission_id')
            ->get();
            if(count($alreadyExists)>0){
                //RolesAndPermissions::where($whereCondition1)->delete();
                RolesAndPermissions::where($whereCondition1)->where('permissions.type',$type)
                ->join('permissions','permissions.id','role_privileges.permission_id')->delete();
            }
            if($request->checked)
            {
                $permissions = ModelsRoles::getPermissionOnly($type);
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
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Role Updated Successfully"]);
            return CommonApp::webEncrypt($res);
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




            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Role Updated Successfully"]);
            return CommonApp::webEncrypt($res);
        }
        else{
            RolesAndPermissions::where('id',$duplicateValue->id)->first()->delete();
            $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Role Updated Successfully"]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* To Get Roles of the company */
    public static function getRoles(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validate = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if($validate->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validate->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['status','=','1']
        ];
        $roles = ModelsRoles::getRolesOnly($whereCondition);

        $res = json_encode(["status_code"=>200,"status"=>"success","data"=>$roles]);
        return CommonApp::webEncrypt($res);
    }

    public static function getModules(){
        $modules = ModelsRoles::getModulesOnly();
        $res = json_encode(["status_code"=>200,"status"=>"success","data"=>$modules]);
        return CommonApp::webEncrypt($res);
    }

    /* This function is used to Get the Roles list */
    public function get_inquiry_roles_list(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(['status_code'=>401,'errors'=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $companyID = $request->company_id;
        $workSpaceID = $request->workspace_id;
        $role_id = ($request->role_id)?$request->role_id:'';
        $module = ($request->module)?$request->module:'';
        $permissions = ModelsRoles::get_inquiry_permissions($module);
        $roles_has_permissions = ModelsRoles::roles_has_permissions($companyID,$workSpaceID,$role_id);
        $result=[];
        $result['permissions'] = $permissions;
        $result['roles_has_permissions'] = $roles_has_permissions;

        $res = json_encode(['status_code'=>200,'status'=>'Success','data'=>$result],200);
        return CommonApp::webEncrypt($res);
    }

    public static function getInquiryModules(){
        $modules = ModelsRoles::getInquiryModulesOnly();
        $res = json_encode(["status_code"=>200,"status"=>"success","data"=>$modules]);
        return CommonApp::webEncrypt($res);
    }
}
