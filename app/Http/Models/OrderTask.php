<?php

namespace App\Models;

use App\Common\CommonApp;
use Aws\Arn\Exception\InvalidArnException;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderTask extends Model
{
    use HasFactory;

    protected $table = 'order_task_data';

    public static function checkIfStaffIsPresentInActiveOrder($id){
        $isStaffActiveInOrder = OrderTask::leftjoin('orders','orders.id','order_task_data.order_id')
        ->where("orders.status","1")
        ->where("task_pic",$id)
        ->where("task_accomplished_date",null)
        ->get();

        return $isStaffActiveInOrder;
    }

    public static function addTasks($request){
        DB::beginTransaction();
        try{
            $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
            $whereConditions =[
                ['workspace_id','=',$request->workspace_id],
                ['company_id', '=', $request->company_id],
                ['order_id','=',$request->order_id]
            ];

            $aldreadyExists = OrderTask::select("id")->where($whereConditions)->get();
            if(!empty($aldreadyExists)){
                OrderTask::where($whereConditions)->delete();
            }
            $orderId = $request->order_id;
            /******************** To add template id in ORDERS Table ******************/
            $addTemplateToOrder = Order::where('id',$request->order_id)->first();
            $addTemplateToOrder->order_task_template = $request->template_id;
            $addTemplateToOrder->save();

            $orderProductionArr = [];
            $orderProductionArr['user_id']= $companyDetails->user_id;
            $orderProductionArr['company_id']= $request->company_id;
            $orderProductionArr['workspace_id']= $request->workspace_id;
            $orderProductionArr['staff_id']= $request->staff_id ?? 0;
            $orderProductionArr['order_id']= $request->order_id;
            $orderProductionArr['template_id']= $request->template_id;
            $orderProductionArr['created_by']= $companyDetails->user_id;
            $orderProductionArr['created_user_type']= "User";
            $orderProductionArr['task_accomplished_date']= $request->task_accomplished_date??null;
            $orderProductionArr['reschedule_reason']= $request->reschedule_reason ?? '';
            $orderProductionArr['reschedule_order_task_data_id']= $request->reschedule_order_task_data_id ?? 0;
            $orderProductionArr['rescheduled']= $request->rescheduled??null;
            $orderProductionArr['category_contacts']= $request->category_contacts ?? '';
            $orderProductionArr['task_contacts']= $request->task_contacts ?? '';
            // foreach($request->template_data as $templates){
                foreach($request->template_data as $key=>$template){
                    $orderProductionArr['cat_title']= $key;
                    foreach($template as $data){
                        $orderProductionArr['task_title']= $data['title'];
                        $orderProductionArr['task_schedule_start_date']= $data['startdate'] != "" ? date('Y-m-d',strtotime($data['startdate'])) : NULL;
                        $orderProductionArr['actual_start_date']= $data['startdate'] != "" ? date('Y-m-d',strtotime($data['startdate'])) : NULL;
                        $orderProductionArr['task_schedule_end_date']= $data['enddate'] != "" ? date('Y-m-d',strtotime($data['enddate'])) : NULL;
                        $orderProductionArr['task_pic']= $data['pic_id'] != "" ? $data['pic_id'] : "0";
                        $orderProductionArr['created_at']=date('Y-m-d H:i:s');
                        $orderProductionArr['updated_at']=date('Y-m-d H:i:s');
                        OrderTask::insert($orderProductionArr);
                    }
                }
            // }

            /*Update Order Step Status*/
           $getORD= Order::select("id")->where('id',$orderId)->where('step_level','<','4')->first();
           if(!empty($getORD)){
            $addOrderArr=[];
            $addOrderArr['step_level'] = '4';
            Order::where('id',$orderId)->update($addOrderArr);
           }
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArnException("Unable to Post Data");
        }
        DB::commit();
    }

    public static function getTaskDetails($request){
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
           // $taskDetails = OrderTask::where($whereConditions)->orderByRaw('FIELD(cat_title,"'.implode('","',$cat_titles).'" )ASC')->get();
            $taskDetails = OrderTask::where($whereConditions)->orderByRaw('FIELD(cat_title,"'.implode('","',$cat_titles).'" )')->get();
        }else{
            $taskDetails = OrderTask::where($whereConditions)->orderBy('cat_title','asc')->OrderBy('id','asc')->get();
        }

        $subTaskDetails = OrderTask::where($whereConditions1)->get();
        // dd($subTaskDetails);
		$arr=array();$i=$j=$k=$l=0; $catTitle=''; $subArr = array();

        foreach($taskDetails as $tasks){
            $templateID = $tasks->template_id;
            if($i == 0 ){
                $catTitle = $tasks->cat_title;
            }
            if( $tasks->cat_title != $catTitle ){
                $catTitle = $tasks->cat_title;

                $keys = array_keys(array_column($arr, 'task_title'), $catTitle);
                if(!empty($keys)){
                    foreach($keys as $ky){
                        $sub_count=  count($arr[$ky]['task_subtitles']);
                        $k=$ky; $j=$sub_count;
                    }
                }else{
                    $k++; $j=0;
                    $subArr[$catTitle] = array();
                }
            }

            if($tasks->cat_title == $catTitle ){
                $subArr[$catTitle][$j]["id"] = $tasks->id;
                $subArr[$catTitle][$j]["title"] = $tasks->cat_title;
                $subArr[$catTitle][$j]["subtitle"] = $tasks->task_title;
                $subArr[$catTitle][$j]["start_date"] = $tasks->task_schedule_start_date;
                $subArr[$catTitle][$j]["actual_start_date"] = $tasks->actual_start_date;
                $subArr[$catTitle][$j]["end_date"] = $tasks->task_schedule_end_date;
                $subArr[$catTitle][$j]["accomplished_date"] = $tasks->task_accomplished_date;
                $subArr[$catTitle][$j]["pic_id"] = $tasks->task_pic;
                $subArr[$catTitle][$j]["inprogress_percentage"] = $tasks->inprogress_percentage;
                foreach($subTaskDetails as $subtask){
                    if($subtask->parent_task_id === $tasks->id){
                        $subtaskArr=[];
                        // $subArr[$j]['subtasks'][$l]["id"] = $subtask->id;
                        // $subArr[$j]['subtasks'][$l]["title"] = $subtask->cat_title;
                        // $subArr[$j]['subtasks'][$j]["subtitle"] = $subtask->task_title;
                        // $subArr[$j]['subtasks'][$l]["subtasktitle"] = $subtask->subtask_title;
                        // $subArr[$j]['subtasks'][$l]["start_date"] = $subtask->task_schedule_start_date;
                        // $subArr[$j]['subtasks'][$l]["actual_start_date"] = $subtask->actual_start_date;
                        // $subArr[$j]['subtasks'][$l]["end_date"] = $subtask->task_schedule_end_date;
                        // $subArr[$j]['subtasks'][$l]["accomplished_date"] = $subtask->task_accomplished_date;
                        // $subArr[$j]['subtasks'][$l]["pic_id"] = $subtask->task_pic;
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
                        // $l++;
                        $subArr[$catTitle][$j]['subtasks'][]=$subtaskArr;
                    }
                }
                // $l=0;
                $j++;
            }
            if( !empty($subArr[$tasks->cat_title]) ){
                $arr[$k]["task_title"] = $tasks->cat_title;
                $arr[$k]["task_subtitles"] = $subArr[$tasks->cat_title];
            }

            $i++;
        }
        $taskArr['taskTemplateId'] = $templateID;
        $taskArr['arr']=$arr;
       // dd($arr);
        return $taskArr;
    }

    public static function actualStartDate($request,$whereConditions){
        DB::beginTransaction();
        $theTask = OrderTask::where($whereConditions)->first();
        try{
            if($theTask->actual_start_date != null){
                throw new InvalidArgumentException("Already Updated");
            }
            elseif(strtotime($theTask->task_schedule_start_date)>strtotime($request->actualStartDate)){
                throw new InvalidArgumentException("Please enter the date correctly.");
            }
            elseif(strtotime($request->actualStartDate) > strtotime($theTask->task_schedule_end_date)){
                $parentTask = OrderTask::where('id',$theTask->parent_task_id)->first();
                if(strtotime($request->actualStartDate) > strtotime($theTask->task_schedule_end_date)){
                    throw new InvalidArgumentException("Please Reschedule the End Date To Continue");
                }
                elseif(!empty($parentTask) && strtotime($request->actualStartDate) > strtotime($parentTask->task_schedule_end_date)){
                    throw new InvalidArgumentException("Please Reschedule Main Task And Sub Task End Date To Continue");
                }
                else{
                    throw new InvalidArgumentException("Please Reschedule Sub Task End Date To Continue");
                }
            }
            elseif($theTask->task_schedule_start_date != null && $theTask->task_schedule_end_date != null
            && $theTask->task_pic != 0 && $theTask->task_accomplished_date == null){
                $theTask->actual_start_date = date('Y-m-d',strtotime($request->actualStartDate));
                $theTask->save();
            }else{
                throw new InvalidArgumentException("Please Fill the Start and End Dates");
            }
        }catch(Exception $e){
            DB::rollBack();
            if($e->getMessage() === "Please Fill the Start and End Dates"){
                throw new InvalidArgumentException("Please Fill the Start and End Dates and Person-In-Charge");
            }
            elseif($e->getMessage() === "Please enter the date correctly."){
                throw new InvalidArgumentException("Please enter the date correctly.");
            }
            elseif($e->getMessage() === "Please Reschedule Main Task And Sub Task End Date To Continue"){
                throw new InvalidArgumentException("Please Reschedule Main Task And Sub Task End Date To Continue");
            }
            elseif($e->getMessage() === "Please Reschedule the End Date To Continue"){
                throw new InvalidArgumentException("Please Reschedule the End Date To Continue");
            }
            elseif($e->getMessage() === "Please Reschedule Sub Task End Date To Continue"){
                throw new InvalidArgumentException("Please Reschedule Sub Task End Date To Continue");
            }
            elseif($e->getMessage() === "Already Updated"){
                throw new InvalidArgumentException("Already updated");
            }else{
                throw new InvalidArgumentException("Unable To Post Data");
            }
        }
        DB::commit();
    }
    public static function UpdateTasks($request){
        DB::beginTransaction();
        try{
            $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
            $whereConditions =[
                ['workspace_id','=',$request->workspace_id],
                ['company_id', '=', $request->company_id],
                ['order_id','=',$request->order_id]
            ];

            // $aldreadyExists = OrderTask::where($whereConditions)->get();
            // if(!empty($aldreadyExists)){
            //     OrderTask::where($whereConditions)->delete();
            // }

            /*Update the Existing Template Id */
            $updateTaskArr=[];
            $updateTaskArr['template_id'] = $request->template_id;
            OrderTask::where($whereConditions)->update($updateTaskArr);

            /******************** To add template id in ORDERS Table ******************/
            $addTemplateToOrder = Order::where('id',$request->order_id)->first();
            $addTemplateToOrder->order_task_template = $request->template_id;
            $addTemplateToOrder->save();

            $orderProductionArr = [];
            $orderProductionArr['user_id']= $companyDetails->user_id;
            $orderProductionArr['company_id']= $request->company_id;
            $orderProductionArr['workspace_id']= $request->workspace_id;
            $orderProductionArr['staff_id']= $request->staff_id ?? 0;
            $orderProductionArr['order_id']= $request->order_id;
            $orderProductionArr['template_id']= $request->template_id;
            $orderProductionArr['created_by']= $companyDetails->user_id;
            $orderProductionArr['created_user_type']= "User";
            $orderProductionArr['task_accomplished_date']= $request->task_accomplished_date??null;
            $orderProductionArr['reschedule_reason']= $request->reschedule_reason ?? '';
            $orderProductionArr['reschedule_order_task_data_id']= $request->reschedule_order_task_data_id ?? 0;
            $orderProductionArr['rescheduled']= $request->rescheduled??null;
            $orderProductionArr['category_contacts']= $request->category_contacts ?? '';
            $orderProductionArr['task_contacts']= $request->task_contacts ?? '';
            //dd($request->template_data);
            // foreach($request->template_data as $templates){
                foreach($request->template_data as $key=>$template){
                    //dd($template);
                    $orderProductionArr['cat_title']= $key;
                    //echo $key; exit;
                    foreach($template as $data){
                       // dd($data);
                        $whereConditionsTask =[
                            ['workspace_id','=',$request->workspace_id],
                            ['company_id', '=', $request->company_id],
                            ['order_id','=',$request->order_id],
                            ['cat_title','=',(string)$orderProductionArr['cat_title']],
                            ['task_title','=', (string)$data['title']]
                        ];
                        $aldreadyExists = OrderTask::where($whereConditionsTask)->count();
                        //echo $aldreadyExists."<br>";
                        if($aldreadyExists==0){
                            $orderProductionArr['task_title']= $data['title'];
                            $orderProductionArr['task_schedule_start_date']= $data['startdate'] != "" ? date('Y-m-d',strtotime($data['startdate'])) : NULL;
                            $orderProductionArr['actual_start_date']= $data['startdate'] != "" ? date('Y-m-d',strtotime($data['startdate'])) : NULL;
                            $orderProductionArr['task_schedule_end_date']= $data['enddate'] != "" ? date('Y-m-d',strtotime($data['enddate'])) : NULL;
                            $orderProductionArr['task_pic']= $data['pic_id'] != "" ? $data['pic_id'] : "0";
                            $orderProductionArr['created_at']=date('Y-m-d H:i:s');
                            $orderProductionArr['updated_at']=date('Y-m-d H:i:s');
                            OrderTask::insert($orderProductionArr);
                        }
                    }
                }
            // }

        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArnException($e->getMessage());
        }
        DB::commit();
    }
}
