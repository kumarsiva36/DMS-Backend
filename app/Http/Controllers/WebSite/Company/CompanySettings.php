<?php

namespace App\Http\Controllers\WebSite\Company;

use App\Common\CommonApp;
use App\Common\Logs;
use App\Common\Uploads;
use App\Http\Controllers\Controller;
use App\Models\CompanySettings as ModelsCompanySettings;
use App\Models\Language;
use App\Models\Order;
use App\Models\PaymentHistory;
use App\Models\Plan;
use App\Models\Roles;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\User;
use App\Models\UserPreferences;
use App\Models\Workspace;
use App\Models\WorkspaceType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CompanySettings extends Controller
{
    /* Add new company details */
    public static function registerCompany(Request $request){
        $validated = Validator::make($request->all(),[
            'company_name' => 'required',
            'contact_person' => 'required|min:2|max:30',
            'contact_number' => 'required|string|min:7|max:15',
            //'logo' => 'nullable|mimes:jpeg,png,jpg|max:100|dimensions:max_width=440,max_height=220'
            'logo' => 'nullable|mimes:jpeg,png,jpg|max:50|dimensions:max_width=350,max_height=350'
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validated->errors()]);
        }

        try{
            $company_id = ModelsCompanySettings::companyRegisteration($request);
            return response()->json(["status_code"=>200,"status" =>"Success","company_id"=>$company_id,
            "message"=>"Company Details Successsfully Added"],200);
        }catch(Exception $e){
            return response()->json(["status_code"=>401,"status" =>"Failure",
            "error"=>$e->getMessage()]);
        }

    }

    /* Get the Company Details for Viewing */

    public static function viewCompanyDetails(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required'
        ]);
        if ($validated->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $companyDetails= ModelsCompanySettings::getCompanyDetails($request);
        $res = json_encode(["status_code"=>200,"status"=>"Success","data"=>$companyDetails],200);
        return CommonApp::webEncrypt($res);
    }

    /* Get the workspace Type from the DB */
    public function workspaceType(){
        $workspaceType = WorkspaceType::getAllWorkspaceType();
        $res = json_encode(["status_code"=>200,"data"=>$workspaceType]);
        return CommonApp::webEncrypt($res);
    }

    /* Create a new Workspace for the company */
    public function createWorkspace(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $companyDetails = ModelsCompanySettings::getCompanyInfoUsingID($request->company_id);
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'name' => ['required', Rule::unique('workspace')
                        ->where(function ($query) use($companyDetails) {
                            $query->where('id',$companyDetails->id);
                            $query->where('user_id',$companyDetails->user_id);
                            return $query;
                        })],
        ]);

        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $workSpaceCount=Workspace::getWorkspaceCount($request->company_id);
        if($workSpaceCount==0){
            try{
                $workspaceDetails = Workspace::createWorkspace($request,$companyDetails);
                $res = json_encode(["status_code"=>200,"status" =>"Success","workspace_id"=>$workspaceDetails['workspaceID'],
                "workspaceType"=>$workspaceDetails['workspaceType'],"workspaceName"=>$workspaceDetails['workspaceName'],
                "language"=>$workspaceDetails['language'],"message"=>"Workspace Created Successfully"]);
                return CommonApp::webEncrypt($res);
            }catch(Exception $e){
                $res = json_encode(["status_code"=>401,"status" =>"Failure",
                "error"=>$e->getMessage()]);
                return CommonApp::webEncrypt($res);
            }
        }else{
            $res = json_encode(["status_code"=>201,"status" =>"Error","message"=>"Workspace Already Exists"]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Get the company specific workspace details */
    public function getWorkspace(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required'
        ]);
        if($validator->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validator->errors()]);
        }
        $companyDetails = ModelsCompanySettings::getCompanyInfoUsingID($request->company_id);
        $workspaces = Workspace::getWorkspaces($companyDetails);

        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$workspaces]);
    }

    /* Update the company details*/
    public function updateCompanyDetails(Request $request){
        $validated = Validator::make($request->all(),[
            'company_id' => 'required|numeric',
            'contact_person' => 'required|min:2|max:30',
            'contact_number' => 'required|string|min:7|max:15',
            //'logo' => 'nullable|mimes:jpeg,png,jpg|max:100|dimensions:max_width=440,max_height=220'
            'logo' => 'nullable|mimes:jpeg,png,jpg|max:50|dimensions:max_width=350,max_height=350'
        ]);
        if($validated->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validated->errors()]);
        }
        $companyToUpdate = ModelsCompanySettings::getCompanyInfoUsingID($request->company_id);
        if($request->hasfile('logo')){
            $awsCompanyPath = $companyToUpdate->aws_s3_path;
            $logo = $request->file('logo');
            $logoName = time().'_'.$logo->getClientOriginalName();
            $filepath = $awsCompanyPath.'/Logo/'.$logoName;
            Uploads::companyLogoUpload($filepath,$logo);
            $companyToUpdate->logo = $filepath;
        }
        $companyToUpdate->contact_person = $request->contact_person;
        $companyToUpdate->contact_number = $request->contact_number;
        $companyToUpdate->address1 = $request->input('address1','');
        $companyToUpdate->address2 = $request->input('address2','');
        $companyToUpdate->city = $request->input('city','');
        $companyToUpdate->state = $request->input('state','');
        $companyToUpdate->zipcode = $request->input('zipcode','');
        $companyToUpdate->country_id = $request->input('country_id');
        $companyToUpdate->account_no = $request->input('account_no','0');
        $companyToUpdate->ifsc_code = $request->input('ifsc_code','0');
        $companyToUpdate->gst_number = $request->input('gst_number','0');
        $companyToUpdate->pan_number = $request->input('pan_number','0');
        $companyToUpdate->updated_at = date("Y-m-d H:i:s");
        $companyToUpdate->save();

        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Company updated successfully"]);
    }

    /* To Change the usage of default logo in pdf's */
    public static function changeDefaultLogo(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'use_logo' => 'required|integer|max:1|min:0',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            $companySettings = ModelsCompanySettings::find($request->company_id);
            $companySettings->use_logo = (int)$request->use_logo;
            $companySettings->save();
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"errors"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
        $res = json_encode(["status_code"=>200,"message"=>"Preference Updated Successfully"]);
        return CommonApp::webEncrypt($res);
    }

    /* Get UserLogo Status and Logo */
    public static function getUserLogoStatus($id){
        //$userLogo = ModelsCompanySettings::find($id)->select('use_logo','logo');
       //dd($userLogo);
        $userLogo = ModelsCompanySettings::where('id',$id)->select('use_logo','logo')->first();
        $userLogoData['useLogo']=(int)$userLogo->use_logo;
        $userLogoData['userLogo']=$userLogo->logo;
        return $userLogoData;
    }
}
