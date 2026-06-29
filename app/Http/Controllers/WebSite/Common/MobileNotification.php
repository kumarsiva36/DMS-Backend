<?php

namespace App\Http\Controllers\WebSite\Common;


use App\Http\Controllers\Controller;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Client;
use App\Models\Staff;
use App\Models\TechPack;
use App\Models\User;
use Illuminate\Support\Facades\DB;

ini_set('memory_limit',-1);
class MobileNotification extends Controller
{
    /* Function For sending tasks that are due today */
    public static function mobileTasksDueToday(){
        $orders = Order::where('orders.status','=',"1")
            ->where('task_schedule_end_date','=',date('Y-m-d'))
            ->where('order_task_data.task_accomplished_date',NULL)
            ->where('order_task_data.staff_id','!=','0')
            //->where('task_schedule_end_date','<=',date('Y-m-d',strtotime("-10 day")))
            ->join('order_task_data','order_task_data.order_id','orders.id')
            ->select('orders.id','order_no','style_no','orders.company_id','orders.workspace_id','orders.user_id','task_title','order_task_data.staff_id')
            ->orderby('orders.user_id','asc')
            ->orderby('order_task_data.staff_id','asc')
            ->limit(50)
            ->get();
        //dd($orders);
        $ord_id = $orders[0]->id ?? 0;
        $staff_id = $orders[0]->staff_id ?? 0;
        $user_id = $orders[0]->user_id ?? 0;
        $ord_arr = $user_arr = $staff_arr =[];
        $user_i = $staff_i = $i= 0;
        foreach ($orders as $ord){
            //echo $ord_id."==>".$ord->id."\n";
            $i++;
            if($ord_id == $ord->id){
                $ord_arr['order_id']=$ord->id;
                $ord_arr['order_no']=$ord->order_no;
                $ord_arr['style_no']=$ord->style_no;
                $ord_arr['company_id']=$ord->company_id;
                $ord_arr['workspace_id']=$ord->workspace_id;
                $ord_arr['type']='task';

                $user_arr[$user_i]['task_title']=$ord->task_title;
                $user_arr[$user_i]['user_id']=$ord->user_id;
                $user_i++;

                if($staff_id == $ord->staff_id){
                    $staff_arr[$staff_i]['task_title']=$ord->task_title;
                    $staff_arr[$staff_i]['staff_id']=$ord->staff_id;
                    $staff_i++;
                }else{
                    try{
                        MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'staff','DueToday');
                    }catch(Exception $e){
                    };
                    $staff_arr =[];
                    $staff_i =0;
                    $staff_id = $ord->staff_id;
                    $staff_arr[$staff_i]['task_title']=$ord->task_title;
                    $staff_arr[$staff_i]['staff_id']=$ord->staff_id;
                    $staff_i++;
                }
            }else{
                try{
                    MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'user','DueToday');
                }catch(Exception $e){
                };
                if(!empty($staff_arr)){
                    try{
                        MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'staff','DueToday');
                    }catch(Exception $e){
                    };
                }
                $ord_id = $ord->id ?? 0;
                $staff_id = $ord->staff_id ?? 0;
                $user_id = $ord->user_id ?? 0;
                $ord_arr = $user_arr = $staff_arr =[];
                $user_i = $staff_i =0;

                $ord_arr['order_id']=$ord->id;
                $ord_arr['order_no']=$ord->order_no;
                $ord_arr['style_no']=$ord->style_no;
                $ord_arr['company_id']=$ord->company_id;
                $ord_arr['workspace_id']=$ord->workspace_id;
                $ord_arr['type']='task';

