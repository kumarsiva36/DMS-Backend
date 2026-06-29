<?php

namespace App\Http\Controllers\WebSite\Order\DataInput;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\HolidaySetting;
use App\Models\Order;
use App\Models\OrderProduction;
use App\Models\OrderSku;
use App\Models\Size;
use App\Models\UpdateSkuQuantity;
use App\Models\WeekOff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Illuminate\Support\Facades\DB;
use App\Common\CommonApp;
use App\Models\MultipleDeliveryDates;

class GetData extends Controller
{
    /* Get the data to shown in the data input */
    public  function getCalendarData(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'order_id' => 'required',
            'workspace_id' => 'required',
            'type_of_production' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions = $whereConditions1 = [
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id],
            ['type_of_production','=',$request->type_of_production]
        ];

        $prodDetails = OrderProduction::where($whereConditions)->get();
        $dayByDaySKUupdates = UpdateSkuQuantity::where($whereConditions)->get();
        $todaysEntries = UpdateSkuQuantity::where($whereConditions)->where("sku_date", date('Y-m-d'))->sum('updated_quantity');
        $poppedCondition = array_splice($whereConditions, 0, 3);
        $skuDetails = OrderSku::where($poppedCondition)->get();
        // echo "<pre>"; print_r($prodDetails); exit;
        $order = Order::where("id",$request->order_id)->where("company_id",$request->company_id)->first();
        $holidaysCount = OrderProduction::where($whereConditions1)->where("holiday_flag","1")->count();
        $weekOffs = WeekOff::where("company_id",$request->company_id)->where("workspace_id",$request->workspace_id)->select('days')->get();
        $dataArr=[];
        if($request->type_of_production === "Cut"){
            $dataArr['prodDetails']['cutStartDate']=$order->cutting_start_date;
            $dataArr['prodDetails']['cutEndDate']=$order->cutting_end_date;
            $dataArr['prodDetails']['isCutAccomplished']=$order->cutting_completion;
            $dataArr['prodDetails']['cutAccomplishedDate']=$order->cutting_accomplished_date;
            $dataArr['knobChart']['totalQuantity']=$order->total_quantity;
            $startDate = new DateTime($dataArr['prodDetails']['cutStartDate']);
            $endDate = new DateTime( $dataArr['prodDetails']['cutEndDate']);
            $endDateForExcessCalculation=$dataArr['prodDetails']['cutEndDate'];
            $day = (($startDate->diff($endDate)->days)+1)-$holidaysCount;
            $holidaysAfterEndDate = HolidaySetting::where("company_id",$request->company_id)->where("workspace_id",$request->workspace_id)
            ->where('holiday_start_date','>',$order->cutting_end_date)->where('status',"1")
            ->select('name','holiday_start_date as start_date','holiday_end_date as end_date','description','days')->get();
        }
        if($request->type_of_production === "Sew"){
            $dataArr['prodDetails']['sewStartDate']=$order->sewing_start_date;
            $dataArr['prodDetails']['sewEndDate']=$order->sewing_end_date;
            $dataArr['prodDetails']['isSewAccomplished']=$order->sewing_completion;
            $dataArr['prodDetails']['sewAccomplishedDate']=$order->sewing_accomplished_date;
            $dataArr['knobChart']['totalQuantity']=$order->total_quantity;
            $startDate = new DateTime($dataArr['prodDetails']['sewStartDate']);
            $endDate = new DateTime( $dataArr['prodDetails']['sewEndDate']);
            $endDateForExcessCalculation=$dataArr['prodDetails']['sewEndDate'];
            $day = (($startDate->diff($endDate)->days)+1)-$holidaysCount;
            $holidaysAfterEndDate = HolidaySetting::where("company_id",$request->company_id)->where("workspace_id",$request->workspace_id)
            ->where('holiday_start_date','>',$order->sewing_end_date)->where('status',"1")
            ->select('name','holiday_start_date as start_date','holiday_end_date as end_date','description','days')->get();
        }
        if($request->type_of_production === "Pack"){
            $dataArr['prodDetails']['packStartDate']=$order->packing_start_date;
            $dataArr['prodDetails']['packEndDate']=$order->packing_end_date;
            $dataArr['prodDetails']['isPackAccomplished']=$order->packing_completion;
            $dataArr['prodDetails']['packAccomplishedDate']=$order->packing_accomplished_date;
            $dataArr['knobChart']['totalQuantity']=$order->total_quantity;
            $startDate = new DateTime($dataArr['prodDetails']['packStartDate']);
            $endDate = new DateTime( $dataArr['prodDetails']['packEndDate']);
            $endDateForExcessCalculation=$dataArr['prodDetails']['packEndDate'];
            $day = (($startDate->diff($endDate)->days)+1)-$holidaysCount;
            $holidaysAfterEndDate = HolidaySetting::where("company_id",$request->company_id)->where("workspace_id",$request->workspace_id)
            ->where('holiday_start_date','>',$order->packing_end_date)->where('status',"1")
            ->select('name','holiday_start_date as start_date','holiday_end_date as end_date','description','days')->get();
        }
        $day === 0? $days = 1 : $days = $day;
        $dataArr['weekOffs']= count($weekOffs)>0 ? $weekOffs : [];
        $dataArr['holidays']= count($holidaysAfterEndDate)>0 ? $holidaysAfterEndDate : [];
		$skuDataArr=[];
        $completedQuantity=0;
        foreach($skuDetails as $sku){
            $array=[];
            $array['color_id']= $sku->sku_color_id;
            $array['size_id']= $sku->sku_size_id;
            $array['colorName']= $this->getColorName($sku->sku_color_id);
            $array['sizeName']= $this->getSizeName($sku->sku_size_id);
            $array['total_quantity'] = $sku->sku_quantity;
            $conditionsToSend = [
                ["order_id","=",$request->order_id],
                ["color_id","=",$sku->sku_color_id],
                ["size_id","=",$sku->sku_size_id],
                ["type_of_production","=",$request->type_of_production],
            ];
            $array['updated_quantity'] = (int)$this->getSkuUpdatedQuantity($conditionsToSend);
            $completedQuantity += $array['updated_quantity'];
            $dataArr['knobChart']['completedQuantity']=$completedQuantity;
            $skuDataArr[]=$array;
        }
        $dataArr['skuData']=$skuDataArr;
        $dataArr['knobChart']['completedQuantity']=$completedQuantity;
        $dataArr['knobChart']['pendingQuantity'] = $dataArr['knobChart']['totalQuantity']-$completedQuantity;
        // $todaysValues=0;
        // foreach($todaysEntries as $entry){
        //     $todaysValues += $entry->updated_quantity;
            $dataArr['knobChart']['currentOutputQuantity'] = (int)$todaysEntries;
        // }
        // $dataArr['knobChart']['currentOutputRate'] = round($dataArr['knobChart']['currentOutputQuantity']/$dataArr['knobChart']['reqOutputRate']);
        $colorArr=$sizeArr=[];
        foreach($skuDataArr as $skuData){
            $array = $Array =[];
            $array['color_id']=$skuData['color_id'];
            $array['colorName']=$skuData['colorName'];
            $Array['size_id']=$skuData['size_id'];
            $Array['sizeName']=$skuData['sizeName'];
            $colorArr[]=$array;
            $sizeArr[]=$Array;
        }
        $dataArr['colors']=isset($colorArr)?
        array_map("unserialize",array_unique(array_map("serialize",$colorArr))):[];
        $dataArr['sizes']=isset($sizeArr)?
        array_map("unserialize",array_unique(array_map("serialize",$sizeArr))):[];
        $arr=array();$i=$j=0;
    	foreach ($prodDetails as $value) {

            $todaysValues = (int) UpdateSkuQuantity::where($whereConditions1)->where("sku_date", date('Y-m-d',strtotime($value->date_of_production)))->sum('updated_quantity');
            // dd($todaysValues);
            // $todaysValues=0;
            // foreach($todaysEntries as $entry){
            //     $todaysValues += $entry->updated_quantity;
            // }
            if($todaysValues === 0 && $value->holiday_flag != 1 && $value->date_of_production >= date('Y-m-d')){
                $j++;
            }
            $arr[$i]['date_of_production']=$value->date_of_production;
    		$arr[$i]['target_value']=$value->target_value;
    		//$arr[$i]['actual_value']=$value->actual_value;
            $arr[$i]['actual_value']=$todaysValues;
    		$arr[$i]['holiday_flag']=$value->holiday_flag;
    		$arr[$i]['holiday_detail']=$value->holiday_detail;
    		$i++;
            $dataArr['CalendarData']=$arr;
		}
        $updatedSKUQuantityArr=$excessDateArray=[];
        $k=$l=0;
        foreach($dayByDaySKUupdates as $dayUpdate){
            $array=[];
            $array['color_id']= $dayUpdate->color_id;
            $array['size_id']= $dayUpdate->size_id;
            $array['colorName']= $this->getColorName($dayUpdate->color_id);
            $array['sizeName']= $this->getSizeName($dayUpdate->size_id);
            $array['total_quantity'] = $dayUpdate->updated_quantity;
            $array['date_updated'] = $dayUpdate->sku_date;
            if($dayUpdate->sku_date > $endDateForExcessCalculation){
                $k++;
            }
            $updatedSKUQuantityArr[]=$array;
        }
        if($k>0){
            $excessDatesSku = UpdateSkuQuantity::where($whereConditions1)->where('sku_date','>',$endDateForExcessCalculation)
                ->groupBy('sku_date')->select('sku_date',DB::raw('SUM(updated_quantity) as updated_quantity'),'target_value')->get();
            foreach ($excessDatesSku as $sku){
                $arr[$i]['date_of_production']=$sku->sku_date;
                $arr[$i]['target_value']=$sku->target_value;
                $arr[$i]['actual_value']=(int)$sku->updated_quantity;
                $arr[$i]['holiday_flag']=0;
                $arr[$i]['holiday_detail']=null;
                $i++;$l++;
                $dataArr['CalendarData']=$arr;
            }
        }
        $dataArr['dayByDayUpdates']=$updatedSKUQuantityArr;
        // $reqRate=round($l>0 ? $dataArr['knobChart']['pendingQuantity'] :($dataArr['knobChart']['totalQuantity']-$dataArr['knobChart']['completedQuantity'])/($j>0 ? $j : $days));
        $reqRate=round(($dataArr['knobChart']['totalQuantity']-$completedQuantity)/($j>0 ? $j : 1));
        $dataArr['knobChart']['reqOutputRate']= $reqRate > 0? $reqRate : 0;

