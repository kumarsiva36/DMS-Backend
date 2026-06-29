<?php

namespace App\Http\Controllers\Mobile\v1\Order;

use App\Common\CommonApp;
use App\Common\GetUserLanguage;
use App\Common\NotificationAddition;
use App\Common\NotificationText;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use App\Jobs\AccomplishedMailJob;
use App\Models\NotificationSettings;
use App\Models\Order;
use App\Models\OrderTask;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderContacts;
use App\Models\RolesAndPermissions;
use Exception;
use App\Models\MultipleDeliveryDates;
use App\Models\Orderlog;
use App\Models\OrderTemplate;
use App\Models\TaskPercentageUpdate;
use Illuminate\Validation\Rule;

class OrderTasks extends Controller
{
    /* To add new Tasks in the templates */
    public static function addTaskData(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'template_id' => 'required',
            'template_data'=>'required|array',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id]
        ];

        $aldreadyExists = OrderTask::where($whereConditions)->get();
        if(!empty($aldreadyExists)){
            OrderTask::where($whereConditions)->delete();
        }
        /******************** To add template id in ORDERS Table ******************/
        $addTemplateToOrder = Order::where('id',$request->order_id)->first();
        $addTemplateToOrder->order_task_template = $request->template_id;
        $addTemplateToOrder->save();

        $orderProductionArr = [];
        $orderProductionArr['user_id']= $companyDetails->user_id;
        $orderProductionArr['company_id']= $request->company_id;
        $orderProductionArr['workspace_id']= $request->workspace_id;
        $orderProductionArr['staff_id']= $request->input('staff_id','0');
        $orderProductionArr['order_id']= $request->order_id;
        $orderProductionArr['template_id']= $request->template_id;
        $orderProductionArr['created_by']= $companyDetails->user_id;
        $orderProductionArr['created_user_type']= "User";
        $orderProductionArr['task_accomplished_date']= $request->input('task_accomplished_date',NULL);
        $orderProductionArr['reschedule_reason']= $request->input('reschedule_reason','');
        $orderProductionArr['reschedule_order_task_data_id']= $request->input('reschedule_order_task_data_id','0');
        $orderProductionArr['rescheduled']= $request->input('rescheduled');
        $orderProductionArr['category_contacts']= $request->input('category_contacts','');
        $orderProductionArr['task_contacts']= $request->input('task_contacts','');
        // foreach($request->template_data as $templates){
            foreach($request->template_data as $key=>$template){
                $orderProductionArr['cat_title']= $key;
                foreach($template as $data){
                    $orderProductionArr['task_title']= $data['title'];
                    $orderProductionArr['task_schedule_start_date']= $data['start_date'] != "" ? date('Y-m-d',strtotime($data['start_date'])) : NULL;
                    $orderProductionArr['actual_start_date']= $data['start_date'] != "" ? date('Y-m-d',strtotime($data['start_date'])) : NULL;
                    $orderProductionArr['task_schedule_end_date']= $data['end_date'] != "" ? date('Y-m-d',strtotime($data['end_date'])) : NULL;
                    $orderProductionArr['task_pic']= $data['pic_id'] != "" ? $data['pic_id'] : "0";
                    $orderProductionArr['created_at']=date('Y-m-d H:i:s');
                    $orderProductionArr['updated_at']=date('Y-m-d H:i:s');
                    OrderTask::insert($orderProductionArr);
                }
            }
        // }
        return response()->json(["status_code"=>200,'status'=>"success","message"=>"Task Data Added Successfully"],200);
    }
    /* Get the task Details */
    public static function getTaskDetails(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id],
            ['is_subtask','=',0]
        ];
        $whereConditions1 =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id],
            ['is_subtask','=',1]
        ];

        $cat_titles = OrderTask::where($whereConditions)->groupby('cat_title')->orderby('id','asc')->pluck('cat_title')->toArray();
        if(!empty($cat_titles)){
            $taskDetails = OrderTask::where($whereConditions)->orderByRaw('FIELD(cat_title,"'.implode('","',$cat_titles).'" )ASC')->get();
        }else{
            $taskDetails = OrderTask::where($whereConditions)->orderBy('cat_title','asc')->OrderBy('id','asc')->get();
        }

       // $taskDetails = OrderTask::where($whereConditions)->get();
        $taskTemplateId = OrderTask::where($whereConditions)->first();
        $subTaskDetails = OrderTask::where($whereConditions1)->get();
		$arr=array();$i=$j=$k=$l=0; $catTitle=''; $subArr = array();

        if(!empty($taskDetails)){
            foreach($taskDetails as $tasks){
                if($i == 0 ){
                    $catTitle = $tasks->cat_title;
                }
                if( $tasks->cat_title != $catTitle ){
                    $catTitle = $tasks->cat_title;
                    $k++; $j=0;
                    $subArr = array();
                }

                if($tasks->cat_title == $catTitle ){
                    $subArr[$j]["id"] = $tasks->id;
                    $subArr[$j]["title"] = $tasks->cat_title;
                    $subArr[$j]["subtitle"] = $tasks->task_title;
                    $subArr[$j]["start_date"] = $tasks->task_schedule_start_date;
                    $subArr[$j]["actual_start_date"] = $tasks->actual_start_date;
                    $subArr[$j]["end_date"] = $tasks->task_schedule_end_date;
                    $subArr[$j]["accomplished_date"] = $tasks->task_accomplished_date;
                    $subArr[$j]["pic_id"] = $tasks->task_pic;
                    $subArr[$j]["inprogress_percentage"] = $tasks->inprogress_percentage;
                    $subArr[$j]['subtasks']=[];
                    foreach($subTaskDetails as $subtask){
                        if($subtask->parent_task_id === $tasks->id){
                            $subtaskArr=[];
                            $subtaskArr["id"] = $subtask->id;
                            $subtaskArr["title"] = $subtask->cat_title;
                            $subtaskArr["subtitle"] = $subtask->task_title;
                            $subtaskArr["subtasktitle"] = $subtask->subtask_title;
                            $subtaskArr["start_date"] = $subtask->task_schedule_start_date;
                            $subtaskArr["actual_start_date"] = $subtask->actual_start_date;
                            $subtaskArr["end_date"] = $subtask->task_schedule_end_date;
                            $subtaskArr["accomplished_date"] = $subtask->task_accomplished_date;
                            $subtaskArr["pic_id"] = $subtask->task_pic;
                            $subtaskArr["inprogress_percentage"] = $subtask->inprogress_percentage;
                            $subArr[$j]['subtasks'][]=$subtaskArr;
                        }
                    }
                    $j++;
                }
                if( !empty($subArr) ){
                    $arr[$k]["task_title"] = $tasks->cat_title;
                    $arr[$k]["task_subtitles"] = $subArr;
                }
                $i++;
            }
            /* Order Task Count */
            $order = Order::where('id',$request->order_id)->first();
            $whereCondition = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['order_id','=',$request->order_id],
                ['template_id','=',$order->order_task_template]
            ];
            $templateData = OrderTask::where($whereCondition)->get();
            $taskDataArr = array();
            $totalTask = count($templateData);
            $scheduledTasks = count(OrderTask::where($whereCondition)->where('task_schedule_start_date','!=',NULL)
            ->where('task_schedule_end_date','!=',NULL)->get());
            $accomplishedTasks = count(OrderTask::where($whereCondition)->where('task_accomplished_date','!=',NULL)->get());
            // $yetToStart = count(OrderTask::where($whereCondition)->where('task_schedule_start_date','=',NULL)
            // ->where('task_schedule_end_date','=',NULL)->where('task_accomplished_date','=',NULL)->get());
            $yetToStart =count(OrderTask::where($whereCondition)->where('task_schedule_start_date','>',date('Y-m-d'))->get());
            $pending=($totalTask-$accomplishedTasks);
            $pendingTask=$pending>0?$pending:0;
            $taskDataArr['totalTask'] = $totalTask;
            $taskDataArr['scheduledTasks'] = $scheduledTasks;
            $taskDataArr['accomplishedTasks'] = $accomplishedTasks;
            $taskDataArr['yetToStart']=$yetToStart;
            $taskDataArr['pending']=$pendingTask;

            /*Get Order Task PIC */
            $getOrderPIC=OrderTasks::getOrderPIC($request->company_id,$request->workspace_id,$request->order_id);
            //Delivery Dates
            $whereConditionDel = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['order_id','=',$request->order_id]
                    ];
            //$getDeliveryDates=MultipleDeliveryDates::select("delivery_date")->where($whereConditionDel)->orderby("delivery_date","ASC")->get();
            $delivery_date = MultipleDeliveryDates::where($whereConditionDel)->where('is_delivered','=','0')
                            ->orderBy('delivery_date',"ASC")->pluck('delivery_date')->first();
            $delivery_date_exceed=0;
            if($delivery_date!="" && $delivery_date!=null){
                if($delivery_date < date('Y-m-d')){
                    $delivery_date_exceed=1;
                }
            }

            $res = json_encode(["status_code"=>200,"status" =>"Success","templateID"=>$taskTemplateId->template_id,
            "orderNo"=>$order->order_no,"styleNo"=>$order->style_no,"data"=>$arr,"orderTaskCount"=>$taskDataArr,"pic"=>$getOrderPIC,
            "order_created_date"=>date("Y-m-d",strtotime($order->created_at)),'order_inquiry_date'=>$order->inquiry_date,
            "delivery_date"=>$delivery_date,"delivery_date_exceed"=>$delivery_date_exceed,'inprogress_per_show'=>config('constant.task_inprogress_percentage')]);
            return CommonApp::apiEncrypt($res);
        }else{
            //Delivery Dates
            $whereConditionDel = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['order_id','=',$request->order_id]
                    ];
            //$getDeliveryDates=MultipleDeliveryDates::select("delivery_date")->where($whereConditionDel)->orderby("delivery_date","ASC")->get();
            $delivery_date = MultipleDeliveryDates::where($whereConditionDel)->where('is_delivered','=','0')
                            ->orderBy('delivery_date',"ASC")->pluck('delivery_date')->first();
            $delivery_date_exceed=0;
            if($delivery_date!="" && $delivery_date!=null){
                if($delivery_date < date('Y-m-d')){
                    $delivery_date_exceed=1;
                }
            }
            $res = json_encode(["status_code"=>201,"status" =>"Error","templateID"=>$taskTemplateId->template_id,"message"=>"Data Not Found","data"=>[],
            "delivery_date"=>$delivery_date,"delivery_date_exceed"=>$delivery_date_exceed,'inprogress_per_show'=>config('constant.task_inprogress_percentage')]);
            return CommonApp::apiEncrypt($res);
        }
    }

    /* Accomplished Task Date */
    public static function accomplishedTask(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'taskId' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'accomplishedDate' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to Update Task
            $per = CommonApp::checkStaffPermission($request,'25');
            if($per===0){
                return CommonApp::checkStaffPermissionResponseMobile();
            }
        }
        $whereConditions=[
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=', $request->company_id],
            ['id','=',$request->taskId],
            ['task_schedule_start_date',"!=",NULL],
            ['task_schedule_end_date',"!=",NULL],
            ['task_pic',"!=",0],
        ];
        $whereConditions1=[
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=', $request->company_id],
            ['id','=',$request->taskId],
            ['task_schedule_start_date',"!=",NULL],
            ['task_schedule_end_date',"!=",NULL]
        ];
        $whereCondition=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
        ];
        $staffHasPermission = 1; /* Assume Staff has view all Order Permission */

        if(isset($request->staff_id) && $request->staff_id>0){
            $whereCondition[]=['staff_id','=',$request->staff_id];
            $user = Staff::where('id',$request->staff_id)->select(DB::raw('CONCAT(first_name," ",last_name) as name'),'email')->first();
            $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"User",$request->staff_id);
            $whereCondition1=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $whereConditionToSend=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['id','=',$request->staff_id]
            ];
            $dateFormat=GetUserSettings::getPeopleDateFormat("Staff",$whereConditionToSend);
            $whereCondition1[]=['role_id','=',$user->role_id];
            $whereCondition1[]=['permission_id','=','36'];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $staffHasPermission =0; /*Staff doen not have view all Order Permission */
            }
        }else{

            if(isset($request->user_id)){
                $whereCondition[]=['user_id','=',$request->user_id];
                $user = User::where('id',$request->user_id)->first();
                $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"User",$request->user_id);
                $whereConditionToSend=[
                    ['company_id','=',$request->company_id],
                    ['id','=',$request->user_id]
                ];
                $dateFormat=GetUserSettings::getPeopleDateFormat("User",$whereConditionToSend);
            }

        }
        $orderTask = OrderTask::where($whereConditions)->first();
        if($orderTask->is_subtask==0){
            $subtasks = OrderTask::where('is_subtask',1)->where('parent_task_id',$request->taskId)
            ->where('task_accomplished_date',NULL)->get();
            if(count($subtasks)>0){
                $res = json_encode(["status_code"=>600,"message"=>"Please Accomplish All The Subtasks"]);
                return CommonApp::apiEncrypt($res);
            }
        }
        $checkPic = OrderTask::where($whereConditions1)->first();
        if($checkPic->task_pic == 0){
            $res = json_encode(["status_code"=>202,"status" =>"Failure","message"=>"Please Enter a PIC"]);
            return CommonApp::apiEncrypt($res);
        }
        if(strtotime($orderTask->task_schedule_start_date) > strtotime($request->accomplishedDate)){
            $res = json_encode(["status_code"=>400,"message"=>"Please enter the date correctly."]);
            return CommonApp::apiEncrypt($res);
        }
        if(empty($orderTask)){
            $res = json_encode(["status_code"=>201,"status" =>"Failure","message"=>"Please fill all the fields"]);
            return CommonApp::apiEncrypt($res);
        }
        else if(strtotime($orderTask->task_schedule_start_date) > strtotime($request->accomplishedDate)){
            $res = json_encode(["status_code"=>400,"message"=>"Please enter the date correctly."]);
            return CommonApp::apiEncrypt($res);
        }
        if(isset($request->staffId)){
            if($staffHasPermission === 0 && $orderTask->task_pic != $request->staffId){
                    $res = json_encode(["status_code"=>601,"message"=>"Permission Denied."]);
                    return CommonApp::apiEncrypt($res);
            }
        }
        if($orderTask->task_accomplished_date != NULL){
            $res = json_encode(["status_code"=>600,"message"=>"Already Updated."]);
            return CommonApp::apiEncrypt($res);
        }
        $whereConditionsubT=[
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=', $request->company_id],
            ['parent_task_id','=',$request->taskId],
            ['is_subtask','=',1],
            ['task_schedule_start_date',"!=",NULL],
            ['task_schedule_end_date',"!=",NULL],
            ['task_accomplished_date',"!=",NULL],
            ['task_accomplished_date',">",$request->accomplishedDate],
            ['task_pic',"!=",0],
        ];
        $orderSubTaskCond = OrderTask::select('task_accomplished_date')->where($whereConditionsubT)->count();
        if($orderSubTaskCond>0){
            $res = json_encode(["status_code"=>600,"message"=>"Task Accomplished date should be equal or greater than Subtask Accomplished date."]);
            return CommonApp::apiEncrypt($res);
        }

        $orderDetail = Order::where('orders.id',$orderTask->order_id)
            ->leftjoin('order_factory','order_factory.id','orders.factory_id')
            ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
            ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
            ->select('order_buyer.name as buyer','order_pcu.name as pcu','order_factory.name as factory','orders.order_no','orders.style_no')
            ->first();
        $theNotifications = NotificationSettings::where($whereCondition)->select('email_task_accomplishment')->first();
        $theNotification = "";
        if(!empty($theNotifications)){
            $theNotification = $theNotifications->email_task_accomplishment;
        }
        if(empty($orderTask)){
            $res = json_encode(["status_code"=>201,"status" =>"Failure","message"=>"Please fill all the fields"]);
            return CommonApp::apiEncrypt($res);
        }
        // dd($orderTask);
        $orderTask->task_accomplished_date = date('Y-m-d',strtotime($request->accomplishedDate));
        $orderTask->save();

        $notificationData=[];
        $notificationData['company_id']=$orderTask->company_id;
        $notificationData['workspace_id']=$orderTask->workspace_id;
        $notificationData['user_id']=$request->user_id ?? 0;
        $notificationData['staff_id'] =$request->staff_id ?? 0;
        $notificationData['order_id']=$orderTask->order_id;
        $notificationData['notification_type']="Accomplished";
        $notiData=[];
        $notiData['order_id']=$orderTask->order_id;
        $notiData['notification_type']="Accomplished";
        $notiData['taskName']= $orderTask->task_title;
        $notiData['accomplishedOn']= $orderTask->task_accomplished_date;
        $notiData['accomplishedBy'] = $user->name;
        $notificationData['texts']=NotificationText::toGetAccomplishedTexts($notiData);
        NotificationAddition::addNotifications($notificationData,$notiData);
        if($theNotification === "6"){
            $details=[];
            $details['to'] = $user->email;
            $details['userName'] = $user->name;
            $details['plannedDate']=date($dateFormat,strtotime($orderTask->task_schedule_end_date));
            $details['completedDate']=date($dateFormat,strtotime($request->accomplishedDate));
            $details['orderNo']=$orderDetail->order_no;
            $details['styleNo']=$orderDetail->style_no;
            $details['buyer']=$orderDetail->buyer;
            $details['factory']=$orderDetail->factory;
            $details['pcu']=$orderDetail->pcu;
            $details['taskName']=$orderTask->task_title;
            $details['language']=$language;
            $details['dateFormat']=$dateFormat;
            AccomplishedMailJob::dispatch($details);
        }
        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Date added successfully"]);
        return CommonApp::apiEncrypt($res);
    }

    /* Update task data */
    public static function updateTask(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'task_id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'pic_id' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to Update Task
            $per = CommonApp::checkStaffPermission($request,'25');
            if($per===0){
                return CommonApp::checkStaffPermissionResponseMobile();
            }
        }
        $whereConditions=[
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=', $request->company_id],
            ['id','=',$request->task_id]
        ];

        if((isset($request->start_date) && isset($request->end_date)) && strtotime($request->start_date) > strtotime($request->end_date)){
            $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"End Date must be Greater than or equal to Start Date"]);
            return CommonApp::apiEncrypt($res);
        }
        $updateTask = OrderTask::where($whereConditions)->first();
        if($updateTask->task_schedule_start_date != null && $updateTask->task_schedule_end_date != null && $updateTask->task_pic > 0){
            $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"Already Updated."]);
            return CommonApp::apiEncrypt($res);
        }
        if($updateTask->is_subtask === 1){
            $whereConditions2=[
                ['workspace_id','=',$request->workspace_id],
                ['company_id','=', $request->company_id],
                ['id','=',$updateTask->parent_task_id]
            ];
            $parentTask = OrderTask::where($whereConditions2)->first();
            if($parentTask->task_schedule_start_date != null && $parentTask->task_schedule_end_date!= null){
                if( strtotime($request->start_date) >= strtotime($parentTask->task_schedule_start_date)
                && strtotime($request->end_date) <= strtotime($parentTask->task_schedule_end_date)){
                    $updateTask->task_schedule_start_date = date('Y-m-d',strtotime($request->start_date));
                    $updateTask->actual_start_date = date('Y-m-d',strtotime($request->start_date));
                    $updateTask->task_schedule_end_date = date('Y-m-d',strtotime($request->end_date));
                    $updateTask->task_pic = $request->pic_id;
                    $updateTask->save();
                }else{
                    $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"The Dates Should Be In The Range of Parent Task Dates"]);
                    return CommonApp::apiEncrypt($res);
                }
            }else{
                $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"Please Enter Main Task Dates"]);
                return CommonApp::apiEncrypt($res);
            }
        }else{
            $updateTask->task_schedule_start_date = date('Y-m-d',strtotime($request->start_date));
            $updateTask->actual_start_date = date('Y-m-d',strtotime($request->start_date));
            $updateTask->task_schedule_end_date = date('Y-m-d',strtotime($request->end_date));
            $updateTask->task_pic = $request->pic_id;
            $updateTask->save();
        }

        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Updated successfully"]);
        return CommonApp::apiEncrypt($res);
    }

    /*Get Order PIC Details */
    public static function getOrderPIC($company_id,$workspace_id,$order_id){

        $whereConditions =[
            ['order_contacts.workspace_id','=',$workspace_id],
            ['order_contacts.company_id', '=', $company_id],
            ['order_contacts.order_id','=',$order_id]
        ];

        $skuDetails = OrderContacts::where($whereConditions)
                        ->join('staff','staff.id','order_contacts.staff_id')
                        ->select('order_contacts.staff_id', 'staff.first_name', 'staff.last_name')
                        ->get();
		$arr=array();$i=0;
    	foreach ($skuDetails as $value) {
    		$arr[$i]['staff_id']=$value->staff_id;

    		$arr[$i]['staff_name']=$value->first_name.' '.$value->last_name;
    		$i++;
		}
        return $arr;
        //return response()->json(["status_code"=>200,"status" =>"Success","data"=>$arr],200);
    }

    /* Actual Start Date */
    public static function actualStartDate(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'taskId' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'actualStartDate' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to Update Task
            $per = CommonApp::checkStaffPermission($request,'25');
            if($per===0){
                return CommonApp::checkStaffPermissionResponseMobile();
            }
        }
        $whereConditions=[
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=', $request->company_id],
            ['id','=',$request->taskId]
        ];
        try{
            OrderTask::actualStartDate($request,$whereConditions);
            $res = json_encode(["status_code"=>200,"message"=>"Updated Successfully"]);
            return CommonApp::apiEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>600,"message"=>$e->getMessage()]);
            return CommonApp::apiEncrypt($res);
        }
    }

    /* To Update Tasks in the templates */
    public static function updateTaskData(Request $request){
        $request = CommonApp::apiDecrypt($request->getContent());
        $request->template_data = json_decode(json_encode($request->template_data), true);
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'template_id' => 'required',
            'template_data'=>'required|array',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to Update Task
            $per = CommonApp::checkStaffPermission($request,'25');
            if($per===0){
                return CommonApp::checkStaffPermissionResponseMobile();
            }
        }
        try{
            OrderTask::UpdateTasks($request);
            $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Task Data Updated Successfully"],200);
            return CommonApp::apiEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::apiEncrypt($res);
        }
    }

    /** Delete Task Data **/
    public static function deleteTaskDetails(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'taskId' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            //'reason'=>'required',
            'taskname' =>'required',
            'tasktitle' =>'required',
            'template_name' => ['required', Rule::unique('order_task_template')
            ->where(function ($query) use($request) {
                $query->where('company_id',$request->company_id);
                $query->where('template_name',$request->template_name);
                $query->orwhere('is_default','=','0');
                return $query;
            })]
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to Delete Task
            $per = CommonApp::checkStaffPermission($request,'73');
            if($per===0){
                return CommonApp::checkStaffPermissionResponseMobile();
            }
        }
        $whereConditions=[
            ['order_task_data.workspace_id','=',$request->workspace_id],
            ['order_task_data.company_id','=', $request->company_id],
            ['order_task_data.order_id','=',$request->order_id],
            ['order_task_data.id','=',$request->taskId]
        ];

        $getTask=OrderTask::select("order_task_template.id","order_task_template.task_template_structure")->where($whereConditions)
        ->leftjoin('order_task_template','order_task_data.template_id','order_task_template.id')
        ->first();
        if($getTask==null){
            $res = json_encode(["status_code"=>600,"message"=>"Task Not Found"]);
            return CommonApp::apiEncrypt($res);
        }
        $deleteTaskName='';
        $taskTemplate=json_decode($getTask->task_template_structure,true);
        $taskAryTitle=[];
        foreach($taskTemplate as $taskData){

            $taskArySubtitle=[];
            foreach($taskData['task_subtitles'] as $subval){
                if($taskData['task_title']==$request->tasktitle && $subval==$request->taskname){
                    $deleteTaskName==$request->taskname;
                }else{

                    $taskArySubtitle[]=$subval;
                }
            }
            if(!empty($taskArySubtitle)){
                $shh=[];
                $shh['task_title']=$taskData['task_title'];
                $shh['task_subtitles']=$taskArySubtitle;
                $taskAryTitle[]=$shh;
            }
        }
        $output=json_encode($taskAryTitle, true, JSON_UNESCAPED_SLASHES);
        $updatedTemplate=stripslashes($output);
        $newTemplateArr = [];
        $newTemplateArr['company_id']= $request->company_id;
        $newTemplateArr['workspace_id']= $request->workspace_id;
        $newTemplateArr['user_id']= $request->user_id;
        $newTemplateArr['staff_id']=$request->staff_id??0;
        $newTemplateArr['order_id']= $request->order_id;
        $newTemplateArr['template_name']= $request->template_name;
        $newTemplateArr['status']='1';
        $newTemplateArr['is_default']='1';
        $newTemplateArr['task_template_structure']= $updatedTemplate;
        $newTemplateArr['created_by']= $request->staff_id>0?$request->staff_id:$request->user_id;
        $newTemplateArr['created_user_type']= $request->staff_id>0?'Staff':'User';
        $newTemplateArr['created_at']= date('Y-m-d H:i:s');
        $newTemplateArr['updated_at']= date('Y-m-d H:i:s');
        DB::beginTransaction();
        try{
            OrderTemplate::insert($newTemplateArr);
            $templateID = DB::getPdo()->lastInsertId();

            $whereConditionsDelete=[
                ['workspace_id','=',$request->workspace_id],
                ['company_id','=', $request->company_id],
                ['order_id','=',$request->order_id],
                ['id','=',$request->taskId]
            ];
            $whereConditionsDeleteSubTask=[
                ['workspace_id','=',$request->workspace_id],
                ['company_id','=', $request->company_id],
                ['order_id','=',$request->order_id],
                ['parent_task_id','=',$request->taskId]
            ];
            //$getTaskDetails=OrderTask::where($whereConditionsDelete)->orwhere($whereConditionsDeleteSubTask)->get();
            $getTaskDetails=OrderTask::where($whereConditionsDelete)->orWhere(function($query) use ($request) {
                $query->where('workspace_id', $request->workspace_id)
                ->where('company_id', $request->company_id)
                ->where('order_id', $request->order_id)
                ->where('parent_task_id', $request->parent_task_id);
            })->get();
            $whereConditionsUpdate=[
                ['workspace_id','=',$request->workspace_id],
                ['company_id','=', $request->company_id],
                ['order_id','=',$request->order_id],
                ['template_id','=',$getTask->id]
            ];
            $whereConditionsUpdateOrd=[
                ['workspace_id','=',$request->workspace_id],
                ['company_id','=', $request->company_id],
                ['id','=',$request->order_id],
                ['order_task_template','=',$getTask->id]
            ];
            try{
                $getTaskdel=OrderTask::where($whereConditionsDelete)->delete();
                OrderTask::where($whereConditionsDeleteSubTask)->delete();
                $newTemplateID=[];
                $newTemplateID['template_id']= $templateID;

                $newOrdTemplateID=[];
                $newOrdTemplateID['order_task_template']= $templateID;
                OrderTask::where($whereConditionsUpdate)->update($newTemplateID);
                Order::where($whereConditionsUpdateOrd)->update($newOrdTemplateID);
            }catch(Exception $e){
                $res = json_encode(["status_code"=>600,"message"=>"Unable To Delete Task"]);
                return CommonApp::apiEncrypt($res);
            }
        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>600,"message"=>"Unable To Modify Template"]);
            return CommonApp::apiEncrypt($res);
        }
        DB::commit();
        $logArry = array();
        $logArry['order_id'] =$request->order_id;
        $logArry['company_id'] = $request->company_id;
        $logArry['workspace_id'] = $request->workspace_id;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] = "Delete";
        $logArry['before_values'] = json_encode($getTaskDetails,true);
        $logArry['after_values'] = "Delete Task :".$deleteTaskName.", Template ID Changed ".$getTask->id." to ".$templateID.", Deleted Task Line Number ".$request->taskId.", Reason :".$request->reason;
        Orderlog::insert($logArry);
        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Task Deleted successfully"]);
        return CommonApp::apiEncrypt($res);
    }
     /* Update task inprogress Percentage */
    public static function update_inprogress_percentage(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'taskId' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'picId' => 'required',
            'picName' => 'required',
            'inprogress_percentage'=>'required|numeric|max:100|min:0'
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereConditions=[
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=', $request->company_id],
            ['id','=',$request->taskId]
        ];

        $updateTask = OrderTask::where($whereConditions)->first();
        $inprogress_percentage = $updateTask->inprogress_percentage ?? 0;
        $updateTask->inprogress_percentage = $request->inprogress_percentage;
        $updateTask->save();

        $logArry = array();
        $logArry['company_id'] = $request->company_id;
        $logArry['workspace_id'] = $request->workspace_id;
        $logArry['order_id'] =$updateTask->order_id;
        $logArry['task_id'] =$updateTask->id;
        $logArry['template_id'] =$updateTask->template_id;
        $logArry['cat_title'] =$updateTask->cat_title;
        $logArry['task_title'] =$updateTask->task_title;
        $logArry['pic_id'] =$request->picId;
        $logArry['pic_name'] =$request->picName;
        $logArry['previous_percentage'] =$inprogress_percentage;
        $logArry['update_percentage'] =$request->inprogress_percentage;
        $logArry['created_at'] =date('Y-m-d H:i:s');
        $logArry['updated_at'] =date('Y-m-d H:i:s');
        TaskPercentageUpdate::insert($logArry);

        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Updated successfully"]);
        return CommonApp::apiEncrypt($res);
    }

    /* Update task inprogress Percentage */
    public static function inprogress_percentage_history(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'taskId' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereConditions=[
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=', $request->company_id],
            ['task_id','=',$request->taskId]
        ];

        $data = TaskPercentageUpdate::where($whereConditions)->orderBy('id',"DESC")->get();


        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$data]);
        return CommonApp::apiEncrypt($res);
    }


}
