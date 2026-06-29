<?php

namespace App\Http\Controllers;
ini_set('max_execution_time', 0);

use App\Common\GetUserLanguage;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use Illuminate\Http\Request;
use App\Jobs\CreateOrderEmail;
use App\Models\Buyer;
use App\Models\Factory;
use App\Models\Order;
use App\Models\OrderContacts;
use App\Models\OrderTask;
use App\Models\PCU;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\UpdateSkuQuantity;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Support\Facades\App;

class testEmail extends Controller
{
    public function index()
    {
    $getEmail = [];
    $getEmail['to'] = "saravanakumar.r@catech.co.jp";
    $getEmail['name'] = 'saravanan';
    $getEmail['subject'] = "Hi";
    $getEmail['message'] = "Test Message";
    CreateOrderEmail::dispatch($getEmail);
    print_r("successfully");
    }

    public function getDataForPDF(){

        App::setLocale('jp');
        $whereCondition =[
            ['company_id','=',"1"],
            ['workspace_id','=',"1"],
            ['order_no','=',"TESTORD-01"]
        ];
        $orders = Order::where($whereCondition)->get();
        //dd($orders);
        $pendingTaskArr=[];
        $totalPendingTasksCounts=0;
        foreach($orders as $order) {
            $taskDetails=[];
            $pendingTaskArr['orderNo'] = $order->order_no;
            // $pendingTaskArr['orderLastDate'] = date('d-m-Y', strtotime($order->packing_end_date));
            $pendingTaskArr['styleCount'] = $this->getNumberOfStyle($whereCondition);
            $taskDetails['orderNo'] = $order->order_no;
            $taskDetails['styleNo'] = $order->style_no;
            $taskDetails['styleId'] = $order->id;
            if($order->factory_id != NULL){
                $forType = [
                    ['company_id','=',1],
                    ['workspace_id','=',1],
                    ['id','=',$order->factory_id]
                ];
                $taskDetails['factory'] = ($this->getDetails($forType,"Factory"))->name;
            }
            if($order->pcu_id != NULL){
                $forType = [
                    ['company_id','=',1],
                    ['workspace_id','=',1],
                    ['id','=',$order->pcu_id]
                ];
                $taskDetails['pcu'] = ($this->getDetails($forType,"PCU"))->name;
            }
            if($order->buyer_id != NULL){
                $forType = [
                    ['company_id','=',1],
                    ['workspace_id','=',1],
                    ['id','=',$order->buyer_id]
                ];
                $taskDetails['buyer'] =  ($this->getDetails($forType,"Buyer"))->name;
            }
            $forTaskDetails=[
                ['company_id','=',1],
                ['workspace_id','=',1],
                ['order_id','=',$order->id],
                ['task_accomplished_date', '=' ,NULL],
            ];
            $taskDetail = $this->getTaskDetails($forTaskDetails,$dateFormat="Y-m-d");
            $totalPendingTasksCounts+=$taskDetail['taskCount'];
            $pendingTaskArr['pendingTask']=$totalPendingTasksCounts;
            $taskDetails['pendingCount']=$taskDetail['taskCount'];
            $taskDetails['taskData']=$taskDetail['taskDetails'];
            $taskDetails['orderLastDate'] = date("Y-m-d", strtotime($order->packing_end_date));
            $pendingTaskArr['picDetails']=isset($taskDetail['picDetails'])?
            array_map("unserialize",array_unique(array_map("serialize",$taskDetail['picDetails']))):[];
            $pendingTaskArr['styleDetails'][] = $taskDetails;
        }
        // dd($pendingTaskArr);
        // return response()->json(["status_code"=>200,"status" =>"success","data"=>$pendingTaskArr]);
        view()->share("pendingTask",$pendingTaskArr);
        // return view("pendingTaskPDF",["pendingTask"=>$pendingTaskArr]);
        $pdf = Pdf::loadView('pendingTaskPDF');
        // $path = public_path() . '/PendingTask/' .$request->orderNo.date('d-m-Y').'.pdf';
        // $pdf->save($path);
        // return response()->download($path);
        // $pdf->setPaper('A4', 'portrait');
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->stream("dompdf_out.pdf", array("Attachment" => false));
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

    /* To get the number of Styles */
    public function getNumberOfStyle($Data){
        $OrderCount = Order::select('id')->where($Data)->get();
        return  count($OrderCount);

    }

    /* To get the Task Details and to filter the data */
    public function getTaskDetails($Data,$dateFormat){
        $tasks=[];
            $taskDetail = OrderTask::where($Data)->select('task_title','task_schedule_end_date','task_schedule_start_date',
            'task_pic',DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'))->orderBy('task_schedule_end_date','DESC')->get();
        $tasks['taskCount'] = count($taskDetail);
        foreach($taskDetail as $task){
            $taskDetails=[];
            $picdetail=[];
            $taskDetails['title'] = $task->task_title;
            $taskDetails['scheduledDate'] = $task->task_schedule_end_date != null ?date($dateFormat,strtotime($task->task_schedule_end_date)) : $task->task_schedule_end_date;
            $taskIntervals = $this->dateDifference($task->task_schedule_start_date,$task->task_schedule_end_date,$task->noOfDays);
            $taskDetails['days'] = $taskIntervals['delay'];
            $taskDetails['type'] = $taskIntervals['type'];
            $taskDetails['pic'] = explode("||",$this->getPIC($task->task_pic))[0];
            $tasks['taskDetails'][]=$taskDetails;
            if($taskDetails['pic']!=""){
                $picdetail['name']=$taskDetails['pic'];
                $picdetail['id']=explode("||",$this->getPIC($task->task_pic))[1];
                $tasks['picDetails'][]=$picdetail;
            }
        }
        return $tasks;
    }

    /* To get the PIC Name and ID */
    public function getPIC($data){
        if($data === 0){
            return ""."||"."";
        }
        else{
            $pic = Staff::where('id',$data)->first();
            return ($pic->first_name." ".$pic->last_name."||".$pic->id);
        }
    }

    /* To Get the day difference */
    public static function dateDifference($startdate,$endDate,$days)
    {
        $lastDate = new DateTime($endDate);
        $startDate = new DateTime($startdate);
        $today = new DateTime(date("Y-m-d"));
        // dd($startDate > $today);
        $interval = [];
        if($startdate === "" || $startdate === null){
            $interval['delay'] = null;
            $interval['type']="";
        }
        else if($startDate > $today){
            $interval['delay'] = (int)$today->diff($startDate)->format("%r%a");
            $interval['type']="YetToBeStarted";
        }
        else if($startDate >= $today){
            $interval['delay'] = (int) 0;
            $interval['type']="StartsToday";
        }
        else{
            $interval['delay'] = $days;
            $interval['type']="Progress";
        }

        return $interval;
    }

    public function getProductionData(){
        $whereCondition =[
            // ['orders.company_id','=',$request['company_id']],
            // ['orders.workspace_id','=',$request['workspace_id']],
            // ['orders.order_no','=',$request->orderNo]
            ['orders.company_id','=',1],
            ['orders.workspace_id','=',1],
            ['orders.order_no','=',"ORD001"]
        ];
        $orders = Order::where($whereCondition)
                // ->join('order_factory','order_factory.id','orders.factory_id')
                // ->join('order_buyer','order_buyer.id','orders.buyer_id')
                // ->join('order_pcu','order_pcu.id','orders.pcu_id')
                // ->select('orders.total_quantity','orders.cutting_start_date','orders.cutting_end_date','orders.sewing_start_date'
                // ,'orders.sewing_end_date','orders.packing_start_date','orders.packing_end_date','order_factory.name as factory',
                // 'order_pcu.name as pcu','order_buyer.name as buyer')
                ->get();
        $pendingProductionArr=[];
        foreach($orders as $order){
            $productionDetails=$prodData=$cutArr=$sewArr=$packArr=[];
            $pendingProductionArr['orderNo']=$order->order_no;
            $productionDetails['orderNo']=$order->order_no;
            $productionDetails['styleNo']=$order->style_no;
            /* Cut */
            $cutArr['title']="Cutting";
            $cutArr['startDate']=$order->cutting_start_date;
            $cutArr['endDate']=$order->cutting_end_date;
            $cutArr['totalQuantity']=$order->total_quantity;
            $cutArr['updatedQuantity']=$this->getUpdatedProductionSum($order,"Cut");
            $cutArr['pendingQuantity']=$cutArr['totalQuantity'] - $cutArr['updatedQuantity'];
            $cutArr['delay']=$this->dateDifferences($order->cutting_end_date);
            if($cutArr['pendingQuantity'] >0){
                $prodData[]=$cutArr;
            }
            /* Sew */
            $sewArr['title']="Sewing";
            $sewArr['startDate']=$order->sewing_start_date;
            $sewArr['endDate']=$order->sewing_end_date;
            $sewArr['totalQuantity']=$order->total_quantity;
            $sewArr['updatedQuantity']=$this->getUpdatedProductionSum($order,"Sew");
            $sewArr['pendingQuantity']=$sewArr['totalQuantity'] - $sewArr['updatedQuantity'];
            $sewArr['delay']=$this->dateDifferences($order->sewing_end_date);
            if($sewArr['pendingQuantity'] >0){
                $prodData[]=$sewArr;
            }
            /* Pack */
            $packArr['title']="Packing";
            $packArr['startDate']=$order->packing_start_date;
            $packArr['endDate']=$order->packing_end_date;
            $packArr['totalQuantity']=$order->total_quantity;
            $packArr['updatedQuantity']=$this->getUpdatedProductionSum($order,"Pack");
            $packArr['pendingQuantity']=$packArr['totalQuantity'] - $packArr['updatedQuantity'];
            $packArr['delay']=$this->dateDifferences($order->packing_end_date);
            if($packArr['pendingQuantity'] >0){
                $prodData[]=$packArr;
            }
            if($order->factory_id != NULL){
                $forType = [
                    ['company_id','=',1],
                    ['workspace_id','=',1],
                    // ['company_id','=',$request['company_id']],
                    // ['workspace_id','=',$request['workspace_id']],
                    ['id','=',$order->factory_id]
                ];
                $productionDetails['factory'] = ($this->getDetails($forType,"Factory"))->name;
            }
            if($order->pcu_id != NULL){
                $forType = [
                    ['company_id','=',1],
                    ['workspace_id','=',1],
                    // ['company_id','=',$request['company_id']],
                    // ['workspace_id','=',$request['workspace_id']],
                    ['id','=',$order->pcu_id]
                ];
                $productionDetails['pcu'] = ($this->getDetails($forType,"PCU"))->name;
            }
            if($order->buyer_id != NULL){
                $forType = [
                    ['company_id','=',1],
                    ['workspace_id','=',1],
                    // ['company_id','=',$request['company_id']],
                    // ['workspace_id','=',$request['workspace_id']],
                    ['id','=',$order->buyer_id]
                ];
                $productionDetails['buyer'] =  ($this->getDetails($forType,"Buyer"))->name;
            }
            /* Data append to array */
            $productionDetails['prodData']=$prodData;
            $productionDetails['lastDate']=$packArr['endDate'];
            $pendingProductionArr['productionData'][]=$productionDetails;
        }
        view()->share("productionData",$pendingProductionArr);
        //    return response()->json(["status_code"=>200,"data"=>$pendingProductionArr]);
        return view('pendingProductionPDF');
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
    public function dateDifferences($endDate)
    {
        $lastDate = new DateTime($endDate);
        $today = new DateTime(date("Y-m-d"));

        $interval = $today->diff($lastDate)->format("%r%a");

        return (int)$interval;
    }

    public function getDailyDataPDF(){
        $whereCondition =[
            ['company_id',"=",1],
            ['workspace_id','=',1],
            ['step_level','=','5'],
            ['id','=',58]
        ];
        $whereCondition[]=['status','=',"1"];
        $orders = Order::where($whereCondition)->first();
        $whereConditionToSend=[
            ['company_id','=',1],
            ['id','=',1]
        ];
        $dateFormat=GetUserSettings::getPeopleDateFormat("User",$whereConditionToSend);
        $language = GetUserLanguage::getLanguageOfUserWithId(1,1,"User",1);
        App::setlocale($language);
        $whereCondition1 =[
            ['company_id',"=",1],
            ['workspace_id','=',1],
            ['order_id','=',58]
        ];
        $dailyUpdatesArr=$dailyUpdate=[];
        $startDate= $orders->cutting_start_date;
        $endDate= $orders->packing_end_date;
        if($orders->factory_id != NULL){
            $forType = [
                ['company_id','=',1],
                ['workspace_id','=',1],
                ['id','=',$orders->factory_id]
            ];
            $dailyUpdatesArr['factory'] = ($this->getDetails($forType,"Factory"))->name;
        }
        if($orders->pcu_id != NULL){
            $forType = [
                ['company_id','=',1],
                ['workspace_id','=',1],
                ['id','=',$orders->pcu_id]
            ];
            $dailyUpdatesArr['pcu'] = ($this->getDetails($forType,"PCU"))->name;
        }
        if($orders->buyer_id != NULL){
            $forType = [
                ['company_id','=',1],
                ['workspace_id','=',1],
                ['id','=',$orders->buyer_id]
            ];
            $dailyUpdatesArr['buyer'] =  ($this->getDetails($forType,"Buyer"))->name;
        }
        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
        ->select('type_of_production',DB::raw("SUM(updated_quantity) as updated_quantity"),'sku_date','target_value',
        DB::raw("(SUM(updated_quantity) - target_value) as diff"))
        ->groupBy('type_of_production')->groupBy('sku_date')->orderBy('sku_date','ASC')->get();
        // dd($dailyUpdates);
        $i=0;
        $dailyUpdatesArr['orderNo']=$orders->order_no;
        $dailyUpdatesArr['styleNo']=$orders->style_no;
        $dailyUpdatesArr['startDate']=$startDate;
        $dailyUpdatesArr['endDate']=$endDate;
        $dailyUpdatesArr['dateFormat']=$dateFormat;
        foreach($dailyUpdates as $updates){
            $updatesArr=[];
            $updatesArr['sku_date']=date($dateFormat,strtotime($updates->sku_date));
            $updatesArr[$updates->type_of_production."_actual"]=(int)$updates->updated_quantity;
            $updatesArr[$updates->type_of_production."_target"]=(int)$updates->target_value;
            $updatesArr[$updates->type_of_production."_diff"]=(int)$updates->diff;
            /* To place the prod datas in their respective dates */
            $res=array_search(date($dateFormat,strtotime($updates->sku_date)), array_column($dailyUpdate, 'sku_date')) ?? -1;

            if($res>=0 && $res!=''){
                $dailyUpdate[$res][$updates->type_of_production."_actual"]=(int)$updates->updated_quantity;
                $dailyUpdate[$res][$updates->type_of_production."_target"]=(int)$updates->target_value;
                $dailyUpdate[$res][$updates->type_of_production."_diff"]=(int)$updates->diff;

            }else{
                $dailyUpdate[$i]=$updatesArr;
                $i++;
            }
        }
        $dailyUpdatesArr['prodData']=$dailyUpdate;
        // dd($dailyUpdatesArr);
        // echo '<pre>'; print_r($dailyUpdatesArr); exit;
        view()->share("dailyUpdates",$dailyUpdatesArr);
        // return view("DayByDayReportPDF",["dailyUpdates"=>$dailyUpdatesArr]);
        $pdf = Pdf::loadView('DayByDayReportPDF');
        // $path = public_path() . '/PendingTask/' .$request->orderNo.date('d-m-Y').'.pdf';
        // $pdf->save($path);
        // return response()->download($path);
        // $pdf->setPaper('A4', 'portrait');
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->stream("dompdf_out.pdf", array("Attachment" => false));
    }

    public function orderReport(){
        $request = [
            "company_id"=>"1",
            "workspace_id"=>"1",
            "user_id" =>"1",
            "staff_id" =>"0",
            "factory_id"=>"0",
            "pcu_id"=>"0"
        ];
        $whereCondition=[
            ['orders.company_id','=',$request['company_id']],
            ['orders.workspace_id','=',$request['workspace_id']]
        ];
        /* For Status Filter Start */
        $statusFilter ="All";
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
        if( $request['factory_id'] > 0)
            $whereCondition[]=['orders.factory_id',"=",$request['factory_id']];
        // if(  $request['buyer_id'] > 0)
        //     $whereCondition[]=['orders.buyer_id',"=",$request['buyer_id']];
        if( $request['pcu_id'] > 0)
            $whereCondition[]=['orders.pcu_id',"=",$request['pcu_id']];
        /* For Factory, Buyer and PCU End */
        /* Language Start */
        if($request['user_id']>0){
            $whereConditionToSend=[
                ['company_id','=',$request['company_id']],
                ['id','=',$request['user_id']]
            ];
            $dateFormat=GetUserSettings::getPeopleDateFormat("User",$whereConditionToSend);
            $language = GetUserLanguage::getLanguageOfUserWithId($request['company_id'],$request['workspace_id'],"User",$request['user_id']);
        }
        else if($request['staff_id']>0){
            $whereConditionToSend=[
                ['company_id','=',$request['company_id']],
                ['workspace_id','=',$request['workspace_id']],
                ['id','=',$request['staff_id']]
            ];
            $dateFormat=GetUserSettings::getPeopleDateFormat("Staff",$whereConditionToSend);
            $language = GetUserLanguage::getLanguageOfUserWithId($request['company_id'],$request['workspace_id'],"Staff",$request['staff_id']);
        }
        App::setlocale($language);
        /* Language End */
        if($request['staff_id'] > 0){
            $whereCondition1= $whereCondition2 = [
                ['company_id','=',$request['company_id']],
                ['workspace_id','=',$request['workspace_id']]
            ];
            $staffRoleHasPermission = Staff::where('id',$request['staff_id'])->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['permission_id','=','19'];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request['staff_id']];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $theOrders = [];
                foreach($involedOrders as $order) {
                    // $theOrder = Order::where($whereCondition)->where("id", $order->order_id)->first();
                    $theOrders[]=$order->order_id;
                }
                $query1 = DB::raw("(CASE WHEN orders.staff_id=0 THEN users.name WHEN orders.staff_id>0 THEN CONCAT(staff.first_name,'',staff.last_name) END) as createdBy");
                $query2 = DB::raw("(CASE WHEN orders.action_done_user_id>0 THEN U.name WHEN orders.action_done_staff_id>0 THEN CONCAT(S.first_name,'',S.last_name) END) as actionDoneBy");
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
                ->get();
            }else{
                $query1 = DB::raw("(CASE WHEN orders.staff_id=0 THEN users.name WHEN orders.staff_id>0 THEN CONCAT(staff.first_name,'',staff.last_name) END) as createdBy");
                $query2 = DB::raw("(CASE WHEN orders.action_done_user_id>0 THEN U.name WHEN orders.action_done_staff_id>0 THEN CONCAT(S.first_name,'',S.last_name) END) as actionDoneBy");
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
                ->get();
            }
        }else{
            $query1 = DB::raw("(CASE WHEN orders.staff_id=0 THEN users.name WHEN orders.staff_id>0 THEN CONCAT(staff.first_name,'',staff.last_name) END) as createdBy");
            $query2 = DB::raw("(CASE WHEN orders.action_done_user_id>0 THEN U.name WHEN orders.action_done_staff_id>0 THEN CONCAT(S.first_name,'',S.last_name) END) as actionDoneBy");
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
            ->get();
        }

        $dataArr=[];
        $dataArr['dateFormat']=$dateFormat;
        $dataArr['statusFilter']=$statusFilter;
        $dataArr['orders']=$orders;
        // if($request->has('factory_id')){
            if($request['factory_id'] > 0){
                $forType = [
                    ['company_id','=',$request['company_id']],
                    ['workspace_id','=',$request['workspace_id']],
                    ['id','=',$request['factory_id']]
                ];
                $dataArr['factory'] = ($this->getDetails($forType,"Factory"))->name;
            }else{
                $dataArr['factory'] = "All";
            }
        // }
        // if($request->has('buyer_id')){
            // if($request['buyer_id'] > 0){
            //     $forType = [
            //         ['company_id','=',$request['company_id']],
            //         ['workspace_id','=',$request['workspace_id']],
            //         ['id','=',$request['buyer_id']]
            //     ];
            //     $dataArr['buyer'] = ($this->getDetails($forType,"Buyer"))->name;
            // }else{
            //     $dataArr['buyer'] = "All";
            // }
        // }
        // if($request->has('pcu_id')){
            if($request['pcu_id'] > 0){
                $forType = [
                    ['company_id','=',$request['company_id']],
                    ['workspace_id','=',$request['workspace_id']],
                    ['id','=',$request['pcu_id']]
                ];
                $dataArr['pcu'] = ($this->getDetails($forType,"PCU"))->name;
            }
            else{
                $dataArr['pcu'] = "All";
            }
        // }

        // return response()->json(["status_code"=>200,"status" =>"success","data"=>$dataArr]);
        return view('OrderReportPDF',['orderData'=>$dataArr]);
        view()->share("orderData",$dataArr);
        $pdf = Pdf::loadView('OrderReportPDF');
        // $pdf->setPaper('A4', 'portrait');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->stream("dompdf_out.pdf", array("Attachment" => false));
    }
}
