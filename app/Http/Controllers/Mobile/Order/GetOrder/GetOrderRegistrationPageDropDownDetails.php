<?php

namespace App\Http\Controllers\Mobile\Order\GetOrder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Buyer;
use App\Models\Factory;
use App\Models\PCU;
use App\Models\FabricType;
use App\Models\OrderCategory;
use App\Models\ArticleName;
use App\Models\Currency;
use App\Models\IncomeTerms as ModelsIncomeTerms;

class GetOrderRegistrationPageDropDownDetails extends Controller
{
    /**
     * Handle the incoming request.
     * Order Page Drop Down Details
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }
        $whereConditions = [
            ['workspace_id', '=', $request->workspace_id],
            ['company_id', '=', $request->company_id]
        ];
        $getOrderPage = [];
        $getOrderPage['factory'] = $this->getFactory($whereConditions);
        $getOrderPage['buyer'] = $this->getBuyers($whereConditions);
        $getOrderPage['pcu'] = $this->getPCU($whereConditions);
        $getOrderPage['fabrictype'] = $this->getFabricType($whereConditions);
        $getOrderPage['category'] = $this->getOrderCategory($whereConditions);
        $getOrderPage['article'] = $this->getArticleName($whereConditions);
        $getOrderPage['incometerms'] = $this->getIncomeTerms();
        $getOrderPage['currency'] = $this->getCurrency();

        return response()->json(["status_code" => 200, "status" => "Success", "data" => $getOrderPage]);
    }


    /* View  Factories */
    public function getFactory($whereConditions)
    {
        $factories = Factory::select('id', 'name')->where($whereConditions)->get();
        return $factories;
    }
    /* Get the Buyer */
    public function getBuyers($whereConditions)
    {
        $buyers = Buyer::select('id', 'name')->where($whereConditions)->get();
        return $buyers;
    }
    /* View the added PCU's */
    public function getPCU($whereConditions)
    {
        $getPCU = PCU::select('id', 'name')->where($whereConditions)->get();
        return $getPCU;
    }
    /* Get the Fabric Type */
    public function getFabricType($whereConditions)
    {
        $getFabricType = FabricType::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();
        return $getFabricType;
    }
    /* Get order category */
    public function getOrderCategory($whereConditions)
    {
        $getordercategory = OrderCategory::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();
        return $getordercategory;
    }
    /* Get the article name */
    public function getArticleName($whereConditions)
    {
        $article = ArticleName::select('id','name')->where($whereConditions)->orwhere('is_default','0')->get();
        return $article;
    }
    /* Get The Income Terms */
    public  function getIncomeTerms(){
        $incomeTerms = ModelsIncomeTerms::select('id','name','description')->get();
        return $incomeTerms;
    }
    /* Get the currency */
    public  function getCurrency(){
        $listOfCurrencies = Currency::select('id','name','symbol')->get();
        return $listOfCurrencies;
    }

}
