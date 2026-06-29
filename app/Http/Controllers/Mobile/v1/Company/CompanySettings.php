<?php

namespace App\Http\Controllers\Mobile\v1\Company;

use App\Common\CommonApp;
use App\Common\Logs;
use App\Common\Uploads;
use App\Http\Controllers\Controller;
use App\Models\CompanySettings as ModelsCompanySettings;
use App\Models\Language;
use App\Models\Order;
use App\Models\PaymentHistory;
use App\Models\Plan;
use App\Models\Staff;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

use App\Models\Roles;
use App\Models\UserPreferences;

class CompanySettings extends Controller
{
    /* Add new company details */
    public static function registerCompany(Request $request){
        $validated = Validator::make($request->all(),[
            'company_name' => 'required',
            'contact_person' => 'required',
            'contact_number' => 'required|numeric',
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validated->errors()]);
        }

        $company_id = ModelsCompanySettings::companyRegisteration($request);

        return response()->json(["status_code"=>200,"status" =>"Success","company_id"=>$company_id,
        "message"=>"Company Details Successsfully Added"],200);
    }

    /* Get the Company Details for Viewing */

    public static function viewCompanyDetails(Request $request){
        $validated = Validator::make($request->all(),[
            'company_id' => 'required'
        ]);
        if ($validated->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validated->errors()]);
        }
        $companyDetails= ModelsCompanySettings::getCompanyDetails($request);
        return  response()->json(["status_code"=>200,"status"=>"Success","data"=>$companyDetails],200);
    }

    /* Get the workspace Type from the DB */
    public function workspaceType(){
        $workspaceType = WorkspaceType::getAllWorkspaceType();
        return response()->json(["status_code"=>200,"data"=>$workspaceType]);
    }

    /* Create a new Workspace for the company */
    public function createWorkspace(Request $request){
        $companyDetails = ModelsCompanySettings::getCompanyInfoUsingID($request->company_id);
        $validated = Validator::make($request->all(),[
            'company_id' => 'required',
            'name' => ['required', Rule::unique('workspace')
                        ->where(function ($query) use($companyDetails) {
                            $query->where('id',$companyDetails->id);
                            $query->where('user_id',$companyDetails->user_id);
                            return $query;
                        })],
        ]);

        if($validated->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validated->errors()]);
        }
        $workSpaceCount=Workspace::getWorkspaceCount($request->company_id);
        if($workSpaceCount==0){
            $workspaceDetails = Workspace::createWorkspace($request,$companyDetails);
            return response()->json(["status_code"=>200,"status" =>"Success","workspace_id"=>$workspaceDetails['workspaceID'],
            "workspaceType"=>$workspaceDetails['workspaceType'],"workspaceName"=>$workspaceDetails['workspaceName'],
            "language"=>$workspaceDetails['language'],"message"=>"Workspace Created Successfully"]);
        }else{
            return response()->json(["status_code"=>201,"status" =>"Error","message"=>"Workspace Already Exists"]);
        }
    }

    /* Get the company specific workspace details */
    public function getWorkspace(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required'
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
       // $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $workspace = Workspace::where('company_id',$request->company_id)
                        ->select('id','name','workspace_type')
                        ->first();
        $workspaceList=[];
        if(isset($request->staff_id) && $request->staff_id>0){
            $staff = Staff::where('id',$request->staff_id)->select('id','user_id','company_id','workspace_id','first_name','last_name','role_id','user_id','email')->first();
                $role=Roles::getRoleName($staff->role_id);

            $staffPresentList = Staff::where('email',$staff->email)->get();

            $staffPreference = UserPreferences::where('company_id',$staff->company_id)->where('workspace_id',$staff->workspace_id)
            ->where('staff_id',$staff->id)->first();
            if(empty($staffPreference)){
                $dateFormat="";
            }else{
                $dateFormat=$staffPreference->date_format;
            }
            $firstName=$staff->first_name;
            $lastName=$staff->last_name;
            $i=0;
            foreach ($staffPresentList as $staffs){
                $staffPreferences = UserPreferences::where('company_id',$staffs->company_id)->where('workspace_id',$staffs->workspace_id)
                ->where('staff_id',$staffs->id)->first();
                if(empty($staffPreferences)){
                    $dateFormat="";
                    $language_id=0;
                    $language_code='en';
                }else{
                    $dateFormat=$staffPreferences->date_format;
                    $language_id=$staffPreferences->language_id;
                    if($language_id>0){
                    $langc=Language::getLanguagesCodeUsingId($language_id);
                    $language_code=$langc->lang_code;
                    }else{
                    $language_code='en';
                    }
                }
                $workspaces = CommonApp::getWorkspaceDetails($staffs->user_id);
                $planExpiryDate = ModelsCompanySettings::getExpiryInfo($staffs);
                if(strtotime(date('Y-m-d')) >= strtotime(date('Y-m-d',strtotime($planExpiryDate->account_expire_at))) ){
                    $plan = "Expired";
                }else{
                    $plan = "Active";
                }
                if($plan == "Active"){
                    $staffWorkspace=[];
                    $staffWorkspace['company_id']=$staffs->company_id;
                    $staffWorkspace['workspace_id']=$workspaces->id;
                    $staffWorkspace['user_id']=$staffs->user_id;
                    $staffWorkspace['staff_id']=$staffs->id;
                    $staffWorkspace['first_name']=$staffs->first_name;
                    $staffWorkspace['last_name']=$staffs->last_name;
                    $staffWorkspace['workspaceName']=$workspaces->name;
                    $staffWorkspace['workspaceType']=$workspaces->workspace_type;
                    $staffWorkspace['user_name']=$staffs->first_name." ".$staffs->last_name;
                    $staffWorkspace['language']=$language_code;
                    $staffWorkspace['language_id']=$language_id;

                    $roles = Roles::getRoleName($staffs->role_id);
                    $permissions = Roles::getPermissionsForStaff($staffs);
                    $modules = Roles::getModulesForStaff($staffs);
                    $staffWorkspace['permissions']=$permissions;
                    $staffWorkspace['modules']=isset($modules)?
                    array_values(array_unique($modules)):[];
                    // dd($modules);
                    $staffWorkspace['role']=$roles->name;
                    $staffWorkspace['roleId']=$roles->id;
                    $staffWorkspace['dateformat']=$dateFormat;
                    $user_email= DB::table("users")->where('company_id','=',$staffs->company_id)->pluck('email')->first();
                    $staffWorkspace['admin_email']=($staffs->email==$user_email)?1:0;
                    $workspaceList[]=$staffWorkspace;
                    $i++;
                }
            }
            $permission = Roles::getPermissionsForStaff($staff);
            $moduler = Roles::getModulesForStaff($staff);
            $module = isset($moduler)?array_values(array_unique($moduler)):[];
        }else{
            //$workspaceList[]='';
            $res = json_encode(["status_code"=>200,"user_id"=>'',"staff_id"=>'',"first_name"=>'',"last_name"=>'',"company_id"=>'',
            "workspace_id"=>$workspace->id,"workspaceName"=>$workspace->name,"workspaceType"=>$workspace->workspace_type,"role"=>'',
            "roleId"=>'',"workspacesList"=>$workspaceList,"permissions"=>'',"module"=>'',"dateformat"=>''],200);
            return CommonApp::apiEncrypt($res);
        }

        $res = json_encode(["status_code"=>200,"user_id"=>$staff->user_id,"staff_id"=>$staff->id,"first_name"=>$firstName,"last_name"=>$lastName,"company_id"=>$staff->company_id,
        "workspace_id"=>$workspace->id,"workspaceName"=>$workspace->name,"workspaceType"=>$workspace->workspace_type,"role"=>$role->name,
        "roleId"=>$role->id,"workspacesList"=>$workspaceList,"permissions"=>$permission,"module"=>$module,"dateformat"=>$dateFormat],200);
        return CommonApp::apiEncrypt($res);
    }
}
