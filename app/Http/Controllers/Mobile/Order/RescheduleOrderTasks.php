<?php

namespace App\Http\Controllers\Mobile\Order;

use App\Common\GetUserLanguage;
use App\Common\NotificationAddition;
use App\Common\NotificationText;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use App\Jobs\RescheduleMailJob;
use App\Models\NotificationSettings;
use App\Models\Order;
use App\Models\OrderTask;
use App\Models\RescheduleTasks;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Common\CommonApp;

class RescheduleOrderTasks extends Controller
{
    /* Reschedule Tasks */
    public static function rescheduleTask(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'id' => 'required',
            // 'the_type' => 'required',
            'reschedule_type' =>'required',
            'reason' => 'required',
            // 'date' => 'required',
            // 'pic_id'=>'required',
            // 'userId' => 'required',
            // 'staffId' => 'required',
            // 'user_type' => 'required',
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
        $theTask = OrderTask::where('id',$request->id)->first();
        $whereCondition=[
            ['company_id','=',$theTask->company_id],
            ['workspace_id','=',$theTask->workspace_id],
        ];
        if(isset($request->staff_id) && $request->staff_id == 0){
            $whereCondition[]=['user_id','=',$request->user_id];
            $userType = "User";
            $language = GetUserLanguage::getLanguageOfUserWithId($theTask->company_id,$theTask->workspace_id,"User",$request->user_id);
            $whereConditionToSend=[
                ['company_id','=',$theTask->company_id],
                ['id','=',$request->user_id]
            ];
            $dateFormat=GetUserSettings::getPeopleDateFormat("User",$whereConditionToSend);
        }
        if(isset($request->staff_id) && $request->staff_id >0){
            $whereCondition[]=['staff_id','=',$request->staff_id];
            $userType = "Staff";
            $language = GetUserLanguage::getLanguageOfUserWithId($theTask->company_id,$theTask->workspace_id,"Staff",$request->staff_id);
            $whereConditionToSend=[
                ['company_id','=',$theTask->company_id],
                ['workspace_id','=',$theTask->workspace_id],
                ['id','=',$request->staff_id]
            ];
            $dateFormat=GetUserSettings::getPeopleDateFormat("Staff",$whereConditionToSend);
        }
        $theNotifications = NotificationSettings::where($whereCondition)->select('email_task_reschedule')->first();
        $theNotification = "";
        if(!empty($theNotifications)){
            $theNotification = $theNotifications->email_task_reschedule;
        }
        $orderDetail = Order::where('orders.id',$theTask->order_id)
            ->leftjoin('order_factory','order_factory.id','orders.factory_id')
            ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
            ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
            ->select('order_buyer.name as buyer','order_pcu.name as pcu','order_factory.name as factory','orders.order_no','orders.style_no')
            ->first();

        $reschduleArr=[];
        $reschduleArr['company_id']=$theTask->company_id;
        $reschduleArr['workspace_id']=$theTask->workspace_id;
        $reschduleArr['orderTaskData_id']=$theTask->id;
        $reschduleArr['order_id']=$theTask->order_id;
        $reschduleArr['template_id']=$theTask->template_id;
        $reschduleArr['cat_title']=$theTask->cat_title;
        $reschduleArr['task_title']=$theTask->task_title;
        $reschduleArr['reason']=$request->reason;
        if(isset($request->staff_id) && $request->staff_id == 0){
            $reschduleArr['rescheduled_by']=$request->user_id;
            $reschedulingPerson = User::where('id',$request->user_id)->first();
        }
        if(isset($request->staff_id) && $request->staff_id >0){
            $reschduleArr['rescheduled_by']=$request->staff_id;
            $reschedulingPerson = Staff::where('id',$request->staff_id)->select(DB::raw('CONCAT(first_name," ",last_name) as name'),'email')->first();
        }
        $reschduleArr['user_type']=$userType;
        $reschduleArr['created_at']=date('Y-m-d H:i:s');

        $notificationData=[];
        $notificationData['company_id']=$theTask->company_id;
        $notificationData['workspace_id']=$theTask->workspace_id;
        $notificationData['user_id']=$request->user_id;
        $notificationData['staff_id'] =$request->staffId ?? 0;
        $notificationData['order_id']=$theTask->order_id;

        $notiTexts =$details =[];
        $details['language']=$language;
        $notiTexts['taskName']= $theTask->task_title;
        $notiTexts['reason']=$request->reason;
        $notiTexts['type']=$request->reschedule_type;
        /* Reschedule */
        if(isset($request->reschedule_type) && $request->reschedule_type === "Reschedule"){
            if(isset($request->the_type) && $request->the_type === "StartDate"){
                if(isset($request->date) && $request->date == ""){
                    $res = json_encode(['status_code' => 400, 'status'=>'failure','message'=>'Please enter a date']);
                    return CommonApp::apiEncrypt($res);
                }
                if(isset($request->date) && strtotime($request->date) >strtotime($theTask->task_schedule_end_date) ){
                    $res = json_encode(['status_code' => 402, 'status'=>'failure','message'=>'Start Date must be lesser than or equal to End Date']);
                    return CommonApp::apiEncrypt($res);
                }
                $theSubtasks = OrderTask::where('parent_task_id',$request->id)->get();
                if(count($theSubtasks)>0){
                    $counts = 0;
                    foreach($theSubtasks as $subTask){
                        if($subTask->task_schedule_start_date!=null){
                            if(strtotime($request->date) > strtotime($subTask->task_schedule_start_date)){
                                $counts += 1;
                            }
                        }
                    }
                    if($counts>0){
                        $res = json_encode(['status_code' => 603, 'status'=>'failure','message'=>'Reschedule Subtasks Date And Try Again']);
                        return CommonApp::apiEncrypt($res);
                    }
                }
                $reschduleArr['start_date']=$theTask->task_schedule_start_date;
                $reschduleArr['end_date']=null;
                $reschduleArr['rescheduled_start_date']= date('Y-m-d',strtotime($request->date));
                $reschduleArr['rescheduled_end_date']=null;
                $reschduleArr['pic_id']=$theTask->task_pic;
                $reschduleArr['prev_pic_id']=$theTask->task_pic;
                $reschduleArr['rescheduled_type']=$request->reschedule_type;
                RescheduleTasks::insert($reschduleArr);
                $lastInsertedId = DB::getPdo()->lastInsertId();

                $theTask->task_schedule_start_date=date('Y-m-d',strtotime($request->date));
                $theTask->actual_start_date=date('Y-m-d',strtotime($request->date));
                $theTask->reschedule_reason = $request->reason;
                $theTask->reschedule_order_task_data_id = $lastInsertedId;
                $theTask->rescheduled = "1";
                $theTask->save();

                $notificationData['notification_type']="Reschedule";
                $notiTexts['from']=$reschduleArr['start_date'];
                $notiTexts['to']=$reschduleArr['rescheduled_start_date'];
                $notificationData['texts'] = NotificationText::toGetRescheduleTexts($notiTexts);
                NotificationAddition::addNotifications($notificationData,$notiTexts);
                if($theNotification === "6"){
                    $details['to']=$reschedulingPerson->email;
                    $details['userName']=$reschedulingPerson->name;
                    $details['type']="StartDate";
                    $details['plannedDate']=$reschduleArr['start_date'] != null ? date($dateFormat,strtotime($reschduleArr['start_date'])):"---";
                    $details['changedDate']=date($dateFormat,strtotime($request->date));
                    $details['orderNo']=$orderDetail->order_no;
                    $details['styleNo']=$orderDetail->style_no;
                    $details['buyer']=$orderDetail->buyer;
                    $details['factory']=$orderDetail->factory;
                    $details['pcu']=$orderDetail->pcu;
                    $details['taskName']=$theTask->task_title;
                    $details['reason']=$request->reason;
                    $details['dateFormat']=$dateFormat;
                    RescheduleMailJob::dispatch($details);
                }
            }
            else if(isset($request->the_type) && $request->the_type === "EndDate"){
                if(isset($request->date) && $request->date == ""){
                    $res = json_encode(['status_code' => 400, 'status'=>'failure','message'=>'Please enter a date']);
                    return CommonApp::apiEncrypt($res);
                }
                if(isset($request->date) && strtotime($request->date) < strtotime($theTask->task_schedule_start_date) ){
                    $res = json_encode(['status_code' => 402, 'status'=>'failure','message'=>'End Date must be greater than or equal to Start Date']);
                    return CommonApp::apiEncrypt($res);
                }
                if(isset($request->isSubtask) && $request->isSubtask === "Subtask"){
                    $parentTask = OrderTask::where('id',$theTask->parent_task_id)->first();
                    if(strtotime($request->date) > strtotime($parentTask->task_schedule_end_date)){
                        $res = json_encode(['status_code' => 403, 'status'=>'failure','message'=>'End Date must be lesser than or equal to Parent Task End Date']);
                        return CommonApp::apiEncrypt($res);
                    }
                }else{
                    $theSubtasks = OrderTask::where('parent_task_id',$request->id)->get();
                    if(count($theSubtasks)>0){
                        $counts = 0;
                        foreach($theSubtasks as $subTask){
                            if(strtotime($subTask->task_schedule_end_date) > strtotime($request->date)){
                                $counts += 1;
                            }
                        }
                        if($counts>0){
                            $res = json_encode(['status_code' => 603, 'status'=>'failure','message'=>'Reschedule Subtasks Date And Try Again']);
                            return CommonApp::apiEncrypt($res);
                        }
                    }
                }
                $reschduleArr['start_date']=null;
                $reschduleArr['end_date']=$theTask->task_schedule_end_date;
                $reschduleArr['rescheduled_start_date']=null;
                $reschduleArr['rescheduled_end_date']= date('Y-m-d',strtotime($request->date));
                $reschduleArr['pic_id']=$theTask->task_pic;
                $reschduleArr['prev_pic_id']=$theTask->task_pic;
                $reschduleArr['rescheduled_type']=$request->reschedule_type;
                RescheduleTasks::insert($reschduleArr);
                $lastInsertedId = DB::getPdo()->lastInsertId();

                $theTask->task_schedule_end_date=date('Y-m-d',strtotime($request->date));
                $theTask->reschedule_reason = $request->reason;
                $theTask->reschedule_order_task_data_id = $lastInsertedId;
                $theTask->rescheduled = "1";
                $theTask->save();

                $notificationData['notification_type']="Reschedule";
                $notiTexts['from']=$reschduleArr['end_date'];
                $notiTexts['to']=$reschduleArr['rescheduled_end_date'];
                $notificationData['texts'] = NotificationText::toGetRescheduleTexts($notiTexts);
                NotificationAddition::addNotifications($notificationData,$notiTexts);
                if($theNotification === "6"){
                    $details['to']=$reschedulingPerson->email;
                    $details['userName']=$reschedulingPerson->name;
                    $details['type']="EndDate";
                    $details['plannedDate']=$reschduleArr['end_date'] != null?date($dateFormat,strtotime($reschduleArr['end_date'])):"---";
                    $details['changedDate']=date($dateFormat,strtotime($request->date));
                    $details['orderNo']=$orderDetail->order_no;
                    $details['styleNo']=$orderDetail->style_no;
                    $details['buyer']=$orderDetail->buyer;
                    $details['factory']=$orderDetail->factory;
                    $details['pcu']=$orderDetail->pcu;
                    $details['taskName']=$theTask->task_title;
                    $details['reason']=$request->reason;
                    $details['dateFormat']=$dateFormat;
                    RescheduleMailJob::dispatch($details);
                }
            }
            $res = json_encode(['status_code' => 200, 'status'=>'success','message'=>'Date Rescheduled Successfully']);
            return CommonApp::apiEncrypt($res);
        }
        /* Reassign */
        if(isset($request->reschedule_type) && $request->reschedule_type === "Reassign"){
            if(isset($request->pic_id) && $request->pic_id == ""){
                $res = json_encode(['status_code' => 400, 'status'=>'failure','message'=>'Please Select a PIC']);
                return CommonApp::apiEncrypt($res);
            }
            $reschduleArr['start_date']=null;
            $reschduleArr['end_date']=null;
            $reschduleArr['rescheduled_start_date']=null;
            $reschduleArr['rescheduled_end_date']=null;
            $reschduleArr['prev_pic_id']=$theTask->task_pic == 0 ? "0" : $theTask->task_pic;
            $reschduleArr['pic_id']=$request->pic_id;
            $reschduleArr['rescheduled_type']=$request->reschedule_type;
            RescheduleTasks::insert($reschduleArr);
            $lastInsertedId = DB::getPdo()->lastInsertId();

            $theTask->task_pic=$request->pic_id;
            $theTask->reschedule_reason = $request->reason;
            $theTask->reschedule_order_task_data_id = $lastInsertedId;
            $theTask->rescheduled = "1";
            $theTask->save();

            $notificationData['notification_type']="Reassign";
            $notiTexts['from']=$reschduleArr['prev_pic_id'];
            $notiTexts['to']=$reschduleArr['pic_id'];
            $notificationData['texts'] = NotificationText::toGetRescheduleTexts($notiTexts);
            NotificationAddition::addNotifications($notificationData,$notiTexts);
            if($theNotification === "6"){
                $details['to']=$reschedulingPerson->email;
                $details['userName']=$reschedulingPerson->name;
                $details['type']="Reassign";
                $details['plannedPIC']=$reschduleArr['prev_pic_id'] == 0 ? "None" :((Staff::where('id',$reschduleArr['prev_pic_id'])->select(DB::raw('CONCAT(first_name," ",last_name) as staffName'))
                        ->first())->staffName);
                $details['changedPIC']=(Staff::where('id',$request->pic_id)->select(DB::raw('CONCAT(first_name," ",last_name) as staffName'))
                ->first())->staffName;
                $details['orderNo']=$orderDetail->order_no;
                $details['styleNo']=$orderDetail->style_no;
                $details['buyer']=$orderDetail->buyer;
                $details['factory']=$orderDetail->factory;
                $details['pcu']=$orderDetail->pcu;
                $details['taskName']=$theTask->task_title;
                $details['reason']=$request->reason;
                $details['dateFormat']=$dateFormat;
                RescheduleMailJob::dispatch($details);
            }

            $res = json_encode(['status_code' => 200, 'status'=>'success','message'=>'PIC Changed Successfully']);
            return CommonApp::apiEncrypt($res);
        }

    }
    /* Get task's Reschedule History */
    public function getRescheduleTaskUsingId(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request,[
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' =>  'required',
            'task_id' => 'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        if (isset($request->company_id) && $request->company_id!=""){
            $whereCondition['reschedule_tasks.company_id'] = $request->company_id;
        }
        if (isset($request->workspace_id) && $request->workspace_id!=""){
            $whereCondition['reschedule_tasks.workspace_id'] = $request->workspace_id;
        }
        if (isset($request->order_id) && $request->order_id!=""){
            $whereCondition['reschedule_tasks.order_id'] = $request->order_id;
        }
        if (isset($request->task_id) && $request->task_id!=""){
            $whereCondition['reschedule_tasks.orderTaskData_id'] = $request->task_id;
        }
        $getRescheduleTasksDetails = RescheduleTasks::where($whereCondition)
                                    // ->join('users','users.id','reschedule_tasks.rescheduled_by')
                                    ->leftjoin('staff as prevStaff','prevStaff.id','reschedule_tasks.prev_pic_id')
                                    ->leftjoin('staff as nextStaff','nextStaff.id','reschedule_tasks.pic_id')
                                    ->select('cat_title','task_title','start_date','end_date','rescheduled_start_date',
                                    'rescheduled_end_date','reason','rescheduled_type','pic_id','prev_pic_id','reschedule_tasks.created_at',
                                    'reschedule_tasks.user_type','reschedule_tasks.rescheduled_by',
                                    DB::raw('CONCAT(prevStaff.first_name," ",prevStaff.last_name) as prevStaffName'),
                                    DB::raw('CONCAT(nextStaff.first_name," ",nextStaff.last_name) as nextStaffName'))
                                    ->get();

        if(count($getRescheduleTasksDetails) === 0){
            $res = json_encode(['status_code' => 400,'status'=>"failure" ,'message' => "Reschedule History Not Found"]);
            return CommonApp::apiEncrypt($res);
        }

        $tasksAllHistoryArr=[];
        foreach($getRescheduleTasksDetails as $tasks){
            // dd($tasks);
            $tasksHistoryArr=[];
            $tasksHistoryArr['cat_title']=$tasks->cat_title;
            $tasksHistoryArr['task_title']=$tasks->task_title;
            $tasksHistoryArr['start_date']=$tasks->start_date;
            $tasksHistoryArr['end_date']=$tasks->end_date;
            $tasksHistoryArr['rescheduled_start_date']=$tasks->rescheduled_start_date;
            $tasksHistoryArr['rescheduled_end_date']=$tasks->rescheduled_end_date;
            $tasksHistoryArr['reason']=$tasks->reason;
            $tasksHistoryArr['rescheduled_type']=$tasks->rescheduled_type;
            $tasksHistoryArr['pic_id']=$tasks->pic_id;
            $tasksHistoryArr['prev_pic_id']=$tasks->prev_pic_id;
            $tasksHistoryArr['created_at']=$tasks->created_at;
            $tasksHistoryArr['prevStaffName']=$tasks->prevStaffName;
            $tasksHistoryArr['nextStaffName']=$tasks->nextStaffName;
            if($tasks->user_type === "User"){
                $tasksHistoryArr['userName'] = (User::where('id',$tasks->rescheduled_by)->select('name')->first())->name;
            }
            else if($tasks->user_type === "Staff"){
                $tasksHistoryArr['userName'] = (Staff::where('id',$tasks->rescheduled_by)->
                select(DB::raw('CONCAT(first_name," ",last_name) as name'))->first())->name;
            }
            $tasksAllHistoryArr[]=$tasksHistoryArr;
        }

        $res = json_encode(['status_code' => 200,'status'=>"success" ,'data' => $tasksAllHistoryArr]);
        return CommonApp::apiEncrypt($res);
    }

}

