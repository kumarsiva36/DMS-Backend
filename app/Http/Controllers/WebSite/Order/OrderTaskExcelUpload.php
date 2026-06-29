<?php

namespace App\Http\Controllers\website\Order;

use App\Common\CommonApp;
use App\Common\GetUserLanguage;
use App\Common\NotificationAddition;
use App\Common\NotificationText;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use App\Jobs\AccomplishedMailJob;
use App\Models\MultipleDeliveryDates;
use App\Models\NotificationSettings;
use App\Models\Order;
use App\Models\OrderTask;
use App\Models\RolesAndPermissions;
use App\Models\Staff;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderTemplate;
use App\Models\Orderlog;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportTaskDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\CompanySettings as ModelsCompanySettings;
use App\Common\Uploads;



use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class OrderTaskExcelUpload extends Controller
{
    public function excel_file_upload(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'file' => 'required|mimes:xls,xlsx',
            'template_name' => ['required', Rule::unique('order_task_template')
            ->where(function ($query) use($request) {
                $query->where('company_id',$request->company_id);
                $query->where('template_name',$request->template_name);
                $query->orwhere('is_default','=','0');
                return $query;
            })]
            
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
      
        $datas = Excel::toArray(new ImportTaskDetails,request()->file('file'));
         $filedata=$this->getExcelJobDetails($datas,$request);
    
        if($filedata['status']=='error'){
            return json_encode(["status_code"=>407,"status" =>"Error","message"=>"Excel Validation Error","data"=>$filedata['data']]);
        }
        $logArry = array();
        $logArry['order_id'] =$request->order_id;
        $logArry['company_id'] = $request->company_id;
        $logArry['workspace_id'] = $request->workspace_id;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] = 'create';
        $logArry['before_values'] = json_encode($filedata['data'],true) ?? '';
        $logArry['after_values'] = "Excel upload Task Template id :".$filedata['template_id'];
        
        if($request->hasfile('file')){
            $companyToUpdate = ModelsCompanySettings::getCompanyInfoUsingID($request->company_id);
            $awsCompanyPath = $companyToUpdate->aws_s3_path;
            $excelfile = $request->file('file');
            $excelfileName = time().'_'.$excelfile->getClientOriginalName();
            $filepath = $awsCompanyPath.'/ExcelUpload/'.$excelfileName;
            Uploads::companyLogoUpload($filepath,$excelfile);
            $logArry['comments'] = 'Reference Excel File :'.$filepath;
        }
        Orderlog::insert($logArry);
       return json_encode(["status_code"=>200,"status" =>"Success","message"=>"Task Added  successfully","template_id"=>$filedata['template_id'],"data"=>json_encode($filedata['data'],true)]);
    }
   
    public function getExcelJobDetails($datas,$request){
        if(is_array($datas[0])){
            try{
            $ary=[];
            $ary_err=[];
            $cat_ary=[];
            $cat_ary_valid=[];
            $cat_ary_valid_data=[];
            $task_ary=[];
            foreach($datas[0] as $key =>$data){
                if($key>0){
                    $subary=[];                  
            
              if($data[0]=='' && $data[1]=='' && $data[2]=='' && $data[3]==''){
                break;
            }
            $task=preg_replace('/\s+/', ' ', $data[0]);
            $task=str_replace('  ', ' ', $task);
            $validateTask=$this->validateStringType($key,$task,'Category');
            if(is_array($validateTask)){
                $ary_err[]=$validateTask;
            }
              $subary['task']=$task;

              $subtask=preg_replace('/\s+/', ' ', $data[1]);
              $subtask=str_replace('  ', ' ', $subtask);
               $validateSubTask=$this->validateStringType($key,$subtask,'Task');
              if(is_array($validateSubTask)){
                  $ary_err[]=$validateSubTask;
              }
              $subary['subtask']=$subtask;
              $stDate=$this->validatedateFormat($key,$data[2],'Start Date');
              $edDate=$this->validatedateFormat($key,$data[3],'End Date');
             
              if(!is_array($stDate)){
$startDate=$stDate;
              }else{
                $startDate='';
                $ary_err[]=$stDate;
              }
                          
              if(!is_array($edDate)){
$endDate=$edDate;
              }else{
                $endDate='';
                $ary_err[]=$edDate;
              }
              if(!is_array($stDate) && !is_array($edDate)){
              $validatedate=$this->validateTwoDates($key,$stDate,$edDate);
              if(is_array( $validatedate)){
                $ary_err[]=$validatedate;
              }
            }
              $subary['startdate']= $startDate;
          
              $subary['enddate']=$edDate;
              $picName=$data[4];
              $validatePIC=$this->validateStringType($key,$picName,'Incharge');
             
              if(is_array($validatePIC)){
                
                $ary_err[]=$validatePIC;
              }

              $getPIC=$this->getStaffDetailsWithName($data[4],$request->company_id);
              if(!empty($getPIC)){
                $subary['pic']=array("id"=>$getPIC['id'],"name"=>$data[4]);
                $pic_id=$getPIC['id'];
              }else{
                $subary['pic']=array("id"=>0,"name"=>$data[4]);
                $pic_id=0;
              }
         
               $ary[]=$subary;
 
            //    if(!in_array(trim(($data[0])),$cat_ary)){
              
            //     array_push($cat_ary,trim(($data[0])));
             
            // }else{
              
            // }
            if(!empty($cat_ary)){
                $match=0;
            foreach($cat_ary as $cat_aryf){
     if(trim(strtolower($cat_aryf))==trim(strtolower(($data[0])))){
$match=1;
     }
    
            }
            if($match==0){
                array_push($cat_ary,trim(($data[0])));
             }
        }else{
            array_push($cat_ary,trim(($data[0])));
        }
             
                $cat_task_name=str_replace(' ', '',trim(strtolower($data[0])))."#@*@#".str_replace(' ', '',trim(strtolower($data[1])));
            if(!in_array($cat_task_name,$cat_ary_valid)){
                $cat_ary_valid[]=$cat_task_name;
                $cat_task_namev=trim(($data[0]))."#@*@#".trim(($data[1]));
                $cat_ary_valid_data[]=$cat_task_namev."#@*@#".trim($data[2])."#@*@#".trim($data[3])."#@*@#".trim($pic_id);
                }else{
                    $ary_err[]=array("error"=>"Duplicate Task : ".$data[0]." - ".$data[1]);
                }


                }

              
            }
        }catch(Exception $e){
            $ary_err=[];
            $ary_err[]=array("error"=>"Upload Correct Format Data In Excel");
              return array("status"=>"error","data"=>json_encode($ary_err,true));
        }
         
        }

        if(empty($cat_ary)){
            $ary_err[]=array("error"=>"No Data In Excel");
        }
       if(!empty($ary_err)){
        return array("status"=>"error","data"=>json_encode($ary_err,true));
       }
    
    
      $templateAry= $this->convertTaskTemplate($cat_ary,$cat_ary_valid_data);
    
     $insertTemplate=$this->insertTemplate($templateAry['template'],$request);
       return array("status"=>"success","data"=>$templateAry['data'],"catary"=>$task,"template_id"=>$insertTemplate);
        
    }

    public function validatedateFormat($key,$value,$type){
        $asKey=$key;
      if(is_numeric($value)){
        $prevYear=date("Y",strtotime("-1year"));
        $fuYear=date("Y",strtotime("+1year"));
        $getyr=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format("Y");
        if(($prevYear==($getyr)) || ($fuYear==($getyr)) || ($getyr==date("Y"))){
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format("Y-m-d");   
         }else{
            return array("error"=>"Invalid ".$type." Format.Line number ".($asKey+1)." - Year Format not correct ".$getyr);
        }
         }else{
     
        return array("error"=>"Invalid ".$type." Format. Date Format is (DD/MM/YYYY) - Line number ".($asKey+1));
      }
    }

    public function validateStringType($key,$value,$type){
        $asKey=$key;
        if(trim($value)){
        if($type=='Incharge'){
        if (preg_match('/^[A-Za-z ]*$/', $value)) {
            $strlen=strlen($value);
            if($strlen>=30){
                return array("error"=>$type." name too Long( ".$value." ) - Line number ".($asKey+1));
            }
            if($strlen>0 && $strlen<=2){
                return array("error"=>$type." name too small( ".$value." ) - Line number ".($asKey+1));
            }
           return true;
        }else{
            return array("error"=>"Invalid ".$type." name ( ".$value." ) - Line number ".($asKey+1));
        }
    }else{
        if (preg_match('/^[A-Za-z0-9-_& - \/ ]*$/', $value)) {
            $strlen=strlen($value);
            if($strlen>=50){
                return array("error"=>$type." name too Long( ".$value." ) - Line number ".($asKey+1));
            }
            if($strlen>0 && $strlen<=2){
                return array("error"=>$type." name too small( ".$value." ) - Line number ".($asKey+1));
            }
            return true;
         }else{
             return array("error"=>"Invalid ".$type." name ( ".$value." ) - Line number ".($asKey+1));
         }
    }
}else{
    return array("error"=>"".$type." name is Required,Line number ".($asKey+1));
}
    }

    public static function getStaffDetailsWithName($name,$company_id){
       return staff::select('id', 'first_name', 'last_name')->where("company_id",$company_id)
        ->whereRaw('CONCAT(first_name, " ", last_name) LIKE ?', ['%'.trim($name).'%'])
        ->first();
    }

    public function convertTaskTemplate($catary,$catsubcatary){
        //dd($catary,$catsubcatary);
        $ary_task_apt=[];
        $ary_task_apt_data=[];
        foreach($catary as $key => $cat){
            $ary_reas=[];
            $scat_ary=[];
            $ary_reas_data=[];
            $scat_ary_data=[];
            foreach($catsubcatary as $keyv=>$subcat){
                $esc=explode("#@*@#",$subcat);
                if(strtolower(trim($esc[0]))==strtolower(trim($cat))){
                $scat_ary[]=(trim($esc[1]));

               // $scat_ary_data[]=array("task"=>ucfirst(trim($esc[1])),"start_date"=>$this->validatedateFormatData(trim($esc[2])),"end_date"=>$this->validatedateFormatData(trim($esc[3])),"pic_id"=>$esc[4]);
                $scat_ary_data[]=(trim($esc[1]))."@#$".$this->validatedateFormatData(trim($esc[2]))."@#$".$this->validatedateFormatData(trim($esc[3]))."@#$".$esc[4];
                }
            }
            $ary_reas["task_title"]=ucfirst(trim($cat));
            $ary_reas["task_subtitles"]=$scat_ary;
            $ary_task_apt[]=$ary_reas;

            $ary_reas_data["task_title"]=ucfirst(trim($cat));
            $ary_reas_data["task_subtitles"]=$scat_ary_data;
            $ary_task_apt_data[]=$ary_reas_data;
        }

        return array("template"=>$ary_task_apt,"data"=>$ary_task_apt_data);
    }

    public function validatedateFormatData($value){
       
      if(is_numeric($value)){
   
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format("Y-m-d");   
        
    }
}
public function insertTemplate($value,$request){
  
    $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
    $newTemplateArr = [];
        $newTemplateArr['company_id']= $request->company_id;
        $newTemplateArr['workspace_id']= $request->workspace_id;
        $newTemplateArr['user_id']= $companyDetails['user_id'];
        $newTemplateArr['staff_id']=$request->staff_id??0;
        $newTemplateArr['order_id']= $request->order_id;
        $newTemplateArr['template_name']= $request->template_name;
        $newTemplateArr['status']='1';
        $newTemplateArr['is_default']='1';
        $newTemplateArr['task_template_structure']= json_encode($value,true);
        $newTemplateArr['created_by']= $companyDetails['user_id'];
        $newTemplateArr['created_user_type']= 'User';
        $newTemplateArr['created_at']= date('Y-m-d H:i:s');
        $newTemplateArr['updated_at']= date('Y-m-d H:i:s');
        OrderTemplate::insert($newTemplateArr);
        $templateID = DB::getPdo()->lastInsertId();
        return $templateID;
}

public function validateTwoDates($key,$startDate,$endDate){
    $akey=$key;
    if (strtotime($startDate) <= strtotime($endDate)) {

    }else{
        return array("error"=>"Start and End Date Not valid - Line number ".($akey+1));
    }
}
   
}
