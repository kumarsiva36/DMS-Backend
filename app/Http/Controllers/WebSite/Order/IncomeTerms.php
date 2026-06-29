<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\IncomeTerms as ModelsIncomeTerms;
use Illuminate\Http\Request;

class IncomeTerms extends Controller
{
    /* Get The Income Terms */
    public static function getIncomeTerms(Request $request){
        $incomeTerms = ModelsIncomeTerms::select('id','name','description')->get();
        $res = json_encode(["status_code"=>200,"data"=>$incomeTerms],200);
        return CommonApp::webEncrypt($res);
    }
}
