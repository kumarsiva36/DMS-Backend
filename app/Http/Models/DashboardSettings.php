<?php

namespace App\Models;

use App\Http\Controllers\WebSite\Common\DashboardNew;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DashboardSettings extends Model
{
    use HasFactory;

    protected $table = 'dashboard_settings';

    /* Get Task Details */
    public static function getTaskDetails($request){
        $dataArr=[];
        $taskChartConditions=[
            ['order_id','=',$request->order_id],
            ['task_schedule_start_date','!=',NULL],
            ['task_schedule_end_date','!=',NULL],
           // ['is_subtask','=',0]
        ];

        //$tasks = OrderTask::where('order_id',$request->order_id)->where('is_subtask',0)->get();
        $tasks = OrderTask::where('order_id',$request->order_id)->get();
        $tasksChart = OrderTask::where($taskChartConditions)
        ->leftjoin('staff','staff.id','order_task_data.task_pic')
        ->select('order_task_data.cat_title','order_task_data.task_title','order_task_data.subtask_title',
        'order_task_data.actual_start_date','order_task_data.task_schedule_start_date','order_task_data.task_schedule_end_date',
        'order_task_data.task_accomplished_date','order_task_data.task_pic','staff.first_name','staff.last_name')
        ->get();
        $counts = DashboardSettings::getTasksCount($tasks);
        $dataArr['total'] = $counts['total'];
        $dataArr['completed'] = $counts['completed'];
        $dataArr['delayedCompleted'] = $counts['delayedCompleted'];
        $dataArr['delay']= $counts['delay'];
        $dataArr['inProgress'] = $counts['inProgress'];
        $dataArr['yetToStart']= $counts['yetToStart'];

        $tasksArr['taskCount'][]=$dataArr;
        $tasksArr['tasksChart']=$tasksChart;

        return $tasksArr;
    }

    /* To Get separated Task Counts  */
    public static function getTasksCount($tasks){
        $completed=$delayedCompleted=$delay=$inProgress=$yetToStart=$total=$delayedStart=$notScheduled=0;
        foreach($tasks as $task){
            if($task->task_accomplished_date != null && $task->task_accomplished_date <= $task->task_schedule_end_date){
                $completed +=1;
            }
            if($task->task_accomplished_date != null && $task->task_accomplished_date > $task->task_schedule_end_date){
                $delayedCompleted +=1;
            }
            if(($task->task_accomplished_date == null && $task->task_schedule_start_date != null && $task->task_schedule_end_date != null)
           // if(($task->task_accomplished_date == null && (($task->actual_start_date) != null) && $task->task_schedule_end_date != null)
            && date('Y-m-d') > $task->task_schedule_end_date){
                $delay +=1;
            }
            if(($task->task_schedule_start_date == null && $task->task_schedule_end_date == null)){
                $notScheduled+=1;
            }
            //if(($task->task_accomplished_date == null && $task->task_schedule_start_date == null && $task->task_schedule_end_date == null)||
            ////(date('Y-m-d') < $task->task_schedule_start_date)){
               // ((date('Y-m-d') < ($task->actual_start_date?$task->actual_start_date:$task->task_schedule_start_date)) && (($task->actual_start_date?$task->actual_start_date:$task->task_schedule_start_date)!=null))){
            if(($task->task_accomplished_date == null && $task->task_schedule_start_date!=null) && (date('Y-m-d') < $task->task_schedule_start_date)){
                $yetToStart +=1;
            }
            if(($task->task_accomplished_date == null && $task->task_schedule_start_date != null && $task->task_schedule_end_date != null) &&
                ($task->task_schedule_start_date <= date('Y-m-d') && $task->task_schedule_end_date >= date('Y-m-d'))){
                if($task->actual_start_date!=null && ($task->actual_start_date > $task->task_schedule_start_date)){
                    $delayedStart += 1;
                }
                $inProgress +=1;
            }
            $total +=1;
        }
        $countArr=[];
        $countArr['total']=$total;
        $countArr['completed']=$completed;
        $countArr['delayedCompleted']=$delayedCompleted;
        $countArr['delay']=$delay;
        $countArr['yetToStart']=$yetToStart;
        $countArr['inProgress']=$inProgress;
        $countArr['delayedStart']=$delayedStart;
        $countArr['notScheduled']=$notScheduled;

        return $countArr;
    }

    /* To get Production Details */
    public static function getProdDetails($request){
        $arr=[];
        $orderData = Order::getOrderDetailUsingID($request->order_id);
        $prodData = OrderProduction::where('order_id',$request->order_id)->where('date_of_production','<=',date('Y-m-d'))->get();
        $dataInputData = UpdateSkuQuantity::where('order_id',$request->order_id)->get();
        $counts = DashboardSettings::prodCounts($prodData,$dataInputData,$orderData,'orderStatus');
        $arr['orderNo']= $orderData->order_no;
        $arr['styleNo']= $orderData->style_no;
        $arr['total'] = $orderData->total_quantity;
        $arr['cutStartDate']= $orderData->cutting_start_date;
        $arr['cutEndDate']= $orderData->cutting_end_date;
        $arr['sewStartDate']= $orderData->sewing_start_date;
        $arr['sewEndDate']= $orderData->sewing_end_date;
        $arr['packStartDate']= $orderData->packing_start_date;
        $arr['packEndDate']= $orderData->packing_end_date;
        $arr['cutPercentage']=$counts['cutPercentage'];
        $arr['sewPercentage']=$counts['sewPercentage'];
        $arr['packPercentage']=$counts['packPercentage'];
        $arr['cutTargets']=$counts['cutTargetPercentage'];
        $arr['sewTargets']=$counts['sewTargetPercentage'];
        $arr['packTargets']=$counts['packTargetPercentage'];
        $arr['cutPerDay']=$counts['cutPerDay'];
        $arr['sewPerDay']=$counts['sewPerDay'];
        $arr['packPerDay']=$counts['packPerDay'];
        $arr['cutCompleted']=$counts['cutCompleted'];
        $arr['sewCompleted']=$counts['sewCompleted'];
        $arr['packCompleted']=$counts['packCompleted'];
        $arr['cutStatus']=$counts['cutStatus'];
        $arr['sewStatus']=$counts['sewStatus'];
        $arr['packStatus']=$counts['packStatus'];
        $arr['cutHoliday']=$counts['cutHoliday'];
        $arr['sewHoliday']=$counts['sewHoliday'];
        $arr['packHoliday']=$counts['packHoliday'];
        $arr['cutActualTargetValue'] = $counts['cutActualTargetValue'];
        $arr['sewActualTargetValue'] = $counts['sewActualTargetValue'];
        $arr['packActualTargetValue'] = $counts['packActualTargetValue'];
        $arr['cutTodayUpdatedValue'] = $counts['cutTodayUpdatedValue'];
        $arr['sewTodayUpdatedValue'] = $counts['sewTodayUpdatedValue'];
        $arr['packTodayUpdatedValue'] = $counts['packTodayUpdatedValue'];
        $arr['cutAvgPerDay'] = $counts['cutAvgPerDay'];
        $arr['sewAvgPerDay'] = $counts['sewAvgPerDay'];
        $arr['packAvgPerDay'] = $counts['packAvgPerDay'];
        $arr['cutEstDate'] = $counts['cutEstDate'];
        $arr['sewEstDate'] = $counts['sewEstDate'];
        $arr['packEstDate'] = $counts['packEstDate'];
        $arr['cutActualDate'] = $counts['cutActualDate'];
        $arr['sewActualDate'] = $counts['sewActualDate'];
        $arr['packActualDate'] = $counts['packActualDate'];
        $prodArr[]=$arr;
        return $prodArr;
    }

    /* To Calculate the Production Data */
    public static function prodCounts($production,$dataInput,$order,$type){
        // dd($production);
        $cutTargetValue = $sewTargetValue = $packTargetValue = 0;
        $cutActualTargetValue = $sewActualTargetValue = $packActualTargetValue = 0;
        $cutIsHoliday = $sewIsHoliday = $packIsHoliday = 2;
        $cutUpdatedValue = $sewUpdatedValue = $packUpdatedValue = 0;
        $cutTodayUpdatedValue = $sewTodayUpdatedValue = $packTodayUpdatedValue = 0;
        $cutCount = $sewCount = $packCount = 1;
        $cutPerDay = $sewPerDay = $packPerDay =0;
        $cutActualDate = $sewActualDate = $packActualDate ="";
        $whereCondition = [
            ['workspace_id','=',$order->workspace_id],
            ['company_id', '=', $order->company_id],
            ['order_id','=',$order->id],
            ['holiday_flag','!=',1],
            ['is_accomplished','!=',1]
        ];

        if(count($production)>0){
             foreach($production as $prodData){

                if($prodData->type_of_production === "Cut"){
                    $cutTargetValue += $prodData->target_value;
                    $cutIsHoliday = $prodData->holiday_flag;
                   // if(date('Y-m-d') === $prodData->date_of_production){
                        $cutActualTargetValue = $prodData->target_value;
                  //  }
                    // if(date('Y-m-d') === $prodData->date_of_production && $prodData->is_accomplished === 0)
                    //     $cutPerDay = $prodData->target_value;
                }
                else if($prodData->type_of_production === "Sew"){
                    $sewTargetValue += $prodData->target_value;
                    $sewIsHoliday = $prodData->holiday_flag;
                   // if(date('Y-m-d') === $prodData->date_of_production){
                        $sewActualTargetValue = $prodData->target_value;
                   // }
                    // if(date('Y-m-d') === $prodData->date_of_production && $prodData->is_accomplished === 0)
                    //     $sewPerDay = $prodData->target_value;
                }
                else if($prodData->type_of_production === "Pack"){
                    $packTargetValue += $prodData->target_value;
                    $packIsHoliday = $prodData->holiday_flag;
                   // if(date('Y-m-d') === $prodData->date_of_production){
                        $packActualTargetValue = $prodData->target_value;
                    //}
                    // if(date('Y-m-d') === $prodData->date_of_production && $prodData->is_accomplished === 0)
                    //     $packPerDay = $prodData->target_value;
                }
            }
        }
        if(count($dataInput)>0){
            foreach($dataInput as $data){
                if($data->type_of_production == "Cut"){
                    $cutUpdatedValue += $data->updated_quantity;
                    if(date('Y-m-d') === $data->sku_date){
                        $cutTodayUpdatedValue += $data->updated_quantity;
                    }
                    // $cutTargetValue += $data->target_value;
                }
                else if($data->type_of_production == "Sew"){
                    $sewUpdatedValue += $data->updated_quantity;
                    if(date('Y-m-d') === $data->sku_date){
                        $sewTodayUpdatedValue += $data->updated_quantity;
                    }
                    // $sewTargetValue += $data->target_value;
                }
                else if($data->type_of_production == "Pack"){
                    $packUpdatedValue += $data->updated_quantity;
                    if(date('Y-m-d') === $data->sku_date){
                        $packTodayUpdatedValue += $data->updated_quantity;
                    }
                    // $packTargetValue += $data->target_value;
                }
            }
        }
        /* To Get the prod Updated Dates */
        $updatedDates=UpdateSkuQuantity::where('order_id',$order->id)
        ->groupBy('type_of_production')
        ->groupBy('sku_date')
        ->get();
        $cutDays = $packDays = $sewDays = 0;
        foreach($updatedDates as $dates){
            if($dates->type_of_production === "Cut"){
                $cutDays+=1;
            }
            if($dates->type_of_production === "Sew"){
                $sewDays+=1;
            }
            if($dates->type_of_production === "Pack"){
                $packDays+=1;
            }
        }
        if($type == "orderStatus"){
            $cutCount = OrderProduction::where($whereCondition)
            ->where('type_of_production',"cut")
            ->where("date_of_production",">=",date('Y-m-d'))->count();
            $sewCount = OrderProduction::where($whereCondition)
            ->where('type_of_production',"sew")
            ->where("date_of_production",">=",date('Y-m-d'))->count();
            $packCount = OrderProduction::where($whereCondition)
            ->where('type_of_production',"pack")
            ->where("date_of_production",">=",date('Y-m-d'))->count();
        }
        $total = $order->total_quantity;
        // dd($total,$cutTargetValue,$sewTargetValue,$packTargetValue,$cutUpdatedValue,$sewUpdatedValue,$packUpdatedValue);
        $cutPercentage = $sewPercentage = $packPercentage =0;
        $cutPercentage = ($cutUpdatedValue / $total)*100;
        $sewPercentage = ($sewUpdatedValue / $total)*100;
        $packPercentage = ($packUpdatedValue / $total)*100;
        $cutTargetPercentage = $sewTargetPercentage = $packTargetPercentage =0;
        $cutTargetPercentage = (($cutUpdatedValue-$cutTargetValue)/$total)*100;
        $sewTargetPercentage = (($sewUpdatedValue-$sewTargetValue)/$total)*100;
        $packTargetPercentage = (($packUpdatedValue-$packTargetValue)/$total)*100;
        $cutPendingPercentage = 100 - $cutPercentage;
        $sewPendingPercentage = 100 - $sewPercentage;
        $packPendingPercentage = 100 - $packPercentage;
        $cutStatus = $sewStatus = $packStatus = "";
        $cutPerDay = round(($total - $cutUpdatedValue)/($cutCount>0 ? $cutCount : 1));
        $sewPerDay = round(($total - $sewUpdatedValue)/($sewCount>0 ? $sewCount : 1));
        $packPerDay = round(($total - $packUpdatedValue)/($packCount>0 ? $packCount : 1));
        if(($total - $cutUpdatedValue)>0 && $order->cutting_accomplished_date == null){
            $cutAvgPerDay = round($cutUpdatedValue/($cutDays>0 ? $cutDays :1));
            $cutEstDate = date('Y-m-d',strtotime("+".round(($total-$cutUpdatedValue)/($cutAvgPerDay > 0 ? $cutAvgPerDay : 1))."days"));
        }else{
            $cutAvgPerDay = 0;
            $cutEstDate = "";
            $cutActualDate = $order->cutting_accomplished_date;
        }
        if(($total - $sewUpdatedValue)>0 && $order->sewing_accomplished_date == null){
            $sewAvgPerDay = round($sewUpdatedValue/($sewDays>0 ? $sewDays :1));
            $sewEstDate = date('Y-m-d',strtotime("+".round(($total-$sewUpdatedValue)/($sewAvgPerDay > 0 ? $sewAvgPerDay : 1))."days"));
        }else{
            $sewAvgPerDay = 0;
            $sewEstDate = "";
            $sewActualDate = $order->sewing_accomplished_date;
        }
        if(($total - $packUpdatedValue)>0 && $order->packing_accomplished_date == null){
            $packAvgPerDay = round($packUpdatedValue/($packDays>0 ? $packDays :1));
            $packEstDate = date('Y-m-d',strtotime("+".round(($total-$packUpdatedValue)/($packAvgPerDay > 0 ? $packAvgPerDay : 1))."days"));
        }else{
            $packAvgPerDay = 0;
            $packEstDate = "";
            $packActualDate = $order->packing_accomplished_date;
        }
        /* Cut Status */
        if($cutPercentage === 100){
            if($order->cutting_end_date >= $order->cutting_accomplished_date )
                $cutStatus = "Completed";
            else if ($order->cutting_end_date < $order->cutting_accomplished_date )
                $cutStatus = "Delayed Completion";
        }
        else if($cutPercentage != 100 && date('Y-m-d') > $order->cutting_end_date)
            $cutStatus = "Delayed";
        else if($cutPercentage != 100 && date('Y-m-d') <= $order->cutting_end_date && date('Y-m-d') >= $order->cutting_start_date)
            $cutStatus = "In Progress";
        else if($cutPercentage != 100 && date('Y-m-d') < $order->cutting_start_date)
            $cutStatus = "Yet To Start";
        /* Sew */
        if($sewPercentage === 100){
            if($order->sewing_end_date >= $order->sewing_accomplished_date )
                $sewStatus = "Completed";
            else if ($order->sewing_end_date < $order->sewing_accomplished_date )
                $sewStatus = "Delayed Completion";
        }
        else if($sewPercentage != 100 && date('Y-m-d')>$order->sewing_end_date)
            $sewStatus = "Delayed";
        else if($sewPercentage != 100 && date('Y-m-d') <= $order->sewing_end_date && date('Y-m-d') >= $order->sewing_start_date)
            $sewStatus = "In Progress";
        else if($sewPercentage != 100 && date('Y-m-d') < $order->sewing_start_date)
            $sewStatus = "Yet To Start";
        /* Pack */
        if($packPercentage === 100){
            if($order->packing_end_date >= $order->packing_accomplished_date )
                $packStatus = "Completed";
            else if ($order->packing_end_date < $order->packing_accomplished_date )
                $packStatus = "Delayed Completion";
        }
        else if($packPercentage != 100 && date('Y-m-d')>$order->packing_end_date)
            $packStatus = "Delayed";
        else if($packPercentage != 100 && date('Y-m-d') <= $order->packing_end_date && date('Y-m-d') >= $order->packing_start_date)
            $packStatus = "In Progress";
        else if($packPercentage != 100 && date('Y-m-d') < $order->packing_start_date)
            $packStatus = "Yet To Start";
        $arr=[];
        $arr['cutPercentage'] = $cutPercentage;
        $arr['sewPercentage'] = $sewPercentage;
        $arr['packPercentage'] = $packPercentage;
        $arr['cutPendingPercentage']= $cutPendingPercentage;
        $arr['sewPendingPercentage'] = $sewPendingPercentage;
        $arr['packPendingPercentage'] = $packPendingPercentage;
        $arr['cutTargetPercentage'] = $cutTargetPercentage;
        $arr['sewTargetPercentage'] = $sewTargetPercentage;
        $arr['packTargetPercentage'] = $packTargetPercentage;
        $arr['cutPerDay'] = $cutPerDay;
        $arr['sewPerDay'] = $sewPerDay;
        $arr['packPerDay'] = $packPerDay;
        $arr['cutActualTargetValue'] = $cutActualTargetValue;
        $arr['sewActualTargetValue'] = $sewActualTargetValue;
        $arr['packActualTargetValue'] = $packActualTargetValue;
        $arr['cutCompleted'] = $cutUpdatedValue;
        $arr['sewCompleted'] = $sewUpdatedValue;
        $arr['packCompleted'] = $packUpdatedValue;
        $arr['cutTodayUpdatedValue'] = $cutTodayUpdatedValue;
        $arr['sewTodayUpdatedValue'] = $sewTodayUpdatedValue;
        $arr['packTodayUpdatedValue'] = $packTodayUpdatedValue;
        $arr['cutStatus'] = $cutStatus;
        $arr['sewStatus'] = $sewStatus;
        $arr['packStatus'] = $packStatus;
        $arr['cutHoliday'] = $cutIsHoliday;
        $arr['sewHoliday'] = $sewIsHoliday;
        $arr['packHoliday'] = $packIsHoliday;
        $arr['cutAvgPerDay'] = $cutAvgPerDay;
        $arr['sewAvgPerDay'] = $sewAvgPerDay;
        $arr['packAvgPerDay'] = $packAvgPerDay;
        $arr['cutEstDate'] = $cutEstDate;
        $arr['sewEstDate'] = $sewEstDate;
        $arr['packEstDate'] = $packEstDate;
        $arr['cutActualDate'] = $cutActualDate;
        $arr['sewActualDate'] = $sewActualDate;
        $arr['packActualDate'] = $packActualDate;
        // dd($cutPercentage,$sewPercentage,$packPercentage);
        return $arr;
    }

    /* Top 5 Production Delay */
    public static function top5ProdDelay($request){
        $whereConditions=[
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id],
            ['order_production_data.is_accomplished',"=",0],
            ['orders.status','=',"1"],
            ['orders.step_level','=',"6"],
        ];
        $whereCondition1= $whereCondition2 = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id]
        ];
        if(isset($request->staff_id) && $request->staff_id > 0){
            $staffRoleHasPermission = Staff::where('id',$request->staff_id)->select('role_id','company_id')->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
            $whereCondition1[]=['permission_id','=','19'];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $theOrders = [];
                foreach($involedOrders as $order) {
                    $theOrders[]=$order->order_id;
                }
                $orders = Order::where($whereConditions)->whereIn("orders.id",$theOrders)
                ->leftjoin('order_production_data','order_production_data.order_id','orders.id')
                ->select(DB::raw('DATEDIFF(MAX(order_production_data.date_of_production),NOW()) as delay'),
                    'order_production_data.type_of_production','orders.order_no','orders.style_no','orders.cutting_start_date'
                    ,'orders.cutting_end_date','orders.sewing_start_date','orders.sewing_end_date','orders.packing_start_date',
                    'orders.packing_end_date','orders.id')
                ->having('delay','<',0)
                ->orderBy('delay','asc')
                ->orderBy('date_of_production','desc')
                ->groupBy('type_of_production')
                ->groupBy('style_no')
                ->orderByRaw('FIELD(type_of_production, "Cut","Sew","Pack")ASC')
                ->orderByRaw('FIELD(orders.order_priority, "Low","Medium","High")DESC')
                ->limit(5)
                ->get();
            }else{
                $orders = Order::where($whereConditions)
                ->leftjoin('order_production_data','order_production_data.order_id','orders.id')
                ->select(DB::raw('DATEDIFF(MAX(order_production_data.date_of_production),NOW()) as delay'),
                    'order_production_data.type_of_production','orders.order_no','orders.style_no','orders.cutting_start_date'
                    ,'orders.cutting_end_date','orders.sewing_start_date','orders.sewing_end_date','orders.packing_start_date',
                    'orders.packing_end_date','orders.id')
                ->having('delay','<',0)
                ->orderBy('delay','asc')
                ->orderBy('date_of_production','desc')
                ->groupBy('type_of_production')
                ->groupBy('style_no')
                ->orderByRaw('FIELD(type_of_production, "Cut","Sew","Pack")ASC')
                ->orderByRaw('FIELD(orders.order_priority, "Low","Medium","High")DESC')
                ->limit(5)
                ->get();
            }
        }else{
            $orders = Order::where($whereConditions)
            ->leftjoin('order_production_data','order_production_data.order_id','orders.id')
            ->select(DB::raw('DATEDIFF(MAX(order_production_data.date_of_production),NOW()) as delay'),
                'order_production_data.type_of_production','orders.order_no','orders.style_no','orders.cutting_start_date'
                ,'orders.cutting_end_date','orders.sewing_start_date','orders.sewing_end_date','orders.packing_start_date',
                'orders.packing_end_date','orders.id')
            ->having('delay','<',0)
            ->orderBy('delay','asc')
            ->orderBy('date_of_production','desc')
            ->groupBy('type_of_production')
            ->groupBy('style_no')
            ->orderByRaw('FIELD(type_of_production, "Cut","Sew","Pack")ASC')
            ->orderByRaw('FIELD(orders.order_priority, "Low","Medium","High")DESC')
            ->limit(5)
            ->get();
        }
        $prodArr=$orderArr=$styleArr=$cutArr=$sewArr=$packArr=$ordidArr=[];
        foreach ($orders as $order){
            $orderArr[]=$order->order_no;
            $styleArr[]=$order->style_no;
            $ordidArr[]=$order->id;
            if($order->type_of_production == "Cut"){
                $cutArr[]=abs($order->delay);
                $sewArr[]=null;
                $packArr[]=null;
            }
            else if($order->type_of_production == "Sew"){
                $cutArr[]=null;
                $sewArr[]=abs($order->delay);
                $packArr[]=null;
            }
            else if($order->type_of_production == "Pack"){
                $cutArr[]=null;
                $sewArr[]=null;
                $packArr[]=abs($order->delay);
            }
        }
        $prodArr['order_no']=$orderArr;
        $prodArr['style_no']=$styleArr;
        $prodArr['cut_delay']=$cutArr;
        $prodArr['sew_delay']=$sewArr;
        $prodArr['pack_delay']=$packArr;
        $prodArr['order_id']=$ordidArr;

        $finalArr['prodArr']=$prodArr;
        $finalArr['forTableView']=$orders;
        // return $orders;
        return $finalArr;
    }

    /* Top 5 Task Delay */
    public static function top5TaskDelay($request){
        $whereConditions=[
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id],
            ['orders.step_level','=',6],
            ['orders.status','=',"1"],
            ['order_task_data.task_accomplished_date','=',null],
            ['order_task_data.task_schedule_end_date','!=',null],
            ['order_task_data.is_subtask','=',0],
        ];
        $whereCondition1= $whereCondition2 = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id]
        ];
        if(isset($request->staff_id) && $request->staff_id > 0){
            $staffRoleHasPermission = Staff::where('id',$request->staff_id)->select('role_id','company_id')->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
            $whereCondition1[]=['permission_id','=','19'];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $theOrders = [];
                foreach($involedOrders as $order) {
                    $theOrders[]=$order->order_id;
                }
                $delayedTasks=Order::where($whereConditions)->whereIn("orders.id",$theOrders)
                ->leftjoin('order_task_data','order_task_data.order_id','orders.id')
                ->leftjoin('staff','staff.id','order_task_data.task_pic')
                ->select('orders.id as order_id','orders.order_no','orders.style_no','order_task_data.task_title','order_task_data.task_schedule_end_date'
                ,'order_task_data.task_accomplished_date','order_task_data.task_schedule_start_date','order_task_data.task_pic',
                DB::raw('DATEDIFF(order_task_data.task_schedule_end_date, NOW()) as noOfDays'),DB::raw('CONCAT(first_name," ",last_name) as staffName'))
                ->having('noOfDays', "<" ,0)
                ->orderBy('noOfDays','asc')
                ->orderByRaw('FIELD(orders.order_priority, "Low","Medium","High")DESC')
                ->limit(5)
                ->get();
            }else{
                $delayedTasks=Order::where($whereConditions)
                ->leftjoin('order_task_data','order_task_data.order_id','orders.id')
                ->leftjoin('staff','staff.id','order_task_data.task_pic')
                ->select('orders.id as order_id','orders.order_no','orders.style_no','order_task_data.task_title','order_task_data.task_schedule_end_date'
                ,'order_task_data.task_accomplished_date','order_task_data.task_schedule_start_date','order_task_data.task_pic',
                DB::raw('DATEDIFF(order_task_data.task_schedule_end_date, NOW()) as noOfDays'),DB::raw('CONCAT(first_name," ",last_name) as staffName'))
                ->having('noOfDays', "<" ,0)
                ->orderBy('noOfDays','asc')
                ->orderByRaw('FIELD(orders.order_priority, "Low","Medium","High")DESC')
                ->limit(5)
                ->get();
            }
        }else{
            $delayedTasks=Order::where($whereConditions)
            ->leftjoin('order_task_data','order_task_data.order_id','orders.id')
            ->leftjoin('staff','staff.id','order_task_data.task_pic')
            ->select('orders.id as order_id','orders.order_no','orders.style_no','order_task_data.task_title','order_task_data.task_schedule_end_date'
            ,'order_task_data.task_accomplished_date','order_task_data.task_schedule_start_date','order_task_data.task_pic',
            DB::raw('DATEDIFF(order_task_data.task_schedule_end_date, NOW()) as noOfDays'),DB::raw('CONCAT(first_name," ",last_name) as staffName'))
            ->having('noOfDays', "<" ,0)
            ->orderBy('noOfDays','asc')
            ->orderByRaw('FIELD(orders.order_priority, "Low","Medium","High")DESC')
            ->limit(5)
            ->get();
        }

        return $delayedTasks;
    }

    /* Get Production Status  */
    public static function getProductionStatus($request){
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['widget_id','=',1]
        ];
        if($request->staff_id == "0"){
            $dashboardArr['user_id'] = $request->user_id;
            $whereConditions[] = ['user_id','=',$request->user_id];
        }
        else if($request->staff_id > "0"){
            $dashboardArr['staff_id'] = $request->staff_id;
            $whereConditions[] = ['staff_id','=',$request->staff_id];
        }
        $productionOrders = DashboardSettings::where($whereConditions)->first();
        $prodArr=$styleArr=$orderArr=$cutArr=$sewArr=$packArr=$forTableView=$orderIdArr=$quantityArr=$statusArr=[];
        // $cutArr[]=0;
        // $sewArr[]=0;
        // $packArr[]=0;
        // $orderIdArr[]=0;
        if(!empty($productionOrders)){
            $orders = explode(",",$productionOrders->order_ids);
            foreach($orders as $order){
                $orderData = Order::where('id',$order)->where("status","1")->first();
                if(!empty($orderData)){
                    $arr=[];
                    $prodData = OrderProduction::where('order_id',$order)->where('date_of_production','<=',date('Y-m-d'))->get();
                    $dataInputData = UpdateSkuQuantity::where('order_id',$order)->get();
                    $counts = DashboardSettings::prodCounts($prodData,$dataInputData,$orderData,'');
                    $arr['orderNo']= $orderData->order_no;
                    $arr['styleNo']= $orderData->style_no;
                    $arr['total'] = $orderData->total_quantity;
                    $arr['cutPercentage']=round($counts['cutPercentage'],2);
                    $arr['sewPercentage']=round($counts['sewPercentage'],2);
                    $arr['packPercentage']=round($counts['packPercentage'],2);
                    $arr['cutPendingPercentage']=$counts['cutPendingPercentage'];
                    $arr['sewPendingPercentage']=$counts['sewPendingPercentage'];
                    $arr['packPendingPercentage']=$counts['packPendingPercentage'];
                    $arr['cutTargets']=$counts['cutTargetPercentage'];
                    $arr['sewTargets']=$counts['sewTargetPercentage'];
                    $arr['packTargets']=$counts['packTargetPercentage'];
                    $arr['cutCompleted'] = $counts['cutCompleted'];
                    $arr['sewCompleted'] = $counts['sewCompleted'];
                    $arr['packCompleted'] = $counts['packCompleted'];
                    $arr['cutStatus'] = $counts['cutStatus'];
                    $arr['sewStatus'] = $counts['sewStatus'];
                    $arr['packStatus'] = $counts['packStatus'];
                    $arr['cutting_start_date'] = $orderData->cutting_start_date;
                    $arr['cutting_end_date'] = $orderData->cutting_end_date;
                    $arr['sewing_start_date'] = $orderData->sewing_start_date;
                    $arr['sewing_end_date'] = $orderData->sewing_end_date;
                    $arr['packing_start_date'] = $orderData->packing_start_date;
                    $arr['packing_end_date'] = $orderData->packing_end_date;
                    $forTableView[]=$arr;
                    /* For Chart View */
                    $orderArr[]= $orderData->order_no;
                    $styleArr[]= $orderData->style_no;
                    $orderIdArr[]= $orderData->id;
                    $quantityArr[]=$orderData->total_quantity;
                    $statusArr[] = $orderData->packing_accomplished_date != NULL ? 1 : (($orderData->packing_end_date > date('Y-m-d')) ? 2 : 3);
                    $cutArr[]= round($counts['cutPercentage']);
                    $sewArr[]= round($counts['sewPercentage']);
                    $packArr[]= round($counts['packPercentage']);
                    // dd($prodData);
                }
            }
        }else{
            $whereCondition1= $whereCondition2 = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $whereCondition3= [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['step_level','=',6],
                ['status','=',"1"]
            ];
            if($request->staff_id > 0){
                $staffRoleHasPermission = Staff::where('id',$request->staff_id)->select('role_id','company_id')->first();
                $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
                $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
                $whereCondition1[]=['permission_id','=','19'];
                $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
                if(empty($isPermissionGiven)){
                    $whereCondition2[]=['staff_id','=',$request->staff_id];
                    $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                    $theOrders = [];
                    $i=1;
                    foreach($involedOrders as $order) {
                        $theOrder = Order::where("id", $order->order_id)->where("step_level","6")
                        ->where('status',"1")->first();
                        if(!empty($theOrder)){
                            if($i<=5) {
                                $theOrders[]=$theOrder;
                            }
                            $i++;
                        }
                    }
                    $orders=$theOrders;
                }else{
                    $orders = Order::where($whereCondition3)->orderByRaw('FIELD(order_priority, "Low","Medium","High")DESC')
                    ->limit(5)->get();
                }
            }else{
                $orders = Order::where($whereCondition3)->orderByRaw('FIELD(order_priority, "Low","Medium","High")DESC')
                    ->limit(5)->get();
            }
            foreach($orders as $order){
                $arr=[];
                $prodData = OrderProduction::where('order_id',$order->id)->where('date_of_production','<=',date('Y-m-d'))->get();
                $dataInputData = UpdateSkuQuantity::where('order_id',$order->id)->get();
                $counts = DashboardSettings::prodCounts($prodData,$dataInputData,$order,'');
                $arr['orderNo']= $order->order_no;
                $arr['styleNo']= $order->style_no;
                $arr['total'] = $order->total_quantity;
                $arr['cutPercentage']=round($counts['cutPercentage'],2);
                $arr['sewPercentage']=round($counts['sewPercentage'],2);
                $arr['packPercentage']=round($counts['packPercentage'],2);
                $arr['cutPendingPercentage']=$counts['cutPendingPercentage'];
                $arr['sewPendingPercentage']=$counts['sewPendingPercentage'];
                $arr['packPendingPercentage']=$counts['packPendingPercentage'];
                $arr['cutTargets']=$counts['cutTargetPercentage'];
                $arr['sewTargets']=$counts['sewTargetPercentage'];
                $arr['packTargets']=$counts['packTargetPercentage'];
                $arr['cutCompleted'] = $counts['cutCompleted'];
                $arr['sewCompleted'] = $counts['sewCompleted'];
                $arr['packCompleted'] = $counts['packCompleted'];
                $arr['cutStatus'] = $counts['cutStatus'];
                $arr['sewStatus'] = $counts['sewStatus'];
                $arr['packStatus'] = $counts['packStatus'];
                $arr['cutting_start_date'] = $order->cutting_start_date;
                $arr['cutting_end_date'] = $order->cutting_end_date;
                $arr['sewing_start_date'] = $order->sewing_start_date;
                $arr['sewing_end_date'] = $order->sewing_end_date;
                $arr['packing_start_date'] = $order->packing_start_date;
                $arr['packing_end_date'] = $order->packing_end_date;
                $forTableView[]=$arr;
                /* For Chart View */
                $orderArr[]= $order->order_no;
                $styleArr[]= $order->style_no;
                $orderIdArr[]= $order->id;
                $quantityArr[]=$order->total_quantity;
                $statusArr[] = $order->packing_accomplished_date != NULL ? 1 : (($order->packing_end_date > date('Y-m-d')) ? 2 : 3);
                $cutArr[]= round($counts['cutPercentage']);
                $sewArr[]= round($counts['sewPercentage']);
                $packArr[]= round($counts['packPercentage']);
            }
        }
        $prodArr['orderNo'] = $orderArr;
        $prodArr['styleNo'] = $styleArr;
        $prodArr['orderId'] = $orderIdArr;
        $prodArr['cut'] = $cutArr;
        $prodArr['sew'] = $sewArr;
        $prodArr['pack'] = $packArr;
        $prodArr['quantity'] = $quantityArr;
        $prodArr['status'] = $statusArr;
        $finalArr['prodArr'] = $prodArr;
        $finalArr['forTableView'] = $forTableView;
        return $finalArr;
    }

    /* Top 5 Production Delay */
    public static function top5ProdDelayMob($request){
        $whereConditions=[
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id],
            ['order_production_data.is_accomplished',"=",0],
            ['orders.status','=',"1"],
            ['orders.step_level','=',"6"],
        ];
        $whereCondition1= $whereCondition2 = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id]
        ];
        if(isset($request->staff_id) && $request->staff_id > 0){
            $staffRoleHasPermission = Staff::where('id',$request->staff_id)->select('role_id','company_id')->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
            $whereCondition1[]=['permission_id','=','19'];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $theOrders = [];
                foreach($involedOrders as $order) {
                    $theOrders[]=$order->order_id;
                }
                $orders = Order::where($whereConditions)->whereIn("orders.id",$theOrders)
                ->leftjoin('order_production_data','order_production_data.order_id','orders.id')
                ->select(DB::raw('DATEDIFF(MAX(order_production_data.date_of_production),NOW()) as delay'),
                    'order_production_data.type_of_production','orders.order_no','orders.style_no','orders.cutting_start_date'
                    ,'orders.cutting_end_date','orders.sewing_start_date','orders.sewing_end_date','orders.packing_start_date',
                    'orders.packing_end_date')
                ->having('delay','<',0)
                ->orderBy('delay','asc')
                ->orderBy('date_of_production','desc')
                ->groupBy('type_of_production')
                ->groupBy('style_no')
                ->orderByRaw('FIELD(orders.order_priority, "Low","Medium","High")DESC')
                ->limit(5)
                ->get();
            }else{
                $orders = Order::where($whereConditions)
                ->leftjoin('order_production_data','order_production_data.order_id','orders.id')
                ->select(DB::raw('DATEDIFF(MAX(order_production_data.date_of_production),NOW()) as delay'),
                    'order_production_data.type_of_production','orders.order_no','orders.style_no','orders.cutting_start_date'
                    ,'orders.cutting_end_date','orders.sewing_start_date','orders.sewing_end_date','orders.packing_start_date',
                    'orders.packing_end_date')
                ->having('delay','<',0)
                ->orderBy('delay','asc')
                ->orderBy('date_of_production','desc')
                ->groupBy('type_of_production')
                ->groupBy('style_no')
                ->orderByRaw('FIELD(orders.order_priority, "Low","Medium","High")DESC')
                ->limit(5)
                ->get();
            }
        }else{
            $orders = Order::where($whereConditions)
            ->leftjoin('order_production_data','order_production_data.order_id','orders.id')
            ->select(DB::raw('DATEDIFF(MAX(order_production_data.date_of_production),NOW()) as delay'),
                'order_production_data.type_of_production','orders.order_no','orders.style_no','orders.cutting_start_date'
                ,'orders.cutting_end_date','orders.sewing_start_date','orders.sewing_end_date','orders.packing_start_date',
                'orders.packing_end_date')
            ->having('delay','<',0)
            ->orderBy('delay','asc')
            ->orderBy('date_of_production','desc')
            ->groupBy('type_of_production')
            ->groupBy('style_no')
            ->orderByRaw('FIELD(orders.order_priority, "Low","Medium","High")DESC')
            ->limit(5)
            ->get();
        }

        return $orders;
    }

 /*======================================================Start New Dashboard For Staff v2 On 13-12-2023 by saravanan */
   /* Get staff Production Status max 2 records */
   public static function getStaffProductionStatus($request){
    $whereConditions=[
        ['company_id','=',$request->company_id],
        ['workspace_id','=',$request->workspace_id],
        ['widget_id','=',1]
    ];
    if($request->staff_id == "0"){
        $dashboardArr['user_id'] = $request->user_id;
        $whereConditions[] = ['user_id','=',$request->user_id];
    }
    else if($request->staff_id > "0"){
        $dashboardArr['staff_id'] = $request->staff_id;
        $whereConditions[] = ['staff_id','=',$request->staff_id];
    }
    $productionOrders = DashboardSettings::where($whereConditions)->first();
    $prodArr=$styleArr=$orderArr=$cutArr=$sewArr=$packArr=$forTableView=$orderIdArr=[];
    $styleArr=[];
    $cutArr=[];
    $sewArr=[];
    $packArr=[];
    $orderIdArr=[];
    if(!empty($productionOrders)){
        $orders = explode(",",$productionOrders->order_ids);
        $jk=1;

        foreach($orders as $order){
            if($jk<=2){
                $jk++;
            $orderData = Order::where('id',$order)->where("status","1")->first();
            if(!empty($orderData)){
                if($orderData->id>0){
                $arr=[];
                $prodData = OrderProduction::where('order_id',$order)->where('date_of_production','<=',date('Y-m-d'))->get();
                $dataInputData = UpdateSkuQuantity::where('order_id',$order)->get();
                $counts = DashboardSettings::prodCounts($prodData,$dataInputData,$orderData,'');
                $arr['orderNo']= $orderData->order_no;
                $arr['styleNo']= $orderData->style_no;
                $arr['total'] = $orderData->total_quantity;
                $arr['cutPercentage']=round($counts['cutPercentage'],2);
                $arr['sewPercentage']=round($counts['sewPercentage'],2);
                $arr['packPercentage']=round($counts['packPercentage'],2);
                $arr['cutPendingPercentage']=$counts['cutPendingPercentage'];
                $arr['sewPendingPercentage']=$counts['sewPendingPercentage'];
                $arr['packPendingPercentage']=$counts['packPendingPercentage'];
                $arr['cutTargets']=$counts['cutTargetPercentage'];
                $arr['sewTargets']=$counts['sewTargetPercentage'];
                $arr['packTargets']=$counts['packTargetPercentage'];
                $forTableView[]=$arr;
                /* For Chart View */
                $orderArr[]= $orderData->order_no;
                $styleArr[]= $orderData->style_no;
                $orderIdArr[]= $orderData->id;
                $cutArr[]= round($counts['cutPercentage']);
                $sewArr[]= round($counts['sewPercentage']);
                $packArr[]= round($counts['packPercentage']);
                // dd($prodData);
            }
        }
        }
        }

    }else{

        $whereCondition1= $whereCondition2 = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id]
        ];
        $whereCondition3= [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['step_level','=',6],
            ['status','=',"1"]
        ];
        if($request->staff_id > 0){
            $staffRoleHasPermission = Staff::where('id',$request->staff_id)->select('role_id','company_id')->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['company_id','=',$request->company_id];
            $whereCondition1[]=['permission_id','=','19'];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $theOrders = [];
                $i=1;
                foreach($involedOrders as $order) {
                    $theOrder = Order::where("id", $order->order_id)->where("step_level","6")
                    ->where('status',"1")->first();
                    if(!empty($theOrder)){
                        if($i<=2) {
                            $theOrders[]=$theOrder;
                        }
                        $i++;
                    }
                }
                $orders=$theOrders;
            }else{
                $orders = Order::where($whereCondition3)->orderByRaw('FIELD(order_priority, "Low","Medium","High")DESC')
                ->limit(2)->get();
            }
        }else{
            $orders = Order::where($whereCondition3)->orderByRaw('FIELD(order_priority, "Low","Medium","High")DESC')
                ->limit(2)->get();
        }
        //dd(count($orders));
        foreach($orders as $order){
            if(!empty($order)){
                if($order->id>0){
            $arr=[];
            $prodData = OrderProduction::where('order_id',$order->id)->where('date_of_production','<=',date('Y-m-d'))->get();
            $dataInputData = UpdateSkuQuantity::where('order_id',$order->id)->get();
            $counts = DashboardSettings::prodCounts($prodData,$dataInputData,$order,'');
            //dd($counts);
            $arr['orderNo']= $order->order_no;
            $arr['styleNo']= $order->style_no;
            $arr['total'] = $order->total_quantity;
            $arr['cutPercentage']=round($counts['cutPercentage'],2);
            $arr['sewPercentage']=round($counts['sewPercentage'],2);
            $arr['packPercentage']=round($counts['packPercentage'],2);
            $arr['cutPendingPercentage']=$counts['cutPendingPercentage'];
            $arr['sewPendingPercentage']=$counts['sewPendingPercentage'];
            $arr['packPendingPercentage']=$counts['packPendingPercentage'];
            $arr['cutTargets']=$counts['cutTargetPercentage'];
            $arr['sewTargets']=$counts['sewTargetPercentage'];
            $arr['packTargets']=$counts['packTargetPercentage'];
            $forTableView[]=$arr;
            /* For Chart View */
            $orderArr[]= $order->order_no;
            $styleArr[]= $order->style_no;
            $orderIdArr[]= $order->id;
            $cutArr[]= round($counts['cutPercentage']);
            $sewArr[]= round($counts['sewPercentage']);
            $packArr[]= round($counts['packPercentage']);
                }
            }
        }
    }
    $prodArr['orderNo'] = $orderArr;
    $prodArr['styleNo'] = $styleArr;
    $prodArr['orderId'] = $orderIdArr;
    $prodArr['cut'] = $cutArr;
    $prodArr['sew'] = $sewArr;
    $prodArr['pack'] = $packArr;
    $finalArr['prodArr'] = $prodArr;
    $finalArr['forTableView'] = $forTableView;
    return $finalArr;
}
 /*======================================================End New Dashboard For Staff v2 On 13-12-2023 by saravanan */

    public static function getRecentOrderDetails($condition){
        $orderArr = Order::where($condition)
        ->join('order_article_name','order_article_name.id','orders.article_id')
        ->leftjoin('order_category','order_category.id','orders.category_id')
        ->select('orders.id as order_id','orders.order_no','orders.style_no','orders.total_quantity','order_article_name.name as article',
        'order_category.name as category','orders.completed_on'
        )->orderBy('orders.id', 'DESC')->get();

        $orders = [];$i=0;
        if(!empty($orderArr)){
            foreach($orderArr as $order){
                $orders[$i]['order_no'] = $order->order_no;
                $orders[$i]['style_no'] = $order->style_no;
                $orders[$i]['total_quantity'] = $order->total_quantity;
                $orders[$i]['article'] = $order->article;
                $orders[$i]['category'] = $order->category;
                $orders[$i]['total_task'] = OrderTask::where('order_id',$order->order_id)->count();
                $del_date = DashboardSettings::getOrderNextDeliveryDate($order->order_id);
                $orders[$i]['delivery_date'] = $del_date;
                $orders[$i]['status'] = ($order->completed_on!=NULL)? 1 : ((strtotime($del_date) > strtotime(date('Y-m-d')))? 2 : 3 );
                $orders[$i]['delivery_dates'] = DashboardSettings::getOrderAllDeliveryDates($order->order_id);
                $i++;
            }
        }
        //dd($orders);
        return $orders;
    }

    public static function getOrderNextDeliveryDate($order_id){
        $date = MultipleDeliveryDates::where('order_id',$order_id)->where('is_delivered','0')->pluck('delivery_date')->first();
        if($date==NULL)
            $date = MultipleDeliveryDates::where('order_id',$order_id)->pluck('delivery_date')->first();

        return $date;
    }

    public static function getOrderAllDeliveryDates($order_id){
        $dates = MultipleDeliveryDates::where('order_id',$order_id)->select('delivery_date','is_delivered')->get();
        return $dates;
    }
    public static function getStaffInviteDetails($condition){
        $orderArr = Order::where($condition)
        ->join('workspace','workspace.id','orders.workspace_id')
        ->join('order_contacts','order_contacts.order_id','orders.id')
        ->join('staff','staff.id','order_contacts.staff_id')
        ->join('roles','roles.id','staff.role_id')
        ->select('orders.id as order_id','orders.order_no','orders.style_no','staff.first_name','staff.last_name','order_contacts.created_at as invitedDate',
        'workspace.name as workspace_name','roles.name as role'
        )->orderBy('orders.id', 'DESC')->get();
        return $orderArr;
    }

     /* Get Production Status  */
    public static function getMobileOrderTaskProductionStatus($request){
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['widget_id','=',1]
        ];
        if($request->staff_id == "0"){
            $dashboardArr['user_id'] = $request->user_id;
            $whereConditions[] = ['user_id','=',$request->user_id];
        }
        else if($request->staff_id > "0"){
            $dashboardArr['staff_id'] = $request->staff_id;
            $whereConditions[] = ['staff_id','=',$request->staff_id];
        }
        $prodArr=$styleArr=$orderArr=$cutArr=$sewArr=$packArr=$forTableView=$orderIdArr=$quantityArr=$statusArr=[];

        $whereCondition1= $whereCondition2 = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $whereCondition3= [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['step_level','=',6],
                ['status','=',"1"]
            ];
            if($request->staff_id > 0){
                $staffRoleHasPermission = Staff::where('id',$request->staff_id)->select('role_id','company_id')->first();
                $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
                $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
                $whereCondition1[]=['permission_id','=','19'];
                $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
                if(empty($isPermissionGiven)){
                    $whereCondition2[]=['staff_id','=',$request->staff_id];
                    $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                    $theOrders = [];
                    $i=1;
                    foreach($involedOrders as $order) {
                        $theOrder = Order::where("id", $order->order_id)->where("step_level","6")
                        ->where('status',"1")->first();
                        if(!empty($theOrder)){
                            if($i<=5) {
                                $theOrders[]=$theOrder;
                            }
                            $i++;
                        }
                    }
                    $orders=$theOrders;
                }else{
                    $orders = Order::where($whereCondition3)->orderByRaw('FIELD(order_priority, "Low","Medium","High")DESC')
                    ->limit(5)->get();
                }
            }else{
                $orders = Order::where($whereCondition3)->orderByRaw('FIELD(order_priority, "Low","Medium","High")DESC')
                    ->limit(5)->get();
            }
            foreach($orders as $order){
                $arr=[];
                $prodData = OrderProduction::where('order_id',$order->id)->where('date_of_production','<=',date('Y-m-d'))->get();
                $dataInputData = UpdateSkuQuantity::where('order_id',$order->id)->get();
                $counts = DashboardSettings::prodCounts($prodData,$dataInputData,$order,'');
                /*Task Data*/
                $total_tasks = OrderTask::where('order_id',$order->id)->count();
                $completed_tasks = OrderTask::where('order_id',$order->id)->where('task_accomplished_date','!=',NULL)->count();

                $arr['orderNo']= $order->order_no;
                $arr['styleNo']= $order->style_no;
                $arr['deliveryDate']= $order->delivery_date;
                $arr['completedDate']= $order->completed_on;
                $arr['totalTasks'] = $total_tasks;
                $arr['completedTasks'] = $completed_tasks;
                $arr['totalQty'] = $order->total_quantity;
                $arr['cutPercentage']=round($counts['cutPercentage'],2);
                $arr['sewPercentage']=round($counts['sewPercentage'],2);
                $arr['packPercentage']=round($counts['packPercentage'],2);
                //$arr['cutPendingPercentage']=$counts['cutPendingPercentage'];
                //$arr['sewPendingPercentage']=$counts['sewPendingPercentage'];
                //$arr['packPendingPercentage']=$counts['packPendingPercentage'];
                //$arr['cutTargets']=$counts['cutTargetPercentage'];
                //$arr['sewTargets']=$counts['sewTargetPercentage'];
                //$arr['packTargets']=$counts['packTargetPercentage'];
                $arr['cutCompleted'] = $counts['cutCompleted'];
                $arr['sewCompleted'] = $counts['sewCompleted'];
                $arr['packCompleted'] = $counts['packCompleted'];
                //$arr['cutStatus'] = $counts['cutStatus'];
                //$arr['sewStatus'] = $counts['sewStatus'];
                //$arr['packStatus'] = $counts['packStatus'];
                // $arr['cutting_start_date'] = $order->cutting_start_date;
                // $arr['cutting_end_date'] = $order->cutting_end_date;
                // $arr['sewing_start_date'] = $order->sewing_start_date;
                // $arr['sewing_end_date'] = $order->sewing_end_date;
                // $arr['packing_start_date'] = $order->packing_start_date;
                // $arr['packing_end_date'] = $order->packing_end_date;
                $forTableView[]=$arr;
                /* For Chart View */
                $orderArr[]= $order->order_no;
                $styleArr[]= $order->style_no;
                $orderIdArr[]= $order->id;
                $quantityArr[]=$order->total_quantity;
                $statusArr[] = $order->packing_accomplished_date != NULL ? 1 : (($order->packing_end_date > date('Y-m-d')) ? 2 : 3);
                $cutArr[]= round($counts['cutPercentage']);
                $sewArr[]= round($counts['sewPercentage']);
                $packArr[]= round($counts['packPercentage']);
            }

        $prodArr['orderNo'] = $orderArr;
        $prodArr['styleNo'] = $styleArr;
        $prodArr['orderId'] = $orderIdArr;
        $prodArr['cut'] = $cutArr;
        $prodArr['sew'] = $sewArr;
        $prodArr['pack'] = $packArr;
        $prodArr['quantity'] = $quantityArr;
        $prodArr['status'] = $statusArr;
        $finalArr['prodArr'] = $prodArr;
        $finalArr['forTableView'] = $forTableView;
        return $finalArr;
    }
}
