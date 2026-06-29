<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\ArticleName;
use App\Models\Buyer;
use App\Models\Color;
use App\Models\Currency;
use App\Models\FabricType;
use App\Models\Factory;
use App\Models\IncomeTerms;
use App\Models\OrderCategory;
use App\Models\OrderUnits;
use App\Models\PCU;
use App\Models\Size;
use App\Models\FabricComposition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetAllInOneOrder extends Controller
{
    /* To Get All the Order Registration Helping API's in a Single Call */
    public static function getOrderAllDetails(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $whereConditions = [
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=',$request->company_id]
        ];


        $data=[];
        $data['buyers'] = Buyer::select('id','name')->where($whereConditions)->get();
        $data['factories'] = Factory::select('id','name')->where($whereConditions)->get();
        $data['pcus'] = PCU::select('id','name')->where($whereConditions)->get();
        $data['fabric_composition'] = FabricComposition::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();
        if(isset($request->referenceId) && $request->referenceId!=''){
            // $data['fabric'] = FabricType::select('id','name')->where($whereConditions)->where('inquiry_reference_id',$request->referenceId)
            $data['fabric'] = FabricType::select('id','name')->where($whereConditions)
            ->orwhere('is_default','0')->get();
        }else{
            $data['fabric'] = FabricType::select('id','name')->where($whereConditions)
            ->orwhere('is_default','0')->get();
        }
        if(isset($request->referenceId) && $request->referenceId!=''){
            // $data['article'] = ArticleName::select('id','name')->where($whereConditions)->where('inquiry_reference_id',$request->referenceId)
            $data['article'] = ArticleName::select('id','name')->where($whereConditions)
            ->orwhere('is_default','0')->get();
        }else{
            $data['article'] = ArticleName::select('id','name')->where($whereConditions)
            ->orwhere('is_default','0')->get();
        }


        $data['category'] = OrderCategory::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();
        $data['incomeTerms'] = IncomeTerms::select('id','name','description')->get();
        $data['currencies'] = Currency::getCurrencies();
        $data['units'] = OrderUnits::get_order_units($request,"order_units");

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$data]);
        return CommonApp::webEncrypt($res);
    }

    /* Get Colors and Sizes For SKU */
    public static function getSizeAndColor(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id'=>'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $data=[];
        $data['sizes'] = Size::getSizes($request);
        $data['colors'] = Color::getColors($request);

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$data]);
        return CommonApp::webEncrypt($res);
    }
}
