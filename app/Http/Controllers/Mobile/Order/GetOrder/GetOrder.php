<?php

namespace App\Http\Controllers\Mobile\Order\GetOrder;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Common\CommonApp;
use Carbon\Carbon;
class GetOrder extends Controller
{
    /******* Get Orders for the Filter Purpose ********/
    public function getOrdersList(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id'=>'required',
            'workspace_id'=>'required'
        ]);
        if($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $whereCondition =[
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id],
        ];
        if($request->style_no!=""){
           // $whereCondition['style_no'] = $request->style_no;
            $whereCondition['id'] = $request->style_no;
        }
        if ($request->factory_id!=""){
            $whereCondition['factory_id'] = $request->factory_id;
        }
        if ($request->pcu_id!=""){
            $whereCondition['pcu_id'] = $request->pcu_id;
        }
        if ($request->buyer_id!=""){
            $whereCondition['buyer_id'] = $request->buyer_id;
        }
        if ($request->buyer_id!=""){
            $whereCondition['buyer_id'] = $request->buyer_id;
        }
        // $orderNoLength = $orderNo->count();
        $orders = Order::where($whereCondition)

            ->select('order_no','id', 'user_id','staff_id', 'company_id', 'workspace_id', 'order_no', 'style_no',
            'buyer_id', 'pcu_id', 'factory_id',
            'cutting_start_date', 'cutting_end_date', 'sewing_start_date', 'sewing_start_date',
            'packing_start_date', 'packing_end_date',
            'status','step_level', 'status_request', 'created_at', 'updated_at')
            ->orderby( 'order_no','ASC')
            ->get()->unique('order_no');

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

                $getApendary=[];
                $getApendary['orderId']=$order['id'];
                $getApendary['orderNo']=$order['order_no'];
                $getApendary['styleNo']=$order['style_no'];
                $getApendary['step_level']=$order['step_level'];
                $getApendary['personIncharge']=$this->getOrderCreatedBy($Data);
                $styleCount=$this->getNumberOfStyle($Data);
                $getApendary['styleCount']=$styleCount;
                $getApendary['cutting_start_date']=$order['cutting_start_date'];
                $getApendary['packing_end_date']=$order['packing_end_date'];
                $getApendary['status']=$order['status'];



                $getTaskDetails=$this->getSubTaskStyleNoDetails($Data);

                $getApendary['subtask_details']=$getTaskDetails;
                $style_arr[]=$getApendary;

            }
        }

        //echo '<pre></pre>'; print_r($order_arr); exit;


        if(count($orders) === 0){
            return response()->json(['status_code' => 400,
            'status'=>"failure" ,'message' => "Order not found"]);
        }
        return response()->json(['status_code' => 200,
        'status'=>"success",
        'data' => $style_arr]);
    }
    /* Get the task Counts */
    public function getSubTaskDetailsCount($Data,$type){
        //dd($Data);
        if($type=='complete'){
        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ['order_id','=',$Data['orderId']],
           ['task_accomplished_date','!=',NULL],
        ];
    }elseif($type=='delay'){
        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ['order_id','=',$Data['orderId']],
           ['task_schedule_end_date','<',date("Y-m-d")],
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

        $completeTaskCount = OrderTask::where($whereCondition)->get();
      //  dd( count($completeTaskCount));
        //$taskDataArr = [];
       return  count($completeTaskCount);
    }
    /* Get the task Details */
    public function getSubTaskDetails($Data){
        //dd($Data);
        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ['order_id','=',$Data['orderId']],
            ['task_schedule_start_date','!=',NULL],
            ['task_schedule_end_date','!=',NULL],


        ];
        $completeTaskCount = OrderTask::select('cat_title','task_title','task_schedule_end_date','task_schedule_start_date','task_accomplished_date')->where($whereCondition)->orderby('cat_title','asc')->orderby('task_schedule_start_date','asc')->orderby('task_title','asc')->get();
        return  $completeTaskCount;
    }
    /* Get the Style Details */
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

               $taskdetailsarry['subtask_complete_count']=$getTask_complete;
               $taskdetailsarry['subtask_delay_count']=$getTask_delay;
               $taskdetailsarry['subtask_notstart_count']=$getTask_notstart;
               $taskdetailsarry['subtaskDetailsList']=$this->getSubTaskDetails($Data);
               $totalTaskArray[]=$taskdetailsarry;
        }
        return  $totalTaskArray;
    }
    /* get the number of the styles */
    public function getNumberOfStyle($Data){

        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ['order_no','=',$Data['orderNo']], ];
        $OrderCount = Order::select('id')->where($whereCondition)->get();
        return  count($OrderCount);

    }
    /* Get the order created by */
    public function getOrderCreatedBy($Data){
        $whereCondition = [
            ['company_id','=',$Data['company_id']],
            ['workspace_id','=',$Data['workspace_id']],
            ];
            if($Data['staff_id']>0){
              $getDet=CommonApp::getStaffDetailsByID($Data['staff_id']);
              $name=$getDet['first_name'];
            }else{
                $getDet=CommonApp::getUserDetailsById($Data['user_id']);
                $name=$getDet['username'];
            }
return $name;
    }
    /* Get the task end and start date  */
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

}
