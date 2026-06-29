<?php

namespace App\Http\Controllers\WebSite\Order\GetOrder;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Common\CommonApp;
use App\Models\HolidaySetting;
use App\Models\UpdateSkuQuantity;
use App\Models\OrderProduction;
use Illuminate\Support\Facades\DB;
use App\Models\Staff;
use App\Models\WeekOff;

class GetOrder extends Controller
{
    /******* Get Orders for the Filter Purpose ********/
    public function getOrdersList(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id'=>'required',
            'workspace_id'=>'required'
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition =[
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id],
        ];
        if(isset($request->style_no) && $request->style_no!=""){
           // $whereCondition['style_no'] = $request->style_no;
            $whereCondition['id'] = $request->style_no;
        }
        // if ($request->factory_id!=""){
        //     $whereCondition['factory_id'] = $request->factory_id;
        // }
        // if ($request->pcu_id!=""){
        //     $whereCondition['pcu_id'] = $request->pcu_id;
        // }
        // if ($request->buyer_id!=""){
        //     $whereCondition['buyer_id'] = $request->buyer_id;
        // }
        // if ($request->buyer_id!=""){
        //     $whereCondition['buyer_id'] = $request->buyer_id;
        // }
        // $orderNoLength = $orderNo->count();
        $orders = Order::where($whereCondition)

            ->select('order_no','id', 'user_id','staff_id', 'company_id', 'workspace_id', 'order_no', 'style_no',
            'buyer_id', 'pcu_id', 'factory_id', 'total_quantity',
            'cutting_start_date', 'cutting_end_date', 'sewing_start_date', 'sewing_start_date',
            'packing_start_date', 'packing_end_date',
            'status','step_level', 'status_request', 'created_at', 'updated_at')
            ->orderby( 'order_no','ASC')
            ->get()->unique('order_no');

            if(isset($request->staff_id) && $request->staff_id>0){
            $staffRoleHasPermission = Staff::where('id',$request->staff_id)->first();
            $staffRoleId=$staffRoleHasPermission->role_id;
                }else{
           $staffRoleId=0;
                }

        $order_arr= $style_arr= array();
        $order_no = ''; $j=$k=0;
        $userName = $cutting_start_date = $packing_end_date = '';
        if(count($orders)>0){
           // for($i=0; $i<count($orders); $i++){
                foreach($orders as $order){

                $Data=[];
                $Data['orderId']=$order['id'];
                $Data['orderNo']=$order['order_no'];
                $Data['workspace_id']=$order['workspace_id'];
                $Data['company_id']=$order['company_id'];
                $Data['user_id']=$order['user_id'];
                $Data['staff_id']=$order['staff_id'];
                $Data['staff_roleid']=$staffRoleId;
                $Data['getstaff_id']=$request->staff_id;

                $getApendary=[];
                $getApendary['orderId']=$order['id'];
                $getApendary['orderNo']=$order['order_no'];
                $getApendary['styleNo']=$order['style_no'];
                $getApendary['quantity']=$order['total_quantity'];
                $getApendary['step_level']=$order['step_level'];
                $getApendary['personIncharge']=$this->getOrderCreatedBy($Data);
               // $styleCount=$this->getNumberOfStyle($Data);
                $styleCount=0;
                $totalTaskCount=$this->getTotalTaskCount($Data);
                $totalComplateTaskCount=$this->getCompleteTaskCount($Data);

                $getApendary['styleCount']=$styleCount;
                $getApendary['totalTaskCount']=$totalTaskCount;
                $getApendary['totalFinishedTaskCount']=$totalComplateTaskCount;

                $getApendary['cutting_start_date']=$order['cutting_start_date'];
                $getApendary['packing_end_date']=$order['packing_end_date'];
                $getApendary['status']=$order['status'];



                 $getTaskDetails=$this->getSubTaskStyleNoDetails($Data);
                  $getApendary['subtask_details']=$getTaskDetails;
                  $getSkuQuantityCutPercentage=$this->getSkuQuantityPercentage($Data,'Cut');
                  $getApendary['cut_percentage']=$getSkuQuantityCutPercentage;
                  $getSkuQuantitySewPercentage=$this->getSkuQuantityPercentage($Data,'Sew');
                  $getApendary['sew_percentage']=$getSkuQuantitySewPercentage;
                  $getSkuQuantityPackPercentage=$this->getSkuQuantityPercentage($Data,'Pack');
                  $getApendary['pack_percentage']=$getSkuQuantityPackPercentage;
                $style_arr[]=$getApendary;

            }
        }

