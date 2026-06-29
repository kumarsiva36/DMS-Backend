<?php

namespace App\Http\Controllers\Mobile\Order;

use App\Http\Controllers\Controller;
use App\Models\IncomeTerms as ModelsIncomeTerms;
use Illuminate\Http\Request;

class IncomeTerms extends Controller
{
    /* Get Income Terms */
    public static function getIncomeTerms(Request $request){
        $incomeTerms = ModelsIncomeTerms::select('id','name','description')->get();
        return response()->json(["status_code"=>200,"data"=>$incomeTerms],200);
    }
}
