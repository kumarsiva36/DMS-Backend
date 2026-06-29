<?php
namespace App\Http\Controllers\WebSite\SAM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Common\CommonApp;
use App\Models\CompanySettings;
use App\Models\SamMaster;
use App\Models\SamQuantity;
use App\Models\SamReportSettings;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;


class Sam extends Controller
{
    public static function get_sam_master_data(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
           'company_id'=>'required',
           'workspace_id'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = SamMaster::where('company_id', '=', $request->company_id)
                ->where('workspace_id', '=', $request->workspace_id)
                ->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    public static function add_sam_master_data(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
           'company_id'=>'required',
           'workspace_id'=>'required',
           'value'=>'required',
           'type'=>'required', //Shift,Unit,Line,Supervisor
           'shift_from_time'  => 'required_if:type,==,Shift',
           'shift_end_time'  => 'required_if:type,==,Shift',
           'line_unit'  => 'required_if:type,==,Line',
           'line_no_of_machines'  => 'required_if:type,==,Line',
           'line_machine_type'  => 'required_if:type,==,Line',
           'supervisor_id'  => 'required_if:type,==,Supervisor',
           'user_id'=>'required',
           'staff_id'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        /*Check the shift, unit and line no validation */
        if($request->type=='Shift'){
            $check=SamMaster::check_shift_exists($request);
            if($check>0){
                $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>"Shift already exists"],201);
                return CommonApp::webEncrypt($res);
            }
        }
        if($request->type=='Unit'){
            $check=SamMaster::check_unit_exists($request);
            if($check>0){
                $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>"Unit already exists"],201);
                return CommonApp::webEncrypt($res);
            }
        }
        if($request->type=='Line'){
            $check=SamMaster::check_line_exists($request);
            if($check>0){
                $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>"Line already exists"],201);
                return CommonApp::webEncrypt($res);
            }
        }
        if($request->type=='Supervisor'){
            $check=SamMaster::check_supervisor_exists($request);
            if($check>0){
                $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>"Supervisor already exists"],201);
                return CommonApp::webEncrypt($res);
            }
        }

        DB::beginTransaction();
        try{
            SamMaster::save_master_data($request);
        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Data Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function save_sam_report_settings(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
           'company_id'=>'required|numeric',
           'workspace_id'=>'required|numeric',
           'order_id'=>'required|numeric',
           'order_no'=>'required',
           'style_no'=>'required',
           'shift_id'=>'required|numeric',
           'shift_value'=>'required',
           'unit_id'=>'required|numeric',
           'unit_value'=>'required|numeric',
           'sam_value'=>'required|numeric',
           'supervisor_id'=>'required|numeric',
           'supervisor_name'=>'required',
           'line_no_id'=>'required|numeric',
           'line_no_value'=>'required|numeric',
           'no_of_tailors'=>'required|numeric',
           'no_of_helpers'=>'required|numeric',
           'tailor_salary'=>'required|numeric',
           'helper_salary'=>'required|numeric',
           'report_date'=>'required',
           'from_time'=>'required',
           'to_time'=>'required',
           'break_hours'=>'required|numeric',
           'additional_hours'=>'required|numeric',
           'user_id'=>'required|numeric',
           'staff_id'=>'required|numeric',
           'production_type'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $check = SamReportSettings::check_report_settings_exists($request);
        if($check>0){
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>"Report settings already added"],201);
            return CommonApp::webEncrypt($res);
        }

        DB::beginTransaction();
        try{
            SamReportSettings::save_report($request);
        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Report Settings Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_sam_report_settings(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
           'company_id'=>'required',
           'workspace_id'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = SamReportSettings::get_report_settings($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_sam_report_time(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
           'report_id'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = SamReportSettings::get_sam_report_time($request);
        $qty_times = SamQuantity::get_sam_added_report_times($request);
        $report_details = SamReportSettings::get_sam_report_basic_details($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data,"updated_times"=>$qty_times,"details"=>$report_details],200);
        return CommonApp::webEncrypt($res);
    }

    public static function save_sam_quantity(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
           'report_id'=>'required',
           'time_slot'=>'required',
           'quantity'=>'required|numeric',
           'user_id'=>'required',
           'staff_id'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        DB::beginTransaction();
        try{
            SamQuantity::save_quantity_data($request);
        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Data Added Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_sam_quantity(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
           'report_id'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = SamQuantity::get_sam_quantity($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_sam_report(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
           'company_id'=>'required|numeric',
           'workspace_id'=>'required|numeric',
           'date'=>'required',
           'shift_id'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = SamReportSettings::get_sam_daily_report($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","graph_qty"=>$data['graph_qty'],"graph_label"=>$data['graph_label'],"data"=>$data['data'],"timings"=>$data['time_slot'],"manpower_cost"=>$data['manpower_cost'],
        "earned_cost"=>$data['earned_cost'],"calculations"=>$data['calculations']],200);
        return CommonApp::webEncrypt($res);
    }
    public static function download_sam_report(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id'=>'required|numeric',
            'workspace_id'=>'required|numeric',
            'date'=>'required',
            'shift_id'=>'required',
            'user_id'=>'required|numeric',
            'staff_id'=>'required|numeric',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        App::setlocale($dateFormatAndLanguage['language']);
        $responses = SamReportSettings::get_sam_daily_report($request);
        $data['responses']=$responses['data'];
        $data['timings']=$responses['time_slot'];
        $data['manpower_cost']=$responses['manpower_cost'];
        $data['earned_cost']=$responses['earned_cost'];
        $data['calculations']=$responses['calculations'];
        $data['date']=$request->date;
        $data['dateFormat']=$dateFormatAndLanguage['dateFormat'];
        $data['advFilter']=$responses['advFilter'];
        if(count($responses['data'])>0){
            view()->share("data",$data);
            $pdf = Pdf::loadView('SamReport');
            $pdf->setPaper('A4', 'landscape');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            return $pdf->download();
            //$path = public_path() . '/Fabric/sam.pdf';
            //$pdf->save($path);
        }
    }

    public static function sam_report_setting_details(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
           'report_id'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = SamReportSettings::sam_report_setting_details($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    public static function update_report_setting_details(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
           'report_id'=>'required|numeric',
           'additional_hours'=>'required|numeric',
           'user_id'=>'required|numeric',
           'staff_id'=>'required|numeric',
           'additional_from_time'=>'required_if:additional_hours,==,1',
           'additional_to_time'=>'required_if:additional_hours,==,1',
           'additional_tailor_salary'=>'required_if:additional_hours,==,1',
           'additional_helper_salary'=>'required_if:additional_hours,==,1',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        DB::beginTransaction();
        try{
            SamReportSettings::update_report_setting_details($request);
        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::webEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Report Settings updated Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_pervious_sam_report_settings(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'workspace_id'=>'required|numeric',
            'company_id'=>'required|numeric',
            'style_no'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = SamReportSettings::get_pervious_sam_report_settings($request);
        $accessories_value = SamReportSettings::get_order_accessories_value($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data,"accessories_value"=>$accessories_value],200);
        return CommonApp::webEncrypt($res);
    }
    public static function get_pervious_sam_report_details(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'workspace_id'=>'required|numeric',
            'company_id'=>'required|numeric',
            'style_no'=>'required',
            'unit_id'=>'required',
            'line_no_id'=>'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $data = SamReportSettings::get_pervious_sam_report_details($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

}