        //echo '<pre></pre>'; print_r($order_arr); exit;
        $holidayDetails = HolidaySetting::getHolidays($request);
        $whereConditions = [
            ["company_id","=",$request->company_id],
            ["workspace_id","=",$request->workspace_id]
        ];
        $weekOffs = WeekOff::where($whereConditions)->select(DB::raw('GROUP_CONCAT(days) as days'))->get();
        if(count($orders) === 0){
            $res = json_encode(['status_code' => 400,
            'status'=>"failure" ,'message' => "Order not found"]);
            return CommonApp::webEncrypt($res);
        }
        $res = json_encode(['status_code' => 200,
        'status'=>"success",
        'data' => $style_arr,"holidayDetails"=>$holidayDetails,"weekOffs"=>$weekOffs]);
        return CommonApp::webEncrypt($res);
    }

    /* To get the task counts */
    public function getSubTaskDetailsCount($Data,$type){
        //dd($Data);
        if($type=='complete'){
        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ['order_id','=',$Data['orderId']],
           // ['task_schedule_end_datet','>=','task_accomplished_date'],
            ['task_accomplished_date','!=',NULL],
        ];
    }elseif($type=='delay'){
        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ['order_id','=',$Data['orderId']],
            ['task_schedule_end_date','!=',NULL],
            ['task_schedule_start_date','!=',NULL],
           ['task_schedule_end_date','<',date("Y-m-d")],
           ['task_accomplished_date','=',NULL],

        ];

    }
    elseif($type=='delaycomplete'){
        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ['order_id','=',$Data['orderId']],
           //['task_schedule_end_date','<','task_accomplished_date'],
           //['task_accomplished_date','!=',NULL],
        ];
        //dd($whereCondition);
    }elseif($type=='inprogress'){
        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ['order_id','=',$Data['orderId']],
            ['task_schedule_start_date','<=',date("Y-m-d")],
           // ['actual_start_date','<=',date("Y-m-d")],
            ['task_schedule_end_date','>=',date("Y-m-d")],
            ['task_accomplished_date','=',NULL],
            ['task_schedule_start_date','!=',NULL],
            ['task_schedule_end_date','!=',NULL],
        ];

    }

    else{
        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ['order_id','=',$Data['orderId']],
           ['task_schedule_start_date','>',date("Y-m-d")],
        ];

    }
    if($type=='complete' && $Data['company_id']>0 && $Data['workspace_id']>0 && $Data['orderId']>0){
        // $completeTaskCount = DB::table('order_task_data')
        // ->select( DB::raw("select * from `order_task_data` where (`company_id` = ".$Data['company_id']." and `workspace_id` = ".$Data['workspace_id']." and `order_id` = ".$Data['orderId']." and DATE_FORMAT(`task_schedule_end_date`,'%Y-%m-%d') >=DATE_FORMAT(`task_accomplished_date`,'%Y-%m-%d') and `task_accomplished_date` is not null);") );
       $completeTaskCount = OrderTask::where($whereCondition)
    //    ->where('task_schedule_end_date','>=','task_accomplished_date')
        ->whereRaw("DATE_FORMAT(task_schedule_end_date,'%Y-%m-%d') >= DATE_FORMAT(task_accomplished_date,'%Y-%m-%d')")
       // ->where("task_schedule_end_date",">=" ,"task_accomplished_date")
       ->get();
    }   elseif($type=='delaycomplete'  && $Data['company_id']>0 && $Data['workspace_id']>0 && $Data['orderId']>0){
        // $completeTaskCount = DB::table('order_task_data')
        // ->select( DB::raw("select * from `order_task_data` where (`company_id` = ".$Data['company_id']." and `workspace_id` = ".$Data['workspace_id']." and `order_id` = ".$Data['orderId']." and DATE_FORMAT(`task_schedule_end_date`,'%Y-%m-%d') <DATE_FORMAT(`task_accomplished_date`,'%Y-%m-%d') and `task_accomplished_date` is not null);") );
        $completeTaskCount = OrderTask::where($whereCondition)
    //    ->where('task_schedule_end_date','>=','task_accomplished_date')
        ->whereRaw("DATE_FORMAT(task_schedule_end_date,'%Y-%m-%d') < DATE_FORMAT(task_accomplished_date,'%Y-%m-%d')")
       //->whereDate("task_schedule_end_date22","<" ,"task_accomplished_date1")
       ->get();
       //dd($whereCondition,count($completeTaskCount));
    }else{
        $completeTaskCount = OrderTask::where($whereCondition)->get();
    }


       //dd( count($completeTaskCount));
        //$taskDataArr = [];
       return  count($completeTaskCount);
    }

    /* To get the Task Details */
    public function getSubTaskDetails($Data){
        //dd($Data);


        if($Data['getstaff_id']>0 && $Data['staff_roleid']==3){

            $whereCondition = [
                ['order_task_data.company_id','=',$Data['company_id']],
                ['order_task_data.workspace_id','=',$Data['workspace_id']],
                ['order_task_data.order_id','=',$Data['orderId']],
                ['order_task_data.task_schedule_start_date','!=',NULL],
                ['order_task_data.task_schedule_end_date','!=',NULL],
                ['order_task_data.task_pic','=',$Data['getstaff_id']],
            ];
        }else{
            $whereCondition = [
                ['order_task_data.company_id','=',$Data['company_id']],
                ['order_task_data.workspace_id','=',$Data['workspace_id']],
                ['order_task_data.order_id','=',$Data['orderId']],
                ['order_task_data.task_schedule_start_date','!=',NULL],
                ['order_task_data.task_schedule_end_date','!=',NULL],
            ];
        }

       // $completeTaskCount = OrderTask::select('cat_title','task_title','task_schedule_end_date','task_schedule_start_date','task_accomplished_date')->where($whereCondition)->orderby('cat_title','asc')->orderby('task_schedule_start_date','asc')->orderby('task_title','asc')->get();
        //$completeTaskCount = OrderTask::select('cat_title','task_title','task_schedule_end_date','task_schedule_start_date','task_accomplished_date')->where($whereCondition)->orderby('task_schedule_start_date','asc')->get();
        $completeTaskCount = OrderTask::select('order_task_data.id','order_task_data.cat_title','order_task_data.task_title','order_task_data.subtask_title',
        'order_task_data.actual_start_date','order_task_data.task_schedule_end_date','order_task_data.task_schedule_start_date',
        'order_task_data.task_accomplished_date','order_task_data.task_pic','staff.first_name','staff.last_name')
        ->leftjoin('staff','staff.id','order_task_data.task_pic')
        ->where($whereCondition)
        ->orderby('task_schedule_start_date','asc')
        ->get();
        return  $completeTaskCount;
    }

    /* To get the Task Details*/
    public function getSubTaskStyleNoDetails($Data){
        //dd($Data);
        $totalTaskArray=[];
        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
           // ['order_no','=',$Data['orderNo']],
           ['id','=',$Data['orderId']],
             ];
        $orderStyleDetails = Order::select('id','order_no','style_no','cutting_start_date','packing_end_date')->where($whereCondition)->get();
        foreach($orderStyleDetails as $orderdet)
        {
            $taskdetailsarry=[];
            $taskdetailsarry['order_id']=$orderdet['id'];
            $taskdetailsarry['order_no']=$orderdet['order_no'];
            $taskdetailsarry['style_no']=$orderdet['style_no'];
            $taskdetailsarry['cutting_start_date']=$orderdet['cutting_start_date'];
            $taskdetailsarry['packing_end_date']=$orderdet['packing_end_date'];
            $getTaskStartDate=$this->getTaskStartandEndDate($Data,'min');
            $getTaskEndDate=$this->getTaskStartandEndDate($Data,'max');
            $taskdetailsarry['task_start_date']=$getTaskStartDate['task_schedule_start_date'];
            $taskdetailsarry['task_end_date']=$getTaskEndDate['task_schedule_end_date'];

            $getTask_complete=$this->getSubTaskDetailsCount($Data,'complete');
            $getTask_delay=$this->getSubTaskDetailsCount($Data,'delay');
            $getTask_notstart=$this->getSubTaskDetailsCount($Data,'notstart');
            $getTask_delaycomp=$this->getSubTaskDetailsCount($Data,'delaycomplete');
            $getTask_inprogress=$this->getSubTaskDetailsCount($Data,'inprogress');


              $taskdetailsarry['subtask_complete_count']=$getTask_complete;
               $taskdetailsarry['subtask_delay_count']=$getTask_delay;
               $taskdetailsarry['subtask_delay_complete']=$getTask_delaycomp;
               $taskdetailsarry['subtask_inprogress']=$getTask_inprogress;
               $taskdetailsarry['subtask_notstart_count']=$getTask_notstart;
               $taskdetailsarry['subtaskDetailsList']=$this->getSubTaskDetails($Data);

               $totalTaskArray[]=$taskdetailsarry;
        }
        return  $totalTaskArray;
    }

    /* To get the number of styles  */
    public function getNumberOfStyle($Data){

        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ['order_no','=',$Data['orderNo']], ];
        $OrderCount = Order::select('id')->where($whereCondition)->get();
        return  count($OrderCount);

    }

    /* To get the total task counts */
    public function getTotalTaskCount($Data){

        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ['order_id','=',$Data['orderId']], ];
        $OrderCount = OrderTask::select('id')->where($whereCondition)->get();
        return  count($OrderCount);

    }

    /* To get the completed task counts */
    public function getCompleteTaskCount($Data){

        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ['order_id','=',$Data['orderId']],
            ['task_accomplished_date','!=',NULL],

        ];
        $OrderCount = OrderTask::select('id')->where($whereCondition)->get();
        return  count($OrderCount);

    }

    /* to get order created data */
    public function getOrderCreatedBy($Data){
        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
        ];
        if($Data['staff_id']>0  && $Data['staff_roleid']==3){
            $getDet=CommonApp::getStaffDetailsByID($Data['staff_id']);
            $name=$getDet['first_name'];
        }else{
            $getDet=CommonApp::getUserDetailsById($Data['user_id']);
            $name=$getDet['username'];
        }
        return $name;
    }

    /* Get the task end and start date */
    public function getTaskStartandEndDate($Data,$type){
        $completeTaskCount=[];
        if($type=='min'){
            $whereCondition = [
                ['company_id','=',$Data['company_id']],
                ['workspace_id','=',$Data['workspace_id']],
                ['order_id','=',$Data['orderId']],
                ['task_schedule_start_date','!=',NULL],
            ];
            $completeTaskCount = OrderTask::select('task_schedule_start_date')->where($whereCondition)->orderby('task_schedule_start_date','asc')->first();
            if(empty($completeTaskCount)){
                $completeTaskCount['task_schedule_start_date']='';
            }
        }else{
            $whereCondition = [
                ['company_id','=',$Data['company_id']],
                ['workspace_id','=',$Data['workspace_id']],
                ['order_id','=',$Data['orderId']],
                ['task_schedule_end_date','!=',NULL],
              ];

            $completeTaskCount = OrderTask::select('task_schedule_end_date')->where($whereCondition)->orderby('task_schedule_end_date','desc')->first();
            if(empty($completeTaskCount)){
                $completeTaskCount['task_schedule_end_date']='';
            }
        }


        return  $completeTaskCount;


      }

      /* Get the SKU percentage */
      public static function getSkuQuantityPercentage($Data,$type){
        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ['order_id','=',$Data['orderId']],
               ];
        $updUpdateQty = UpdateSkuQuantity::where($whereCondition)->where("type_of_production",$type)->sum('updated_quantity');
        $prodTotalQty = OrderProduction::where($whereCondition)->where("type_of_production",$type)->sum('target_value');
        if($prodTotalQty>0){
         $percentageValue = ($updUpdateQty / $prodTotalQty) * 100;
        }else{
            $percentageValue = 0;
        }
        return array("type"=>$type,"orderqty"=>$prodTotalQty,"updatedqty"=>$updUpdateQty,"percentage"=>round($percentageValue));
    }

}