                $user_arr[$user_i]['task_title']=$ord->task_title;
                $user_arr[$user_i]['user_id']=$ord->user_id;
                $user_i++;
                if($staff_id == $ord->staff_id){
                    $staff_arr[$staff_i]['task_title']=$ord->task_title;
                    $staff_arr[$staff_i]['staff_id']=$ord->staff_id;
                    $staff_i++;
                }else{
                    $staff_arr =[];
                    $staff_i =0;
                    $staff_id = $ord->staff_id;
                    $staff_arr[$staff_i]['task_title']=$ord->task_title;
                    $staff_arr[$staff_i]['staff_id']=$ord->staff_id;
                    $staff_i++;
                }
            }

            if(count($orders)==$i){
               if(!empty($user_arr)){
                    try{
                        MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'user','DueToday');
                    }catch(Exception $e){
                    };
               }
               if(!empty($staff_arr)){
                try{
                    MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'staff','DueToday');
                }catch(Exception $e){
                };
           }
            }
        }
        exit('success');
    }

    /* Function For sending tasks that are start today */
    public static function mobileTasksStartToday(){
        $orders = Order::where('orders.status','=',"1")
            ->where('task_schedule_start_date','=',date('Y-m-d'))
            ->where('order_task_data.task_accomplished_date',NULL)
            ->where('order_task_data.staff_id','!=','0')
            ->join('order_task_data','order_task_data.order_id','orders.id')
            ->select('orders.id','order_no','style_no','orders.company_id','orders.workspace_id','orders.user_id','task_title','order_task_data.staff_id')
            ->orderby('orders.user_id','asc')
            ->orderby('order_task_data.staff_id','asc')
            //->limit(50)
            ->get();
        //dd($orders);
        $ord_id = $orders[0]->id ?? 0;
        $staff_id = $orders[0]->staff_id ?? 0;
        $user_id = $orders[0]->user_id ?? 0;
        $ord_arr = $user_arr = $staff_arr =[];
        $user_i = $staff_i = $i= 0;
        foreach ($orders as $ord){
            //echo $ord_id."==>".$ord->id."\n";
            $i++;
            if($ord_id == $ord->id){
                $ord_arr['order_id']=$ord->id;
                $ord_arr['order_no']=$ord->order_no;
                $ord_arr['style_no']=$ord->style_no;
                $ord_arr['company_id']=$ord->company_id;
                $ord_arr['workspace_id']=$ord->workspace_id;
                $ord_arr['type']='task';

                $user_arr[$user_i]['task_title']=$ord->task_title;
                $user_arr[$user_i]['user_id']=$ord->user_id;
                $user_i++;

                if($staff_id == $ord->staff_id){
                    $staff_arr[$staff_i]['task_title']=$ord->task_title;
                    $staff_arr[$staff_i]['staff_id']=$ord->staff_id;
                    $staff_i++;
                }else{
                    try{
                        MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'staff','StartToday');
                    }catch(Exception $e){
                    };
                    $staff_arr =[];
                    $staff_i =0;
                    $staff_id = $ord->staff_id;
                    $staff_arr[$staff_i]['task_title']=$ord->task_title;
                    $staff_arr[$staff_i]['staff_id']=$ord->staff_id;
                    $staff_i++;
                }
            }else{
                try{
                    MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'user','StartToday');
                }catch(Exception $e){
                };
                if(!empty($staff_arr)){
                    try{
                        MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'staff','StartToday');
                    }catch(Exception $e){
                    };
                }
                $ord_id = $ord->id ?? 0;
                $staff_id = $ord->staff_id ?? 0;
                $user_id = $ord->user_id ?? 0;
                $ord_arr = $user_arr = $staff_arr =[];
                $user_i = $staff_i =0;

                $ord_arr['order_id']=$ord->id;
                $ord_arr['order_no']=$ord->order_no;
                $ord_arr['style_no']=$ord->style_no;
                $ord_arr['company_id']=$ord->company_id;
                $ord_arr['workspace_id']=$ord->workspace_id;
                $ord_arr['type']='task';

                $user_arr[$user_i]['task_title']=$ord->task_title;
                $user_arr[$user_i]['user_id']=$ord->user_id;
                $user_i++;
                if($staff_id == $ord->staff_id){
                    $staff_arr[$staff_i]['task_title']=$ord->task_title;
                    $staff_arr[$staff_i]['staff_id']=$ord->staff_id;
                    $staff_i++;
                }else{
                    $staff_arr =[];
                    $staff_i =0;
                    $staff_id = $ord->staff_id;
                    $staff_arr[$staff_i]['task_title']=$ord->task_title;
                    $staff_arr[$staff_i]['staff_id']=$ord->staff_id;
                    $staff_i++;
                }
            }

            if(count($orders)==$i){
               if(!empty($user_arr)){
                    try{
                        MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'user','StartToday');
                    }catch(Exception $e){
                    };
               }
               if(!empty($staff_arr)){
                try{
                    MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'staff','StartToday');
                }catch(Exception $e){
                };
           }
            }
        }
        exit('success');
    }

    /* Function For sending tasks that are start today */
    public static function mobileDelayedTasks(){
        $orders = Order::where('orders.status','=',"1")
            ->where('task_schedule_end_date','<',date('Y-m-d'))
            ->where('order_task_data.task_accomplished_date',NULL)
            ->where('order_task_data.staff_id','!=','0')
            ->join('order_task_data','order_task_data.order_id','orders.id')
            ->select('orders.id','order_no','style_no','orders.company_id','orders.workspace_id','orders.user_id','task_title','order_task_data.staff_id'
            ,DB::raw('DATEDIFF(task_schedule_end_date, NOW()) as noOfDays'))
            ->orderby('orders.user_id','asc')
            ->orderby('order_task_data.staff_id','asc')
            //->limit(50)
            ->get();
        //dd($orders);
        $ord_id = $orders[0]->id ?? 0;
        $staff_id = $orders[0]->staff_id ?? 0;
        $user_id = $orders[0]->user_id ?? 0;
        $ord_arr = $user_arr = $staff_arr =[];
        $user_i = $staff_i = $i= 0;
        foreach ($orders as $ord){
            //echo $ord_id."==>".$ord->id."\n";
            $i++;
            if($ord_id == $ord->id){
                $ord_arr['order_id']=$ord->id;
                $ord_arr['order_no']=$ord->order_no;
                $ord_arr['style_no']=$ord->style_no;
                $ord_arr['company_id']=$ord->company_id;
                $ord_arr['workspace_id']=$ord->workspace_id;
                $ord_arr['type']='task';

                $user_arr[$user_i]['task_title']=$ord->task_title ." (".abs($ord->noOfDays)." days)";
                $user_arr[$user_i]['user_id']=$ord->user_id;
                $user_i++;

                if($staff_id == $ord->staff_id){
                    $staff_arr[$staff_i]['task_title']=$ord->task_title ." (".abs($ord->noOfDays)." days)";
                    $staff_arr[$staff_i]['staff_id']=$ord->staff_id;
                    $staff_i++;
                }else{
                    try{
                        MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'staff','DelayTask');
                    }catch(Exception $e){
                    };
                    $staff_arr =[];
                    $staff_i =0;
                    $staff_id = $ord->staff_id;
                    $staff_arr[$staff_i]['task_title']==$ord->task_title ." (".abs($ord->noOfDays)." days)";
                    $staff_arr[$staff_i]['staff_id']=$ord->staff_id;
                    $staff_i++;
                }
            }else{
                try{
                    MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'user','DelayTask');
                }catch(Exception $e){
                };
                if(!empty($staff_arr)){
                    try{
                        MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'staff','DelayTask');
                    }catch(Exception $e){
                    };
                }
                $ord_id = $ord->id ?? 0;
                $staff_id = $ord->staff_id ?? 0;
                $user_id = $ord->user_id ?? 0;
                $ord_arr = $user_arr = $staff_arr =[];
                $user_i = $staff_i =0;

                $ord_arr['order_id']=$ord->id;
                $ord_arr['order_no']=$ord->order_no;
                $ord_arr['style_no']=$ord->style_no;
                $ord_arr['company_id']=$ord->company_id;
                $ord_arr['workspace_id']=$ord->workspace_id;
                $ord_arr['type']='task';

                $user_arr[$user_i]['task_title']=$ord->task_title ." (".abs($ord->noOfDays)." days)";
                $user_arr[$user_i]['user_id']=$ord->user_id;
                $user_i++;
                if($staff_id == $ord->staff_id){
                    $staff_arr[$staff_i]['task_title']=$ord->task_title ." (".abs($ord->noOfDays)." days)";
                    $staff_arr[$staff_i]['staff_id']=$ord->staff_id;
                    $staff_i++;
                }else{
                    $staff_arr =[];
                    $staff_i =0;
                    $staff_id = $ord->staff_id;
                    $staff_arr[$staff_i]['task_title']=$ord->task_title ." (".abs($ord->noOfDays)." days)";
                    $staff_arr[$staff_i]['staff_id']=$ord->staff_id;
                    $staff_i++;
                }
            }

            if(count($orders)==$i){
                if(!empty($user_arr)){
                    try{
                        MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'user','DelayTask');
                    }catch(Exception $e){
                    };
                }
                if(!empty($staff_arr)){
                    try{
                        MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'staff','DelayTask');
                    }catch(Exception $e){
                    };
                }
            }
        }
        exit('success');
    }

    public static function send_techpack_push_notification($request){
        $user_id = $request->staff_id > 0 ? $request->user_id : 0;
        $staff_id = $request->staff_id;
        $ord_arr= $user_arr=$staff_arr = [];

        $tec = TechPack::select("po_no", "style_no")->where('id',$request->techpack_id)->first();

        $ord_arr['order_id']=$request->techpack_id;
        $ord_arr['order_no']=$tec['po_no'];
        $ord_arr['style_no']=$tec['style_no'];
        $ord_arr['techpack_type']=$request->techpack_type;
        $ord_arr['company_id']=$request->company_id;
        $ord_arr['workspace_id']=$request->workspace_id;
        $ord_arr['type']='techpack';

        if($user_id > 0){
            $user_arr[0]['user_id']=$user_id;
            try{
                MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'user','TechpackComment');
            }catch(Exception $e){
            };
        }else{
            $staffs = Staff::where('company_id',$request->company_id)->where('workspace_id',$request->workspace_id)->where('id','!=',$staff_id)->select('id')->get();
            foreach($staffs as $satff){
                $staff_arr[0]['staff_id']=$satff->id;
                try{
                    MobileNotification::send_push_notification($ord_arr,$user_arr,$staff_arr,'user','TechpackComment');
                }catch(Exception $e){
                };
            }
        }

    }

    public static function send_push_notification($ord_arr,$user_arr,$staff_arr,$type,$title){
        //dd($user_arr);
        if($type=='user'){
            $user_id = $user_arr[0]['user_id']; //9;

            $detail = User::where('users.id',$user_id)
                     ->leftjoin('user_preferences','users.id','user_preferences.user_id')
                     ->leftjoin('language','language.id','user_preferences.language_id')
                     ->select('fcm_token','device_details','language.lang_code')->first();
            if($detail->fcm_token!=NUll && $detail->fcm_token!=""){
                $device_details = json_decode($detail->device_details);
                $device = $device_details->platform;
                $message="";
                if($title=="DueToday")
                {
                    if($detail->lang_code=='en')
                        $title="Task Due Today";
                    else
                        $title="今日が期限のタスク";
                }elseif($title=="StartToday")
                {
                    if($detail->lang_code=='en')
                        $title="Task Starts Today";
                    else
                        $title="タスクは今日開始されます";
                }elseif($title=="DelayTask")
                {
                    if($detail->lang_code=='en')
                        $title="Delayed Tasks";
                    else
                        $title="遅延したタスク";
                }elseif($title=="TechpackComment")
                {
                    if($detail->lang_code=='en'){
                        $title="New Comment Added";
                        $message="New comment added for ".$ord_arr['techpack_type']." of PO#".$ord_arr['order_no']." & StyleNo: ".$ord_arr['style_no'];
                    }
                    else{
                        $title="新しいコメントが追加されました";
                        $message="PO#".$ord_arr['order_no']." の ".$ord_arr['techpack_type']." とスタイル番号: ".$ord_arr['style_no']." に新しいコメントが追加されました";
                    }
                }
                if($device=="Android"){
                    MobileNotification::android_notification($detail->fcm_token,$title,$message,$ord_arr,$user_arr);
                }
            }
        }else{
            $staff_id = $staff_arr[0]['staff_id'];

            $detail = Staff::where('staff.id',$staff_id)
                     ->leftjoin('user_preferences','staff.id','user_preferences.staff_id')
                     ->leftjoin('language','language.id','user_preferences.language_id')
                     ->select('fcm_token','device_details','language.lang_code')->first();
            if($detail->fcm_token!=NUll && $detail->fcm_token!=""){
                $device_details = json_decode($detail->device_details);
                $device = $device_details->platform;
                $message="";
                if($title=="DueToday")
                {
                    if($detail->lang_code=='en')
                        $title="Task Due Today";
                    else
                        $title="今日が期限のタスク";
                }elseif($title=="StartToday")
                {
                    if($detail->lang_code=='en')
                        $title="Task Starts Today";
                    else
                        $title="タスクは今日開始されます";
                }elseif($title=="OrderInvite")
                {
                    if($detail->lang_code=='en'){
                        $title="Order Invitation";
                        $message="You have been invited to participate in the Order (".$ord_arr['order_no'].")";
                    }
                    else{
                        $title="注文の招待状";
                        $message="You have been invited to participate in the Order (".$ord_arr['order_no'].")";
                    }
                }
                elseif($title=="TechpackComment")
                {
                    if($detail->lang_code=='en'){
                        $title="New Comment Added";
                        $message="New comment added for ".$ord_arr['techpack_type']." of PO#".$ord_arr['order_no']." & StyleNo: ".$ord_arr['style_no'];
                    }
                    else{
                        $title="新しいコメントが追加されました";
                        $message="PO#".$ord_arr['order_no']." の ".$ord_arr['techpack_type']." とスタイル番号: ".$ord_arr['style_no']." に新しいコメントが追加されました";
                    }
                }

                if($device=="Android"){
                    MobileNotification::android_notification($detail->fcm_token,$title,$message,$ord_arr,$staff_arr);
                }
            }
        }
    }

    public static function android_notification($deviceToken,$title,$message,$ord_arr,$user_arr){
        $url = 'https://fcm.googleapis.com/v1/projects/dms-notify-b927c/messages:send';
        $tokenRefersh=MobileNotification::refershToken();
        if($message!=""){
            $data = [
                'message' => [
                    'token' => $deviceToken,
                    'data' => [
                        "title" => $title,
                        "body" => $message,
                        "order_id"=>(string)$ord_arr['order_id'],
                        "company_id"=>(string)$ord_arr['company_id'],
                        "workspace_id"=>(string)$ord_arr['workspace_id'],
                        "type"=>$ord_arr['type'],
                    ]
                ],
            ];

        }else{
            $data = [
                'message' => [
                    'token' => $deviceToken,
                    // 'notification' => [
                    //     'title' => $title,
                    //     'body' => $message,
                    // ],
                    "data"=>[
                        "title" => $title,
                        "body" => $message,
                        "order_no"=>$ord_arr['order_no'],
                        "style_no"=>$ord_arr['style_no'],
                        "order_id"=>(string)$ord_arr['order_id'],
                        "company_id"=>(string)$ord_arr['company_id'],
                        "workspace_id"=>(string)$ord_arr['workspace_id'],
                        "type"=>$ord_arr['type'],
                        "task_array"=>json_encode($user_arr)
                    ],
                ],
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '. $tokenRefersh,
            'Content-Type' => 'application/json',
        ])->post($url, $data);

       //print_r($response);
        return 1;
    }

    public static function refershToken(){
        $client= new Client();
        $filePath = public_path() . '/dms-notify-b927c-firebase-adminsdk-9m4pi-0ed7c5aaa5.json';
        $client->setAuthConfig($filePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        return $token['access_token'];
    }


}
