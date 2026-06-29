<?php

namespace App\Http\Controllers\Website\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderSku;
use App\Models\OrderTask;
use App\Models\User;
use App\Common\CommonApp;
use Carbon\Carbon;
use App\Models\OrderContacts;
use App\Models\UpdateSkuQuantity;
use App\Models\OrderProduction;
use Illuminate\Support\Facades\Validator;
use App\Models\FabricType;
use App\Models\OrderAddSpec;
use Illuminate\Support\Facades\DB;
use App\Models\OrderBOM;


class ViewOrder extends Controller
{
    /**
     * Handle the incoming request.
     * Get the Full Details of the order
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id'=>'required',
            'workspace_id'=>'required',
            'user_id'=>'required',
            //'staff_id'=>'required',
            'order_id'=>'required',
        ]);
        if($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereCondition =[
            ['orders.company_id','=',$request->company_id],
            ['orders.workspace_id','=',$request->workspace_id],
            ['orders.id','=',$request->order_id],
            ['orders.user_id','=',$request->user_id],
        ];
        $whereConditionsku =[
            ['order_sku.company_id','=',$request->company_id],
            ['order_sku.workspace_id','=',$request->workspace_id],
            ['order_sku.order_id','=',$request->order_id],
           // ['order_sku.user_id','=',$request->user_id],
        ];
        $whereConditioncontact =[
            ['order_contacts.company_id','=',$request->company_id],
            ['order_contacts.workspace_id','=',$request->workspace_id],
            ['order_contacts.order_id','=',$request->order_id],
            ['order_contacts.user_id','=',$request->user_id],
        ];
        $whereConditionskuqty =[
            ['update_sku_quantities.company_id','=',$request->company_id],
            ['update_sku_quantities.workspace_id','=',$request->workspace_id],
            ['update_sku_quantities.order_id','=',$request->order_id],
            ['update_sku_quantities.user_id','=',$request->user_id],
        ];

        $whereConditions =[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->order_id],
            ['user_id','=',$request->user_id],
        ];
        $whereConditionsfile =[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->order_id],
            ['user_id','=',$request->user_id],
            ['status','=','1'],
        ];

        $whereConditionOrderTasks =[
            ['order_task_data.company_id','=',$request->company_id],
            ['order_task_data.workspace_id','=',$request->workspace_id],
            ['order_task_data.order_id','=',$request->order_id],
            ['order_task_data.user_id','=',$request->user_id],
            ['order_task_data.is_subtask','=',0],
        ];

        $basicInfo = Order::where($whereCondition)
        ->join('workspace','workspace.id','orders.workspace_id')
        ->leftjoin('order_factory','order_factory.id','orders.factory_id')
        ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
        ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
        ->leftjoin('order_article_name','order_article_name.id','orders.article_id')
        ->leftjoin('order_category','order_category.id','orders.category_id')
        ->leftjoin('income_terms','income_terms.id','orders.income_terms')
        ->leftjoin('multiple_delivery_dates','multiple_delivery_dates.order_id','orders.id')
        ->leftjoin('order_comments','orders.id','order_comments.order_id')
        ->select('orders.order_no as order','orders.factory_id','orders.pcu_id','orders.buyer_id','orders.style_no as style','orders.total_quantity as quantity','orders.units',
        'workspace.name as workspace','order_pcu.name as pcu','order_factory.name as factory','order_buyer.name as buyer','order_category.name as catname',
        'order_article_name.name as articlename','orders.order_task_template','orders.total_quantity','orders.no_of_deliverys','orders.fabric_id',
        'orders.status','orders.cutting_start_date','orders.cutting_end_date','orders.sewing_start_date','orders.sewing_end_date','orders.inquiry_date',
        'orders.packing_start_date','orders.packing_end_date','orders.step_level','orders.order_price','orders.currency_type','orders.order_priority',
        'is_tolerance_req','tolerance_volume','tolerance_perc','income_terms.name as incoTerms','orders.delivery_date',
        DB::raw('GROUP_CONCAT(multiple_delivery_dates.delivery_date) as delivery_dates'),
        'order_comments.comments','order_comments.document_url','order_comments.audio_url','order_comments.video_url')
        ->get();

        $orderSkuDetails = OrderSku::select('order_sku.sku_color_id','color.name as colorname','order_sku.sku_size_id',
        'size.name as sizename','sku_quantity')->where($whereConditionsku)->leftjoin('color','color.id','order_sku.sku_color_id')
        ->leftjoin('size','size.id','order_sku.sku_size_id')->get();

        $sizeDetails = OrderSku::where($whereConditionsku)
        ->select('order_sku.sku_size_id','size.name as sizename',DB::raw('SUM(sku_quantity) as sizeTotal'))
        ->leftjoin('size','size.id','order_sku.sku_size_id')
        ->orderby('order_sku.id','ASC')
        ->groupBy('order_sku.sku_size_id')
        ->get();
        $colorDetails = OrderSku::where($whereConditionsku)
        ->select('order_sku.sku_color_id','color.name as colorname',DB::raw('SUM(sku_quantity) as colorTotal'))
        ->leftjoin('color','color.id','order_sku.sku_color_id')
        ->orderby('order_sku.id','ASC')
        ->groupBy('order_sku.sku_color_id')
        ->get();

        $orderContacts = OrderContacts::where($whereConditioncontact)
        ->leftjoin('staff','staff.id','order_contacts.staff_id')
        ->leftjoin('roles','roles.id','staff.role_id')
        ->select('order_contacts.staff_id','staff.first_name','staff.last_name','staff.email','roles.name','staff.user_type','staff.is_confidentional')
        ->get();

        //$taskDetails = OrderTask::select('order_task_data.id','order_task_data.cat_title','order_task_data.task_title','order_task_data.task_schedule_start_date','order_task_data.task_schedule_end_date','order_task_data.task_accomplished_date','staff.first_name','staff.last_name','staff.email')->leftjoin('staff','staff.id','order_task_data.task_pic')->where($whereConditionOrderTasks)->get();
        $taskDetails = $this->getTaskDetails($whereConditionOrderTasks);
        $prodDetails = OrderProduction::where($whereConditions)->get();

        $completd_production_qty = UpdateSkuQuantity::where('order_id','=',$request->order_id)
                            ->select('order_id','type_of_production', DB::raw('SUM(updated_quantity) as completed_quantity'))
                            ->groupBy('type_of_production')->get();
        $total_production_qty = OrderProduction::where('order_id','=',$request->order_id)
                            ->select('order_id','type_of_production', DB::raw('SUM(target_value) as total'))
                            ->groupBy('type_of_production')->get();
       // dd($total_production_qty);
       // $dayByDaySKUupdates = UpdateSkuQuantity::select('update_sku_quantities.color_id','color.name','update_sku_quantities.size_id','size.name','update_sku_quantities.type_of_production','update_sku_quantities.updated_quantity','update_sku_quantities.sku_date')->where($whereConditionskuqty)->leftjoin('color','color.id','update_sku_quantities.color_id')->leftjoin('size','size.id','update_sku_quantities.size_id')->get();
       $fabric = FabricType::select('id','name')->where('id','=',$basicInfo[0]['fabric_id'])->get();
       $getTaskFiles=OrderAddSpec::select('filename','orginalfilename','filepath','filesize','fileorder','status')->where($whereConditionsfile)->get();
       $getColorDetails=$this->getSKUDetails($whereConditionsku,'color');
       $getSizeDetails=$this->getSKUDetails($whereConditionsku,'size');
        $dataArr=[];
        $dataArr['completd_production_qty'] =$completd_production_qty;
        $dataArr['total_production_qty'] =$total_production_qty;
        $dataArr['basicInfo'] =$basicInfo;
        $dataArr['fabrictype'] = $fabric;
        $dataArr['skuDetails'] =$orderSkuDetails;
        $dataArr['colorTotal'] =$colorDetails;
        $dataArr['sizeTotal'] =$sizeDetails;
        $dataArr['skucolor'] =$getColorDetails;
        $dataArr['skusize'] =$getSizeDetails;
        $dataArr['orderContact'] =$orderContacts;
        $dataArr['taskDetails']= $taskDetails;
        $dataArr['taskFiles']= $getTaskFiles;
        $dataArr['productionDetails']= $prodDetails;
        $dataArr['bomcount']= OrderBOM::select("id")->where("order_id",$request->order_id)->count();
        if(count($basicInfo)>0){
            $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$dataArr],200);
            return CommonApp::webEncrypt($res);
        }else{
            $res = json_encode(["status_code"=>201,"status" =>"error","msg"=>"Data Not Found"],201);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Get the Task Details */
    public function getTaskDetails($whereConditionOrderTasks){
        $taskDetails = OrderTask::select('order_task_data.id','order_task_data.cat_title','order_task_data.task_title','order_task_data.task_schedule_start_date','order_task_data.task_schedule_end_date',
        'order_task_data.task_accomplished_date','order_task_data.task_pic','staff.first_name','staff.last_name','staff.email')
        ->leftjoin('staff','staff.id','order_task_data.task_pic')->where($whereConditionOrderTasks)->get();
        $arr=array();$i=$j=$k=$l=0; $catTitle=''; $subArr = array();

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
                $subArr[$j]["end_date"] = $tasks->task_schedule_end_date;
                $subArr[$j]["accomplished_date"] = $tasks->task_accomplished_date;
                $subArr[$j]["pic_id"] = $tasks->task_pic;
                $subArr[$j]["first_name"] = $tasks->first_name;
                $subArr[$j]["last_name"] = $tasks->last_name;
                $subArr[$j]["email"] = $tasks->email;
                $j++;
            }
            if( !empty($subArr) ){
                $arr[$k]["task_title"] = $tasks->cat_title;
                $arr[$k]["task_subtitles"] = $subArr;
            }
            $i++;
        }

        return $arr;
    }

    /* Get thes SKU Details */
    public function getSKUDetails($whereConditionsku,$type){
        if($type=='color'){
        $orderSkuDetails = OrderSku::select('color.id','color.name')->where($whereConditionsku)->leftjoin('color','color.id','order_sku.sku_color_id')->orderby('order_sku.id','ASC')->groupBy('color.id')->get();
        }else{
            $orderSkuDetails = OrderSku::select('size.id','size.name')->where($whereConditionsku)->leftjoin('size','size.id','order_sku.sku_size_id')->orderby('order_sku.id','ASC')->groupBy('order_sku.sku_size_id')->get();
        }
        return $orderSkuDetails;
    }
}
