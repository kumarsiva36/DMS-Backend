<?php

namespace App\Http\Controllers\WebSite\Order\PendingTasks;

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
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Support\Facades\App;
use App\Common\CommonApp;
use App\Models\MultipleDeliveryDates;
use Illuminate\Support\Facades\Storage;

class DownloadProductionPDF extends Controller
{
    /**
     * Download the production PDF
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
        $whereCondition =[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_no','=',$request->orderNo],
            ['step_level','=','6'],
            ['status','=','1']
        ];
        if(isset($request->staff_id) && $request->staff_id > 0){
            $whereCondition1= $whereCondition2 = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $staffRoleHasPermission = Staff::select('role_id','company_id')->where('id',$request->staff_id)->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['permission_id','=','19'];
            $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $theOrders = [];
                foreach($involedOrders as $order) {
                    $theOrder = Order::where("id", $order->order_id)->first();
                    if($request->orderNo == $theOrder->order_no) {
                        $theOrders[]=$theOrder;
                    }
                }
                $orders=$theOrders;
            }else{
                $orders = Order::where($whereCondition)->get();
            }
        }else{
            $orders = Order::where($whereCondition)->get();
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
        $pendingProductionArr=[];
        foreach($orders as $order){
            $productionDetails=$prodData=$cutArr=$sewArr=$packArr=[];
            $delivery_date = MultipleDeliveryDates::where('order_id',$order->id)->where('is_delivered',0)->pluck('delivery_date')->first();
            $productionDetails['delivery_date'] = $delivery_date;
            $pendingProductionArr['orderNo']=$order->order_no;
            $pendingProductionArr['dateFormat']=$dateFormat;
            $pendingProductionArr['serverURL'] = config('filesystems.disks.s3.url');
            $pendingProductionArr['useLogo'] = $dateFormatAndLanguage['useLogo'];
            //$pendingProductionArr['userLogo'] = $dateFormatAndLanguage['userLogo'];
            $pendingProductionArr['userLogo'] =$dateFormatAndLanguage['useLogo'] !=0 ?  Storage::disk('s3')->temporaryUrl($dateFormatAndLanguage['userLogo'], '+15 minutes') : "";
            $productionDetails['orderNo']=$order->order_no;
            $productionDetails['styleNo']=$order->style_no;
            /* Cut */
            $cutArr['title']="Cutting";
            $cutArr['startDate']=date($dateFormat,strtotime($order->cutting_start_date));
            $cutArr['endDate']=date($dateFormat,strtotime($order->cutting_end_date));
            $cutArr['totalQuantity']=$order->total_quantity;
            $cutArr['updatedQuantity']=$this->getUpdatedProductionSum($order,"Cut");
            $cutArr['pendingQuantity']=$cutArr['totalQuantity'] - $cutArr['updatedQuantity'];
            $cuttingInterval = $this->dateDifference($order->cutting_start_date,$order->cutting_end_date);
            $cutArr['delay']= $cuttingInterval['delay'];
            $cutArr['type']= $cuttingInterval['type'];
            if($cutArr['pendingQuantity'] >0){
                $prodData[]=$cutArr;
            }
            /* Sew */
            $sewArr['title']="Sewing";
            $sewArr['startDate']=date($dateFormat,strtotime($order->sewing_start_date));
            $sewArr['endDate']=date($dateFormat,strtotime($order->sewing_end_date));
            $sewArr['totalQuantity']=$order->total_quantity;
            $sewArr['updatedQuantity']=$this->getUpdatedProductionSum($order,"Sew");
            $sewArr['pendingQuantity']=$sewArr['totalQuantity'] - $sewArr['updatedQuantity'];
            $sewingInterval=$this->dateDifference($order->sewing_start_date,$order->sewing_end_date);
            $sewArr['delay']=$sewingInterval['delay'];
            $sewArr['type']=$sewingInterval['type'];
            if($sewArr['pendingQuantity'] >0){
                $prodData[]=$sewArr;
            }
            /* Pack */
            $packArr['title']="Packing";
            $packArr['startDate']=date($dateFormat,strtotime($order->packing_start_date));
            $packArr['endDate']=date($dateFormat,strtotime($order->packing_end_date));
            $packArr['totalQuantity']=$order->total_quantity;
            $packArr['updatedQuantity']=$this->getUpdatedProductionSum($order,"Pack");
            $packArr['pendingQuantity']=$packArr['totalQuantity'] - $packArr['updatedQuantity'];
            $packingInterval = $this->dateDifference($order->packing_start_date,$order->packing_end_date);
            $packArr['delay']= $packingInterval['delay'];
            $packArr['type']= $packingInterval['type'];
            if($packArr['pendingQuantity'] >0){
                $prodData[]=$packArr;
            }
            if($order->factory_id != NULL){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$order->factory_id]
                ];
                $productionDetails['factory'] = ($this->getDetails($forType,"Factory"))->name;
            }
            if($order->pcu_id != NULL){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$order->pcu_id]
                ];
                $productionDetails['pcu'] = ($this->getDetails($forType,"PCU"))->name;
            }
            if($order->buyer_id != NULL){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$order->buyer_id]
                ];
                $productionDetails['buyer'] =  ($this->getDetails($forType,"Buyer"))->name;
            }
            /* Data append to array */
            $productionDetails['prodData']=$prodData;
            $productionDetails['lastDate']=$packArr['endDate'];
            !empty($productionDetails['prodData']) ? $pendingProductionArr['productionData'][]=$productionDetails
            : $pendingProductionArr['productionData']=[];
        }
        view()->share("productionData",$pendingProductionArr);
        // return response()->json(["status_code"=>200,"data"=>$pendingProductionArr]);
        $pdf = Pdf::loadView('pendingProductionPDF');
        // $path = public_path() . '/PendingTask/' .$request->orderNo.date('d-m-Y').'.pdf';
        // $pdf->save($path);
        // return response()->download($path);
        $pdf->setPaper('A4', 'portrait');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
        return $pdf->download();
    }

    /* To Get The Sum of the updated quantities */
    public function getUpdatedProductionSum($order,$type){
        $whereCondition=[
            ['company_id','=',$order->company_id],
            ['workspace_id','=',$order->workspace_id],
            ['order_id','=',$order->id],
            ['type_of_production','=',$type]
        ];
        $total = UpdateSkuQuantity::where($whereCondition)->sum('updated_quantity');
        return $total;
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
