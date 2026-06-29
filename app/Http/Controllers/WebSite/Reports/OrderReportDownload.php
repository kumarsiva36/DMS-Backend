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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Common\CommonApp;
use Illuminate\Support\Facades\Storage;

class OrderReportDownload extends Controller
{
    /**
     * Handle the incoming request.
     * Download the Order Report
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
        $whereCondition=[
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id]
        ];
        /* For Status Filter Start */
        $statusFilter = isset($request->statusFilter)?$request->statusFilter:"All";
        if($statusFilter === "Completed")
            $whereCondition[]=['orders.status','=','12'];
        else if($statusFilter === "Cancelled")
            $whereCondition[]=['orders.status','=','10'];
        else if($statusFilter === "Deleted")
            $whereCondition[]=['orders.status','=','3'];
        else if($statusFilter === "Active")
            $whereCondition[]=['orders.status','=','1'];
        /* For Status Filter End */
        /* For Factory, Buyer and PCU Start */
        if(isset($request->factory_id) && $request->factory_id > 0)
            $whereCondition[]=['orders.factory_id',"=",$request->factory_id];
        if(isset($request->buyer_id) &&  $request->buyer_id > 0)
            $whereCondition[]=['orders.buyer_id',"=",$request->buyer_id];
        if(isset($request->pcu_id) && $request->pcu_id > 0)
            $whereCondition[]=['orders.pcu_id',"=",$request->pcu_id];
        /* For Factory, Buyer and PCU End */
        /* Language Start */
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
        /* Language End */
        if(isset($request->staff_id) && $request->staff_id>0){
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
                    // $theOrder = Order::where($whereCondition)->where("id", $order->order_id)->first();
                    $theOrders[]=$order->order_id;
                }
                $query1 = DB::raw("(CASE WHEN orders.staff_id=0 THEN users.name WHEN orders.staff_id>0 THEN CONCAT(staff.first_name,' ',staff.last_name) END) as createdBy");
                $query2 = DB::raw("(CASE WHEN orders.action_done_user_id>0 THEN U.name WHEN orders.action_done_staff_id>0 THEN CONCAT(S.first_name,' ',S.last_name) END) as actionDoneBy");
                $orders = Order::where($whereCondition)->whereIn("orders.id",$theOrders)
                ->leftjoin('order_factory','order_factory.id','orders.factory_id')
                ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
                ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
                ->leftjoin('users','users.id','orders.user_id')
                ->leftjoin('users as U','U.id','orders.action_done_user_id')
                ->leftjoin('staff','staff.id','orders.staff_id')
                ->leftjoin('staff as S','S.id','orders.action_done_staff_id')
                ->select('orders.id','orders.order_no','orders.style_no','order_factory.name as factoryName','order_buyer.name as buyerName',
                'order_pcu.name as pcuName','orders.cutting_start_date as startDate','orders.packing_end_date as endDate',$query1,$query2,'orders.action_done_at as actionDate'
                ,'orders.status')
                ->orderBy('orders.id','DESC')
                ->get();
            }else{
                $query1 = DB::raw("(CASE WHEN orders.staff_id=0 THEN users.name WHEN orders.staff_id>0 THEN CONCAT(staff.first_name,' ',staff.last_name) END) as createdBy");
                $query2 = DB::raw("(CASE WHEN orders.action_done_user_id>0 THEN U.name WHEN orders.action_done_staff_id>0 THEN CONCAT(S.first_name,' ',S.last_name) END) as actionDoneBy");
                $orders = Order::where($whereCondition)
                ->leftjoin('order_factory','order_factory.id','orders.factory_id')
                ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
                ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
                ->leftjoin('users','users.id','orders.user_id')
                ->leftjoin('users as U','U.id','orders.action_done_user_id')
                ->leftjoin('staff','staff.id','orders.staff_id')
                ->leftjoin('staff as S','S.id','orders.action_done_staff_id')
                ->select('orders.id','orders.order_no','orders.style_no','order_factory.name as factoryName','order_buyer.name as buyerName',
                'order_pcu.name as pcuName','orders.cutting_start_date as startDate','orders.packing_end_date as endDate',$query1,$query2,'orders.action_done_at as actionDate'
                ,'orders.status')
                ->orderBy('orders.id','DESC')
                ->get();
            }
        }else{
            $query1 = DB::raw("(CASE WHEN orders.staff_id=0 THEN users.name WHEN orders.staff_id>0 THEN CONCAT(staff.first_name,' ',staff.last_name) END) as createdBy");
            $query2 = DB::raw("(CASE WHEN orders.action_done_user_id>0 THEN U.name WHEN orders.action_done_staff_id>0 THEN CONCAT(S.first_name,' ',S.last_name) END) as actionDoneBy");
            $orders = Order::where($whereCondition)
            ->leftjoin('order_factory','order_factory.id','orders.factory_id')
            ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
            ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
            ->leftjoin('users','users.id','orders.user_id')
            ->leftjoin('users as U','U.id','orders.action_done_user_id')
            ->leftjoin('staff','staff.id','orders.staff_id')
            ->leftjoin('staff as S','S.id','orders.action_done_staff_id')
            ->select('orders.id','orders.order_no','orders.style_no','order_factory.name as factoryName','order_buyer.name as buyerName',
            'order_pcu.name as pcuName','orders.cutting_start_date as startDate','orders.packing_end_date as endDate',$query1,$query2,'orders.action_done_at as actionDate'
            ,'orders.status')
            ->orderBy('orders.id','DESC')
            ->get();
        }

        $dataArr=[];
        $dataArr['dateFormat']=$dateFormat;
        $dataArr['statusFilter']=$statusFilter;
        $dataArr['serverURL'] = config('filesystems.disks.s3.url');
        $dataArr['useLogo'] = $dateFormatAndLanguage['useLogo'];
        //$dataArr['userLogo'] = $dateFormatAndLanguage['userLogo'];
        $dataArr['userLogo'] =$dateFormatAndLanguage['useLogo'] !=0 ?  Storage::disk('s3')->temporaryUrl($dateFormatAndLanguage['userLogo'], '+15 minutes') : "";
        $dataArr['orders']=$orders;
        if(isset($request->factory_id)){
            if($request->factory_id > 0){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$request->factory_id]
                ];
                $dataArr['factory'] = ($this->getDetails($forType,"Factory"))->name;
            }else{
                $dataArr['factory'] = "All";
            }
        }
        if(isset($request->buyer_id)){
            if($request->buyer_id > 0){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$request->buyer_id]
                ];
                $dataArr['buyer'] = ($this->getDetails($forType,"Buyer"))->name;
            }else{
                $dataArr['buyer'] = "All";
            }
        }
        if(isset($request->pcu_id)){
            if($request->pcu_id > 0){
                $forType = [
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$request->pcu_id]
                ];
                $dataArr['pcu'] = ($this->getDetails($forType,"PCU"))->name;
            }
            else{
                $dataArr['pcu'] = "All";
            }
        }

        // return response()->json(["status_code"=>200,"status" =>"success","data"=>$dataArr]);

        view()->share("orderData",$dataArr);
        $pdf = Pdf::loadView('OrderReportPDF');
        // $pdf->setPaper('A4', 'portrait');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->download();
    }

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
