<?php

namespace App\Http\Controllers\WebSite\Common;

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
        $countries = Country::getCountries();
        if(!empty($countries)){
            $res = json_encode(['status_code'=>200,'status'=>'success','data'=>$countries]);
            return CommonApp::webEncrypt($res);
        }
        else{
            $res = json_encode(['status_code'=>400,'status'=>'Failure']);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Get the list of languages */
    public function languages(){
        $languages = Language::getLanguages();
        if(!empty($languages)){
            $res = json_encode(['status_code'=>200,'status'=>'success','data'=>$languages]);
            return CommonApp::webEncrypt($res);
        }
        else{
            $res = json_encode(['status_code'=>400,'status'=>'Failure']);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Get the List of currencies */
    public function listCurrencies(){
        $listOfCurrencies = Currency::getCurrencies();

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$listOfCurrencies]);
        return CommonApp::webEncrypt($res);
    }
}
