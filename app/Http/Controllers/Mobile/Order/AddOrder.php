<?php

namespace App\Http\Controllers\Mobile\Order;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AddOrder extends Controller
{
    /**
     * Handle the incoming request.
     *  Add a new Order
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_no' => 'required',
            'style_no' => ['required', Rule::unique('orders')
                            ->where(function ($query) use ($request) {
                                $query->where('company_id', $request->company_id);
                                $query->where('workspace_id', $request->workspace_id);
                                $query->where('order_no', $request->order_no);
                                $query->where('status','!=','3');
                            })],
            // 'buyer_id' => 'required',
           // 'pcu_id' => 'required',
           // 'factory_id' => 'required',
            //'category_id'=> 'required',
            'fabric_id'=> 'required',
            'article_id' => 'required',
            'total_quantity' => 'required',
            'no_of_deliverys' => 'required',
            'order_price' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(["status_code"=>401,"errors"=>$validator->errors()]);
        }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $addOrderArr = [];
        $addOrderArr['user_id'] =$companyDetails->user_id;
        $addOrderArr['company_id'] = $companyDetails->id;
        $addOrderArr['workspace_id'] =$request->workspace_id;
        $addOrderArr['order_no'] =$request->order_no;
        $addOrderArr['style_no'] =$request->style_no;
        $addOrderArr['buyer_id'] =$request->input('buyer_id','0');
        $addOrderArr['pcu_id'] =$request->input('pcu_id','0');
        $addOrderArr['factory_id'] =$request->input('factory_id','0');
        $addOrderArr['fabric_id'] =$request->fabric_id;
        $addOrderArr['category_id'] =$request->input('category_id',0);
        $addOrderArr['article_id'] =$request->article_id;
        $addOrderArr['total_quantity'] =$request->total_quantity;
        $addOrderArr['no_of_deliverys'] =$request->no_of_deliverys;
        $addOrderArr['cutting_start_date'] =$request->input('cutting_start_date',null);
        $addOrderArr['cutting_end_date'] =$request->input('cutting_end_date',null);
        $addOrderArr['sewing_start_date'] =$request->input('sewing_start_date',null);
        $addOrderArr['sewing_end_date'] =$request->input('sewing_end_date',null);
        $addOrderArr['packing_start_date'] =$request->input('packing_start_date',null);
        $addOrderArr['packing_end_date'] =$request->input('packing_end_date',null);
        $addOrderArr['ref_img'] =$request->input('ref_img',0);
        $addOrderArr['cut_weekoffs'] =$request->input('cut_weekoffs',0);
        $addOrderArr['sew_weekoffs'] =$request->input('sew_weekoffs',0);
        $addOrderArr['pack_weekoffs'] =$request->input('pack_weekoff',0);
        $addOrderArr['usual_weekoff'] =$request->input('usual_weekoff',0);
        $addOrderArr['currency_type'] =$request->input('currency_type',0);
        $addOrderArr['order_task_template'] =$request->input('order_task_template',0);
        $addOrderArr['task_feeded'] =$request->input('task_feeded',0);
        $addOrderArr['pending_tasks'] =$request->input('pending_task',0);
        $addOrderArr['cutting_completion'] =$request->input('cutting_completion',0);
        $addOrderArr['sewing_completion'] =$request->input('sewing_completion',0);
        $addOrderArr['packing_completion'] =$request->input('packing_completion',0);
        $addOrderArr['tolerance_volume'] =$request->input('tolerance_volume',0);
        $addOrderArr['quantity_wise'] = $request->input('quantity_wise','SKU-Wise');
        if($request->is_tolerance_req==1){
            $is_tol_req="1";
        }else{
            $is_tol_req="0";
        }
        $addOrderArr['is_tolerance_req'] =$is_tol_req;
        $addOrderArr['tolerance_perc'] =$request->input('tolerance_perc',0);
        $addOrderArr['order_price'] =$request->order_price;
        $addOrderArr['income_terms'] =$request->input('income_terms',0);
        $addOrderArr['units'] =$request->input('units',0);
        $addOrderArr['status'] = '1';
        $addOrderArr['step_level'] = '1';
        $addOrderArr['status_request'] =$request->input('status_request',0);
        $addOrderArr['created_at'] = date('Y-m-d H:i:s');
        $addOrderArr['updated_at'] = date('Y-m-d H:i:s');
        Order::insert($addOrderArr);
        $orderID = DB::getPdo()->lastInsertId();
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Order Added Successfully","id"=>$orderID,"totalQuantity"=>$request->total_quantity],200);
    }
}
