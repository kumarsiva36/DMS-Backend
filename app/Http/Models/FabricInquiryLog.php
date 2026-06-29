<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ArticleName;
use App\Models\FabricType;
use App\Models\IncomeTerms;

class FabricInquiryLog extends Model
{
    use HasFactory;

    protected $table = 'fabric_inquiry_log';

    public static function create_inquiry_log($inquiry_id,$request){
        $logArry = array();
        $logArry['inquiry_id'] =$inquiry_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Create";
        FabricInquiryLog::insert($logArry);
    }
    public static function edit_inquiry_log($inquiry_id,$request){
        $data = [];$before = [];
        if(isset($request->yarn_count) && isset($request->inquiryDetails->yarn_count) && ($request->yarn_count!=$request->inquiryDetails->yarn_count) ){
            $data['yarn_count']= $request->yarn_count ?? '-';
            $before['yarn_count']= $request->inquiryDetails->yarn_count ?? '-';
        }
        if(isset($request->yarn_quantity) && isset($request->inquiryDetails->yarn_quantity) && ($request->yarn_quantity!=$request->inquiryDetails->yarn_quantity) ){
            $data['yarn_quantity']= $request->yarn_quantity ?? '-';
            $before['yarn_quantity']= $request->inquiryDetails->yarn_quantity ?? '-';
        }
        if(isset($request->yarn_quality) && isset($request->inquiryDetails->yarn_quality) && ($request->yarn_quality!=$request->inquiryDetails->yarn_quality) ){
            $data['yarn_quality']= $request->yarn_quality ?? '-';
            $before['yarn_quality']= $request->inquiryDetails->yarn_quality ?? '-';
        }
        if(isset($request->meterial) && isset($request->inquiryDetails->meterial) && ($request->meterial!=$request->inquiryDetails->meterial) ){
            $data['meterial']= $request->meterial ?? '-';
            $before['meterial']= $request->inquiryDetails->meterial ?? '-';
        }
        if(isset($request->composition) && isset($request->inquiryDetails->composition) && ($request->composition!=$request->inquiryDetails->composition) ){
            $data['composition']= $request->composition ?? '-';
            $before['composition']= $request->inquiryDetails->composition ?? '-';
        }
        if(isset($request->reference_inquiry) && isset($request->inquiryDetails->reference_inquiry) && ($request->reference_inquiry!=$request->inquiryDetails->reference_inquiry) ){
            $data['reference_inquiry']= $request->reference_inquiry ?? '-';
            $before['reference_inquiry']= $request->inquiryDetails->reference_inquiry ?? '-';
        }
        if(isset($request->delivery_date) && isset($request->inquiryDetails->delivery_date) && ($request->delivery_date!=$request->inquiryDetails->delivery_date) ){
            $data['delivery_date']= $request->delivery_date ?? '-';
            $before['delivery_date']= $request->inquiryDetails->delivery_date ?? '-';
        }
        if(isset($request->inhouse_date) && isset($request->inquiryDetails->inhouse_date) && ($request->inhouse_date!=$request->inquiryDetails->inhouse_date) ){
            $data['inhouse_date']= $request->inhouse_date ?? '-';
            $before['inhouse_date']= $request->inquiryDetails->inhouse_date ?? '-';
        }
        if(isset($request->currency) && isset($request->inquiryDetails->currency) && ($request->currency!=$request->inquiryDetails->currency) ){
            $data['currency']= $request->currency ?? '-';
            $before['currency']= $request->inquiryDetails->currency ?? '-';
        }

        $logArry = array();
        $logArry['inquiry_id'] =$inquiry_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Edit";
        $logArry['after_values'] = !empty($data)?json_encode($data):'';
        $logArry['before_values'] = !empty($before)?json_encode($before):'';
        FabricInquiryLog::insert($logArry);
    }
    public static function delete_inquiry_log($request){
        $logArry = array();
        $logArry['inquiry_id'] =$request->inquiry_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Delete";
        FabricInquiryLog::insert($logArry);
    }

}