        //Delivery Dates
        // $delivey_dates = MultipleDeliveryDates::where('delivery_date','>=',date('Y-m-d'))->where('order_id','=',$request->order_id)->select('delivery_date')->get();
        // $last_delivey_date = MultipleDeliveryDates::where('order_id','=',$request->order_id)->orderBy('delivery_date',"DESC")->pluck('delivery_date')->first();
        $delivery_date = MultipleDeliveryDates::where('order_id','=',$request->order_id)->where('is_delivered','=','0')
                        ->orderBy('delivery_date',"ASC")->pluck('delivery_date')->first();
        $delivery_date_exceed=0;
        if($delivery_date!="" && $delivery_date!=null){
            if($delivery_date < date('Y-m-d')){
                $delivery_date_exceed=1;
            }
        }
        $res = json_encode(["status_code"=>200,"status" =>"Success","ProductionType"=>$request->type_of_production,"data"=>$dataArr,
            "delivery_date"=>$delivery_date,"delivery_date_exceed"=>$delivery_date_exceed],200);
        return CommonApp::webEncrypt($res);
    }

    /* Get Color Name */
    public function getColorName($id){
        $color = Color::where('id',$id)->first();
        return $color->name;
    }

    /* Get Size Name */
    public function getSizeName($id){
        $size = Size::where('id',$id)->first();
        return $size->name;
    }

    /* To get the sum quantites for a specific SKU */
    public function getSkuUpdatedQuantity($whereCondition){
        $sumOfSku = UpdateSkuQuantity::where($whereCondition)->sum('updated_quantity');
        return $sumOfSku;
    }
}
