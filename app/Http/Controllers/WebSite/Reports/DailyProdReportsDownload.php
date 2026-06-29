<?php

namespace App\Http\Controllers\WebSite\Reports;

use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use App\Models\Buyer;
use App\Models\Factory;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\PCU;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\UpdateSkuQuantity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Common\CommonApp;
use Illuminate\Support\Facades\Storage;

class DailyProdReportsDownload extends Controller
{
    /**
     * Handle the incoming request.
     * Download the Daily Production Report
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
        $type = isset($request->type)?$request->type:"";
        $no_of_excess   = isset($request->no_of_excess)?$request->no_of_excess:"";
        $no_of_short   = isset($request->no_of_short)?$request->no_of_short:"";
        $operator_symb = isset($request->operator_symb)?$request->operator_symb:"";
        $all = "All";
        if(!empty($type))
            $all ='';

        $whereCondition =[
            ['company_id',"=",$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['step_level','=','6'],
            ['id','=',$request->order_id]
        ];
        $whereCondition[]=['status','=',"1"];
        $orders = Order::where($whereCondition)->first();
        if(isset($request->user_id) && $request->user_id>0){
            $whereConditionToSend=[
                ['company_id','=',$request->company_id],
                ['id','=',$request->user_id]
            ];
            // $dateFormat=GetUserSettings::getPeopleDateFormat("User",$whereConditionToSend);
            // $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"User",$request->user_id);
            $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
            $dateFormat = $dateFormatAndLanguage['dateFormat'];
            $language = $dateFormatAndLanguage['language'];
        }
        else if(isset($request->staff_id) && $request->staff_id>0){
            $whereConditionToSend=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['id','=',$request->staff_id]
            ];
            // $dateFormat=GetUserSettings::getPeopleDateFormat("Staff",$whereConditionToSend);
            // $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"Staff",$request->staff_id);
            $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
            $dateFormat = $dateFormatAndLanguage['dateFormat'];
            $language = $dateFormatAndLanguage['language'];
        }
        App::setlocale($language);
        $whereCondition1 =[
            ['company_id',"=",$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->order_id]
        ];
        $dailyUpdatesArr=$dailyUpdate=[];
        $advFilterArr=[];
        if(isset($request->start_date) && isset($request->end_date)){
            $advFilterArr['startDate']=date("Y-m-d",strtotime($request->start_date));
            $advFilterArr['endDate']=date("Y-m-d",strtotime($request->end_date));
        }
        if($all == ""){
            $advFilterArr['type']=strtolower($type);
        }
        if(isset($request->no_of_excess) && $type==="Excess" && $operator_symb !=""){
            $advFilterArr['value']=$no_of_excess;
            $advFilterArr['operator']=$operator_symb;
        }
        if(isset($request->no_of_short) && $type==="Short" && $operator_symb !=""){
            // $advFilterArr['type']=strtolower($type);
            $advFilterArr['value']=$no_of_short;
            $advFilterArr['operator']=$operator_symb;
        }
        if(isset($request->production_type)){
            $advFilterArr['prodType']= $request->production_type;
        }
        $dailyUpdatesArr['advFilter']=$advFilterArr;
        $startDate= $orders->cutting_start_date;
        $endDate= $orders->packing_end_date;
        if($orders->factory_id != NULL){
            $forType = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['id','=',$orders->factory_id]
            ];
            $dailyUpdatesArr['factory'] = ($this->getDetails($forType,"Factory"))->name;
        }
        if($orders->pcu_id != NULL){
            $forType = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['id','=',$orders->pcu_id]
            ];
            $dailyUpdatesArr['pcu'] = ($this->getDetails($forType,"PCU"))->name;
        }
        if($orders->buyer_id != NULL){
            $forType = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['id','=',$orders->buyer_id]
            ];
            $dailyUpdatesArr['buyer'] =  ($this->getDetails($forType,"Buyer"))->name;
        }
        if(isset($request->production_type)){
            $whereCondition1[]=["type_of_production",$request->production_type];
        }
        if(isset($request->start_date) && isset($request->end_date)){
            if($type==="Excess" || $all=="All"){
                if($no_of_excess!='' && $operator_symb!='' ){
                    $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                    ->whereBetween('sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                    ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                        DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                    ->having('diff','>',0)
                    ->having('diff',$operator_symb,$no_of_excess)
                    ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')->get();
                }else{
                    $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                    ->whereBetween('sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                    ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                        DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                    ->having('diff','>',0)
                    ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')->get();
                }
            }
            else if($type==="Short" || $all=="All"){
                if($no_of_short!='' && $operator_symb!='' ){
                    $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                    ->whereBetween('sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                    ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                        DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                    ->having('diff','<',0)
                    ->having('diff',$operator_symb,$no_of_short)
                    ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')->get();
                }else{
                    $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                    ->whereBetween('sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                    ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                        DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                    ->having('diff','<',0)
                    ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')->get();
                }
            }
            else{
                $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                ->whereBetween('sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                    DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')->get();
            }
        }else{
            if($type==="Excess"){
                if($no_of_excess!='' && $operator_symb!='' ){
                    $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                    ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                        DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                    ->having('diff','>',0)
                    ->having('diff',$operator_symb,$no_of_excess)
                    ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')->get();
                }else{
                    $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                    ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                        DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                    ->having('diff','>',0)
                    ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')->get();
                }
            }
            else if($type==="Short"){
                if($no_of_short!='' && $operator_symb!='' ){
                    $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                    ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                        DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                    ->having('diff','<',0)
                    ->having('diff',$operator_symb,$no_of_short)
                    ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')->get();
                }else{
                    $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                    ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                        DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                    ->having('diff','<',0)
                    ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')->get();
                }
            }else{
                $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
                DB::raw("(SUM(updated_quantity) - target_value) as diff"))
                ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')->get();
            }
        }
        // dd($dailyUpdates);
        $i=0;
        $dailyUpdatesArr['orderNo']=$orders->order_no;
        $dailyUpdatesArr['styleNo']=$orders->style_no;
        $dailyUpdatesArr['startDate']=$startDate;
        $dailyUpdatesArr['endDate']=$endDate;
        $dailyUpdatesArr['dateFormat']=$dateFormat;
        $dailyUpdatesArr['serverURL'] = config('filesystems.disks.s3.url');
        $dailyUpdatesArr['useLogo'] = $dateFormatAndLanguage['useLogo'];
        //$dailyUpdatesArr['userLogo'] = $dateFormatAndLanguage['userLogo'];
        $dailyUpdatesArr['userLogo'] =$dateFormatAndLanguage['useLogo'] !=0 ?  Storage::disk('s3')->temporaryUrl($dateFormatAndLanguage['userLogo'], '+15 minutes') : "";
        foreach($dailyUpdates as $updates){
            $updatesArr=[];
            $updatesArr['sku_date']=date($dateFormat,strtotime($updates->sku_date));
            $updatesArr[$updates->type_of_production."_actual"]=(int)$updates->updated_quantity;
            $updatesArr[$updates->type_of_production."_target"]=(int)$updates->target_value;
            $updatesArr[$updates->type_of_production."_diff"]=(int)$updates->diff;
            $updatesArr['total_qty']=(int)$orders->total_quantity;
            /* To place the prod datas in their respective dates */
            $res=array_search(date($dateFormat,strtotime($updates->sku_date)), array_column($dailyUpdate, 'sku_date')) ?? -1;

