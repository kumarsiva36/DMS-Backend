<?php

namespace App\Http\Controllers\Mobile\v1\Common;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Common\CommonApp;

class Countries extends Controller
{
    /* Get the list of countries */
    public function countries(){
        $countries = Country::select('id','name','code')->get();
        if(!empty($countries)){
            $res = json_encode(['status_code'=>200,'status'=>'success','data'=>$countries]);
            return CommonApp::apiEncrypt($res);
        }
        else{
            $res = json_encode(['status_code'=>400,'status'=>'Failure']);
            return CommonApp::apiEncrypt($res);
        }
    }

    /* Get the list of languages */
    public function languages(){
        $languages = Language::select('id','name','lang_code')->where("status",'1')->get();
        if(!empty($languages)){
            $res = json_encode(['status_code'=>200,'status'=>'success','data'=>$languages]);
            return CommonApp::apiEncrypt($res);
        }
        else{
            $res = json_encode(['status_code'=>400,'status'=>'Failure']);
            return CommonApp::apiEncrypt($res);
        }
    }

    /* Get the List of currencies */
    public function listCurrencies(){
        $listOfCurrencies = Currency::select('id','name','symbol')->get();

        //return response()->json(["status_code"=>200,"status" =>"Success","data"=>$listOfCurrencies]);
        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$listOfCurrencies]);
        return CommonApp::apiEncrypt($res);
    }
}
