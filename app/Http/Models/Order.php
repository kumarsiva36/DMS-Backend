<?php

namespace App\Models;

use App\Common\GetUserLanguage;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use App\Jobs\CreateOrderEmail;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    public static function getOrderDetailUsingID($id){
        $order = Order::where('id',$id)->first();

        return $order;
    }

    public static function addOrder($request,$companyDetails){
        DB::beginTransaction();
        try{
            $addOrderArr = [];
            $addOrderArr['user_id'] =$companyDetails->user_id;
            $addOrderArr['company_id'] = $companyDetails->id;
            $addOrderArr['workspace_id'] =$request->workspace_id;
            $addOrderArr['order_no'] =$request->order_no;
            $addOrderArr['style_no'] =$request->style_no;
            $addOrderArr['staff_id'] =$request->staff_id?? 0;
            $addOrderArr['buyer_id'] =$request->buyer_id?? 0;
            $addOrderArr['pcu_id'] =$request->pcu_id?? 0;
            $addOrderArr['factory_id'] =$request->factory_id?? 0;
            $addOrderArr['fabric_id'] =$request->fabric_id?? 0;
            $addOrderArr['category_id'] =$request->category_id?? 0;
            $addOrderArr['article_id'] =$request->article_id?? 0;
            $addOrderArr['inquiry_date'] =$request->inquiry_date != ""? $request->inquiry_date:null;
            $addOrderArr['total_quantity'] =$request->total_quantity;
            $addOrderArr['no_of_deliverys'] =$request->no_of_deliverys;
            $addOrderArr['cutting_start_date'] =$request->cutting_start_date?? null;
            $addOrderArr['cutting_end_date'] =$request->cutting_end_date?? null;
            $addOrderArr['sewing_start_date'] =$request->sewing_start_date?? null;
            $addOrderArr['sewing_end_date'] =$request->sewing_end_date?? null;
            $addOrderArr['packing_start_date'] =$request->packing_start_date?? null;
            $addOrderArr['packing_end_date'] =$request->packing_end_date?? null;
            $addOrderArr['ref_img'] =$request->ref_img??0;
            $addOrderArr['cut_weekoffs'] =$request->cut_weekoffs??0;
            $addOrderArr['sew_weekoffs'] =$request->sew_weekoffs??0;
            $addOrderArr['pack_weekoffs'] =$request->pack_weekoff??0;
            $addOrderArr['usual_weekoff'] =$request->usual_weekoff??0;
            $addOrderArr['currency_type'] =$request->currency_type??0;
            $addOrderArr['order_task_template'] =$request->order_task_template??0;
            $addOrderArr['task_feeded'] =$request->task_feeded??0;
            $addOrderArr['pending_tasks'] =$request->pending_task??0;
            $addOrderArr['cutting_completion'] =$request->cutting_completion??0;
            $addOrderArr['sewing_completion'] =$request->sewing_completion??0;
            $addOrderArr['packing_completion'] =$request->packing_completion??0;
            $addOrderArr['tolerance_volume'] =$request->tolerance_volume??0;
            $addOrderArr['quantity_wise'] = $request->quantity_wise??'SKU-Wise';
            $addOrderArr['tolerance_perc'] =$request->tolerance_perc??0;
            $addOrderArr['order_price'] =$request->order_price??null;
            $addOrderArr['income_terms'] =$request->income_terms??0;
            $addOrderArr['units'] =$request->units??0;
            $addOrderArr['order_priority'] =$request->order_priority??null;
            $addOrderArr['delivery_date'] =$request->delivery_date??null;
            if($request->is_tolerance_req==1){
                $is_tol_req="1";
            }else{
                $is_tol_req="0";
            }
            $addOrderArr['is_tolerance_req'] =$is_tol_req;
            $addOrderArr['status'] = '1';
            $addOrderArr['step_level'] = '1';
            $addOrderArr['status_request'] =$request->status_request??0;
            $addOrderArr['created_at'] = date('Y-m-d H:i:s');
            $addOrderArr['updated_at'] = date('Y-m-d H:i:s');
            Order::insert($addOrderArr);
            $orderID = DB::getPdo()->lastInsertId();
            if( isset($request->delivery_dates) && (int)$request->no_of_deliverys == count($request->delivery_dates)){
                MultipleDeliveryDates::addMultipleDeliveryDates($request,$orderID,$request->delivery_dates);
            }

            if($request->user_id > 0 && $request->staff_id == 0){
                $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"User",$request->user_id);
                $whereConditionToSend=[
                    ['company_id','=',$request->company_id],
                    ['id','=',$request->user_id]
                ];
                $dateFormat=GetUserSettings::getPeopleDateFormat("User",$whereConditionToSend);
            }
            else if($request->staff_id > 0){
                $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"Staff",$request->staff_id);
                $whereConditionToSend=[
                    ['company_id','=',$request->company_id],
                    ['workspace_id','=',$request->workspace_id],
                    ['id','=',$request->staff_id]
                ];
                $dateFormat=GetUserSettings::getPeopleDateFormat("Staff",$whereConditionToSend);
            }
            $userDetails = User::where('id', $companyDetails->user_id)->first();
            $buyer_id=$request->buyer_id??0;
            $buyer = Buyer::where('id', $buyer_id)->first();
            $pcu_id = $request->pcu_id??0;
            $pcu = PCU::where('id', $pcu_id)->first();
            $factory_id=$request->factory_id??0;
            $factory = Factory::where('id', $factory_id)->first();
            $fabric_id=$request->fabric_id??0;
            $fabric = FabricType::where('id', $fabric_id)->first();
            $article_id=$request->article_id??0;
            $article = ArticleName::where('id', $article_id)->first();
            $maildetails=[];
            $maildetails['to']= $userDetails->email;
            $maildetails['created_by']= $userDetails->name;
            $maildetails['created_at']= date($dateFormat);
            $maildetails['orderNo']= $addOrderArr['order_no'];
            $maildetails['styleNo']= $addOrderArr['style_no'];
            $maildetails['language']=$language;
            $maildetails['buyer']=$buyer->name??'';
            $maildetails['pcu']=$pcu->name??'';
            $maildetails['factory']=$factory->name??'';
            $maildetails['fabric']=$fabric->name??'';
            $maildetails['article']=$article->name??'';
           // dd($maildetails);
            CreateOrderEmail::dispatch($maildetails);

            /* Order Log creation starts*/
            $logArry = array();
            $logArry['order_id'] =$orderID;
            $logArry['company_id'] = $request->company_id;
            $logArry['workspace_id'] = $request->workspace_id;
            $logArry['staff_id'] =$request->staff_id ?? 0;
            $logArry['user_id'] = $request->user_id ?? 0;
            $logArry['action'] = 'Create';
            Orderlog::insert($logArry);
            /* Order Log creation end*/
        }catch(Exception $e){
            Log::info($e);
            DB::rollBack();
            throw new InvalidArgumentException('Unable to post Data');
        }
        DB::commit();
        return $orderID;
    }
}
