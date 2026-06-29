<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Roles extends Model
{
    use HasFactory;

    protected $table = 'roles';

    static function get_permissions($module)
    {
    	$query =  DB::table('permissions')->select('permissions.sub_module','permissions.module','permissions.display_module','permissions.id','permissions.display_name')->where('status','1')->where('type','1')->orderby('order_sequence','ASC');
        if($module!="")
            $query->where('permissions.module','=',$module);
    	$result = $query->get();
        /* Old Array format */
    	/* $arr=array();$i=$j=0;$module='';
    	foreach ($result as $value) {
    		if($i==0)
    			$module = $value->module;

    		if($module != $value->module || $j==0){
                $i=0;
                $module = $value->module;
            }


    		$arr[$value->module][$i]['sub_module']=$value->sub_module;
    		$arr[$value->module][$i]['id']=$value->id;
    		$j++;$i++;
		} */

        $arr=array();$i=$k=$j=0;$module=''; $sub_moudle_arr=[];
    	foreach ($result as $value) {
            $k++;
    		if($i==0){
    			$module = $value->module;
                $display_module = $value->display_module;
            }

            if($module == $value->module)
            {
                $sub_moudle_arr[$i]['sub_module']=$value->sub_module;
                $sub_moudle_arr[$i]['display_name']=$value->display_name;
                $sub_moudle_arr[$i]['id']=$value->id;
                $i++;
            }else{
                $arr[$j]['module']=$module;
                $arr[$j]['display_module']=$display_module;
                $arr[$j]['id']=rand(100000,999999);
                $arr[$j]['sub_module_list']=$sub_moudle_arr;
                $i=0;
                $module = $value->module;
                $display_module = $value->display_module;
                $j++;
                $sub_moudle_arr=[];
                $sub_moudle_arr[$i]['sub_module']=$value->sub_module;
                $sub_moudle_arr[$i]['display_name']=$value->display_name;
                $sub_moudle_arr[$i]['id']=$value->id;
                $i++;
            }

            if(count($result)==$k){
                $arr[$j]['module']=$module;
                $arr[$j]['display_module']=$display_module;
                $arr[$j]['id']=rand(100000,999999);;
                $arr[$j]['sub_module_list']=$sub_moudle_arr;
            }


		}
        //echo '<pre>'; print_r($arr); exit;

		return $arr;
    }

    static function roles_has_permissions($companyID,$workspaceID,$role_id)
    {
        $query =  DB::table('roles')->select('permissions.id','permissions.sub_module','permissions.module','roles.name as role','roles.id as role_id');
        $query->leftJoin('role_privileges', function($join) use($companyID,$workspaceID)
                         {
                             $join->on('role_privileges.role_id', '=', 'roles.id');
                             $join->on('role_privileges.company_id','=',DB::raw($companyID));
                             $join->on('role_privileges.workspace_id','=',DB::raw($workspaceID));

                         });
    	$query->leftjoin('permissions','role_privileges.permission_id', '=', 'permissions.id');
		$query->where('roles.status','=','1');
        if($companyID!='')
        {
            $query->where( function  ($query) use($companyID)
            {
                $query->where('roles.company_id','=',$companyID)
                    ->orWhere('roles.is_default','=','0');
            });
            //$query->where('role_privileges.company_id','=',$companyID);
        }
        if($workspaceID!='')
        {
            $query->where( function  ($query) use($workspaceID)
            {
                $query->where('roles.workspace_id','=',$workspaceID)
                    ->orWhere('roles.is_default','=','0');
            });
            //$query->where('role_privileges.workspace_id','=',$workspaceID);
        }
        if($role_id!="")
        {
            $query->where('roles.id','=',$role_id);
        }
        $query->orderBy('role_id','ASC')->orderBy('roles.order_sequence','DESC')->orderby('module','ASC');
		$result = $query->get();
		// $arr=array();$i=$j=0;$role='';
    	// foreach ($result as $value) {
    	// 	if($i==0)
    	// 		$role = $value->role;

    	// 	if($role != $value->role || $j==0){
        //         $i=0;
        //         $role = $value->role;
        //     }

    	// 	$arr[$value->role][$i]['sub_module']=$value->sub_module;
    	// 	$arr[$value->role][$i]['id']=$value->id;
    	// 	$arr[$value->role][$i]['module']=$value->module;
    	// 	$arr[$value->role][$i]['role_id']=$value->role_id;
    	// 	$j++;$i++;

		// }

        $arr=array();$i=$k=$j=0;$role=''; $sub_moudle_arr=[];$sub_module_id='';
    	foreach ($result as $value) {
            $k++;
    		if($i==0){
                $role = $value->role;
                $role_id = $value->role_id;
            }


            if($role == $value->role)
            {
                $sub_moudle_arr[$i]['sub_module']=$value->sub_module;
                $sub_moudle_arr[$i]['id']=$value->id;
                $i++;
                $sub_module_id= ($value->id!='')?$sub_module_id.','.$value->id.'|':'';
            }else{
                $arr[$j]['role']=$role;
                $arr[$j]['id']=$role_id;
                $arr[$j]['sub_module_list']=$sub_moudle_arr;
                $arr[$j]['sub_module_ids']=$sub_module_id;
                $i=0;
                $role = $value->role;
                $role_id = $value->role_id;
                $j++;
                $sub_moudle_arr=[];$sub_module_id='';
                $sub_moudle_arr[$i]['sub_module']=$value->sub_module;
                $sub_moudle_arr[$i]['id']=$value->id;
                $sub_module_id= ($value->id!='')?$sub_module_id.','.$value->id.'|':'';
                $i++;
            }

            if(count($result)==$k){
                $arr[$j]['role']=$role;
                $arr[$j]['id']=$role_id;
                $arr[$j]['sub_module_list']=$sub_moudle_arr;
                $arr[$j]['sub_module_ids']=$sub_module_id;
            }


		}
       // dd($arr);
		return $arr;
    }

    static function getRolesOnly($condition){
        $roles = DB::table('roles')->where($condition)->orWhere('is_default','0')->where('status','1')->select('id','name')
        ->orderBy('order_sequence','DESC')->get();
        return $roles;
    }

    /* If the user checks the check all box */
    static function getPermissionOnly($type=2){
        $permissions = DB::table('permissions')->select('id','sub_module','display_name')->where('status','1')->where('type',$type)->get();
        return $permissions;
    }

    static function getModulesOnly(){
        $modules = DB::table('permissions')->select('module','display_module')->groupBy('module')->where('status','1')->where('type','1')->orderby('order_sequence','ASC')->get();
        return $modules;
    }

    static function getRoleName($roleId){
        $roleName = DB::table('roles')->where('id',$roleId)->select('id','name')->first();
        return $roleName;
    }

    public static function getPermissionsForStaff($staff){
        $permissions = DB::table('role_privileges')->where('company_id',$staff->company_id)->where('workspace_id',$staff->workspace_id)
                        ->where('role_id',$staff->role_id)->get();
        $permissionArr=[];
        if(count($permissions)=== 0){
            return $permissionArr;
        }else{
            foreach($permissions as $permission){
                if(Roles::getPermissionsOnly($permission->permission_id)!=null)
                    $permissionArr[] = (Roles::getPermissionsOnly($permission->permission_id))->sub_module;
            }
            return $permissionArr;
        }
    }
    public static function getPermissionsOnly($permissionId){
        $permissions = DB::table('permissions')->where('id',$permissionId)->where('status','1')->select('sub_module')->first();
        return $permissions;
    }
    public static function getModulesForStaff($staff){
        $permissions = DB::table('role_privileges')->where('company_id',$staff->company_id)->where('workspace_id',$staff->workspace_id)
                        ->where('role_id',$staff->role_id)->get();
        $modulesArr=[];
        if(count($permissions)=== 0){
            return $modulesArr;
        }else{
            foreach($permissions as $permission){
                if(Roles::getModulesOnlyForStaff($permission->permission_id)!=null)
                    $modulesArr[] = (Roles::getModulesOnlyForStaff($permission->permission_id))->module;
            }
            return $modulesArr;
        }
    }
    public static function getModulesOnlyForStaff($permissionId){
        $modules = DB::table('permissions')->where('id',$permissionId)->where('status','1')->select('module')->first();
        return $modules;
    }
    public static function getDefaultRolesOnly(){
        $modules = DB::table('roles')->where('is_default','0')->where('status','1')->get();
        return $modules;
    }

    static function get_inquiry_permissions($module)
    {
    	$query =  DB::table('permissions')->select('permissions.sub_module','permissions.module','permissions.display_module','permissions.id','permissions.display_name')
        ->where('status','1')->where('type','2')->orderby('order_sequence','ASC');
        if($module!="")
            $query->where('permissions.module','=',$module);
    	$result = $query->get();
        $arr=array();$i=$k=$j=0;$module=''; $sub_moudle_arr=[];
    	foreach ($result as $value) {
            $k++;
    		if($i==0){
    			$module = $value->module;
                $display_module = $value->display_module;
            }

            if($module == $value->module)
            {
                $sub_moudle_arr[$i]['sub_module']=$value->sub_module;
                $sub_moudle_arr[$i]['display_name']=$value->display_name;
                $sub_moudle_arr[$i]['id']=$value->id;
                $i++;
            }else{
                $arr[$j]['module']=$module;
                $arr[$j]['display_module']=$display_module;
                $arr[$j]['id']=rand(100000,999999);
                $arr[$j]['sub_module_list']=$sub_moudle_arr;
                $i=0;
                $module = $value->module;
                $display_module = $value->display_module;
                $j++;
                $sub_moudle_arr=[];
                $sub_moudle_arr[$i]['sub_module']=$value->sub_module;
                $sub_moudle_arr[$i]['display_name']=$value->display_name;
                $sub_moudle_arr[$i]['id']=$value->id;
                $i++;
            }
            if(count($result)==$k){
                $arr[$j]['module']=$module;
                $arr[$j]['display_module']=$display_module;
                $arr[$j]['id']=rand(100000,999999);;
                $arr[$j]['sub_module_list']=$sub_moudle_arr;
            }
		}
		return $arr;
    }

    static function getInquiryModulesOnly(){
        $modules = DB::table('permissions')->select('module','display_module')->groupBy('module')->where('status','1')->where('type','2')->orderby('order_sequence','ASC')->get();
        return $modules;
    }
}
