<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class UserPreferences extends Model
{
    use HasFactory;

    protected $table = 'user_preferences';

    public static function getStaffPreference($staff){
        $preference = UserPreferences::where('company_id',$staff->company_id)->where('workspace_id',$staff->workspace_id)
        ->where('staff_id',$staff->id)
        ->join('language','user_preferences.language_id','language.id')
        ->select('language.lang_code as language','user_preferences.date_format','user_preferences.time_zone_format','user_preferences.dashboard_widget_ids'
        ,'user_preferences.language_id')
        ->first();
        return $preference;
    }

    public static function getUserPreference($user){
        $preference = UserPreferences::where('company_id',$user->company_id)->where('user_id',$user->id)->first();
        return $preference;
    }

    /* Get DateFormat */
    public static function getTheDateFormat($type,$whereCondition){
        if($type === "User"){
            $user = User::where($whereCondition)->first();
            $dateFormats = (UserPreferences::where('user_id',$user->id)->first());
        }
        else if($type === "Staff"){
            $user = Staff::where($whereCondition)->first();
            $dateFormats = (UserPreferences::where('staff_id',$user->id)->first());
        }
        if(empty($dateFormats) || $dateFormats->date_format == NUll || $dateFormats->date_format == "" || empty($dateFormats->date_format)){
            $dateFormat = "d M Y";
        }else{
            $dates =$dateFormats->date_format;
            $dateFormat = $dates;
        }
        return $dateFormat;
    }

    /* For Getting Data For Dashboard Widgets selected info */
    public static function dashboardWidgetSettingsOrders($request){
        $whereCondition =[
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id],
            ['orders.step_level','=',6],
            // ['orders.status','=',"1"]
        ];
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['widget_id','=',$request->widget_id],
        ];
        if($request->staff_id > 0){
            $whereCondition1= $whereCondition2 =[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id]
            ];
            $whereConditions[] = ['staff_id','=',$request->staff_id];
            $staffRoleHasPermission = Staff::where('id',$request->staff_id)->select('role_id','company_id')->first();
            $whereCondition1[]=['role_id','=',$staffRoleHasPermission->role_id];
            $whereCondition1[]=['company_id','=',$staffRoleHasPermission->company_id];
            $whereCondition1[]=['permission_id','=','19'];
            $isPermissionGiven = RolesAndPermissions::where($whereCondition1)->first();
            if(empty($isPermissionGiven)){
                $whereCondition2[]=['staff_id','=',$request->staff_id];
                $involedOrders = OrderContacts::where($whereCondition2)->select('order_id')->get();
                $theOrders = [];
                $whereCondition3 = [
                    ['orders.company_id','=',$request->company_id],
                    ['orders.workspace_id','=',$request->workspace_id],
                ];
                foreach($involedOrders as $order){
                    $whereCondition3['orders.id']=$order->order_id;
                    $theOrder = Order::where($whereCondition3)
                            ->leftjoin('order_factory','order_factory.id','orders.factory_id')
                            ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
                            ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
                            ->select('orders.id','order_factory.name as factoryName','order_buyer.name as buyerName','order_pcu.name as pcuName',
                            'order_no','style_no','cutting_start_date','orders.status as status')
                            ->first();
                    if(!empty($theOrder)){
                        $theOrders[]=$theOrder;
                    }
                }
                $orders = $theOrders;
            }else{
                $orders = Order::where($whereCondition)
                    ->leftjoin('order_factory','order_factory.id','orders.factory_id')
                    ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
                    ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
                    ->select('orders.id','order_factory.name as factoryName','order_buyer.name as buyerName','order_pcu.name as pcuName',
                    'order_no','style_no','cutting_start_date','orders.status as status')
                    ->get();
            }
        }else{
            $whereConditions[] = ['user_id','=',$request->user_id];
            $orders = Order::where($whereCondition)
                    ->leftjoin('order_factory','order_factory.id','orders.factory_id')
                    ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
                    ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
                    ->select('orders.id','order_factory.name as factoryName','order_buyer.name as buyerName','order_pcu.name as pcuName',
                    'order_no','style_no','cutting_start_date','orders.status as status')
                    ->get();
        }
        $dataArr=[];
        $dataArr['orders']=$orders;
        $isWidgetDataPresent = DashboardSettings::where($whereConditions)->first();
        if(!empty($isWidgetDataPresent)){
            // foreach($isWidgetDataPresent as $widget){
            //     $arr=[];
            //     $arr['name'] = config('constant.dashboard_modules.'.$widget->widget_id);
            //     $arr['selectedOrders']=explode(",",$widget->order_ids);
            //     $dataArr['orders'][] = $arr;
            // }
            $data=explode(",",$isWidgetDataPresent->order_ids);
        }
        $dataArr['selectedOrders'] = !empty($data) ? $data : [];

        return $dataArr;
    }

    /*  */
    public static function addDashboardWidgetsOrders($request){
        $dashboardArr=[];
        $dashboardArr['company_id'] = $request->company_id;
        $dashboardArr['workspace_id'] = $request->workspace_id;
        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
        ];
        if($request->staff_id == "0"){
            $dashboardArr['user_id'] = $request->user_id;
            $whereConditions[] = ['user_id','=',$request->user_id];
        }
        else if($request->staff_id > "0"){
            $dashboardArr['staff_id'] = $request->staff_id;
            $whereConditions[] = ['staff_id','=',$request->staff_id];
        }
        $whereConditions[] = ['widget_id','=',$request->widget_id];
        $isWidgetDataPresent = DashboardSettings::where($whereConditions)->first();
        if(empty($isWidgetDataPresent)){
            $dashboardArr['widget_id'] = $request->widget_id;
            $dashboardArr['order_ids'] = implode(",",$request->dashboardOrders);
            $dashboardArr['created_at'] = date('Y-m-d H:i:s');
            DashboardSettings::insert($dashboardArr);
        }else{
            $isWidgetDataPresent->order_ids = implode(",",$request->dashboardOrders);
            $isWidgetDataPresent->save();
        }
    }

    public static function getSelectDashboardWidgets($whereConditions){
        $selectedWidgets = UserPreferences::where($whereConditions)->first();
        if($selectedWidgets===null){
            $dashBoard = [];
        }else{
            $dashBoard = explode(",",$selectedWidgets->dashboard_widget_ids);
        }

        return $dashBoard;
    }

    public static function addDashboardWidget($request,$whereConditions){
        $selectedWidgets = UserPreferences::where($whereConditions)->first();
        DB::beginTransaction();
        try{
            if(!empty($selectedWidgets)){
                $selectedWidgets->dashboard_widget_ids = implode(",",$request->data);
                $selectedWidgets->save();
            }else{
                $dashboardsettings=[];
                $dashboardsettings['company_id']= $request->company_id;
                $dashboardsettings['workspace_id']= $request->workspace_id;
                $dashboardsettings['user_id']= $request->user_id;
                if(isset($request->staff_id) && $request->staff_id>0){
                    $dashboardsettings['staff_id']= $request->staff_id;
                    $dashboardsettings['user_id']= 0;
                }else{
                    $dashboardsettings['staff_id']= 0;
                    $dashboardsettings['user_id']= $request->user_id;
                }

                $dashboardsettings['dashboard_widget_ids']= implode(",",$request->data);
                UserPreferences::insert($dashboardsettings);
            }
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException('Unable to post Data');
        }
        DB::commit();
    }

    public static function addUserPreference($request,$companyDetails,$whereConditions){
        DB::beginTransaction();
        try{
            $checkEntry = UserPreferences::where($whereConditions)->first();
            if(empty($checkEntry)){
                $userPreferencesArr = [];
                $userPreferencesArr['company_id'] = $companyDetails->id;
                $userPreferencesArr['workspace_id'] =$request->workspaceId ;
                $userPreferencesArr['user_id'] = $companyDetails->user_id;
                $userPreferencesArr['staff_id'] = '0';
                if(isset($request->date_format) && $request->date_format != ""){
                $userPreferencesArr['date_format'] = $request->date_format;
                }
                if(isset($request->languageId) && $request->languageId!=''){
                $userPreferencesArr['language_id'] = $request->languageId;
                }
                if(isset($request->timezoneId) && $request->timezoneId!=''){
                $userPreferencesArr['time_zone_format'] = $request->timezoneId;
                }
                $userPreferencesArr['created_at'] = date('Y-m-d H:i:s');
                $userPreferencesArr['updated_at'] = date('Y-m-d H:i:s');
                UserPreferences::insert($userPreferencesArr);
            }
            else{
                $checkEntry['company_id'] = $companyDetails->id;
                $checkEntry['workspace_id'] =$request->workspaceId ;
                $checkEntry['user_id'] = $companyDetails->user_id;
                $checkEntry['staff_id'] = '0';
                if(isset($request->date_format) && $request->date_format != ""){
                $checkEntry['date_format'] = $request->date_format;
                }
                if(isset($request->languageId) && $request->languageId!=''){
                $checkEntry['language_id'] = $request->languageId;
                }
                if(isset($request->timezoneId) && $request->timezoneId!=''){
                $checkEntry['time_zone_format'] = $request->timezoneId;
                }
                $checkEntry['created_at'] = date('Y-m-d H:i:s');
                $checkEntry['updated_at'] = date('Y-m-d H:i:s');
                $checkEntry->save();
            }
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException('Unable to post Data');
        }
        DB::commit();
    }

    public static function getUserSettingPreferences($whereConditions){
        $userPreference = UserPreferences::where($whereConditions)->first();
        return $userPreference;
    }
}
