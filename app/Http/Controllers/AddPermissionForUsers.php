<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use App\Models\RolesAndPermissions;
use App\Models\Workspace;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AddPermissionForUsers extends Controller
{
    //
    public static function addPermissionForRoles(Request $request){
        $validated = Validator::make($request->all(),[
            'perm_id'=>'required|integer|min:1',
        ]);
        if($validated->fails()) {
            Session::flash('failure', $validated->errors());
        }
        $workspaces = Workspace::all();
        $roles = Roles::all();
        $request->perm_id = ltrim($request->perm_id,'0');
        if($request->perm_id > 0){
            try{
                foreach($workspaces as $workspace){
                    $arr=[];
                    foreach($roles as $role){
                        if($role->is_default == "0" && $role->status == "1"){
                            $arr['company_id'] = $workspace->company_id;
                            $arr['workspace_id'] = $workspace->id;
                            $arr['user_id'] = $workspace->user_id;
                            $arr['role_id'] = $role->id;
                            $arr['permission_id'] = $request->perm_id;
                            $arr['status'] = "1";
                            $arr['created_at'] = date('Y-m-d H:i:s');
                            $arr['updated_at'] = date('Y-m-d H:i:s');
                            RolesAndPermissions::insert($arr);
                        }
                        elseif(($role->is_default== "1" && $role->status == "1")
                        && $workspace->id === $role->workspace_id){
                            $arr['company_id'] = $role->company_id;
                            $arr['workspace_id'] = $role->workspace_id;
                            $arr['user_id'] = $role->user_id;
                            $arr['role_id'] = $role->id;
                            $arr['permission_id'] = $request->perm_id;
                            $arr['status'] = "1";
                            $arr['created_at'] = date('Y-m-d H:i:s');
                            $arr['updated_at'] = date('Y-m-d H:i:s');
                            RolesAndPermissions::insert($arr);
                        }
                    }
                }
                $successMsg = 'All Roles are granted with Permission ID : '.$request->perm_id;
                Session::flash('success',$successMsg);
            }catch(Exception $e){
                Session::flash('failure',$e->getMessage());
            }
        }else{
            Session::flash("failure",'Enter ID Greater Than 1');
        }

        return back();
    }
}
