<?php

namespace App\Models;

use App\Common\CommonApp;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderProduction extends Model
{
    use HasFactory;

    protected $table = 'order_production_data';

    public static function addProductionData($request){
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $companyId = $request->company_id;
        $workspaceId = $request->workspace_id;
        $orderId = $request->order_id;
        $order = Order::where('id',$orderId)->first();
        DB::beginTransaction();
        try{
            foreach ($request->prod_datas as  $productionData){
                if(!empty($productionData)){
                    if($productionData['type_of_production']=='Cut'){
                    $whereConditions =[
                        ['workspace_id','=',$request->workspace_id],
                        ['company_id', '=', $request->company_id],
                        ['order_id','=',$request->order_id],
                       // ['type_of_production','=',$productionData['type_of_production']]
                    ];
                }else{
                    $whereConditions =[
                        ['workspace_id','=',$request->workspace_id],
                        ['company_id', '=', $request->company_id],
                        ['order_id','=',$request->order_id],
                        ['type_of_production','=',$productionData['type_of_production']]
                    ];
                }
                    $aldreadyExists = OrderProduction::select("id")->where($whereConditions)->get();
                    if(!empty($aldreadyExists)){
                        OrderProduction::where($whereConditions)->delete();
                    }

                    if($productionData['start_date'] != "" && $productionData['end_date'] != ""){
                        if($productionData['type_of_production'] == 'Cut'){
                            $order->cutting_start_date = date('Y-m-d',strtotime($productionData['start_date']));
                            $order->cutting_end_date = date('Y-m-d',strtotime($productionData['end_date']));
                            $order->save();
                            /*Update Order Step Status*/
                            $addOrderArr=[];
                            $addOrderArr['step_level'] = '5';
                            Order::where('id',$orderId)->update($addOrderArr);
                        }
                        else if($productionData['type_of_production'] == 'Sew'){
                            $order->sewing_start_date = date('Y-m-d',strtotime($productionData['start_date']));
                            $order->sewing_end_date = date('Y-m-d',strtotime($productionData['end_date']));
                            $order->save();
                            /*Update Order Step Status*/
                            $addOrderArr=[];
                            $addOrderArr['step_level'] = '5';
                            Order::where('id',$orderId)->update($addOrderArr);
                        }
                        else if($productionData['type_of_production'] == 'Pack'){
                            $order->packing_start_date = date('Y-m-d',strtotime($productionData['start_date']));
                            $order->packing_end_date = date('Y-m-d',strtotime($productionData['end_date']));
                            $order->save();
                            /*Update Order Step Status*/
                            $addOrderArr=[];
                            $addOrderArr['step_level'] = '6';
                            Order::where('id',$orderId)->update($addOrderArr);
                        }
                        $orderProductionArr = [];
                        $orderProductionArr['user_id']= $companyDetails->user_id;
                        $orderProductionArr['company_id']= $companyId;
                        $orderProductionArr['workspace_id']= $workspaceId;
                        $orderProductionArr['order_id']= $orderId;
                        $orderProductionArr['type_of_production']= $productionData['type_of_production'];
                        if(!empty($productionData['prod_data'])){
                            foreach ($productionData['prod_data'] as $prodData){
                                $orderProductionArr['date_of_production']= date('Y-m-d',strtotime($prodData['date_of_production']));
                                $orderProductionArr['target_value']= $prodData['target_value'];
                                $orderProductionArr['holiday_flag']= $prodData['holiday_flag'];
                                $orderProductionArr['holiday_detail']= $prodData['holiday_detail'];
                                $orderProductionArr['created_at']=date('Y-m-d H:i:s');
                                $orderProductionArr['updated_at']=date('Y-m-d H:i:s');
                                OrderProduction::insert($orderProductionArr);
                            }
                        }
                    }
                }
                else{
                   // return response()->json(["status_code"=>400,"status" =>"Failure","message"=>"Please enter the Dates"]);
                }
            }

             /* Order Log creation starts*/
             if(isset($request->before_values) && !empty($request->before_values)){
                 $logArry = array();
                 $logArry['order_id'] =$request->order_id;
                 $logArry['company_id'] = $request->company_id;
                 $logArry['workspace_id'] = $request->workspace_id;
                 $logArry['staff_id'] =$request->staff_id ?? 0;
                 $logArry['user_id'] = $request->user_id ?? 0;
                 $logArry['action'] = 'Edit';
                 $logArry['before_values'] = json_encode($request->before_values) ?? '';
                 $logArry['after_values'] = json_encode($request->after_values) ?? '';
                 Orderlog::insert($logArry);
             }
             /* Order Log creation end*/
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException("Unable to Post Data");
        }
        DB::commit();
    }

    public static function getProductionData($request){
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id],
            ['type_of_production','=',$request->type_of_production]
        ];

        $prodDetails = OrderProduction::where($whereConditions)->get();
		$arr=array();$i=0;
    	foreach ($prodDetails as $value) {
    		$arr[$i]['date_of_production']=$value->date_of_production;
    		$arr[$i]['target_value']=$value->target_value;
    		$arr[$i]['holiday_flag']=$value->holiday_flag;
    		$arr[$i]['holiday_detail']=$value->holiday_detail;
    		$i++;

		}

        return $arr;
    }
}
