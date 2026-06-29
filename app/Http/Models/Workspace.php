<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class Workspace extends Model
{
    use HasFactory;

    protected $table = 'workspace';
    protected $fillable = ['id','name','user_id','company_id','workspace_type','created_by','status','created_at','updatred_at'];

    public static function getWorkspaceDetails($request){
        $workspace = Workspace::where('company_id',$request->company_id)->where('id',$request->workspace_id)->first();

        return $workspace;
    }

    public static function getWorkspaceCount($companyID){
        $count = Workspace::where('company_id',$companyID)->count();
        return $count;
    }

    public static function createWorkspace($request,$companyDetails){
        DB::beginTransaction();
        try{
            $userDetails = User::getUserByID($companyDetails->user_id);
            $workspaceArr = [];
            $workspaceArr['name'] = $request->name;
            $workspaceArr['user_id'] = $companyDetails->user_id;
            $workspaceArr['company_id'] = $companyDetails->id;
            $workspaceArr['workspace_type'] = $userDetails->user_type;
            $workspaceArr['created_by'] = $companyDetails->user_id ;
            $workspaceArr['status'] = '1';
            $workspaceArr['created_at'] = date("Y-m-d H:i:s");
            $workspaceArr['updated_at'] = date("Y-m-d H:i:s");
            $workspace= Workspace::create($workspaceArr);
            $workspace_id = $workspace->id;
            /* To Insert default permissions for the Manager, Supervisior, Staff and Guest */
            // $permissions = Roles::getPermissionOnly();
            // $roles = Roles::getDefaultRolesOnly();
            $permissionArr['company_id']=$companyDetails->id;
            $permissionArr['workspace_id']=$workspace->id;
            $permissionArr['user_id']=$companyDetails->user_id;
            $manager = config('constant.rolesAndPermissions.Manager');
            if(!empty($manager)){
                $permissionArr['role_id']=2;
                foreach($manager as $value){
                    $permissionArr['permission_id']=$value;
                    $permissionArr['status']='1';
                    $permissionArr['created_at']=date('Y-m-d H:i:s');
                    $permissionArr['updated_at']=date('Y-m-d H:i:s');
                    RolesAndPermissions::insert($permissionArr);
                }
            }
            $merchandiser = config('constant.rolesAndPermissions.Merchandiser');
            if(!empty($merchandiser)){
                $permissionArr['role_id']=6;
                foreach($merchandiser as $value){
                    $permissionArr['permission_id']=$value;
                    $permissionArr['status']='1';
                    $permissionArr['created_at']=date('Y-m-d H:i:s');
                    $permissionArr['updated_at']=date('Y-m-d H:i:s');
                    RolesAndPermissions::insert($permissionArr);
                }
            }
            $supervisor = config('constant.rolesAndPermissions.Supervisor');
            if(!empty($supervisor)){
                $permissionArr['role_id']=5;
                foreach($supervisor as $value){
                    $permissionArr['permission_id']=$value;
                    $permissionArr['status']='1';
                    $permissionArr['created_at']=date('Y-m-d H:i:s');
                    $permissionArr['updated_at']=date('Y-m-d H:i:s');
                    RolesAndPermissions::insert($permissionArr);
                }
            }
            $staff = config('constant.rolesAndPermissions.Staff');
            if(!empty($staff)){
                $permissionArr['role_id']=3;
                foreach($staff as $value){
                    $permissionArr['permission_id']=$value;
                    $permissionArr['status']='1';
                    $permissionArr['created_at']=date('Y-m-d H:i:s');
                    $permissionArr['updated_at']=date('Y-m-d H:i:s');
                    RolesAndPermissions::insert($permissionArr);
                }
            }
            $guest = config('constant.rolesAndPermissions.Guest');
            if(!empty($guest)){
                $permissionArr['role_id']=4;
                foreach($guest as $value){
                    $permissionArr['permission_id']=$value;
                    $permissionArr['status']='1';
                    $permissionArr['created_at']=date('Y-m-d H:i:s');
                    $permissionArr['updated_at']=date('Y-m-d H:i:s');
                    RolesAndPermissions::insert($permissionArr);
                }
            }
            $preferenceArray=[];
            $preferenceArray['company_id']=$permissionArr['company_id'];
            $preferenceArray['workspace_id']=$permissionArr['workspace_id'];
            $preferenceArray['user_id']=$permissionArr['user_id'];
            $preferenceArray['staff_id']=0;
            $preferenceArray['date_format']="Y-m-d";
            $preferenceArray['time_zone_format']="";
            $preferenceArray['language_id'] = (Language::where('lang_code',$companyDetails->language)->select('id')->first())->id;
            $preferenceArray['created_at']=date('Y-m-d H:i:s');
            $preferenceArray['updated_at']=date('Y-m-d H:i:s');
            UserPreferences::insert($preferenceArray);
            $returnArr=[];
            $returnArr['workspaceID'] = $workspace_id;
            $returnArr['workspaceType'] = $userDetails->user_type;
            $returnArr['workspaceName'] = $request->name;
            $returnArr['language'] = $companyDetails->language;
        }catch(Exception $e){
            DB::rollBack();
            Log::info($e->getMessage());
            throw new InvalidArgumentException('Unable to post Data');
        }
        DB::commit();
        return $returnArr;
    }

    public static function getWorkspaces($companyDetails){
        $workspaces = Workspace::where('company_id',$companyDetails->id)->where('user_id',$companyDetails->user_id)
        ->select('id','name','workspace_type')
        ->get();

        return $workspaces;
    }
}