            if($res>=0 && strlen($res)>0){
                $dailyUpdate[$res][$updates->type_of_production."_actual"]=(int)$updates->updated_quantity;
                $dailyUpdate[$res][$updates->type_of_production."_target"]=(int)$updates->target_value;
                $dailyUpdate[$res][$updates->type_of_production."_diff"]=(int)$updates->diff;

            }else{
                $dailyUpdate[$i]=$updatesArr;
                $i++;
            }
        }
        $dailyUpdatesArr['prodData']=$dailyUpdate;
        // echo '<pre>'; print_r($dailyUpdatesArr); exit;
        view()->share("dailyUpdates",$dailyUpdatesArr);
        // return view("DayByDayReportPDF",["dailyUpdates"=>$dailyUpdatesArr]);
        $pdf = Pdf::loadView('DayByDayReportPDF');
        // $path = public_path() . '/PendingTask/' .$request->orderNo.date('d-m-Y').'.pdf';
        // $pdf->save($path);
        // return response()->download($path);
        // $pdf->setPaper('A4', 'portrait');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->download();
    }
    /* To get the Factory/Buyer/PCU details */
    public function getDetails($data, $type){
        if($type === "Factory"){
            $name = Factory::where($data)->select('name')->first();
        }
        if($type === "PCU"){
            $name = PCU::where($data)->select('name')->first();
        }
        if($type === "Buyer"){
            $name = Buyer::where($data)->select('name')->first();
        }
        return $name;
    }
}
