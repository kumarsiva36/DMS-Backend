<?php

namespace App\Http\Controllers\WebSite\Order;

use App\Common\CommonApp;
use App\Common\GetUserLanguage;
use App\Common\NotificationAddition;
use App\Common\NotificationText;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use App\Jobs\AccomplishedMailJob;
use App\Models\MultipleDeliveryDates;
use App\Models\NotificationSettings;
use App\Models\Order;
use App\Models\OrderTask;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderTemplate;
use App\Models\Orderlog;
use App\Models\TaskPercentageUpdate;
use Illuminate\Validation\Rule;

class OrderTasks extends Controller
{
    /* To add new Tasks in the templates */
    public static function addTaskData(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
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
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to add orders
            $per = CommonApp::checkStaffPermission($request,'18');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
            }
        }
        try{
            OrderTask::addTasks($request);
            $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Task Data Added Successfully"],200);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Get the Task details */
    public static function getTaskDetails(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            $arr = OrderTask::getTaskDetails($request);

            //Delivery Dates
            $whereConditionDel = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['order_id','=',$request->order_id]
                    ];
            $delivery_date = MultipleDeliveryDates::where($whereConditionDel)->where('is_delivered','=','0')
                        ->orderBy('delivery_date',"ASC")->pluck('delivery_date')->first();
            $delivery_date_exceed=0;
            if($delivery_date!="" && $delivery_date!=null){
                if($delivery_date < date('Y-m-d')){
                    $delivery_date_exceed=1;
                }
            }

            $res = json_encode(["status_code"=>200,"status" =>"Success","templateID"=>$arr['taskTemplateId'],"data"=>$arr['arr'],
            "delivery_date"=>$delivery_date,"delivery_date_exceed"=>$delivery_date_exceed]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Accomplished Task Date */
    public static function accomplishedTask(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'taskId' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'accomplishedDate' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to Update Task
            $per = CommonApp::checkStaffPermission($request,'25');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
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
        $whereCondition=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
        ];
        $staffHasPermission = 1; /* Assume Staff has view all Order Permission */
        if(isset($request->userId)){
            $whereCondition[]=['user_id','=',$request->userId];
            $user = User::where('id',$request->userId)->first();
            $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"User",$request->userId);
            $whereConditionToSend=[
                ['company_id','=',$request->company_id],
                ['id','=',$request->userId]
            ];
            $dateFormat=GetUserSettings::getPeopleDateFormat("User",$whereConditionToSend);
        }
        if(isset($request->staffId)){
            $whereCondition[]=['staff_id','=',$request->staffId];
            $user = Staff::where('id',$request->staffId)->select(DB::raw('CONCAT(first_name," ",last_name) as name'),'email','role_id')->first();
            $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"User",$request->staffId);
            $whereCondition1=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $whereConditionToSend=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['id','=',$request->staffId]
            ];
            $dateFormat=GetUserSettings::getPeopleDateFormat("Staff",$whereConditionToSend);
            // $staffRoleHasPermission = Staff::where('id',$request->staffId)->first();
            $whereCondition1[]=['role_id','=',$user->role_id];
            $whereCondition1[]=['permission_id','=','36'];
            $whereCondition1[]=['company_id','=',$request->company_id];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $staffHasPermission =0; /*Staff doen not have view all Order Permission */
            }
        }
        $orderTask = OrderTask::where($whereConditions)->first();
        if($orderTask->is_subtask==0){
            $subtasks = OrderTask::where('is_subtask',1)->where('parent_task_id',$request->taskId)
            ->where('task_accomplished_date',NULL)->get();
            if(count($subtasks)>0){
                $res = json_encode(["status_code"=>600,"message"=>"Please Accomplish All The Subtasks"]);
                return CommonApp::webEncrypt($res);
            }
        }
        if(empty($orderTask)){
            $res = json_encode(["status_code"=>201,"status" =>"Failure","message"=>"Please fill all the fields"]);
            return CommonApp::webEncrypt($res);
        }
        else if(isset($request->accomplishedDate) && strtotime($orderTask->task_schedule_start_date) > strtotime($request->accomplishedDate)){
            $res = json_encode(["status_code"=>600,"message"=>"Please enter the date correctly."]);
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staffId) && $request->staffId>0){
            if($staffHasPermission === 0 && $orderTask->task_pic != $request->staffId){
                $res = json_encode(["status_code"=>601,"message"=>"Permission Denied."]);
                return CommonApp::webEncrypt($res);
            }
        }
        if($orderTask->task_accomplished_date != NULL){
            $res = json_encode(["status_code"=>600,"message"=>"Already Updated."]);
            return CommonApp::webEncrypt($res);
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
            return CommonApp::webEncrypt($res);
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
        // dd($orderTask);
        $orderTask->task_accomplished_date = date('Y-m-d',strtotime($request->accomplishedDate));
        $orderTask->save();

        $notificationData=[];
        $notificationData['company_id']=$orderTask->company_id;
        $notificationData['workspace_id']=$orderTask->workspace_id;
        $notificationData['user_id']=$request->userId ?? 0;
        $notificationData['staff_id'] =$request->staffId ?? 0;
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
        return CommonApp::webEncrypt($res);
    }

    /* Update task data */
    public static function updateTask(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'taskId' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'picId' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=', $request->company_id],
            ['id','=',$request->taskId]
        ];

        if((isset($request->startDate) && isset($request->endDate)) && strtotime($request->startDate) > strtotime($request->endDate)){
            $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"End Date must be Greater than or equal to Start Date"]);
            return CommonApp::webEncrypt($res);
        }
        $updateTask = OrderTask::where($whereConditions)->first();
        if($updateTask->task_schedule_start_date != null && $updateTask->task_schedule_end_date != null && $updateTask->task_pic > 0){
            $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"Already Updated."]);
            return CommonApp::webEncrypt($res);
        }
        if($updateTask->is_subtask === 1){
            $whereConditions2=[
                ['workspace_id','=',$request->workspace_id],
                ['company_id','=', $request->company_id],
                ['id','=',$updateTask->parent_task_id]
            ];
            $parentTask = OrderTask::where($whereConditions2)->first();
            if($parentTask->task_schedule_start_date != null && $parentTask->task_schedule_end_date!= null){
                if( strtotime($request->startDate) >= strtotime($parentTask->task_schedule_start_date)
                && strtotime($request->endDate) <= strtotime($parentTask->task_schedule_end_date)){
                    $updateTask->task_schedule_start_date = date('Y-m-d',strtotime($request->startDate));
                    $updateTask->actual_start_date = date('Y-m-d',strtotime($request->startDate));
                    $updateTask->task_schedule_end_date = date('Y-m-d',strtotime($request->endDate));
                    $updateTask->task_pic = $request->picId;
                    $updateTask->save();
                }else{
                    $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"The Dates Should Be In The Range of Parent Task Dates"]);
                    return CommonApp::webEncrypt($res);
                }
            }else{
                $res = json_encode(["status_code"=>600,"status" =>"Failure","message"=>"Please Enter Main Task Dates"]);
                return CommonApp::webEncrypt($res);
            }
        }else{
            $updateTask->task_schedule_start_date = date('Y-m-d',strtotime($request->startDate));
            $updateTask->actual_start_date = date('Y-m-d',strtotime($request->startDate));
            $updateTask->task_schedule_end_date = date('Y-m-d',strtotime($request->endDate));
            $updateTask->task_pic = $request->picId;
            $updateTask->save();
        }

        $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"Updated successfully"]);
        return CommonApp::webEncrypt($res);
    }

    /* Actual Start Date */
    public static function actualStartDate(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'taskId' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'actualStartDate' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=', $request->company_id],
            ['id','=',$request->taskId]
        ];
        try{
            OrderTask::actualStartDate($request,$whereConditions);
            $res = json_encode(["status_code"=>200,"message"=>"Updated Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>600,"message"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* To Update Tasks in the templates */
    public static function updateTaskData(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
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
            return CommonApp::webEncrypt($res);
        }
        try{
            OrderTask::UpdateTasks($request);
            $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Task Data Updated Successfully"],200);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }


    /** Delete Task Data **/
    public static function deleteTaskDetails(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
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
            return CommonApp::webEncrypt($res);
        }
        if(isset($request->staff_id) && $request->staff_id > 0){ //Check if staff have permission to Delete task
            $per = CommonApp::checkStaffPermission($request,'73');
            if($per===0){
                return CommonApp::checkStaffPermissionResponse();
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
        if(!empty($taskAryTitle)){
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
                // $getTaskDetails=OrderTask::where($whereConditionsDelete)->orwhere($whereConditionsDeleteSubTask)->get();
                $getTaskDetails=OrderTask::where($whereConditionsDelete)
                ->orWhere(function ($query) use($request) {
                    $query->where("workspace_id",$request->workspace_id)->where('company_id',$request->company_id)
                        ->where('order_id',$request->order_id)->where('parent_task_id',$request->taskId);
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
                    return CommonApp::webEncrypt($res);
                }
            }catch(Exception $e){
                DB::rollBack();
                $res = json_encode(["status_code"=>600,"message"=>"Unable To Modify Template"]);
                return CommonApp::webEncrypt($res);
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
            return CommonApp::webEncrypt($res);
        }else{
            $res = json_encode(["status_code"=>600,"message"=>"Unable To Delete Task"]);
            return CommonApp::webEncrypt($res);
        }
    }
    /* Update task inprogress Percentage */
    public static function update_inprogress_percentage(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
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
            return CommonApp::webEncrypt($res);
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
        return CommonApp::webEncrypt($res);
    }

    /* Update task inprogress Percentage */
    public static function inprogress_percentage_history(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'taskId' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['workspace_id','=',$request->workspace_id],
            ['company_id','=', $request->company_id],
            ['task_id','=',$request->taskId]
        ];

        $data = TaskPercentageUpdate::where($whereConditions)->orderBy('id',"DESC")->get();


        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$data]);
        return CommonApp::webEncrypt($res);
    }
}
