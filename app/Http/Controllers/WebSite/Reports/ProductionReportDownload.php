<?php

namespace App\Http\Controllers\WebSite\Reports;

use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use App\Models\Buyer;
use App\Models\Factory;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\OrderProduction;
use App\Models\PCU;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\UpdateSkuQuantity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Facades\App;
use App\Common\CommonApp;
use App\Models\MultipleDeliveryDates;
use Illuminate\Support\Facades\Storage;

class ProductionReportDownload extends Controller
{
    /**
     * Handle the incoming request.
     * Download the Production Report
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
        $type_array    = isset($request->type)?$request->type:[];
        $no_of_delay   = isset($request->no_of_delay)?$request->no_of_delay:"";
        $operator_symb = isset($request->operator_symb)?$request->operator_symb:"";
        $all = "All";
        if(!empty($type_array))
            $all ='';
        $whereCondition =[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['step_level','=','6'],
            ['status','=','1']
        ];
        if(isset($request->factory_id)){
            $whereCondition[]=['factory_id','=',$request->factory_id];
        }
        if(isset($request->buyer_id)){
            $whereCondition[]=['buyer_id','=',$request->buyer_id];
        }
        if(isset($request->pcu_id) && $request->pcu_id!=0){
            $whereCondition[]=['pcu_id','=',$request->pcu_id];
        }
        if(isset($request->staff_id) && $request->staff_id > 0){
            $whereCondition1= $whereCondition2 = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $staffRoleHasPermission = Staff::select('role_id','company_id')->where('id',$request->staff_id)->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['permission_id','=','19'];
            $whereCondition1[]=['company_id','=',$request->company_id];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $theOrders = [];
                foreach($involedOrders as $order) {
                    if(isset($request->styleNo) && $request->styleNo !=0){
                        if($request->styleNo === $order->order_id){
                            $theOrder = Order::where($whereCondition)->where("id", $order->order_id)->first();
                        }
                    }else{
                        $theOrder = Order::where($whereCondition)->where("id", $order->order_id)->first();
                    }
                    if(!empty($theOrder)) {
                        $theOrders[]=$theOrder;
                    }
                }
                $orders=$theOrders;
            }else{
                if(isset($request->styleNo) && $request->styleNo !=0){
                    $whereCondition[]=['id','=',$request->styleNo];
                }
                $orders = Order::where($whereCondition)->orderBy('id','DESC')->get();
            }
        }else{
            if(isset($request->styleNo) && $request->styleNo !=0){
                $whereCondition[]=['id','=',$request->styleNo];
            }
            $orders = Order::where($whereCondition)->orderBy('id','DESC')->get();
        }
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
        $advFilterArr=[];
        if(isset($request->startDate) && isset($request->endDate)){
            $advFilterArr['startDate']=date($dateFormat,strtotime($request->startDate));
            $advFilterArr['endDate']=date($dateFormat,strtotime($request->endDate));
        }
        if(count($type_array)>0){
            $advFilterArr['type']= $type_array;
        }
        if($no_of_delay !="" && $operator_symb !=""){
            $advFilterArr['delay']= $no_of_delay;
            $advFilterArr['operator']= $operator_symb;
        }
        $pendingProductionArr=[];
        $pendingProductionArr['statusFilter']=$request->statusFilter;
        $pendingProductionArr['advFilter']=$advFilterArr;
        $pendingProductionArr['dateFormat']=$dateFormat;
        $pendingProductionArr['serverURL'] = config('filesystems.disks.s3.url');
        $pendingProductionArr['useLogo'] = $dateFormatAndLanguage['useLogo'];
        //$pendingProductionArr['userLogo'] = $dateFormatAndLanguage['userLogo'];
        $pendingProductionArr['userLogo'] =$dateFormatAndLanguage['useLogo'] !=0 ?  Storage::disk('s3')->temporaryUrl($dateFormatAndLanguage['userLogo'], '+15 minutes') : "";
        foreach($orders as $order){
            $productionDetails=$prodData=$cutArr=$sewArr=$packArr=[];

            // $pendingProductionArr['orderNo']=$order->order_no;
            /* Cut */
            $cutArr['title']="Cutting";
            if(isset($request->startDate) && isset($request->endDate)){
                $cutArr['startDate']=date($dateFormat,strtotime($request->startDate));
                $cutArr['endDate']=date($dateFormat,strtotime($request->endDate));
                $filterCondition = ["startDate"=>$request->startDate, "endDate"=>$request->endDate];
            }else{
                $cutArr['startDate']=date($dateFormat,strtotime($order->cutting_start_date));
                $cutArr['endDate']=date($dateFormat,strtotime($order->cutting_end_date));
                $filterCondition = [];
            }
            $cutQuantities = ProductionReports::getUpdatedProductionSum($order,"Cut",$filterCondition);
            if(array_key_exists('target_value',$cutQuantities))
                $cutArr['totalQuantity']=$cutQuantities['target_value'];
            else
                $cutArr['totalQuantity']=$order->total_quantity;
            $cutArr['updatedQuantity']=$cutQuantities['updated_quantity'];
            $cutArr['accomplishedDate']=$order->cutting_accomplished_date;
            $cutArr['actualEndDate']=$order->cutting_end_date;
            $cutArr['pendingQuantity']=$cutArr['totalQuantity'] - $cutArr['updatedQuantity'];
            $cuttingInterval = $this->dateDifference($order->cutting_start_date,$order->cutting_end_date);
            $cutArr['delay']= $cuttingInterval['delay'];
            $cutArr['type']= $cuttingInterval['type'];
            if(in_array('Cut',$type_array) || $all=="All"){
                if($no_of_delay!='' && $operator_symb!='' ){
                    if(version_compare(abs($cuttingInterval['delay']),$no_of_delay,$operator_symb) && $cuttingInterval['delay'] < 0
                    && $cutArr['pendingQuantity'] !=0){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$cutArr;
                    }
                }
                else{
                    /* Status Filter Starts */
                    if( $request->statusFilter === "Completed" && $order->cutting_accomplished_date != null){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$cutArr;
                    }
                    else if( $request->statusFilter === "DelCompletion" && $order->cutting_accomplished_date != null
                    && $order->cutting_accomplished_date > $order->cutting_end_date){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$cutArr;
                    }
                    else if( $request->statusFilter === "Delay" && $order->cutting_accomplished_date == null
                    && $order->cutting_end_date < date('Y-m-d')){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$cutArr;
                    }
                    else if( $request->statusFilter === "InProgress" && $order->cutting_accomplished_date == null
                    && $order->cutting_start_date <= date('Y-m-d') ){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$cutArr;
                    }
                    else if( $request->statusFilter === "YetToStart" && $order->cutting_accomplished_date == null
                    && $order->cutting_start_date > date('Y-m-d')){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$cutArr;
                    }
                    else if ($request->statusFilter === "All"){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$cutArr;
                    }
                    /* Status Filter Ends */
                }
            }
            /* Sew */
            $sewArr['title']="Sewing";
            if(isset($request->startDate) && isset($request->endDate)){
                $sewArr['startDate']=date($dateFormat,strtotime($request->startDate));
                $sewArr['endDate']=date($dateFormat,strtotime($request->endDate));
                $filterCondition = ["startDate"=>$request->startDate, "endDate"=>$request->endDate];
            }else{
                $sewArr['startDate']=date($dateFormat,strtotime($order->sewing_start_date));
                $sewArr['endDate']=date($dateFormat,strtotime($order->sewing_end_date));
                $filterCondition=[];
            }
            $sewQuantities = ProductionReports::getUpdatedProductionSum($order,"Sew",$filterCondition);
            if(array_key_exists('target_value',$sewQuantities))
                $sewArr['totalQuantity']=$sewQuantities['target_value'];
            else
                $sewArr['totalQuantity']=$order->total_quantity;
            $sewArr['updatedQuantity']=$sewQuantities['updated_quantity'];
            $sewArr['accomplishedDate']=$order->sewing_accomplished_date;
            $sewArr['actualEndDate']=$order->sewing_end_date;
            $sewArr['pendingQuantity']=$sewArr['totalQuantity'] - $sewArr['updatedQuantity'];
            $sewingInterval=$this->dateDifference($order->sewing_start_date,$order->sewing_end_date);
            $sewArr['delay']=$sewingInterval['delay'];
            $sewArr['type']=$sewingInterval['type'];
            if(in_array('Sew',$type_array) || $all=="All"){
                // $prodData[]=$sewArr;
                if($no_of_delay!='' && $operator_symb!='' ){
                     if(version_compare(abs($sewingInterval['delay']),$no_of_delay,$operator_symb) && $sewingInterval['delay'] < 0
                     && $sewArr['pendingQuantity'] !=0){
                         $productionDetails['orderNo']=$order->order_no;
                         $productionDetails['styleNo']=$order->style_no;
                         $prodData[]=$sewArr;
                     }
                 }
                 else{
                    /* Status Filter Starts */
                    if( $request->statusFilter === "Completed" && $order->sewing_accomplished_date != null){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$sewArr;
                    }
                    else if( $request->statusFilter === "DelCompletion" && $order->sewing_accomplished_date != null
                    && $order->sewing_accomplished_date > $order->sewing_end_date){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$sewArr;
                    }
                    else if( $request->statusFilter === "Delay" && $order->sewing_accomplished_date == null
                    && $order->sewing_end_date < date('Y-m-d')){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$sewArr;
                    }
                    else if( $request->statusFilter === "InProgress" && $order->sewing_accomplished_date == null
                    && $order->sewing_start_date <= date('Y-m-d')){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$sewArr;
                    }
                    else if( $request->statusFilter === "YetToStart" && $order->sewing_accomplished_date == null
                    && $order->sewing_start_date > date('Y-m-d')){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$sewArr;
                    }
                    else if($request->statusFilter === "All"){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$sewArr;
                    }
                    /* Status Filter Ends */
                 }
             }
            /* Pack */
            $packArr['title']="Packing";
            if(isset($request->startDate) && isset($request->endDate)){
                $packArr['startDate']=date($dateFormat,strtotime($request->startDate));
                $packArr['endDate']=date($dateFormat,strtotime($request->endDate));
                $filterCondition = ["startDate"=>$request->startDate, "endDate"=>$request->endDate];
            }else{
                $packArr['startDate']=date($dateFormat,strtotime($order->packing_start_date));
                $packArr['endDate']=date($dateFormat,strtotime($order->packing_end_date));
                $filterCondition=[];
            }
            $packQuantities = ProductionReports::getUpdatedProductionSum($order,"Pack",$filterCondition);
            if(array_key_exists('target_value',$packQuantities))
                $packArr['totalQuantity']=$packQuantities['target_value'];
            else
                $packArr['totalQuantity']=$order->total_quantity;
            $packArr['updatedQuantity']=$packQuantities['updated_quantity'];
            $packArr['accomplishedDate']=$order->packing_accomplished_date;
            $packArr['actualEndDate']=$order->packing_end_date;
            $packArr['pendingQuantity']=$packArr['totalQuantity'] - $packArr['updatedQuantity'];
            $packingInterval = $this->dateDifference($order->packing_start_date,$order->packing_end_date);
            $packArr['delay']= $packingInterval['delay'];
            $packArr['type']= $packingInterval['type'];
            if(in_array('Pack',$type_array) || $all=="All"){
                // $prodData[]=$packArr;
                if($no_of_delay!='' && $operator_symb!=''){
                     if(version_compare(abs($packingInterval['delay']),$no_of_delay,$operator_symb) && $packingInterval['delay'] < 0
                     && $packArr['pendingQuantity'] !=0){
                         $productionDetails['orderNo']=$order->order_no;
                         $productionDetails['styleNo']=$order->style_no;
                         $prodData[]=$packArr;
                     }
                 }
                 else{
                    /* Status Filter Starts */
                    if( $request->statusFilter === "Completed" && $order->packing_accomplished_date != null){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$packArr;
                    }
                    else if( $request->statusFilter === "DelCompletion" && $order->packing_accomplished_date != null
                    && $order->packing_accomplished_date > $order->packing_end_date){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$packArr;
                    }
                    else if( $request->statusFilter === "Delay" && $order->packing_accomplished_date == null
                    && $order->packing_end_date < date('Y-m-d')){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$packArr;
                    }
                    else if( $request->statusFilter === "InProgress" && $order->packing_accomplished_date == null
                    && $order->packing_start_date <= date('Y-m-d')){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$packArr;
                    }
                    else if( $request->statusFilter === "YetToStart" && $order->packing_accomplished_date == null
                    && $order->packing_start_date > date('Y-m-d')){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$packArr;
                    }
                    else if( $request->statusFilter === "All"){
                        $productionDetails['orderNo']=$order->order_no;
                        $productionDetails['styleNo']=$order->style_no;
                        $prodData[]=$packArr;
                    }
                    /* Status Filter Ends */
                 }
            }
            if(!empty($prodData)){

                if($order->factory_id != NULL){
                    $forType = [
                        ['company_id','=',$request->company_id],
                        ['workspace_id','=',$request->workspace_id],
                        ['id','=',$order->factory_id]
                    ];
                    $productionDetails['factory'] = ($this->getDetails($forType,"Factory"))->name;
                    // $pendingProductionArr['factory'] = ($this->getDetails($forType,"Factory"))->name;
                }
                if($order->pcu_id != NULL){
                    $forType = [
                        ['company_id','=',$request->company_id],
                        ['workspace_id','=',$request->workspace_id],
                        ['id','=',$order->pcu_id]
                    ];
                    $productionDetails['pcu'] = ($this->getDetails($forType,"PCU"))->name;
                    // $pendingProductionArr['pcu'] = ($this->getDetails($forType,"PCU"))->name;
                }
                if($order->buyer_id != NULL){
                    $forType = [
                        ['company_id','=',$request->company_id],
                        ['workspace_id','=',$request->workspace_id],
                        ['id','=',$order->buyer_id]
                    ];
                    $productionDetails['buyer'] =  ($this->getDetails($forType,"Buyer"))->name;
                    // $pendingProductionArr['buyer'] = ($this->getDetails($forType,"Buyer"))->name;
                }
                $productionDetails['lastDate']=$packArr['endDate'];
            }
            /* Data append to array */
            // $productionDetails['prodData']=$prodData;
            (!empty($prodData))?$productionDetails['prodData']=$prodData:"";
            if(!empty($prodData))
            {
                $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
                $productionDetails['delivery_date'] = $delivery_date;
            }
            (!empty($productionDetails))?$pendingProductionArr['productionData'][]=$productionDetails:"";
            // $pendingProductionArr['productionData'][]=$productionDetails;

        }
        //dd($pendingProductionArr);
        // dd($pendingProductionArr['advFilter']['startDate']);
        // return $pendingProductionArr;
        view()->share("productionData",$pendingProductionArr);
        // return response()->json(["status_code"=>200,"data"=>$pendingProductionArr]);
        $pdf = Pdf::loadView('ProductionReportPDF');
        // $path = public_path() . '/PendingTask/' .$request->orderNo.date('d-m-Y').'.pdf';
        // $pdf->save($path);
        // return response()->download($path);
        $pdf->setPaper('A4', 'portrait');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
        return $pdf->download();
    }

    /* To Get The Sum of the updated quantities */
    public static function getUpdatedProductionSum($order,$type,$filterCondition){
        $whereCondition=[
            ['company_id','=',$order->company_id],
            ['workspace_id','=',$order->workspace_id],
            ['order_id','=',$order->id],
            ['type_of_production','=',$type]
        ];
        $totalArr=[];
        if(!empty($filterCondition)){
            $whereCondition2=[
                ['company_id','=',$order->company_id],
                ['workspace_id','=',$order->workspace_id],
                ['order_id','=',$order->id],
                ['type_of_production','=',$type]
            ];
            $whereCondition2[]=['date_of_production','>=',date("Y-m-d",strtotime($filterCondition['startDate']))];
            $whereCondition2[]=['date_of_production','<=',date("Y-m-d",strtotime($filterCondition['endDate']))];
            $whereCondition[]=['sku_date','>=',date("Y-m-d",strtotime($filterCondition['startDate']))];
            $whereCondition[]=['sku_date','<=',date("Y-m-d",strtotime($filterCondition['endDate']))];
            $totalArr['target_value'] = OrderProduction::where($whereCondition2)->sum("target_value");
        }
        // dd($whereCondition);
        // $total = UpdateSkuQuantity::where($whereCondition)->select(DB::raw('SUM(updated_quantity) as updated_quantity'),
        // DB::raw('AVG(target_value) as target_value'))->get();
        $totalArr['updated_quantity'] = UpdateSkuQuantity::where($whereCondition)->sum("updated_quantity");
        return $totalArr;
    }

    /* To Get the day difference */
    public static function dateDifference($startDate,$endDate)
    {
        $lastDate = new DateTime($endDate);
        $startDate = new DateTime($startDate);
        $today = new DateTime(date("Y-m-d"));
        // dd($startDate > $today);
        $interval = [];
        if($startDate > $today){
            $interval['delay'] = (int)$today->diff($startDate)->format("%r%a");
            $interval['type']="YetToBeStarted";
        }
        else if($startDate >= $today){
            $interval['delay'] = (int) 0;
            $interval['type']="StartsToday";
        }
        else{
            $interval['delay'] = (int)$today->diff($lastDate)->format("%r%a");
            $interval['type']="Progress";
        }

        return $interval;
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
