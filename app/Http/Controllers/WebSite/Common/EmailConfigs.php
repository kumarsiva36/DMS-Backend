<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Http\Controllers\Controller;
use App\Models\EmailConfiguration;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmailConfigs extends Controller
{
    //
    /* To Add Own Config Settings */
    public static function addEmailConfigs(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|unique:email_configurations',
            'workspace_id' => 'required',
            'mailer'=>'required',
            'host' => 'required',
            'port' => 'required',
            'username' => 'required',
            'password' => 'required',
            'encryption' => 'required',
            'from_address' => 'required',
            'from_name' => 'required',
            'use_config' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validator->errors()]);
        }

        try{
            EmailConfiguration::addEmailConfigs($request);
            return response()->json(["status_code" => 200,"status"=>"success","message"=>"Configuration Added Successfully"],200);
        }catch(Exception $e){
            return response()->json(["status_code" => 400,"status"=>"failure","message"=>"Try Again"],400);
        }

    }
}
