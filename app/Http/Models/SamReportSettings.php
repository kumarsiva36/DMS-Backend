<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class SamReportSettings extends Model
{
    use HasFactory;

    protected $table = 'sam_report_settings';

    public static function save_report($request){
        $data=[];

        $data['company_id'] = $request->company_id??0;
        $data['workspace_id'] = $request->workspace_id??0;
        $data['order_id'] = $request->order_id??0;
        $data['order_no'] = $request->order_no??NULL;
        $data['style_no'] = $request->style_no??NULL;
        $data['production_type'] = $request->production_type??'Sewing';
        $data['shift_id'] = $request->shift_id??0;
        $data['shift_value'] = $request->shift_value??0;
        $data['unit_id'] = $request->unit_id??0;
        $data['unit_value'] = $request->unit_value??0;
        $data['sam_value'] = $request->sam_value??0;
        $data['supervisor_id'] = $request->supervisor_id??0;
        $data['supervisor_name'] = $request->supervisor_name??NULL;
        $data['line_no_id'] = $request->line_no_id??0;
        $data['line_no_value'] = $request->line_no_value??0;
        $data['no_of_tailors'] = $request->no_of_tailors??0;
        $data['no_of_helpers'] = $request->no_of_helpers??0;
        $data['tailor_salary'] = $request->tailor_salary??0;
        $data['helper_salary'] = $request->helper_salary??0;
        $data['report_date'] = $request->report_date?date('Y-m-d',strtotime($request->report_date)):date('Y-m-d');
        $data['from_time'] = $request->from_time??NULL;
        $data['to_time'] = $request->to_time??NULL;
        $data['break_hours'] = $request->break_hours??0;
        $data['additional_hours'] = $request->additional_hours??0;
        $data['user_id'] = $request->user_id??0;
        $data['staff_id'] = $request->staff_id??0;
        $data['additional_from_time'] = $request->additional_from_time??NULL;
        $data['additional_to_time'] = $request->additional_to_time??NULL;
        $data['additional_salary_type'] = $request->additional_salary_type??1;
        $data['additional_tailor_salary'] = $request->additional_tailor_salary??0;
        $data['additional_helper_salary'] = $request->additional_helper_salary??0;
        $data['factory_factor'] = $request->factory_factor??1;
        $data['alert_percentage'] = $request->alert_percentage??50;
        $data['created_at'] = date('Y-m-d H:i:s');
        SamReportSettings::insert($data);
    }

    public static function update_report_setting_details($request){
        $data=[];
        $id = $request->report_id??0;
        $data['additional_hours'] = $request->additional_hours??0;
        $data['updated_user_id'] = $request->user_id??0;
        $data['updated_staff_id'] = $request->staff_id??0;
        $data['additional_from_time'] = $request->additional_from_time??NULL;
        $data['additional_to_time'] = $request->additional_to_time??NULL;
        $data['additional_salary_type'] = $request->additional_salary_type??1;
        $data['additional_tailor_salary'] = $request->additional_tailor_salary??0;
        $data['additional_helper_salary'] = $request->additional_helper_salary??0;
        SamReportSettings::where('id',$id)->update($data);
    }

    public static function check_report_settings_exists($request){
        $where = [['order_id','=', $request->order_id],['style_no','=',$request->style_no],
        ['production_type','=', $request->production_type],['shift_id','=',$request->shift_id],
        ['unit_id','=', $request->unit_id],['line_no_id','=',$request->line_no_id],
        ['report_date','=',$request->report_date]];

        return SamReportSettings::where($where)->count();
    }

    public static function get_report_settings($request){
        return SamReportSettings::where('company_id', '=', $request->company_id)
                ->where('workspace_id', '=', $request->workspace_id)
                //->where('report_date', '=', date('Y-m-d'))
                ->where(function ($query) {
                    $query->where('report_date', '=', date('Y-m-d'))
                          ->orWhere('report_date', '=', date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d')))));
                })
                ->orderBy('id','DESC')
                ->get();
    }

    public static function get_sam_report_time($request){
        $detail = SamReportSettings::where('id', '=', $request->report_id)
        ->select('from_time','to_time','additional_from_time','additional_to_time')
        ->first();
        $time_arr = array();
        $time_diff = ((strtotime($detail['to_time']) - strtotime($detail['from_time']))/3600);
        $add_time_diff = ((strtotime($detail['additional_to_time']) - strtotime($detail['additional_from_time']))/3600);
        for($i = 0; $i < $time_diff; $i++){
            if(date('H:i',strtotime($detail['from_time'].'+'.$i.' hour')) <= date('H:i'))
                $time_arr[$i]=date('H:i', strtotime($detail['from_time'].'+'.$i.' hour'))."-".date('H:i', strtotime($detail['from_time'].'+'.($i+1).' hour'));
        }
        for($j = 0; $j < $add_time_diff; $j++){
            if(date('H:i',strtotime($detail['additional_from_time'].'+'.$j.' hour')) <= date('H:i'))
                $time_arr[$i]=date('H:i', strtotime($detail['additional_from_time'].'+'.$j.' hour'))."-".date('H:i', strtotime($detail['additional_from_time'].'+'.($j+1).' hour'));
            $i++;
        }
        return array_values($time_arr);
    }

    public static function get_sam_report_basic_details($request){
        $detail = SamReportSettings::where('id', '=', $request->report_id)
        ->select('style_no','unit_value','line_no_value','supervisor_name','sam_value','no_of_tailors')
        ->first();
        return $detail;
    }

    public static function sam_report_setting_details($request){
        $detail = SamReportSettings::where('id', '=', $request->report_id)
        ->first();
        return $detail;
    }

    public static function get_sam_daily_report($request)
    {
        $advFilter['report_date'] = $request->date;
        $advFilter['shift_id'] = $request->shift_id;

        $where = [['report_date','=', $request->date],['shift_id','=',$request->shift_id]];

        if(isset($request->style_no) && $request->style_no !=''){
            $advFilter['style_no'] = $request->style_no;
            $where[] = ['style_no','=', $request->style_no];
        }
        if(isset($request->unit_id) && $request->unit_id !=''){
            $advFilter['unit_id'] = $request->unit_id;
            $where[] = ['unit_id','=', $request->unit_id];
        }
        if(isset($request->Line_id) && $request->Line_id !=''){
            $advFilter['line_no_id'] = $request->Line_id;
            $where[] = ['line_no_id','=', $request->Line_id];
        }
        if(isset($request->supervisor_id) && $request->supervisor_id !=''){
            $advFilter['supervisor_id'] = $request->supervisor_id;
            $where[] = ['supervisor_id','=', $request->supervisor_id];
        }

        $result = SamReportSettings::where($where)
        ->join('sam_quantity_details','sam_quantity_details.report_id','sam_report_settings.id')
        ->orderBy('sam_quantity_details.report_id','asc')
        ->get();

        $data=array();
        $timeslot_arr=$graph_qty=$graph_label=array();
        $total_qty=$gr_id=$gr_i=0;
        $calculations=array();
        foreach($result as $res){
            $total_qty+=$res->quantity;
            $id = $res->report_id;
            $data[$id]['report_id'] = $res->report_id;
            $data[$id]['report_date'] = $res->report_date;
            $data[$id]['line_no'] = $res->line_no_value;
            $data[$id]['style_no'] = $res->style_no;
            $data[$id]['shift'] = $res->shift_value;
            $data[$id]['supervisor'] = $res->supervisor_name;
            $data[$id]['unit'] = $res->unit_value;
            $data[$id]['sam'] = $res->sam_value;
            $data[$id]['tailors'] = $res->no_of_tailors;
            $data[$id]['helpers'] = $res->no_of_helpers;
            $data[$id]['break'] = $res->break_hours;
            $data[$id]['no_of_tailors'] = $res->no_of_tailors;
            $data[$id]['no_of_helpers'] = $res->no_of_helpers;
            $data[$id]['tailor_salary'] = $res->tailor_salary;
            $data[$id]['helper_salary'] = $res->helper_salary;
            $data[$id]['additional_hours'] = $res->additional_hours;
            $data[$id]['additional_tailor_salary'] = $res->additional_tailor_salary;
            $data[$id]['additional_helper_salary'] = $res->additional_helper_salary;
            $data[$id]['additional_salary_type'] = $res->additional_salary_type;
            $data[$id]['additional_from_time'] = $res->additional_from_time;
            $data[$id]['additional_to_time'] = $res->additional_to_time;
            $data[$id]['from_time'] = $res->from_time;
            $data[$id]['to_time'] = $res->to_time;
            $data[$id]['target'] =round(((60/$res->sam_value)*$res->no_of_tailors),0);
            $data[$id]['total_qty'] = $total_qty;
            $data[$id]['blink_per'] = $res->alert_percentage;
            $data[$id]['factory_factor'] = $res->factory_factor;
            $data[$id][$res->time_slot]=array("qty"=>$res->quantity,"comments"=>$res->comments);
            $timeslot_arr[]=$res->time_slot;

            /*For Graph qty */
            if($gr_i==0)
             $gr_id = $id;

            if($gr_id == 0 || $gr_id == $id){
                $graph_qty[$id]['styledata'][$gr_i]=$res->quantity;
                $gr_i++;
            }else{
                $gr_id = $id;
                $gr_i=0;
                $graph_qty[$id]['styledata'][$gr_i]=$res->quantity;
                $gr_i++;
            }
            $graph_label[$id]['type']=$res->production_type;
            $graph_label[$id]['style']=$res->style_no;
            $graph_label[$id]['lineNo']=$res->line_no_value;
            $graph_label[$id]['shift']=$res->shift_value;
            $graph_label[$id]['unit']=$res->unit_value;
            $graph_label[$id]['sam']=$res->sam_value;
            $graph_label[$id]['supervisor']=$res->supervisor_name;
            $graph_label[$id]['helpers']=$res->no_of_helpers;
            $graph_label[$id]['noOfTailors']=$res->no_of_tailors;
           // $graph_label[$id]['finishValued']=round(((60/$res->sam_value)*$res->no_of_tailors),0);
            $graph_label[$id]['target']=round(((60/$res->sam_value)*$res->no_of_tailors),0);
        }
        $res['graph_qty'] = array_values($graph_qty);
        $res['graph_label'] = array_values($graph_label);
        $res['data'] = array_values($data);
        $res['time_slot'] = array_values(array_unique($timeslot_arr));

        $i=$manpower_cost=$total_production=$total_sam=$total_tailors=$total_helpers=$total_tgt=$total_factory_fact=0;
        $cal_time_arr=array();
        foreach($data as $d){
            $addtional_cost=0;
            if($d['additional_hours']==1){
                if($d['additional_salary_type']==1)
                    $addtional_cost=($d['no_of_tailors']*$d['additional_tailor_salary'])+($d['no_of_helpers']*$d['additional_helper_salary']);
                else{
                    $wk_hrs = round(abs(strtotime($d['from_time']) - strtotime($d['to_time'])) / 3600,2);
                    $ad_wk_hrs = round(abs(strtotime($d['additional_to_time']) - strtotime($d['additional_from_time'])) / 3600,2);
                    $per_hr_tailor_sal = $d['tailor_salary'] / ($wk_hrs-$d['break']);
                    $per_hr_helper_sal = $d['helper_salary'] / ($wk_hrs-$d['break']);
                    //$ad_per_hr_tailor_sal = $per_hr_tailor_sal+($per_hr_tailor_sal*($d['additional_tailor_salary']));
                    //$ad_per_hr_helper_sal = $per_hr_helper_sal+($per_hr_helper_sal*($d['additional_helper_salary']));
                    $ad_per_hr_tailor_sal = ($per_hr_tailor_sal*($d['additional_tailor_salary']));
                    $ad_per_hr_helper_sal = ($per_hr_helper_sal*($d['additional_helper_salary']));
                    $addtional_cost = round((($d['no_of_tailors']*($ad_wk_hrs*$ad_per_hr_tailor_sal))+($d['no_of_helpers']*($ad_wk_hrs*$ad_per_hr_helper_sal))),0);
                }
            }
            //echo $addtional_cost."\n";
            $manpower_cost+=($d['no_of_tailors']*$d['tailor_salary'])+($d['no_of_helpers']*$d['helper_salary'])+$addtional_cost;
            $total_production=$d['total_qty'];
            $total_sam+=$d['sam'];
            $total_factory_fact = $d['factory_factor'];
            $total_tailors+=$d['no_of_tailors'];
            $total_helpers+=$d['no_of_helpers'];
            $total_tgt+=$d['target'];
            foreach($timeslot_arr as $t){
               $cal_time_arr[$t][$i]=isset($d[$t]['qty'])?$d[$t]['qty']:0;
            }
            $i++;

        }
        $count=count($data) > 0 ? count($data) : 1;
        $avg_sam = $total_sam/$count;
        $avg_fact = $total_factory_fact/$count;
        $earned_cost = round(($avg_fact*$avg_sam*$total_production),0);
        $res['manpower_cost']=$manpower_cost;
        $res['earned_cost']=$earned_cost;
        $calculations['manpower_cost']=$manpower_cost;
        $calculations['earned_cost']=$earned_cost;
        $calculations['avg_sam']=round($avg_sam,2);
        $calculations['total_tailors']=$total_tailors;
        $calculations['total_helpers']=$total_helpers;
        $calculations['total_target']=$total_tgt;
        $calculations['total_production']=$total_production;
        $calculations['time_arr']=$cal_time_arr;

        $res['calculations']=$calculations;
        $res['advFilter']=$advFilter;

        return $res;
    }

    public static function get_pervious_sam_report_settings($request){
        $detail = SamReportSettings::where('company_id', '=', $request->company_id)->where('workspace_id', '=', $request->workspace_id)
        ->where('style_no', '=', $request->style_no)
        ->select('report_date')
        ->latest()->first();
        if(!empty($detail)){
            $detail = SamReportSettings::where('company_id', '=', $request->company_id)->where('workspace_id', '=', $request->workspace_id)
            ->where('style_no', '=', $request->style_no)
            ->where('report_date', '=', $detail->report_date)
            ->select('unit_id','unit_value','line_no_id','line_no_value')
            ->get();
        }
        return $detail;
    }

    public static function get_pervious_sam_report_details($request){
        $detail = SamReportSettings::where('company_id', '=', $request->company_id)->where('workspace_id', '=', $request->workspace_id)
            ->where('style_no', '=', $request->style_no)
            ->where('unit_id', '=', $request->unit_id)
            ->where('line_no_id', '=', $request->line_no_id)
            ->latest()->first();

        return $detail;
    }

    public static function get_order_accessories_value($request){
        $bom_details = OrderBOM::where('order_id', '=', $request->order_id)->first();
        $accessories_val = 0;
        if(!empty($bom_details)){
            $sewing_accessories  = $bom_details->sewing_accessories!=NULL  ? json_decode($bom_details->sewing_accessories) :array();
            $packing_accessories = $bom_details->packing_accessories!=NULL ? json_decode($bom_details->packing_accessories):array();
            $miscellaneous       = $bom_details->miscellaneous!=NULL       ? json_decode($bom_details->miscellaneous)      :array();

            foreach ($sewing_accessories as $val){
                $accessories_val+= isset($val->pricePerUnit)? (int)$val->pricePerUnit : 0;
            }
            foreach ($packing_accessories as $val){
                $accessories_val+= isset($val->pricePerUnit)? (int)$val->pricePerUnit : 0;
            }
            foreach ($miscellaneous as $val){
                $accessories_val+= isset($val->pricePerUnit)? (int)$val->pricePerUnit : 0;
            }
        }
        return $accessories_val;
    }
}
